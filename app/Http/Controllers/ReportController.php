<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportCategory;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct(private ReportService $service) {}

    /**
     * List all accessible reports, optionally filtered by category.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $categorySlug = $request->query('category');

        $query = $user->accessibleReports()->with('category');

        if ($categorySlug) {
            $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
        }

        $reports = $query->orderBy('sort_order')->get();

        $categories = ReportCategory::where('is_active', true)
            ->whereHas('activeReports', function ($q) use ($user) {
                if (!$user->isAdmin()) {
                    $q->whereHas('roles', fn($r) => $r->where('roles.id', $user->role_id));
                }
            })
            ->orderBy('sort_order')
            ->get();

        $activeCategory = $categorySlug
            ? ReportCategory::where('slug', $categorySlug)->first()
            : null;

        return view('reports.index', compact('reports', 'categories', 'activeCategory'));
    }

    public function run(string $slug)
    {
        $report = Report::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Role access check
        if (!Auth::user()->canViewReport($report)) {
            abort(403, 'You do not have access to this report.');
        }

        // ✅ If route exists → redirect
        if ($report->route && \Route::has($report->route)) {
            return redirect()->route($report->route);
        }

        // ❌ Fallback (optional)
        abort(404, 'Report route not defined.');
    }

    public function execute(Request $request, $slug)
    {
        $report = Report::where('slug', $slug)->firstOrFail();

        if (!Auth::user()->canViewReport($report)) {
            abort(403);
        }

        $customerId = $request->customer_id;
        $from = $request->from_date;
        $to = $request->to_date;

        // 🔥 YOUR QUERY (converted to Laravel)
        $data = DB::select("
        SELECT type, reference, tran_date,
               (ov_amount + ov_gst + ov_freight - ov_discount) AS balance
        FROM 0_debtor_trans
        WHERE debtor_no = ?
        AND tran_date BETWEEN ? AND ?
        ORDER BY tran_date
    ", [$customerId, $from, $to]);

        // Customer Info
        $customer = DB::table('0_debtors_master')
            ->where('debtor_no', $customerId)
            ->first();

        // Generate PDF
        $pdf = Pdf::loadView('reports.aged-customer-analysis', [
            'rows' => $data,
            'customer' => $customer,
            'from' => $from,
            'to' => $to
        ]);

        return $pdf->stream('aging-report.pdf');
    }

    /**
     * Show parameter form for a report.
     */
    public function show(string $slug)
    {
        $report = Report::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        if (!Auth::user()->canViewReport($report)) {
            abort(403);
        }

        // Load customers
        $customers = DB::table('0_debtors_master')->get();

        return view('reports.show', compact('report', 'customers'));
    }

    /**
     * Run report and return PDF.
     */
    public function generate(Request $request, string $slug)
    {
        $report = Report::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if (!Auth::user()->canViewReport($report)) {
            abort(403);
        }

        // Validate required parameters
        $rules = [];
        foreach ($report->parameters as $p) {
            if ($p->is_required) {
                $rules[$p->name] = 'required';
            }
        }
        $request->validate($rules);

        try {
            $params = $request->except(['_token']);
            $data   = $this->service->run($report, $params);
            $pdf    = $this->service->generatePdf($report, $data['rows'], $data['columns'], $params);

            $filename = str_replace(' ', '_', $report->name) . '_' . now()->format('Ymd_His') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Report generation failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Preview report as HTML table (before PDF).
     */
    public function preview(Request $request, string $slug)
    {
        $report = Report::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if (!Auth::user()->canViewReport($report)) {
            abort(403);
        }

        try {
            $params = $request->except(['_token']);
            $data   = $this->service->run($report, $params);
            return view('reports.preview', array_merge($data, compact('report', 'params')));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Preview failed: ' . $e->getMessage()]);
        }
    }

    public function agedCustomerAnalysis()
    {
        return view('reports.aged-customer-analysis');
    }
}
