<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private string $prefix = '0_';

    private const ST_SALESINVOICE = 10;
    private const ST_CUSTCREDIT   = 11;
    private const ST_CUSTPAYMENT  = 12;
    private const ST_BANKDEPOSIT  = 2;
    private const ST_SUPPINVOICE  = 20;
    private const ST_SUPPCREDIT   = 21;
    private const ST_SUPPAYMENT   = 22;
    private const ST_BANKPAYMENT  = 4;

    public function index()
    {
        $user = Auth::user();
        $p    = $this->prefix;

        $monthlySales = (float) DB::selectOne("
            SELECT COALESCE(SUM(ABS(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)),0) AS total
            FROM {$p}debtor_trans
            WHERE type=".self::ST_SALESINVOICE."
              AND YEAR(tran_date)=YEAR(CURDATE()) AND MONTH(tran_date)=MONTH(CURDATE())
        ")->total;

        $prevMonthlySales = (float) DB::selectOne("
            SELECT COALESCE(SUM(ABS(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)),0) AS total
            FROM {$p}debtor_trans
            WHERE type=".self::ST_SALESINVOICE."
              AND tran_date >= DATE_FORMAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),'%Y-%m-01')
              AND tran_date <  DATE_FORMAT(CURDATE(),'%Y-%m-01')
        ")->total;

        $monthlyPurchases = (float) DB::selectOne("
            SELECT COALESCE(SUM(ABS(ov_amount+ov_gst+ov_discount)),0) AS total
            FROM {$p}supp_trans
            WHERE type=".self::ST_SUPPINVOICE."
              AND YEAR(tran_date)=YEAR(CURDATE()) AND MONTH(tran_date)=MONTH(CURDATE())
        ")->total;

        $prevMonthlyPurchases = (float) DB::selectOne("
            SELECT COALESCE(SUM(ABS(ov_amount+ov_gst+ov_discount)),0) AS total
            FROM {$p}supp_trans
            WHERE type=".self::ST_SUPPINVOICE."
              AND tran_date >= DATE_FORMAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),'%Y-%m-01')
              AND tran_date <  DATE_FORMAT(CURDATE(),'%Y-%m-01')
        ")->total;

        $negCust = implode(',', [self::ST_CUSTCREDIT, self::ST_CUSTPAYMENT, self::ST_BANKDEPOSIT]);
        $totalReceivables = (float) DB::selectOne("
            SELECT COALESCE(SUM(
                IF(type IN({$negCust}),-1,1)*(ABS(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc)
            ),0) AS total
            FROM {$p}debtor_trans WHERE type<>13
        ")->total;

        $negSupp = implode(',', [self::ST_SUPPCREDIT, self::ST_SUPPAYMENT, self::ST_BANKPAYMENT]);
        $totalPayables = (float) DB::selectOne("
            SELECT COALESCE(SUM(
                IF(type IN({$negSupp}),-1,1)*(ABS(ov_amount+ov_gst+ov_discount)-alloc)
            ),0) AS total
            FROM {$p}supp_trans
        ")->total;

        $ytdNetSales = (float) DB::selectOne("
            SELECT COALESCE(SUM(td.quantity * td.unit_price * (1 - td.discount_percent/100)),0) AS total
            FROM {$p}debtor_trans_details td
            INNER JOIN {$p}debtor_trans t ON t.trans_no=td.debtor_trans_no AND t.type=td.debtor_trans_type
            WHERE t.type=".self::ST_SALESINVOICE." AND YEAR(t.tran_date)=YEAR(CURDATE())
        ")->total;

        $ytdCost = (float) DB::selectOne("
            SELECT COALESCE(SUM(td.quantity * COALESCE(sm.material_cost,0)),0) AS total
            FROM {$p}debtor_trans_details td
            INNER JOIN {$p}debtor_trans t ON t.trans_no=td.debtor_trans_no AND t.type=td.debtor_trans_type
            LEFT  JOIN {$p}stock_master sm ON sm.stock_id=td.stock_id
            WHERE t.type=".self::ST_SALESINVOICE." AND YEAR(t.tran_date)=YEAR(CURDATE())
        ")->total;

        $ytdGrossProfit = $ytdNetSales - $ytdCost;
        $gpMarginPct    = $ytdNetSales > 0 ? round(($ytdGrossProfit / $ytdNetSales) * 100, 1) : 0;

        $overdueCount = (int) DB::selectOne("
            SELECT COUNT(*) AS cnt FROM {$p}debtor_trans
            WHERE type=".self::ST_SALESINVOICE."
              AND due_date<CURDATE()
              AND (ABS(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc)>0.001
        ")->cnt;

        $overdueAmount = (float) DB::selectOne("
            SELECT COALESCE(SUM(ABS(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc),0) AS total
            FROM {$p}debtor_trans
            WHERE type=".self::ST_SALESINVOICE."
              AND due_date<CURDATE()
              AND (ABS(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc)>0.001
        ")->total;

        // 7-month trend
        $trendStart = Carbon::now()->subMonths(6)->startOfMonth()->toDateString();

        $salesTrend = DB::select("
            SELECT DATE_FORMAT(tran_date,'%Y-%m') AS month,
                   COALESCE(SUM(ABS(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)),0) AS total
            FROM {$p}debtor_trans
            WHERE type=".self::ST_SALESINVOICE." AND tran_date>=?
            GROUP BY DATE_FORMAT(tran_date,'%Y-%m') ORDER BY month
        ", [$trendStart]);

        $purchaseTrend = DB::select("
            SELECT DATE_FORMAT(tran_date,'%Y-%m') AS month,
                   COALESCE(SUM(ABS(ov_amount+ov_gst+ov_discount)),0) AS total
            FROM {$p}supp_trans
            WHERE type=".self::ST_SUPPINVOICE." AND tran_date>=?
            GROUP BY DATE_FORMAT(tran_date,'%Y-%m') ORDER BY month
        ", [$trendStart]);

        $months = collect();
        $cursor = Carbon::now()->subMonths(6)->startOfMonth();
        while ($cursor->lte(Carbon::now()->startOfMonth())) {
            $months->push($cursor->format('Y-m'));
            $cursor->addMonth();
        }

        $salesMap    = collect($salesTrend)->pluck('total', 'month');
        $purchaseMap = collect($purchaseTrend)->pluck('total', 'month');

        $chartLabels    = $months->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->values();
        $chartSales     = $months->map(fn($m) => round((float)($salesMap[$m]    ?? 0), 2))->values();
        $chartPurchases = $months->map(fn($m) => round((float)($purchaseMap[$m] ?? 0), 2))->values();

        $gpTrend = DB::select("
            SELECT DATE_FORMAT(t.tran_date,'%Y-%m') AS month,
                   COALESCE(SUM(
                       td.quantity * td.unit_price * (1-td.discount_percent/100)
                       - td.quantity * COALESCE(sm.material_cost,0)
                   ),0) AS total
            FROM {$p}debtor_trans_details td
            INNER JOIN {$p}debtor_trans t ON t.trans_no=td.debtor_trans_no AND t.type=td.debtor_trans_type
            LEFT  JOIN {$p}stock_master sm ON sm.stock_id=td.stock_id
            WHERE t.type=".self::ST_SALESINVOICE." AND t.tran_date>=?
            GROUP BY DATE_FORMAT(t.tran_date,'%Y-%m') ORDER BY month
        ", [$trendStart]);

        $gpMap   = collect($gpTrend)->pluck('total', 'month');
        $chartGP = $months->map(fn($m) => round((float)($gpMap[$m] ?? 0), 2))->values();

        $salesByCategory = DB::select("
            SELECT COALESCE(sc.description,'Uncategorised') AS category,
                   COALESCE(SUM(td.quantity*td.unit_price*(1-td.discount_percent/100)),0) AS total
            FROM {$p}debtor_trans_details td
            INNER JOIN {$p}debtor_trans t ON t.trans_no=td.debtor_trans_no AND t.type=td.debtor_trans_type
            LEFT  JOIN {$p}stock_master sm ON sm.stock_id=td.stock_id
            LEFT  JOIN {$p}stock_category sc ON sc.category_id=sm.category_id
            WHERE t.type=".self::ST_SALESINVOICE." AND YEAR(t.tran_date)=YEAR(CURDATE())
            GROUP BY sm.category_id, sc.description
            ORDER BY total DESC LIMIT 7
        ");

        $receivablesAging = DB::selectOne("
            SELECT
              COALESCE(SUM(IF(DATEDIFF(CURDATE(),due_date) BETWEEN 0  AND 29,
                ABS(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc,0)),0) AS b1,
              COALESCE(SUM(IF(DATEDIFF(CURDATE(),due_date) BETWEEN 30 AND 59,
                ABS(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc,0)),0) AS b2,
              COALESCE(SUM(IF(DATEDIFF(CURDATE(),due_date) BETWEEN 60 AND 89,
                ABS(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc,0)),0) AS b3,
              COALESCE(SUM(IF(DATEDIFF(CURDATE(),due_date) >= 90,
                ABS(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc,0)),0) AS b4
            FROM {$p}debtor_trans
            WHERE type=".self::ST_SALESINVOICE."
              AND (ABS(ov_amount+ov_gst+ov_freight+ov_freight_tax+ov_discount)-alloc)>0.001
        ");

        $topCustomers = DB::select("
            SELECT d.name, d.debtor_no,
                   COALESCE(SUM(ABS(t.ov_amount+t.ov_gst+t.ov_freight+t.ov_freight_tax+t.ov_discount)),0) AS total
            FROM {$p}debtor_trans t
            INNER JOIN {$p}debtors_master d ON d.debtor_no=t.debtor_no
            WHERE t.type=".self::ST_SALESINVOICE." AND YEAR(t.tran_date)=YEAR(CURDATE())
            GROUP BY d.debtor_no, d.name ORDER BY total DESC LIMIT 5
        ");

        $topItems = DB::select("
            SELECT td.stock_id, td.description,
                   COALESCE(SUM(td.quantity*td.unit_price*(1-td.discount_percent/100)),0) AS total,
                   COALESCE(SUM(td.quantity),0) AS qty
            FROM {$p}debtor_trans_details td
            INNER JOIN {$p}debtor_trans t ON t.trans_no=td.debtor_trans_no AND t.type=td.debtor_trans_type
            WHERE t.type=".self::ST_SALESINVOICE." AND YEAR(t.tran_date)=YEAR(CURDATE())
            GROUP BY td.stock_id, td.description ORDER BY total DESC LIMIT 5
        ");

        $topSuppliers = DB::select("
            SELECT s.supp_name, s.supplier_id,
                   COALESCE(SUM(ABS(t.ov_amount+t.ov_gst+t.ov_discount)),0) AS total
            FROM {$p}supp_trans t
            INNER JOIN {$p}suppliers s ON s.supplier_id=t.supplier_id
            WHERE t.type=".self::ST_SUPPINVOICE." AND YEAR(t.tran_date)=YEAR(CURDATE())
            GROUP BY s.supplier_id, s.supp_name ORDER BY total DESC LIMIT 5
        ");

        // FA has no supp_trans_details — group by supplier for pie
        $purchaseByCategory = DB::select("
            SELECT s.supp_name AS category,
                   COALESCE(SUM(ABS(t.ov_amount+t.ov_gst+t.ov_discount)),0) AS total
            FROM {$p}supp_trans t
            INNER JOIN {$p}suppliers s ON s.supplier_id=t.supplier_id
            WHERE t.type=".self::ST_SUPPINVOICE." AND YEAR(t.tran_date)=YEAR(CURDATE())
            GROUP BY t.supplier_id, s.supp_name ORDER BY total DESC LIMIT 6
        ");

        // Modules — drives sidebar, nav dropdowns AND the search index
        $sidebarModules = $user->accessibleModulesWithItems();
        $totalItems     = $sidebarModules->sum(fn($m) => $m->activeMenuItems->count());

        // Build a flat JSON-safe array for the client-side search engine.
        // Computed here so the Blade template only needs @json($searchIndex).
        $searchIndex = $sidebarModules
            ->flatMap(fn($m) =>
            $m->activeMenuItems
                ->where('type', '!=', 'divider')
                ->map(fn($item) => [
                    'name'   => $item->name,
                    'url'    => $item->url,
                    'type'   => $item->type,
                    'icon'   => $item->icon ?: 'bi-file-text',
                    'module' => $m->name,
                    'color'  => $m->color,
                ])
            )
            ->values()
            ->toArray();

        return view('dashboard', compact(
            'user',
            'monthlySales',       'prevMonthlySales',
            'monthlyPurchases',   'prevMonthlyPurchases',
            'totalReceivables',   'totalPayables',
            'ytdGrossProfit',     'gpMarginPct',     'ytdNetSales',
            'overdueCount',       'overdueAmount',
            'chartLabels',        'chartSales',      'chartPurchases', 'chartGP',
            'salesByCategory',    'purchaseByCategory',
            'receivablesAging',
            'topCustomers',       'topItems',        'topSuppliers',
            'sidebarModules',     'totalItems',
            'searchIndex'
        ));
    }
}
