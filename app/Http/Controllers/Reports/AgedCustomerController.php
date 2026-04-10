<?php

namespace App\Http\Controllers\Reports;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;          // composer require barryvdh/laravel-dompdf
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgedCustomerController extends Controller
{
    private string $prefix = '0_';

    // ─────────────────────────────────────────────────────────────────────────
    // GET  /reports/aged-customer-analysis
    // Show the parameter form
    // ─────────────────────────────────────────────────────────────────────────
    public function index()
    {
        $salesmen  = $this->getSalesmanList();
        $customers = $this->getAllCustomers();

        return view('reports.sales.aged-customer-params', compact('salesmen', 'customers'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET  /reports/aged-customer-analysis/customers?salesman_code=X   (AJAX)
    // Returns JSON list of customers filtered by salesman
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
    // GET  /reports/aged-customer-analysis/generate
    // Builds report data → renders Dompdf → streams PDF inline in browser
    // ─────────────────────────────────────────────────────────────────────────
    public function generate(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $startTime    = microtime(true);   // ← start timer for generation_time_ms

        $from         = $request->input('from');
        $to           = Carbon::parse($request->input('to'));
        $debtorNo     = $request->input('debtor_no')     ?: null;
        $salesmanCode = $request->input('salesman_code') ?: null;

        // ── Which debtors to include ──────────────────────────────────────
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

        // ── Salesman label for report header ──────────────────────────────
        $salesmanLabel = 'All Salesmen';
        if ($salesmanCode) {
            $sm = DB::table($this->prefix . 'salesman')
                ->where('salesman_code', $salesmanCode)
                ->value('salesman_name');
            $salesmanLabel = $sm ?? $salesmanCode;
        }

        // ── Build customer transaction + aging buckets ────────────────────
        $customers = [];

        foreach ($debtors as $debtor) {
            $transactions = $this->getTransactions(
                $debtor->debtor_no, $from, $to->toDateString()
            );

            if ($transactions->isEmpty()) {
                continue;
            }

            $totals = [
                'current'    => 0,
                'days_1_30'  => 0,
                'days_31_60' => 0,
                'over_60'    => 0,
                'balance'    => 0,
            ];

            $txRows = $transactions->map(function ($tx) use ($to, &$totals) {
                $balance = (float)$tx->TotalAmount - (float)($tx->Allocated ?? 0);
                $days    = (int) Carbon::parse($tx->tran_date)->diffInDays($to);

                $current = $b1_30 = $b31_60 = $bOver60 = 0;

                if      ($days <= 0)  { $current = $balance; }
                elseif  ($days <= 30) { $b1_30   = $balance; }
                elseif  ($days <= 60) { $b31_60  = $balance; }
                else                  { $bOver60 = $balance; }

                $totals['current']    += $current;
                $totals['days_1_30']  += $b1_30;
                $totals['days_31_60'] += $b31_60;
                $totals['over_60']    += $bOver60;
                $totals['balance']    += $balance;

                return (array)$tx + compact(
                        'days', 'current', 'b1_30', 'b31_60', 'bOver60', 'balance'
                    );
            })->toArray();

            $customers[] = [
                'name'         => $debtor->name,
                'debtor_no'    => $debtor->debtor_no,
                'transactions' => $txRows,
                'totals'       => $totals,
            ];
        }

        // ── Logo: must be an absolute file path for Dompdf ───────────────
        // Dompdf cannot load URLs — pass the filesystem path or a base64 string.
        $logoPath = public_path('images/lucky-logo.png');
        $logoSrc  = null;

        if (file_exists($logoPath)) {
            // Embed as base64 so Dompdf never has to make an HTTP request
            $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // ── Compute grand totals ──────────────────────────────────────────
        $grand = ['current' => 0, 'days_1_30' => 0, 'days_31_60' => 0, 'over_60' => 0, 'balance' => 0];
        foreach ($customers as $c) {
            $grand['current']    += $c['totals']['current'];
            $grand['days_1_30']  += $c['totals']['days_1_30'];
            $grand['days_31_60'] += $c['totals']['days_31_60'];
            $grand['over_60']    += $c['totals']['over_60'];
            $grand['balance']    += $c['totals']['balance'];
        }

        // ── Render blade → Dompdf ─────────────────────────────────────────
        $from = Carbon::parse($from);   // convert to Carbon so blade can call ->format()

        $pdf = Pdf::loadView('reports.sales.aged-customer-analysis', array_merge(
            compact('customers', 'to', 'from', 'grand', 'salesmanLabel', 'logoSrc')
        ))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont'         => 'DejaVu Sans',
                'isHtml5ParserEnabled'=> true,
                'isRemoteEnabled'     => false,
                'dpi'                 => 150,
            ]);

        $filename = 'aged-customer-analysis-' . $to->format('Y-m-d') . '.pdf';

        // ── Measure generation time & log the report run ──────────────────
        $generationMs = (int) round((microtime(true) - $startTime) * 1000);

        $this->logReportRun($request, $generationMs, [
            'from'          => $from->toDateString(),
            'to'            => $to->toDateString(),
            'salesman_code' => $salesmanCode,
            'debtor_no'     => $debtorNo,
            'salesman_name' => $salesmanLabel,
            'customers'     => count($customers),
        ]);

        // stream() opens in browser tab; download() forces Save dialog — swap as needed
        return $pdf->stream($filename);
    }

    private function logReportRun(Request $request, int $generationMs, array $parameters): void
    {
        try {
            // Look up the report record by its slug (must exist in reports table)
            $reportId = DB::table('menu_items')
                ->where('slug', 'aged-customer-anaylsis')
                ->value('id');

            if (!$reportId) {
                return;
            }

            DB::table('report_logs')->insert([
                'user_id'            => auth()->id(),
                'report_id'          => $reportId,
                'parameters'         => json_encode($parameters),
                'ip_address'         => $request->ip(),
                'generation_time_ms' => $generationMs,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        } catch (\Throwable $e) {
            // Never let logging break the actual PDF response
            \Illuminate\Support\Facades\Log::warning(
                'aged-customer-analysis: report_log insert failed — ' . $e->getMessage()
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
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

    // ── Core FA transaction query — fixed for MySQL ONLY_FULL_GROUP_BY ────────
    private function getTransactions(string $debtorNo, string $from, string $to)
    {
        $p = $this->prefix;

        $ST_SALESINVOICE  = 13;
        $ST_CUSTDELIVERY  = 11;
        $PERSON_TYPE_CUST = 2;

        $allocFrom = DB::table("{$p}cust_allocations as alloc")
            ->select('trans_type_from as trans_type', 'trans_no_from as trans_no')
            ->selectRaw('SUM(amt) as amount')
            ->where('person_id', $debtorNo)
            ->whereRaw('date_alloc <= ?', [$to])
            ->groupBy('trans_type_from', 'trans_no_from');

        $allocTo = DB::table("{$p}cust_allocations as alloc")
            ->select('trans_type_to as trans_type', 'trans_no_to as trans_no')
            ->selectRaw('SUM(amt) as amount')
            ->where('person_id', $debtorNo)
            ->whereRaw('date_alloc <= ?', [$to])
            ->groupBy('trans_type_to', 'trans_no_to');

        return DB::table("{$p}debtor_trans as trans")
            ->leftJoinSub($allocFrom, 'alloc_from', function ($join) {
                $join->on('alloc_from.trans_type', '=', 'trans.type')
                    ->on('alloc_from.trans_no',   '=', 'trans.trans_no');
            })
            ->leftJoinSub($allocTo, 'alloc_to', function ($join) {
                $join->on('alloc_to.trans_type', '=', 'trans.type')
                    ->on('alloc_to.trans_no',   '=', 'trans.trans_no');
            })
            ->leftJoin("{$p}gl_trans as gl_trans", function ($join) {
                $join->on('trans.type',         '=', 'gl_trans.type')
                    ->on('trans.trans_no',      '=', 'gl_trans.type_no')
                    ->on('gl_trans.person_id',  '=', 'trans.debtor_no');
            })
            ->leftJoin("{$p}voided as voided", function ($join) {
                $join->on('trans.type',     '=', 'voided.type')
                    ->on('trans.trans_no', '=', 'voided.id');
            })
            ->select('trans.*', 'gl_trans.memo_')
            ->selectRaw("
                IF(gl_trans.amount, gl_trans.amount,
                    trans.ov_amount + trans.ov_gst + trans.ov_freight
                    + trans.ov_freight_tax + trans.ov_discount
                ) AS TotalAmount
            ")
            ->selectRaw('IFNULL(alloc_from.amount, alloc_to.amount) AS Allocated')
            ->selectRaw("((trans.type = {$ST_SALESINVOICE}) AND trans.due_date < ?) AS OverDue", [$to])
            ->whereRaw('trans.tran_date >= ?', [$from])
            ->whereRaw('trans.tran_date <= ?', [$to])
            ->where('trans.debtor_no',         $debtorNo)
            ->where('trans.type',              '<>', $ST_CUSTDELIVERY)
            ->whereNull('voided.id')
            ->where('gl_trans.amount',         '<>', 0)
            ->where('gl_trans.person_type_id', $PERSON_TYPE_CUST)
            ->orderBy('trans.tran_date')
            ->get();
    }
}
