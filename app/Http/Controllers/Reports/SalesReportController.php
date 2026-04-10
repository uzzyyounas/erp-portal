<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    /**
     * Show the Sales Summary report parameter form.
     * Route: reports.sales.summary  [GET]
     */
    public function summary()
    {
        return view('reports.sales.summary');
    }

    /**
     * Generate the Sales Summary report.
     * Route: reports.sales.summary.generate  [POST]
     *
     * Replace the dummy data below with your real DB queries.
     * Example real query (commented out) shows the pattern.
     */
    public function generateSummary(Request $request)
    {
        $request->validate([
            'date_from'  => 'required|date',
            'date_to'    => 'required|date|after_or_equal:date_from',
            'salesman'   => 'nullable|string|max:100',
        ]);

        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;
        $salesman = $request->salesman;

        // ── Replace this block with your real query ───────────────────
        //
        // $rows = DB::table('sales_transactions as t')
        //     ->join('salesmen as s', 's.id', '=', 't.salesman_id')
        //     ->select(
        //         's.name as salesman',
        //         DB::raw('COUNT(*) as invoices'),
        //         DB::raw('SUM(t.gross_amount) as gross_amount'),
        //         DB::raw('SUM(t.discount) as discount'),
        //         DB::raw('SUM(t.net_amount) as net_amount'),
        //     )
        //     ->whereBetween('t.invoice_date', [$dateFrom, $dateTo])
        //     ->when($salesman, fn($q) => $q->where('s.name', 'like', "%{$salesman}%"))
        //     ->groupBy('s.id', 's.name')
        //     ->orderByDesc('net_amount')
        //     ->get()->toArray();
        //
        // ── Dummy data for demonstration ──────────────────────────────
        $rows = [
            ['Salesman' => 'Ahmed Raza',       'Invoices' => 42, 'Gross Amount' => 850000, 'Discount' => 25000, 'Net Amount' => 825000],
            ['Salesman' => 'Bilal Hassan',      'Invoices' => 38, 'Gross Amount' => 720000, 'Discount' => 18000, 'Net Amount' => 702000],
            ['Salesman' => 'Fatima Malik',      'Invoices' => 31, 'Gross Amount' => 640000, 'Discount' => 12000, 'Net Amount' => 628000],
            ['Salesman' => 'Hassan Siddiqui',   'Invoices' => 27, 'Gross Amount' => 510000, 'Discount' => 8500,  'Net Amount' => 501500],
            ['Salesman' => 'Zainab Qureshi',    'Invoices' => 22, 'Gross Amount' => 430000, 'Discount' => 9000,  'Net Amount' => 421000],
        ];

        // Filter by salesman name if provided (for dummy data)
        if ($salesman) {
            $rows = array_filter($rows, fn($r) =>
                stripos($r['Salesman'], $salesman) !== false
            );
        }

        $params = [
            'Date From' => $dateFrom,
            'Date To'   => $dateTo,
            'Salesman'  => $salesman ?: 'All',
        ];

        return view('reports.sales.summary', compact('rows', 'params', 'dateFrom', 'dateTo', 'salesman'));
    }
}
