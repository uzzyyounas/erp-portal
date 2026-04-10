@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h4><i class="bi bi-house-fill me-2"></i>Dashboard</h4>
            <small class="text-muted">Welcome back, {{ auth()->user()->name }}</small>
        </div>
        <small class="text-muted">{{ now()->format('l, d M Y') }}</small>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-left-color:#1a3a5c;">
                <div class="stat-value">{{ $totalItems }}</div>
                <div class="stat-label"><i class="bi bi-files me-1"></i>Accessible Items</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-left-color:#2d6a9f;">
                <div class="stat-value">{{ $modules->count() }}</div>
                <div class="stat-label"><i class="bi bi-grid me-1"></i>Modules</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-left-color:#e8a020;">
                <div class="stat-value">{{ $recentLogs->count() }}</div>
                <div class="stat-label"><i class="bi bi-clock-history me-1"></i>Recent Runs</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-left-color:#198754;">
                <div class="stat-value">{{ auth()->user()->last_login_at?->format('d M') ?? '—' }}</div>
                <div class="stat-label"><i class="bi bi-calendar-check me-1"></i>Last Login</div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- Module Grid --}}
        <div class="col-lg-8">
            @foreach($modules as $module)
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="bi {{ $module->icon }}" style="color:{{ $module->color }};font-size:1rem;"></i>
                        <span>{{ $module->name }}</span>
                        @if($module->description)
                            <small class="text-muted ms-1">— {{ $module->description }}</small>
                        @endif
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-2">
                            @foreach($module->activeMenuItems as $item)
                                @if($item->type === 'divider')
                                    <div class="col-12"><hr class="my-1"></div>
                                @else
                                    <div class="col-sm-6 col-md-4">
                                        <a href="{{ $item->url }}" class="text-decoration-none">
                                            <div class="d-flex align-items-center gap-2 p-2 rounded border"
                                                 style="transition:all .15s;"
                                                 onmouseover="this.style.background='#f0f4f8';this.style.borderColor='#1a3a5c';"
                                                 onmouseout="this.style.background='';this.style.borderColor='';">
                                                <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                                     style="width:34px;height:34px;background:{{ $module->color }}18;">
                                                    <i class="bi {{ $item->icon ?: 'bi-file-text' }}"
                                                       style="color:{{ $module->color }};font-size:.9rem;"></i>
                                                </div>
                                                <div style="min-width:0;">
                                                    <div class="fw-semibold text-dark"
                                                         style="font-size:.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                        {{ $item->name }}
                                                    </div>
                                                    <div style="font-size:.65rem;">
                                                        <span class="text-muted">
                                                            @if($item->type === 'report')
                                                                <i class="bi bi-bar-chart-line me-1"></i>Report
                                                            @elseif($item->type === 'form')
                                                                <i class="bi bi-pencil-square me-1"></i>Form
                                                            @else
                                                                <i class="bi bi-link-45deg me-1"></i>Link
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            @if($modules->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="bi bi-grid fs-1 d-block mb-2 opacity-25"></i>
                        <h6>No modules assigned to your role yet</h6>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Recently Run Reports ── --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history me-2"></i>Recently Run Reports
                </div>

                @if($recentLogs->isEmpty())
                    <div class="card-body text-center text-muted py-5">
                        <i class="bi bi-bar-chart fs-1 d-block mb-2 opacity-25"></i>
                        <p class="small">No reports run yet. Generate your first report!</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($recentLogs as $log)
                            @php
                                // ReportLog → menuItem (MenuItem) → module (Module)
                                $item   = $log->menuItem;           // MenuItem model
                                $module = $item?->module;           // Module model (= category)
                                $params = $log->parameters ?? [];   // already array via $casts
                            @endphp
                            <div class="list-group-item list-group-item-action py-2 px-3">
                                <div class="d-flex align-items-start gap-2">

                                    {{-- Icon bubble --}}
                                    <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                                         style="width:32px;height:32px;background:{{ $module?->color ?? '#1a3a5c' }}18;">
                                        <i class="bi {{ $item?->icon ?? 'bi-bar-chart-line' }}"
                                           style="color:{{ $module?->color ?? '#1a3a5c' }};font-size:.82rem;"></i>
                                    </div>

                                    {{-- Text --}}
                                    <div style="min-width:0;flex:1;">
                                        {{-- Report name --}}
                                        <div class="fw-semibold text-dark"
                                             style="font-size:.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            {{ $item?->name ?? 'Unknown Report' }}
                                        </div>

                                        {{-- Module name (category) · date range --}}
                                        <div class="text-muted" style="font-size:.68rem;line-height:1.6;">
                                            @if($module)
                                                <i class="bi {{ $module->icon ?? 'bi-grid' }} me-1"
                                                   style="color:{{ $module->color ?? '#64748b' }};"></i>
                                                {{ $module->name }}
                                            @endif

                                            @if(!empty($params['from']) && !empty($params['to']))
                                                &nbsp;&middot;&nbsp;
                                                {{ \Carbon\Carbon::parse($params['from'])->format('d M Y') }}
                                                –
                                                {{ \Carbon\Carbon::parse($params['to'])->format('d M Y') }}
                                            @endif

                                            @if(!empty($params['salesman_name']) && $params['salesman_name'] !== 'All Salesmen')
                                                &nbsp;&middot;&nbsp;{{ $params['salesman_name'] }}
                                            @endif
                                        </div>

                                        {{-- Time ago + generation speed --}}
                                        <div style="font-size:.65rem;margin-top:1px;">
                                        <span class="text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ $log->created_at->diffForHumans() }}
                                        </span>
                                            @if($log->generation_time_ms)
                                                &nbsp;
                                                <span style="background:#f0f4f8;color:#64748b;
                                                         padding:1px 5px;border-radius:4px;">
                                                <i class="bi bi-lightning-charge me-1"></i>{{ number_format($log->generation_time_ms) }} ms
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Re-run button --}}
                                    @if($item && $item->url !== '#')
                                        <a href="{{ $item->url }}"
                                           class="btn btn-xs btn-outline-secondary flex-shrink-0 mt-1"
                                           title="Open report">
                                            <i class="bi bi-play-fill"></i>
                                        </a>
                                    @endif

                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>

    </div>
@endsection
