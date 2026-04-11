<?php

namespace App\Http\Controllers\Reports;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgedCustomerController extends Controller
{
    private string $prefix = '0_';

    // ── FA transaction type constants (from FA includes/types.inc) ────────────
    private const ST_CUSTCREDIT   = 11;  // Credit Note      → negative sign
    private const ST_CUSTDELIVERY = 13;  // Delivery         → excluded
    private const ST_CUSTPAYMENT  = 12;  // Customer Payment → negative sign
    private const ST_SALESINVOICE = 10;  // Sales Invoice
    private const ST_BANKDEPOSIT  = 2;   // Bank Deposit     → negative sign

    // ─────────────────────────────────────────────────────────────────────────
    // GET /reports/aged-customer-analysis  —  show parameter form
    // ─────────────────────────────────────────────────────────────────────────
    public function index()
    {
        $salesmen  = $this->getSalesmanList();
        $customers = $this->getAllCustomers();

        return view('reports.sales.aged-customer-params', compact('salesmen', 'customers'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /reports/aged-customer-analysis/customers?salesman_code=X  (AJAX)
    // ─────────────────────────────────────────────────────────────────────────
    public function customersBySalesman(Request $request)
    {
        $code = $request->get('salesman_code');

        if (!$code) {
            return response()->json($this->getAllCustomers());
        }

        $p    = $this->prefix;
        $rows = DB::select("
            SELECT DISTINCT d.debtor_no, d.name, d.curr_code, d.inactive
            FROM {$p}debtors_master d
            INNER JOIN {$p}cust_branch b ON d.debtor_no = b.debtor_no
            WHERE b.salesman = ?
              AND d.inactive = 0
            ORDER BY d.name
        ", [$code]);

        return response()->json($rows);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /reports/aged-customer-analysis/generate  —  stream PDF
    //
    // Parameters (all GET):
    //   to              string  date  Aging / end date  (ONLY date needed — no from)
    //   salesman_code   string        Filter by salesman (optional)
    //   debtor_no       string        Filter by single customer (optional)
    //   show_allocated  "1"|null      Show Also Allocated (FA: $show_all)
    //   summary_only    "1"|null      Summary Only        (FA: $summaryOnly)
    //   suppress_zeros  "1"|null      Suppress Zeros      (FA: $no_zeros)
    // ─────────────────────────────────────────────────────────────────────────
    public function generate(Request $request)
    {
        $request->validate(['to' => 'required|date']);

        $startTime = microtime(true);

        // ── Parameters ────────────────────────────────────────────────────
        $to           = Carbon::parse($request->input('to'));
        $toDateStr    = $to->format('Y-m-d');           // SQL-format date for raw queries

        $debtorNo     = $request->input('debtor_no')     ?: null;
        $salesmanCode = $request->input('salesman_code') ?: null;

        // FA flags — exactly matching FA parameter names / meanings
        $showAll      = $request->boolean('show_allocated'); // FA: $show_all / $all
        $summaryOnly  = $request->boolean('summary_only');   // FA: $summaryOnly
        $noZeros      = $request->boolean('suppress_zeros'); // FA: $no_zeros

        // ── Aging period from FA company prefs ────────────────────────────
        [$pastDueDays1, $pastDueDays2] = $this->getPastDueDays();

        // ── Determine debtor list ─────────────────────────────────────────
        if ($debtorNo) {
            $debtors = DB::table($this->prefix . 'debtors_master')
                ->where('debtor_no', $debtorNo)
                ->get(['debtor_no', 'name']);

        } elseif ($salesmanCode) {
            $p       = $this->prefix;
            $debtors = collect(DB::select("
                SELECT DISTINCT d.debtor_no, d.name
                FROM {$p}debtors_master d
                INNER JOIN {$p}cust_branch b ON d.debtor_no = b.debtor_no
                WHERE b.salesman = ?
                  AND d.inactive = 0
                ORDER BY d.name
            ", [$salesmanCode]));

        } else {
            $debtors = DB::table($this->prefix . 'debtors_master')
                ->where('inactive', 0)
                ->orderBy('name')
                ->get(['debtor_no', 'name']);
        }

        // ── Salesman label ────────────────────────────────────────────────
        $salesmanLabel = 'All Salesmen';
        if ($salesmanCode) {
            $sm = DB::table($this->prefix . 'salesman')
                ->where('salesman_code', $salesmanCode)
                ->value('salesman_name');
            $salesmanLabel = $sm ?? $salesmanCode;
        }

        // ── Build per-customer data ───────────────────────────────────────
        $customers = [];

        foreach ($debtors as $debtor) {

            // ── Summary totals via get_customer_details() equivalent ───────
            // FA uses this for the bold summary/totals row — a single
            // aggregate (SUM) query, NOT summing individual row results.
            $custrec = $this->getCustomerDetails(
                $debtor->debtor_no, $toDateStr, $showAll, $pastDueDays1, $pastDueDays2
            );

            if (!$custrec) {
                continue;
            }

            // ── Suppress Zeros (FA: $no_zeros) ────────────────────────────
            // FA: if ($no_zeros && floatcmp(array_sum($str), 0) == 0) continue;
            $strSum = $custrec->Balance; // Balance = grand total
            if ($noZeros && abs((float)$strSum) < 0.001) {
                continue;
            }

            // ── Aging buckets from aggregate (FA column ordering) ─────────
            $balance = (float) $custrec->Balance;
            $due     = (float) $custrec->Due;
            $over1   = (float) $custrec->Overdue1;
            $over2   = (float) $custrec->Overdue2;

            $totals = [
                'current'    => $balance - $due,        // FA: Balance - Due
                'days_1_30'  => $due - $over1,          // FA: Due - Overdue1
                'days_31_60' => $over1 - $over2,        // FA: Overdue1 - Overdue2
                'over_60'    => $over2,                  // FA: Overdue2
                'balance'    => $balance,                // FA: Balance
            ];

            // ── Detail transaction rows via get_invoices() equivalent ──────
            // Only fetched when NOT summaryOnly
            $txRows = [];

            if (!$summaryOnly) {
                $transactions = $this->getInvoices(
                    $debtor->debtor_no, $toDateStr, $showAll, $pastDueDays1, $pastDueDays2
                );

                foreach ($transactions as $tx) {
                    $txBal  = (float) $tx->Balance;
                    $txDue  = (float) $tx->Due;
                    $txOv1  = (float) $tx->Overdue1;
                    $txOv2  = (float) $tx->Overdue2;

                    // FA: $datediff = $now - strtotime($trans['tran_date'])
                    //     days = round($datediff / (60 * 60 * 24))
                    // Uses TODAY (time()), NOT the $to date
                    $days = (int) round(
                        (Carbon::now()->startOfDay()->timestamp -
                            Carbon::parse($tx->tran_date)->startOfDay()->timestamp)
                        / 86400
                    );

                    $txRows[] = [
                        'type'      => $tx->type,
                        'reference' => $tx->reference ?? '',
                        'tran_date' => $tx->tran_date,
                        'days'      => $days,
                        'current'   => $txBal - $txDue,
                        'b1_30'     => $txDue - $txOv1,
                        'b31_60'    => $txOv1 - $txOv2,
                        'bOver60'   => $txOv2,
                        'balance'   => $txBal,
                    ];
                }
            }

            $customers[] = [
                'name'         => $debtor->name,
                'debtor_no'    => $debtor->debtor_no,
                'transactions' => $txRows,
                'totals'       => $totals,
            ];
        }

        // ── Logo (base64 — Dompdf cannot make HTTP requests) ─────────────
        $logoPath = public_path('images/lucky-logo.png');
        $logoSrc  = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        // ── Grand totals ──────────────────────────────────────────────────
        $grand = ['current' => 0, 'days_1_30' => 0, 'days_31_60' => 0, 'over_60' => 0, 'balance' => 0];
        foreach ($customers as $c) {
            $grand['current']    += $c['totals']['current'];
            $grand['days_1_30']  += $c['totals']['days_1_30'];
            $grand['days_31_60'] += $c['totals']['days_31_60'];
            $grand['over_60']    += $c['totals']['over_60'];
            $grand['balance']    += $c['totals']['balance'];
        }

        // ── Render PDF ────────────────────────────────────────────────────
        $pdf = Pdf::loadView('reports.sales.aged-customer-analysis', array_merge(
            compact('customers', 'to', 'grand', 'salesmanLabel', 'logoSrc'),
            [
                'showAllocated' => $showAll,
                'summaryOnly'   => $summaryOnly,
                'suppressZeros' => $noZeros,
                'pastDueDays1'  => $pastDueDays1,
                'pastDueDays2'  => $pastDueDays2,
            ]
        ))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 150,
            ]);

        $filename     = 'aged-customer-analysis-' . $to->format('Y-m-d') . '.pdf';
        $generationMs = (int) round((microtime(true) - $startTime) * 1000);

        $this->logReportRun($request, $generationMs, [
            'to'             => $to->toDateString(),
            'salesman_code'  => $salesmanCode,
            'salesman_name'  => $salesmanLabel,
            'debtor_no'      => $debtorNo,
            'customers'      => count($customers),
            'show_allocated' => $showAll,
            'summary_only'   => $summaryOnly,
            'suppress_zeros' => $noZeros,
        ]);

        return $pdf->stream($filename);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // get_customer_details() equivalent
    //
    // Mirrors FA's get_customer_details($customer_id, $to, $show_all):
    //   Returns a SINGLE row with SUM(Balance), SUM(Due), SUM(Overdue1), SUM(Overdue2)
    //   Used for the bold summary row only.
    //
    // Raw SQL used (not query builder) to avoid binding order issues.
    // ─────────────────────────────────────────────────────────────────────────
    private function getCustomerDetails(
        string $debtorNo,
        string $toDate,
        bool   $showAll,
        int    $pastDueDays1,
        int    $pastDueDays2
    ) {
        $p        = $this->prefix;
        $negTypes = implode(',', [self::ST_CUSTCREDIT, self::ST_CUSTPAYMENT, self::ST_BANKDEPOSIT]);
        $allocSub = $showAll ? '' : '- trans.alloc';

        $sql = "
            SELECT
                SUM(
                    IF(`type` IN({$negTypes}), -1, 1)
                    * (
                        IF(trans.prep_amount, trans.prep_amount,
                           ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight
                               + trans.ov_freight_tax + trans.ov_discount)
                        ) {$allocSub}
                      )
                ) AS Balance,

                SUM(IF(
                    (TO_DAYS('{$toDate}') - TO_DAYS(IF(type = " . self::ST_SALESINVOICE . ", due_date, tran_date))) >= 0,
                    IF(`type` IN({$negTypes}), -1, 1)
                    * (IF(trans.prep_amount, trans.prep_amount,
                          ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight
                              + trans.ov_freight_tax + trans.ov_discount)
                       ) {$allocSub}),
                    0
                )) AS Due,

                SUM(IF(
                    (TO_DAYS('{$toDate}') - TO_DAYS(IF(type = " . self::ST_SALESINVOICE . ", due_date, tran_date))) >= {$pastDueDays1},
                    IF(`type` IN({$negTypes}), -1, 1)
                    * (IF(trans.prep_amount, trans.prep_amount,
                          ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight
                              + trans.ov_freight_tax + trans.ov_discount)
                       ) {$allocSub}),
                    0
                )) AS Overdue1,

                SUM(IF(
                    (TO_DAYS('{$toDate}') - TO_DAYS(IF(type = " . self::ST_SALESINVOICE . ", due_date, tran_date))) >= {$pastDueDays2},
                    IF(`type` IN({$negTypes}), -1, 1)
                    * (IF(trans.prep_amount, trans.prep_amount,
                          ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight
                              + trans.ov_freight_tax + trans.ov_discount)
                       ) {$allocSub}),
                    0
                )) AS Overdue2

            FROM {$p}debtor_trans trans
            WHERE debtor_no = ?
              AND type <> " . self::ST_CUSTDELIVERY . "
              AND tran_date <= '{$toDate}'
        ";

        $rows = DB::select($sql, [$debtorNo]);
        return $rows[0] ?? null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // get_invoices() equivalent
    //
    // Mirrors FA's get_invoices($customer_id, $to, $all) EXACTLY.
    // Returns individual transaction rows with Balance/Due/Overdue1/Overdue2.
    //
    // Raw SQL avoids Laravel query builder binding order issues.
    // ─────────────────────────────────────────────────────────────────────────
    private function getInvoices(
        string $debtorNo,
        string $toDate,
        bool   $showAll,
        int    $pastDueDays1,
        int    $pastDueDays2
    ): array {
        $p        = $this->prefix;
        $negTypes = implode(',', [self::ST_CUSTCREDIT, self::ST_CUSTPAYMENT, self::ST_BANKDEPOSIT]);

        // FA: ($all ? '' : '- trans.alloc')
        $allocSub = $showAll ? '' : '- trans.alloc';

        // FA value expression: sign * (prep_amount OR ABS(ov_...) [- alloc])
        $value = "
            IF(`type` IN({$negTypes}), -1, 1)
            * (
                IF(trans.prep_amount, trans.prep_amount,
                   ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight
                       + trans.ov_freight_tax + trans.ov_discount)
                ) {$allocSub}
              )
        ";

        // FA due expression: invoices use due_date, others use tran_date
        $due = "IF(type = " . self::ST_SALESINVOICE . ", due_date, tran_date)";

        // Exact FA SQL from get_invoices()
        $sql = "
            SELECT
                type,
                reference,
                tran_date,
                ({$value})                                                                          AS Balance,
                IF((TO_DAYS('{$toDate}') - TO_DAYS({$due})) >= 0,            ({$value}), 0)  AS Due,
                IF((TO_DAYS('{$toDate}') - TO_DAYS({$due})) >= {$pastDueDays1}, ({$value}), 0)  AS Overdue1,
                IF((TO_DAYS('{$toDate}') - TO_DAYS({$due})) >= {$pastDueDays2}, ({$value}), 0)  AS Overdue2
            FROM {$p}debtor_trans trans
            WHERE type <> " . self::ST_CUSTDELIVERY . "
              AND debtor_no = ?
              AND tran_date <= '{$toDate}'
              AND ABS({$value}) > 0.001
            ORDER BY tran_date
        ";

        return DB::select($sql, [$debtorNo]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Read past_due_days from FA sys_prefs (default 30 → period 1-30 / 31-60 / 60+)
    // ─────────────────────────────────────────────────────────────────────────
    private function getPastDueDays(): array
    {
        // Try both possible column name styles used in different FA versions
        $days1 = (int) (
            DB::table($this->prefix . 'sys_prefs')
                ->where('name', 'past_due_days')   // newer FA
                ->value('value')
            ?? DB::table($this->prefix . 'sys_prefs')
            ->where('name', 'past_due_days')         // older FA
            ->value('value')
            ?? 30
        );

        return [$days1, $days1 * 2];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Log report run to report_logs
    // ─────────────────────────────────────────────────────────────────────────
    private function logReportRun(Request $request, int $generationMs, array $parameters): void
    {
        try {
            $menuItemId = DB::table('menu_items')
                ->where('slug', 'aged-customer-analysis')
                ->value('id');

            if (!$menuItemId) {
                return;
            }

            DB::table('report_logs')->insert([
                'user_id'            => auth()->id(),
                'report_id'          => $menuItemId,
                'parameters'         => json_encode($parameters),
                'ip_address'         => $request->ip(),
                'generation_time_ms' => $generationMs,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

        } catch (\Throwable $e) {
            Log::warning('aged-customer-analysis: report_log insert failed — ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function getSalesmanList()
    {
        $p = $this->prefix;
        return collect(DB::select("
            SELECT DISTINCT s.salesman_code, s.salesman_name
            FROM {$p}debtors_master d
            INNER JOIN {$p}cust_branch b ON d.debtor_no = b.debtor_no
            INNER JOIN {$p}areas a        ON b.area      = a.area_code
            INNER JOIN {$p}salesman s     ON b.salesman  = s.salesman_code
            WHERE d.inactive = 0
            ORDER BY s.salesman_name
        "));
    }

    private function getAllCustomers()
    {
        return DB::table($this->prefix . 'debtors_master')
            ->where('inactive', 0)
            ->orderBy('name')
            ->get(['debtor_no', 'name', 'curr_code', 'inactive']);
    }
}
