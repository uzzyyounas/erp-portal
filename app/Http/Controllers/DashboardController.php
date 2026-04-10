<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportLog;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $recentLogs = ReportLog::with('menuItem.module')
            ->where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get();

        $modules = $user->accessibleModulesWithItems();

        $totalItems = $modules->sum(fn ($m) => $m->activeMenuItems->count());

        return view('dashboard', compact('user', 'recentLogs', 'modules', 'totalItems'));
    }
}
