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

    // ── Default aging thresholds (used when user doesn't override) ────────────
    private const DEFAULT_AGING_DAYS = [30, 60, 90];

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
    // Aging parameters (all GET):
    //   from            string  date    Start date
    //   to              string  date    End / aging date
    //   aging_d1        int             1st threshold  e.g. 30  → bucket "1-30 Days"
    //   aging_d2        int             2nd threshold  e.g. 60  → bucket "31-60 Days"
    //   aging_d3        int             3rd threshold  e.g. 90  → bucket "61-90 Days"
    //   salesman_code   string          Filter by salesman (optional)
    //   debtor_no       string          Filter by single customer (optional)
    //   show_allocated  "1"|null        Show Also Allocated
    //   summary_only    "1"|null        Summary Only
    //   suppress_zeros  "1"|null        Suppress Zeros
    // ─────────────────────────────────────────────────────────────────────────
    public function generate(Request $request)
    {
        $request->validate([
            'from'     => 'required|date',
            'to'       => 'required|date|after_or_equal:from',
            'aging_d1' => 'required|integer|min:1|max:9999',
            'aging_d2' => 'required|integer|min:1|max:9999|gt:aging_d1',
            'aging_d3' => 'required|integer|min:1|max:9999|gt:aging_d2',
        ], [
            'aging_d2.gt' => 'Period 2 must be greater than Period 1.',
            'aging_d3.gt' => 'Period 3 must be greater than Period 2.',
        ]);

        $startTime = microtime(true);

        // ── Date range ────────────────────────────────────────────────────
        $from        = Carbon::parse($request->input('from'));
        $fromDateStr = $from->format('Y-m-d');
        $to          = Carbon::parse($request->input('to'));
        $toDateStr   = $to->format('Y-m-d');

        // ── Custom aging thresholds ───────────────────────────────────────
        // Three thresholds create FOUR aging buckets:
        //   Bucket 1: 1  →  d1          e.g. 1-30 Days
        //   Bucket 2: d1+1 → d2         e.g. 31-60 Days
        //   Bucket 3: d2+1 → d3         e.g. 61-90 Days
        //   Bucket 4: over d3            e.g. Over 90 Days
        $d1 = (int) $request->input('aging_d1', self::DEFAULT_AGING_DAYS[0]);
        $d2 = (int) $request->input('aging_d2', self::DEFAULT_AGING_DAYS[1]);
        $d3 = (int) $request->input('aging_d3', self::DEFAULT_AGING_DAYS[2]);

        // Column labels derived from thresholds (passed to blade)
        $agingLabels = [
            'b1'  => "1-{$d1} Days",
            'b2'  => ($d1 + 1) . "-{$d2} Days",
            'b3'  => ($d2 + 1) . "-{$d3} Days",
            'b4'  => "Over {$d3} Days",
        ];

        // ── Other parameters ──────────────────────────────────────────────
        $debtorNo     = $request->input('debtor_no')     ?: null;
        $salesmanCode = $request->input('salesman_code') ?: null;
        $showAll      = $request->boolean('show_allocated');
        $summaryOnly  = $request->boolean('summary_only');
        $noZeros      = $request->boolean('suppress_zeros');

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

        } elseif ($debtorNo) {
            $p  = $this->prefix;
            $sm = DB::select("
                SELECT s.salesman_name
                FROM {$p}cust_branch b
                INNER JOIN {$p}salesman s ON b.salesman = s.salesman_code
                WHERE b.debtor_no = ?
                LIMIT 1
            ", [$debtorNo]);
            if (!empty($sm)) {
                $salesmanLabel = $sm[0]->salesman_name;
            }
        }

        // ── Build per-customer data ───────────────────────────────────────
        $customers = [];

        foreach ($debtors as $debtor) {

            // Summary row — aggregate SUM query
            $custrec = $this->getCustomerDetails(
                $debtor->debtor_no, $fromDateStr, $toDateStr, $showAll, $d1, $d2, $d3
            );

            if (!$custrec) {
                continue;
            }

            if ($noZeros && abs((float) $custrec->Balance) < 0.001) {
                continue;
            }

            // ── Compute 4 aging buckets from aggregate ────────────────────
            // Logic: each bucket = higher_threshold - lower_threshold
            //   Current = not yet due (Balance - Due)
            //   b1      = due but < d1     (Due - Overdue1)
            //   b2      = d1 to d2         (Overdue1 - Overdue2)
            //   b3      = d2 to d3         (Overdue2 - Overdue3)
            //   b4      = over d3          (Overdue3)
            $balance = (float) $custrec->Balance;
            $due     = (float) $custrec->Due;
            $ov1     = (float) $custrec->Overdue1;
            $ov2     = (float) $custrec->Overdue2;
            $ov3     = (float) $custrec->Overdue3;

            $totals = [
                'current' => $balance - $due,
                'b1'      => $due - $ov1,
                'b2'      => $ov1 - $ov2,
                'b3'      => $ov2 - $ov3,
                'b4'      => $ov3,
                'balance' => $balance,
            ];

            // ── Transaction detail rows ───────────────────────────────────
            $txRows = [];

            if (!$summaryOnly) {
                $transactions = $this->getInvoices(
                    $debtor->debtor_no, $fromDateStr, $toDateStr, $showAll, $d1, $d2, $d3
                );

                foreach ($transactions as $tx) {
                    $txBal = (float) $tx->Balance;
                    $txDue = (float) $tx->Due;
                    $txOv1 = (float) $tx->Overdue1;
                    $txOv2 = (float) $tx->Overdue2;
                    $txOv3 = (float) $tx->Overdue3;

//                    $days = (int) round(
//                        (Carbon::now()->startOfDay()->timestamp -
//                            Carbon::parse($tx->tran_date)->startOfDay()->timestamp)
//                        / 86400
//                    );

                    $days = Carbon::parse($tx->due_date_calc)->diffInDays($to, false);

                    $txRows[] = [
                        'type'      => $tx->type,
                        'reference' => $tx->reference ?? '',
                        'tran_date' => $tx->tran_date,
                        'days'      => $days,
                        'current'   => $txBal - $txDue,
                        'b1'        => $txDue - $txOv1,
                        'b2'        => $txOv1 - $txOv2,
                        'b3'        => $txOv2 - $txOv3,
                        'b4'        => $txOv3,
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
        $grand = ['current' => 0, 'b1' => 0, 'b2' => 0, 'b3' => 0, 'b4' => 0, 'balance' => 0];
        foreach ($customers as $c) {
            $grand['current'] += $c['totals']['current'];
            $grand['b1']      += $c['totals']['b1'];
            $grand['b2']      += $c['totals']['b2'];
            $grand['b3']      += $c['totals']['b3'];
            $grand['b4']      += $c['totals']['b4'];
            $grand['balance'] += $c['totals']['balance'];
        }

        // ── Render PDF ────────────────────────────────────────────────────
        $pdf = Pdf::loadView('reports.sales.aged-customer-analysis', array_merge(
            compact('customers', 'from', 'to', 'grand', 'salesmanLabel', 'logoSrc', 'agingLabels'),
            [
                'showAllocated' => $showAll,
                'summaryOnly'   => $summaryOnly,
                'suppressZeros' => $noZeros,
                'agingD1'       => $d1,
                'agingD2'       => $d2,
                'agingD3'       => $d3,
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
            'from'           => $from->toDateString(),
            'to'             => $to->toDateString(),
            'aging_days'     => [$d1, $d2, $d3],
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
    // getCustomerDetails() — aggregate SUM for the bold summary row
    //
    // Returns one row: Balance, Due, Overdue1, Overdue2, Overdue3
    // where Overdue1/2/3 correspond to the user's custom thresholds d1/d2/d3.
    // ─────────────────────────────────────────────────────────────────────────
    private function getCustomerDetails(
        string $debtorNo,
        string $fromDate,
        string $toDate,
        bool   $showAll,
        int    $d1,
        int    $d2,
        int    $d3
    ) {
        $p        = $this->prefix;
        $negTypes = implode(',', [self::ST_CUSTCREDIT, self::ST_CUSTPAYMENT, self::ST_BANKDEPOSIT]);
        $allocSub = $showAll ? '' : '- trans.alloc';
        $si       = self::ST_SALESINVOICE;

        // Reusable value expression
        $val = "IF(`type` IN({$negTypes}), -1, 1)
                * (IF(trans.prep_amount, trans.prep_amount,
                      ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight
                          + trans.ov_freight_tax + trans.ov_discount)
                   ) {$allocSub})";

        // Due-date expression
        $dueDate = "IF(type = {$si}, due_date, tran_date)";

        $sql = "
            SELECT
                SUM({$val}) AS Balance,

                SUM(IF((TO_DAYS('{$toDate}') - TO_DAYS({$dueDate})) >= 0,
                    {$val}, 0))   AS Due,

                SUM(IF((TO_DAYS('{$toDate}') - TO_DAYS({$dueDate})) >= {$d1},
                    {$val}, 0))   AS Overdue1,

                SUM(IF((TO_DAYS('{$toDate}') - TO_DAYS({$dueDate})) >= {$d2},
                    {$val}, 0))   AS Overdue2,

                SUM(IF((TO_DAYS('{$toDate}') - TO_DAYS({$dueDate})) >= {$d3},
                    {$val}, 0))   AS Overdue3

            FROM {$p}debtor_trans trans
            WHERE debtor_no  = ?
              AND type       <> " . self::ST_CUSTDELIVERY . "
              AND tran_date  >= '{$fromDate}'
              AND tran_date  <= '{$toDate}'
        ";

        $rows = DB::select($sql, [$debtorNo]);
        return $rows[0] ?? null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // getInvoices() — individual transaction rows
    //
    // Returns rows with: type, reference, tran_date,
    //                    Balance, Due, Overdue1, Overdue2, Overdue3
    // ─────────────────────────────────────────────────────────────────────────
    private function getInvoices(
        string $debtorNo,
        string $fromDate,
        string $toDate,
        bool   $showAll,
        int    $d1,
        int    $d2,
        int    $d3
    ): array {
        $p        = $this->prefix;
        $negTypes = implode(',', [self::ST_CUSTCREDIT, self::ST_CUSTPAYMENT, self::ST_BANKDEPOSIT]);
        $allocSub = $showAll ? '' : '- trans.alloc';
        $si       = self::ST_SALESINVOICE;

        $val = "IF(`type` IN({$negTypes}), -1, 1)
                * (IF(trans.prep_amount, trans.prep_amount,
                      ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight
                          + trans.ov_freight_tax + trans.ov_discount)
                   ) {$allocSub})";

        $dueDate = "IF(type = {$si}, due_date, tran_date)";

        $sql = "
            SELECT
                type,
                reference,
                tran_date,
                IF(type = {$si}, due_date, tran_date) AS due_date_calc,
                ({$val})  AS Balance,
                IF((TO_DAYS('{$toDate}') - TO_DAYS({$dueDate})) >= 0,    ({$val}), 0) AS Due,
                IF((TO_DAYS('{$toDate}') - TO_DAYS({$dueDate})) >= {$d1}, ({$val}), 0) AS Overdue1,
                IF((TO_DAYS('{$toDate}') - TO_DAYS({$dueDate})) >= {$d2}, ({$val}), 0) AS Overdue2,
                IF((TO_DAYS('{$toDate}') - TO_DAYS({$dueDate})) >= {$d3}, ({$val}), 0) AS Overdue3
            FROM {$p}debtor_trans trans
            WHERE type       <> " . self::ST_CUSTDELIVERY . "
              AND debtor_no   = ?
              AND tran_date  >= '{$fromDate}'
              AND tran_date  <= '{$toDate}'
              AND ABS({$val}) > 0.001
            ORDER BY tran_date
        ";

        return DB::select($sql, [$debtorNo]);
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

    // ── Helpers ───────────────────────────────────────────────────────────────

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
