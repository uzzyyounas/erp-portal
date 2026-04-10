<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportLog;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users'      => User::count(),
            'reports'    => Report::count(),
            'categories' => ReportCategory::count(),
            'runs_today' => ReportLog::whereDate('created_at', today())->count(),
        ];

        $recentLogs = ReportLog::with(['user', 'report'])
            ->latest()->take(10)->get();

        $topReports = ReportLog::selectRaw('report_id, COUNT(*) as run_count')
            ->with('report')
            ->groupBy('report_id')
            ->orderByDesc('run_count')
            ->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentLogs', 'topReports'));
    }
}
