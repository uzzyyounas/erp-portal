<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportTemplate;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('category')
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->get();

        $categories = ReportCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        return view('admin.reports.index', compact('reports', 'categories'));
    }

    public function create()
    {
        $categories = ReportCategory::where('is_active', true)->orderBy('sort_order')->get();
        $roles      = Role::where('is_active', true)->get();
        return view('admin.reports.form', compact('categories', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $this->validateReport($request);

        // Auto-generate slug
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);

        // Handle checkbox (important)
        $data['is_active'] = $request->has('is_active');

        // Create report
        $report = Report::create($data);

        // Sync roles
        $report->roles()->sync($request->input('roles', []));

        return redirect()
            ->route('admin.reports.edit', $report)
            ->with('success', 'Report created successfully.');
    }

    public function edit(Report $report)
    {
        $categories = ReportCategory::where('is_active', true)->orderBy('sort_order')->get();
        $roles      = Role::where('is_active', true)->get();
        return view('admin.reports.form', compact('report', 'categories', 'roles'));
    }

    public function update(Request $request, Report $report)
    {
        $data = $this->validateReport($request, $report->id);

        // Auto-generate slug
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);

        // Handle checkbox
        $data['is_active'] = $request->has('is_active');

        // Update report
        $report->update($data);

        // Sync roles
        $report->roles()->sync($request->input('roles', []));

        return back()->with('success', 'Report updated successfully.');
    }

    public function destroy(Report $report)
    {
        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report deleted.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function validateReport(Request $request, $id = null)
    {
        return $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:reports,slug,' . $id,
            'category_id' => 'required|exists:report_categories,id',
            'route'       => 'nullable|string|max:255',
            'icon'        => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
        ]);
    }

    // ── DESIGNER ─────────────────────────────────────────────────────────────

    /** Full-page designer for a saved report */
    public function designer(\App\Models\Report $report)
    {
        $co = \App\Models\CompanySetting::all_settings();
        return view('admin.reports.designer', compact('report', 'co'));
    }

    /** Save designer_config via AJAX PUT */
    public function saveDesign(Request $request, \App\Models\Report $report)
    {
        $request->validate(['designer_config' => 'required|string']);
        $decoded = json_decode($request->input('designer_config'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['success' => false, 'error' => 'Invalid JSON'], 422);
        }
        $report->update(['designer_config' => $decoded]);
        return response()->json(['success' => true, 'message' => 'Design saved']);
    }

    /** Return SQL columns for this report (used by designer) */
//    public function getColumns(Request $request, \App\Models\Report $report)
//    {
//        $sql     = $request->input('sql') ?: $report->sql_query;
//        $company = $request->input('company', '0');
//        if (empty($sql)) {
//            return response()->json(['success' => false, 'columns' => [], 'error' => 'No SQL query defined for this report.']);
//        }
//        $sql = str_replace('{company_prefix}', $company, $sql);
//        $sql = preg_replace('/:\w+/', 'NULL', $sql);
//        $wrapped = "SELECT * FROM ({$sql}) AS __cols__ LIMIT 0";
//        try {
//            $stmt = \Illuminate\Support\Facades\DB::getPdo()->query($wrapped);
//            $cols = [];
//            for ($i = 0; $i < $stmt->columnCount(); $i++) {
//                $meta = $stmt->getColumnMeta($i);
//                $cols[] = $meta['name'];
//            }
//            return response()->json(['success' => true, 'columns' => $cols]);
//        } catch (\Exception $e) {
//            // Try with LIMIT 1 and actual execution
//            try {
//                $rows = \Illuminate\Support\Facades\DB::select("SELECT * FROM ({$sql}) AS __cols__ LIMIT 1");
//                $cols = !empty($rows) ? array_keys((array)$rows[0]) : [];
//                return response()->json(['success' => true, 'columns' => $cols]);
//            } catch (\Exception $e2) {
//                return response()->json(['success' => false, 'columns' => [], 'error' => $e2->getMessage()], 422);
//            }
//        }
//    }

// ReportController.php - Modified getColumns method

    /** Return SQL columns for this report (used by designer) */
    public function getColumns(Request $request, \App\Models\Report $report)
    {
        $sql = $request->input('sql') ?: $report->sql_query;
        $company = $request->input('company', '0');

        if (empty($sql)) {
            return response()->json(['success' => false, 'columns' => [], 'error' => 'No SQL query defined for this report.']);
        }

        $sql = str_replace('{company_prefix}', $company, $sql);
        $sql = preg_replace('/:\w+/', 'NULL', $sql);
        $wrapped = "SELECT * FROM ({$sql}) AS __cols__ LIMIT 0";

        try {
            $stmt = \Illuminate\Support\Facades\DB::getPdo()->query($wrapped);
            $cols = [];
            for ($i = 0; $i < $stmt->columnCount(); $i++) {
                $meta = $stmt->getColumnMeta($i);
                $cols[] = $meta['name'];
            }
            return response()->json(['success' => true, 'columns' => $cols]);
        } catch (\Exception $e) {
            // Try with LIMIT 1 and actual execution
            try {
                $rows = \Illuminate\Support\Facades\DB::select("SELECT * FROM ({$sql}) AS __cols__ LIMIT 1");
                $cols = !empty($rows) ? array_keys((array)$rows[0]) : [];
                return response()->json(['success' => true, 'columns' => $cols]);
            } catch (\Exception $e2) {
                return response()->json(['success' => false, 'columns' => [], 'error' => $e2->getMessage()], 422);
            }
        }
    }
}
