<?php

namespace App\Http\Controllers\Reports;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgedSupplierController extends Controller
{
    private string $prefix = '0_';

    // ── FA transaction type constants (supplier-side) ─────────────────────────
    private const ST_SUPPINVOICE  = 20;   // Purchase Invoice  → positive
    private const ST_SUPPCREDIT   = 21;   // Supplier Credit   → negative
    private const ST_SUPPAYMENT   = 22;   // Supplier Payment  → negative
    private const ST_BANKPAYMENT  = 4;    // Bank Payment      → negative

    // ── Default aging thresholds ──────────────────────────────────────────────
    private const DEFAULT_AGING_DAYS = [30, 60, 90];

    // ─────────────────────────────────────────────────────────────────────────
    // GET /reports/aged-supplier-analysis  —  show parameter form
    // ─────────────────────────────────────────────────────────────────────────
    public function index()
    {
        $suppliers = $this->getAllSuppliers();

        return view('reports.purchases.aged-supplier-params', compact('suppliers'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /reports/aged-supplier-analysis/generate  —  build & stream PDF
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

        $d1 = (int) $request->input('aging_d1', self::DEFAULT_AGING_DAYS[0]);
        $d2 = (int) $request->input('aging_d2', self::DEFAULT_AGING_DAYS[1]);
        $d3 = (int) $request->input('aging_d3', self::DEFAULT_AGING_DAYS[2]);

        // Column labels derived from thresholds
        $agingLabels = [
            'b1' => "1-{$d1} Days",
            'b2' => ($d1 + 1) . "-{$d2} Days",
            'b3' => ($d2 + 1) . "-{$d3} Days",
            'b4' => "Over {$d3} Days",
        ];

        // ── Other parameters ──────────────────────────────────────────────
        $supplierId  = $request->input('supplier_id') ?: null;
        $showAll     = $request->boolean('show_allocated');
        $summaryOnly = $request->boolean('summary_only');
        $noZeros     = $request->boolean('suppress_zeros');

        // ── Determine supplier list ───────────────────────────────────────
        if ($supplierId) {
            $suppliers = DB::table($this->prefix . 'suppliers')
                ->where('supplier_id', $supplierId)
                ->get(['supplier_id', 'supp_name']);
        } else {
            $suppliers = DB::table($this->prefix . 'suppliers')
                ->where('inactive', 0)
                ->orderBy('supp_name')
                ->get(['supplier_id', 'supp_name']);
        }

        // ── Supplier label (for PDF header) ───────────────────────────────
        $supplierLabel = 'All Suppliers';
        if ($supplierId) {
            $sn = DB::table($this->prefix . 'suppliers')
                ->where('supplier_id', $supplierId)
                ->value('supp_name');
            $supplierLabel = $sn ?? $supplierId;
        }

        // ── Build per-supplier data ───────────────────────────────────────
        $suppliersData = [];

        foreach ($suppliers as $supplier) {

            $suprec = $this->getSupplierDetails(
                $supplier->supplier_id, $fromDateStr, $toDateStr, $showAll, $d1, $d2, $d3
            );

            if (!$suprec) {
                continue;
            }

            if ($noZeros && abs((float) $suprec->Balance) < 0.001) {
                continue;
            }

            $balance = (float) $suprec->Balance;
            $due     = (float) $suprec->Due;
            $ov1     = (float) $suprec->Overdue1;
            $ov2     = (float) $suprec->Overdue2;
            $ov3     = (float) $suprec->Overdue3;

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
                    $supplier->supplier_id, $fromDateStr, $toDateStr, $showAll, $d1, $d2, $d3
                );

                foreach ($transactions as $tx) {
                    $txBal = (float) $tx->Balance;
                    $txDue = (float) $tx->Due;
                    $txOv1 = (float) $tx->Overdue1;
                    $txOv2 = (float) $tx->Overdue2;
                    $txOv3 = (float) $tx->Overdue3;

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

            $suppliersData[] = [
                'name'         => $supplier->supp_name,
                'supplier_id'  => $supplier->supplier_id,
                'transactions' => $txRows,
                'totals'       => $totals,
            ];
        }

        // ── Logo (base64 for Dompdf) ──────────────────────────────────────
        $logoSrc = null;
        try {
            $logo = DB::table('company_settings')->where('key', 'logo')->value('value');
            if ($logo && file_exists(public_path($logo))) {
                $logoSrc = 'data:image/' . pathinfo($logo, PATHINFO_EXTENSION)
                    . ';base64,' . base64_encode(file_get_contents(public_path($logo)));
            }
        } catch (\Throwable) {}

        // ── Grand totals ──────────────────────────────────────────────────
        $grand = ['current' => 0, 'b1' => 0, 'b2' => 0, 'b3' => 0, 'b4' => 0, 'balance' => 0];
        foreach ($suppliersData as $s) {
            $grand['current'] += $s['totals']['current'];
            $grand['b1']      += $s['totals']['b1'];
            $grand['b2']      += $s['totals']['b2'];
            $grand['b3']      += $s['totals']['b3'];
            $grand['b4']      += $s['totals']['b4'];
            $grand['balance'] += $s['totals']['balance'];
        }

        // ── Render PDF ────────────────────────────────────────────────────
        $pdf = Pdf::loadView('reports.purchases.aged-supplier-analysis', array_merge(
            compact('suppliersData', 'from', 'to', 'grand', 'supplierLabel', 'logoSrc', 'agingLabels'),
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

        $filename     = 'aged-supplier-analysis-' . $to->format('Y-m-d') . '.pdf';
        $generationMs = (int) round((microtime(true) - $startTime) * 1000);

        $this->logReportRun($request, $generationMs, [
            'from'           => $from->toDateString(),
            'to'             => $to->toDateString(),
            'aging_days'     => [$d1, $d2, $d3],
            'supplier_id'    => $supplierId,
            'supplier_name'  => $supplierLabel,
            'suppliers'      => count($suppliersData),
            'show_allocated' => $showAll,
            'summary_only'   => $summaryOnly,
            'suppress_zeros' => $noZeros,
        ]);

        return $pdf->stream($filename);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // getSupplierDetails() — aggregate SUM for the bold summary row
    // ─────────────────────────────────────────────────────────────────────────
    private function getSupplierDetails(
        int    $supplierId,
        string $fromDate,
        string $toDate,
        bool   $showAll,
        int    $d1,
        int    $d2,
        int    $d3
    ) {
        $p        = $this->prefix;
        $negTypes = implode(',', [self::ST_SUPPCREDIT, self::ST_SUPPAYMENT, self::ST_BANKPAYMENT]);
        $allocSub = $showAll ? '' : '- trans.alloc';
        $si       = self::ST_SUPPINVOICE;

        // Absolute value of the transaction
        $val = "IF(`type` IN({$negTypes}), -1, 1)
                * (ABS(trans.ov_amount + trans.ov_gst + trans.ov_discount) {$allocSub})";

        // Due-date expression: invoices use due_date, everything else uses tran_date
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

            FROM {$p}supp_trans trans
            WHERE supplier_id = ?
              AND tran_date   >= '{$fromDate}'
              AND tran_date   <= '{$toDate}'
        ";

        $rows = DB::select($sql, [$supplierId]);
        return $rows[0] ?? null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // getInvoices() — individual transaction rows for detail lines
    // ─────────────────────────────────────────────────────────────────────────
    private function getInvoices(
        int    $supplierId,
        string $fromDate,
        string $toDate,
        bool   $showAll,
        int    $d1,
        int    $d2,
        int    $d3
    ): array {
        $p        = $this->prefix;
        $negTypes = implode(',', [self::ST_SUPPCREDIT, self::ST_SUPPAYMENT, self::ST_BANKPAYMENT]);
        $allocSub = $showAll ? '' : '- trans.alloc';
        $si       = self::ST_SUPPINVOICE;

        $val = "IF(`type` IN({$negTypes}), -1, 1)
                * (ABS(trans.ov_amount + trans.ov_gst + trans.ov_discount) {$allocSub})";

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
            FROM {$p}supp_trans trans
            WHERE supplier_id = ?
              AND tran_date  >= '{$fromDate}'
              AND tran_date  <= '{$toDate}'
              AND ABS({$val}) > 0.001
            ORDER BY tran_date
        ";

        return DB::select($sql, [$supplierId]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Log report run to report_logs
    // ─────────────────────────────────────────────────────────────────────────
    private function logReportRun(Request $request, int $generationMs, array $parameters): void
    {
        try {
            $menuItemId = DB::table('menu_items')
                ->where('slug', 'aged-supplier-analysis')
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
            Log::warning('aged-supplier-analysis: report_log insert failed — ' . $e->getMessage());
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function getAllSuppliers()
    {
        return DB::table($this->prefix . 'suppliers')
            ->where('inactive', 0)
            ->orderBy('supp_name')
            ->get(['supplier_id', 'supp_name', 'curr_code']);
    }
}
