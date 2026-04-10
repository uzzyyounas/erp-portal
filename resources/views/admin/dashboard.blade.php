@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Admin Dashboard</li>
@endsection

@section('content')
<div class="page-header">
    <h4><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h4>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Total Users',    'value'=>$stats['users'],      'icon'=>'bi-people-fill',        'color'=>'#1a3a5c'],
        ['label'=>'Total Reports',  'value'=>$stats['reports'],    'icon'=>'bi-file-earmark-bar-graph-fill', 'color'=>'#2d6a9f'],
        ['label'=>'Categories',     'value'=>$stats['categories'], 'icon'=>'bi-folder-fill',        'color'=>'#e8a020'],
        ['label'=>'Runs Today',     'value'=>$stats['runs_today'], 'icon'=>'bi-activity',            'color'=>'#198754'],
    ] as $s)
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-left-color:{{ $s['color'] }};">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $s['value'] }}</div>
                        <div class="stat-label">{{ $s['label'] }}</div>
                    </div>
                    <i class="bi {{ $s['icon'] }} fs-2" style="color:{{ $s['color'] }};opacity:.3;"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Quick Actions --}}
<div class="row g-3 mb-4">
    @foreach([
        ['route'=>'admin.users.index',      'icon'=>'bi-person-plus-fill',  'label'=>'Add User',     'color'=>'btn-primary'],
//        ['route'=>'admin.categories.index', 'icon'=>'bi-folder-plus',        'label'=>'Add Category', 'color'=>'btn-warning'],
//        ['route'=>'admin.reports.create',   'icon'=>'bi-file-earmark-plus', 'label'=>'Add Report',   'color'=>'btn-success'],
        ['route'=>'admin.roles.index',      'icon'=>'bi-shield-plus',        'label'=>'Manage Roles', 'color'=>'btn-secondary'],
    ] as $a)
        <div class="col-6 col-md-3">
            <a href="{{ route($a['route']) }}" class="btn {{ $a['color'] }} w-100">
                <i class="bi {{ $a['icon'] }} me-2"></i>{{ $a['label'] }}
            </a>
        </div>
    @endforeach
</div>

<div class="row g-3">
    {{-- Top Reports --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-trophy me-2"></i>Top Reports</div>
            <div class="list-group list-group-flush">
                @forelse($topReports as $i => $log)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <div style="font-size:.82rem;">
                            <span class="badge bg-secondary me-2">{{ $i+1 }}</span>
                            {{ $log->report?->name ?? 'Deleted' }}
                        </div>
                        <span class="badge bg-primary">{{ $log->run_count }} runs</span>
                    </div>
                @empty
                    <div class="list-group-item text-muted text-center py-3 small">No data yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Recent Report Runs</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr>
                        <th>User</th><th>Report</th><th>Time</th><th>IP</th><th>Duration</th>
                    </tr></thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                            <tr>
                                <td>{{ $log->user?->name ?? '—' }}</td>
                                <td>{{ $log->report?->name ?? 'Deleted' }}</td>
                                <td>{{ $log->created_at->format('d/m H:i') }}</td>
                                <td><code>{{ $log->ip_address }}</code></td>
                                <td>{{ $log->generation_time_ms }}ms</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No activity yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
