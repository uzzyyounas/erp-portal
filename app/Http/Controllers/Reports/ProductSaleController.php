<?php

namespace App\Http\Controllers\Reports;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductSaleController extends Controller
{
    private string $prefix = '0_';

    // ── FA transaction type constants ─────────────────────────────────────────
    private const ST_SALESINVOICE = 10;
    private const ST_CUSTCREDIT   = 11;  // Credit Note (negative)

    // ─────────────────────────────────────────────────────────────────────────
    // GET /reports/product-sale  —  show parameter form
    // ─────────────────────────────────────────────────────────────────────────
    public function index()
    {
        $salesmen   = $this->getSalesmanList();
        $areas      = $this->getAreasList();
        $customers  = $this->getAllCustomers();
        $categories = $this->getCategoryList();

        return view('reports.sales.product-sale-params',
            compact('salesmen', 'areas', 'customers', 'categories'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /reports/product-sale/customers?salesman_code=X  (AJAX)
    // ─────────────────────────────────────────────────────────────────────────
    public function customersBySalesman(Request $request)
    {
        $code = $request->get('salesman_code');
        $area = $request->get('area_code');

        if (!$code && !$area) {
            return response()->json($this->getAllCustomers());
        }

        $p    = $this->prefix;
        $sql  = "
            SELECT DISTINCT d.debtor_no, d.name, d.curr_code
            FROM {$p}debtors_master d
            INNER JOIN {$p}cust_branch b ON d.debtor_no = b.debtor_no
            WHERE d.inactive = 0
        ";
        $params = [];

        if ($code) {
            $sql    .= ' AND b.salesman = ?';
            $params[] = $code;
        }
        if ($area) {
            $sql    .= ' AND b.area = ?';
            $params[] = $area;
        }

        $sql .= ' ORDER BY d.name';

        return response()->json(DB::select($sql, $params));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /reports/product-sale/items?category_id=X  (AJAX)
    // ─────────────────────────────────────────────────────────────────────────
    public function itemsByCategory(Request $request)
    {
        $categoryId = $request->get('category_id');

        $query = DB::table($this->prefix . 'stock_master')
            ->where('inactive', 0)
            ->orderBy('description');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return response()->json(
            $query->get(['stock_id', 'description', 'category_id'])
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /reports/product-sale/generate  —  build and stream PDF
    // ─────────────────────────────────────────────────────────────────────────
    public function generate(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $startTime = microtime(true);

        // ── Date range ────────────────────────────────────────────────────
        $from        = Carbon::parse($request->input('from'));
        $fromDateStr = $from->format('Y-m-d');
        $to          = Carbon::parse($request->input('to'));
        $toDateStr   = $to->format('Y-m-d');

        // ── Filters ───────────────────────────────────────────────────────
        $salesmanCode = $request->input('salesman_code') ?: null;
        $areaCode     = $request->input('area_code')     ?: null;
        $debtorNo     = $request->input('debtor_no')     ?: null;
        $categoryId   = $request->input('category_id')   ?: null;
        $stockId      = $request->input('stock_id')      ?: null;

        // ── Display options ───────────────────────────────────────────────
        $groupBy     = $request->input('group_by', 'item');   // item | customer
        $summaryOnly = $request->boolean('summary_only');
        $noZeros     = $request->boolean('suppress_zeros');
        $showCost    = $request->boolean('show_cost');
        $orientation = $request->input('orientation', 'landscape');

        // ── Resolve labels ────────────────────────────────────────────────
        $salesmanLabel = 'All Salesmen';
        if ($salesmanCode) {
            $sm = DB::table($this->prefix . 'salesman')
                ->where('salesman_code', $salesmanCode)
                ->value('salesman_name');
            $salesmanLabel = $sm ?? $salesmanCode;
        }

        $areaLabel = 'All Areas';
        if ($areaCode) {
            $al = DB::table($this->prefix . 'areas')
                ->where('area_code', $areaCode)
                ->value('description');
            $areaLabel = $al ?? $areaCode;
        }

        $customerLabel = 'All Customers';
        if ($debtorNo) {
            $cl = DB::table($this->prefix . 'debtors_master')
                ->where('debtor_no', $debtorNo)
                ->value('name');
            $customerLabel = $cl ?? $debtorNo;
        }

        $categoryLabel = 'All Categories';
        if ($categoryId) {
            $cat = DB::table($this->prefix . 'stock_category')
                ->where('category_id', $categoryId)
                ->value('description');
            $categoryLabel = $cat ?? $categoryId;
        }

        // ── Build base query ──────────────────────────────────────────────
        $rows = $this->getSaleLines(
            $fromDateStr, $toDateStr,
            $salesmanCode, $areaCode, $debtorNo, $categoryId, $stockId
        );

        // ── Group data ────────────────────────────────────────────────────
        $reportData = $this->groupData($rows, $groupBy, $summaryOnly, $noZeros);

        // ── Grand totals ──────────────────────────────────────────────────
        $grand = [
            'qty'          => 0,
            'gross_amount' => 0.0,
            'discount'     => 0.0,
            'net_amount'   => 0.0,
            'cost'         => 0.0,
            'profit'       => 0.0,
        ];
        foreach ($reportData as $group) {
            $grand['qty']          += $group['totals']['qty'];
            $grand['gross_amount'] += $group['totals']['gross_amount'];
            $grand['discount']     += $group['totals']['discount'];
            $grand['net_amount']   += $group['totals']['net_amount'];
            $grand['cost']         += $group['totals']['cost'];
            $grand['profit']       += $group['totals']['profit'];
        }

        // ── Company logo ──────────────────────────────────────────────────
        $logoSrc = null;
        try {
            $logo = DB::table('company_settings')->where('key', 'logo')->value('value');
            if ($logo && file_exists(public_path($logo))) {
                $logoSrc = 'data:image/' . pathinfo($logo, PATHINFO_EXTENSION)
                    . ';base64,' . base64_encode(file_get_contents(public_path($logo)));
            }
        } catch (\Throwable) {}

        // ── Render PDF ────────────────────────────────────────────────────
        $pdf = Pdf::loadView('reports.sales.product-sale', compact(
            'reportData', 'grand',
            'from', 'to',
            'salesmanLabel', 'areaLabel', 'customerLabel', 'categoryLabel',
            'groupBy', 'summaryOnly', 'showCost',
            'logoSrc'
        ))
            ->setPaper('a4', $orientation)
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 150,
            ]);

        $filename     = 'product-sale-report-' . $to->format('Y-m-d') . '.pdf';
        $generationMs = (int) round((microtime(true) - $startTime) * 1000);

        $this->logReportRun($request, $generationMs, [
            'from'           => $from->toDateString(),
            'to'             => $to->toDateString(),
            'salesman_code'  => $salesmanCode,
            'salesman_name'  => $salesmanLabel,
            'area_code'      => $areaCode,
            'debtor_no'      => $debtorNo,
            'customer_name'  => $customerLabel,
            'category_id'    => $categoryId,
            'stock_id'       => $stockId,
            'group_by'       => $groupBy,
            'summary_only'   => $summaryOnly,
            'suppress_zeros' => $noZeros,
            'show_cost'      => $showCost,
        ]);

        return $pdf->stream($filename);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // getSaleLines() — fetch raw invoice detail lines with all filter joins
    // ─────────────────────────────────────────────────────────────────────────
    private function getSaleLines(
        string  $fromDate,
        string  $toDate,
        ?string $salesmanCode,
        ?string $areaCode,
        ?string $debtorNo,
        ?string $categoryId,
        ?string $stockId
    ): array {
        $p      = $this->prefix;
        $types  = implode(',', [self::ST_SALESINVOICE, self::ST_CUSTCREDIT]);

        // Sign: invoices positive, credit notes negative
        $sign   = "IF(t.type = " . self::ST_SALESINVOICE . ", 1, -1)";

        $sql = "
            SELECT
                t.type,
                t.reference,
                t.tran_date,
                t.debtor_no,
                d.name                                  AS customer_name,
                cb.salesman                             AS salesman_code,
                s.salesman_name,
                cb.area                                 AS area_code,
                a.description                           AS area_name,
                td.stock_id,
                td.description                          AS item_description,
                sm.category_id,
                sc.description                          AS category_name,
                ({$sign} * td.qty_dispatched)           AS qty,
                td.unit_price,
                td.discount_percent,
                ({$sign} * td.qty_dispatched * td.unit_price)
                                                        AS gross_amount,
                ({$sign} * td.qty_dispatched * td.unit_price
                    * td.discount_percent / 100)        AS discount_amount,
                ({$sign} * td.qty_dispatched * td.unit_price
                    * (1 - td.discount_percent / 100))  AS net_amount,
                ({$sign} * td.qty_dispatched * sm.material_cost)
                                                        AS cost_amount

            FROM {$p}debtor_trans t
            INNER JOIN {$p}debtor_trans_details td ON td.debtor_trans_no   = t.trans_no
                                                   AND td.debtor_trans_type = t.type
            INNER JOIN {$p}debtors_master d         ON d.debtor_no = t.debtor_no
            INNER JOIN {$p}cust_branch cb            ON cb.debtor_no = t.debtor_no
                                                    AND cb.branch_code = t.branch_code
            LEFT  JOIN {$p}salesman s               ON s.salesman_code = cb.salesman
            LEFT  JOIN {$p}areas a                  ON a.area_code = cb.area
            LEFT  JOIN {$p}stock_master sm          ON sm.stock_id = td.stock_id
            LEFT  JOIN {$p}stock_category sc        ON sc.category_id = sm.category_id

            WHERE t.type      IN ({$types})
              AND t.tran_date >= ?
              AND t.tran_date <= ?
              AND d.inactive   = 0
        ";

        $params = [$fromDate, $toDate];

        if ($salesmanCode) {
            $sql    .= ' AND cb.salesman = ?';
            $params[] = $salesmanCode;
        }
        if ($areaCode) {
            $sql    .= ' AND cb.area = ?';
            $params[] = $areaCode;
        }
        if ($debtorNo) {
            $sql    .= ' AND t.debtor_no = ?';
            $params[] = $debtorNo;
        }
        if ($categoryId) {
            $sql    .= ' AND sm.category_id = ?';
            $params[] = $categoryId;
        }
        if ($stockId) {
            $sql    .= ' AND td.stock_id = ?';
            $params[] = $stockId;
        }

        $sql .= ' ORDER BY sm.category_id, td.stock_id, t.tran_date, t.reference';

        return DB::select($sql, $params);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // groupData() — organise raw rows into grouped report structure
    //
    // group_by = 'item'     → group key = stock_id
    // group_by = 'customer' → group key = debtor_no
    // ─────────────────────────────────────────────────────────────────────────
    private function groupData(
        array  $rows,
        string $groupBy,
        bool   $summaryOnly,
        bool   $noZeros
    ): array {
        $groups = [];

        foreach ($rows as $row) {
            $key   = $groupBy === 'customer' ? $row->debtor_no : $row->stock_id;
            $label = $groupBy === 'customer'
                ? ($row->customer_name ?? $row->debtor_no)
                : ($row->item_description ?? $row->stock_id);
            $sub   = $groupBy === 'customer' ? $row->stock_id   : $row->debtor_no;
            $subLb = $groupBy === 'customer'
                ? ($row->item_description ?? $row->stock_id)
                : ($row->customer_name    ?? $row->debtor_no);

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'key'        => $key,
                    'label'      => $label,
                    'category'   => $row->category_name ?? '',
                    'salesman'   => $row->salesman_name  ?? '',
                    'totals'     => ['qty' => 0, 'gross_amount' => 0.0, 'discount' => 0.0, 'net_amount' => 0.0, 'cost' => 0.0, 'profit' => 0.0],
                    'lines'      => [],
                ];
            }

            $qty   = (float) $row->qty;
            $gross = (float) $row->gross_amount;
            $disc  = (float) $row->discount_amount;
            $net   = (float) $row->net_amount;
            $cost  = (float) $row->cost_amount;

            $groups[$key]['totals']['qty']          += $qty;
            $groups[$key]['totals']['gross_amount']  += $gross;
            $groups[$key]['totals']['discount']      += $disc;
            $groups[$key]['totals']['net_amount']    += $net;
            $groups[$key]['totals']['cost']          += $cost;
            $groups[$key]['totals']['profit']        = $groups[$key]['totals']['net_amount'] - $groups[$key]['totals']['cost'];

            if (!$summaryOnly) {
                $groups[$key]['lines'][] = [
                    'date'        => $row->tran_date,
                    'reference'   => $row->reference,
                    'type'        => $row->type,
                    'sub_key'     => $sub,
                    'sub_label'   => $subLb,
                    'qty'         => $qty,
                    'unit_price'  => (float) $row->unit_price,
                    'discount'    => (float) $row->discount_percent,
                    'gross'       => $gross,
                    'disc_amount' => $disc,
                    'net'         => $net,
                    'cost'        => $cost,
                ];
            }
        }

        // ── Suppress zero-net groups if requested ─────────────────────────
        if ($noZeros) {
            $groups = array_filter(
                $groups,
                fn($g) => abs($g['totals']['net_amount']) >= 0.001
            );
        }

        // ── Sort by label ─────────────────────────────────────────────────
        uasort($groups, fn($a, $b) => strcmp($a['label'], $b['label']));

        return array_values($groups);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Log report run to report_logs
    // ─────────────────────────────────────────────────────────────────────────
    private function logReportRun(Request $request, int $generationMs, array $parameters): void
    {
        try {
            $menuItemId = DB::table('menu_items')
                ->where('slug', 'product-sale-report')
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
            Log::warning('product-sale-report: report_log insert failed — ' . $e->getMessage());
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function getSalesmanList()
    {
        $p = $this->prefix;
        return collect(DB::select("
            SELECT DISTINCT s.salesman_code, s.salesman_name
            FROM {$p}salesman s
            WHERE s.inactive = 0
            ORDER BY s.salesman_name
        "));
    }

    private function getAreasList()
    {
        $p = $this->prefix;
        return collect(DB::select("
            SELECT area_code, description
            FROM {$p}areas
            ORDER BY description
        "));
    }

    private function getAllCustomers()
    {
        return DB::table($this->prefix . 'debtors_master')
            ->where('inactive', 0)
            ->orderBy('name')
            ->get(['debtor_no', 'name', 'curr_code']);
    }

    private function getCategoryList()
    {
        return DB::table($this->prefix . 'stock_category')
            ->orderBy('description')
            ->get(['category_id', 'description']);
    }
}
