<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ERP') — {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800&family=Lexend:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --topbar-h:     42px;
            --navbar-h:     38px;
            --total-top:    80px;

            /* Brand */
            --brand:        #1f4e8c;
            --brand-dark:   #163970;
            --brand-lt:     #e8f0fb;
            --accent:       #e8a020;
            --accent-lt:    #fef3df;

            /* UI */
            --bg:           #f0f2f5;
            --card:         #ffffff;
            --border:       #d8dde6;
            --border-lt:    #e8ecf2;
            --text:         #1a2332;
            --text-md:      #3d4f66;
            --text-sm:      #6b7c93;

            /* Status */
            --green:        #2e7d32;
            --green-lt:     #e8f5e9;
            --red:          #c62828;
            --red-lt:       #ffebee;
            --amber:        #e65100;
            --amber-lt:     #fff3e0;
            --blue:         #1565c0;
            --blue-lt:      #e3f2fd;

            --radius:       4px;
            --radius-md:    6px;
            --shadow-card:  0 1px 4px rgba(0,0,0,.1), 0 0 0 1px rgba(0,0,0,.04);
            --font:         'Nunito Sans', sans-serif;
            --font-hd:      'Lexend', sans-serif;
            --font-mono:    'JetBrains Mono', monospace;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0; font-family: var(--font);
            font-size: .82rem; background: var(--bg);
            color: var(--text); -webkit-font-smoothing: antialiased;
        }

        /* ══════════════════ TOP HEADER BAR */
        .top-header {
            position: fixed; top: 0; left: 0; right: 0;
            height: var(--topbar-h);
            background: var(--brand);
            display: flex; align-items: center;
            padding: 0 14px; gap: 12px;
            z-index: 1050;
            box-shadow: 0 2px 6px rgba(0,0,0,.25);
        }

        /* Logo / brand */
        .th-brand {
            display: flex; align-items: center; gap: 8px;
            text-decoration: none; flex-shrink: 0;
        }
        .th-brand-logo {
            font-family: var(--font-hd);
            font-size: .78rem; font-weight: 800;
            color: #fff; letter-spacing: .5px;
            display: flex; align-items: center; gap: 7px;
        }
        .th-brand-logo .logo-box {
            width: 26px; height: 26px; border-radius: 4px;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; color: #fff;
        }
        .th-brand-logo .logo-name { color: #fff; }
        .th-brand-logo .logo-sub  { color: rgba(255,255,255,.55); font-weight: 600; font-size: .68rem; }

        /* Global search */
        .th-search {
            flex: 1; max-width: 420px;
            position: relative; margin: 0 8px;
        }
        .th-search-input-wrap { position: relative; }
        .th-search input {
            width: 100%; height: 30px;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 4px;
            padding: 0 34px 0 32px;
            font-size: .78rem; color: #fff; font-family: var(--font);
            outline: none; transition: background .15s, border-color .15s;
        }
        .th-search input::placeholder { color: rgba(255,255,255,.42); }
        .th-search input:focus {
            background: rgba(255,255,255,.2);
            border-color: rgba(255,255,255,.5);
        }
        .th-search .si-icon {
            position: absolute; left: 9px; top: 50%; transform: translateY(-50%);
            color: rgba(255,255,255,.5); font-size: .82rem; pointer-events: none;
        }
        .th-search .si-clear {
            position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
            color: rgba(255,255,255,.45); font-size: .75rem;
            cursor: pointer; display: none; transition: color .12s;
            background: none; border: none; padding: 0; line-height: 1;
        }
        .th-search .si-clear:hover { color: #fff; }

        /* Search results dropdown */
        .search-dropdown {
            position: absolute; top: calc(100% + 4px); left: 0; right: 0;
            background: #fff;
            border: 1px solid var(--border, #d8dde6);
            border-radius: 6px;
            box-shadow: 0 8px 28px rgba(0,0,0,.18);
            z-index: 9999;
            max-height: 420px; overflow-y: auto;
            display: none;
            font-family: var(--font);
        }
        .search-dropdown.open { display: block; }
        .sd-section-hd {
            padding: 7px 14px 4px;
            font-size: .6rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: .7px; color: #94a3b8;
            border-bottom: 1px solid #f0f3f9;
            background: #f8fafd;
            sticky; top: 0;
        }
        .sd-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 14px; cursor: pointer;
            transition: background .12s; text-decoration: none;
        }
        .sd-item:hover, .sd-item.highlighted { background: #eef3fc; }
        .sd-item-icon {
            width: 28px; height: 28px; border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: .78rem; flex-shrink: 0;
            background: #e8f0fb; color: #1f4e8c;
        }
        .sd-item-icon.type-report { background: #e3f2fd; color: #1565c0; }
        .sd-item-icon.type-form   { background: #fff8e1; color: #e65100; }
        .sd-item-icon.type-link   { background: #f3e8ff; color: #6d28d9; }
        .sd-item-body { flex: 1; min-width: 0; }
        .sd-item-name {
            font-size: .79rem; font-weight: 700; color: #1a2332;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sd-item-name em { font-style: normal; color: #1f4e8c; background: #ddeeff; border-radius: 2px; padding: 0 2px; }
        .sd-item-meta { font-size: .66rem; color: #6b7c93; margin-top: 1px; }
        .sd-type-tag {
            font-size: .58rem; font-weight: 700; padding: 2px 6px;
            border-radius: 3px; flex-shrink: 0; text-transform: uppercase; letter-spacing: .3px;
        }
        .sd-type-tag.report { background: #e3f2fd; color: #1565c0; }
        .sd-type-tag.form   { background: #fff8e1; color: #e65100; }
        .sd-type-tag.link   { background: #f3e8ff; color: #6d28d9; }
        .sd-empty {
            padding: 18px 14px; text-align: center;
            font-size: .78rem; color: #94a3b8;
        }
        .sd-empty i { font-size: 1.3rem; display: block; margin-bottom: 6px; opacity: .4; }
        .sd-footer {
            padding: 7px 14px;
            font-size: .68rem; color: #94a3b8;
            border-top: 1px solid #f0f3f9;
            display: flex; align-items: center; gap: 6px;
        }
        .sd-kbd {
            display: inline-flex; align-items: center; justify-content: center;
            background: #f0f3f9; border: 1px solid #d8dde6; border-radius: 3px;
            padding: 1px 5px; font-size: .6rem; font-family: var(--font-mono, monospace);
            color: #6b7c93;
        }

        /* Top right icons */
        .th-right { margin-left: auto; display: flex; align-items: center; gap: 6px; }
        .th-icon-btn {
            width: 28px; height: 28px; border-radius: 3px;
            display: flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,.7); font-size: .85rem;
            cursor: pointer; transition: background .15s; text-decoration: none;
            position: relative;
        }
        .th-icon-btn:hover { background: rgba(255,255,255,.15); color: #fff; }
        .th-user {
            display: flex; align-items: center; gap: 7px;
            color: rgba(255,255,255,.85); cursor: pointer; padding: 0 6px;
        }
        .th-user-avatar {
            width: 26px; height: 26px; border-radius: 50%;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-hd); font-size: .7rem; font-weight: 800; color: #fff;
        }
        .th-user-info { line-height: 1.2; }
        .th-user-name { font-size: .73rem; font-weight: 700; color: #fff; }
        .th-user-role { font-size: .6rem; color: rgba(255,255,255,.5); }

        /* ══════════════════ HORIZONTAL NAV */
        .main-nav {
            position: fixed; top: var(--topbar-h); left: 0; right: 0;
            height: var(--navbar-h);
            background: var(--brand-dark);
            display: flex; align-items: center;
            padding: 0 10px;
            z-index: 1040;
            border-bottom: 2px solid rgba(255,255,255,.08);
        }

        .nav-items { display: flex; align-items: stretch; height: 100%; flex: 1; }

        .nav-item-wrap { position: relative; }
        .nav-item-wrap > a, .nav-item-wrap > button {
            display: flex; align-items: center; gap: 5px;
            padding: 0 13px; height: var(--navbar-h);
            color: rgba(255,255,255,.72);
            font-size: .77rem; font-weight: 600;
            text-decoration: none; cursor: pointer;
            border: none; background: transparent;
            transition: background .15s, color .15s;
            white-space: nowrap; font-family: var(--font);
        }
        .nav-item-wrap > a:hover,
        .nav-item-wrap > button:hover { background: rgba(255,255,255,.1); color: #fff; }
        .nav-item-wrap > a.active,
        .nav-item-wrap > a.current { background: rgba(255,255,255,.14); color: #fff; }
        .nav-item-wrap > a.current {
            border-bottom: 3px solid var(--accent);
        }
        .nav-item-wrap .nav-chevron { font-size: .6rem; opacity: .6; }

        /* Mega dropdown */
        .nav-dropdown {
            position: absolute; top: 100%; left: 0;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 0 var(--radius-md) var(--radius-md) var(--radius-md);
            box-shadow: 0 8px 24px rgba(0,0,0,.15);
            min-width: 200px; padding: 6px 0;
            display: none; z-index: 2000;
        }
        .nav-item-wrap:hover .nav-dropdown { display: block; }
        .nav-dropdown a {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 16px; font-size: .78rem; color: var(--text-md);
            text-decoration: none; transition: background .12s;
        }
        .nav-dropdown a:hover { background: var(--brand-lt); color: var(--brand); }
        .nav-dropdown a i { font-size: .82rem; color: var(--text-sm); }
        .nav-dropdown-divider { height: 1px; background: var(--border-lt); margin: 4px 0; }

        /* Right nav tools */
        .nav-right { margin-left: auto; display: flex; align-items: center; gap: 2px; }
        .nav-tool {
            padding: 0 10px; height: var(--navbar-h);
            display: flex; align-items: center; gap: 5px;
            color: rgba(255,255,255,.65); font-size: .75rem;
            text-decoration: none; cursor: pointer;
            transition: background .15s, color .15s;
            white-space: nowrap;
        }
        .nav-tool:hover { background: rgba(255,255,255,.1); color: #fff; }
        .nav-tool i { font-size: .82rem; }

        /* ══════════════════ CONTENT */
        .page-wrap {
            margin-top: var(--total-top);
            padding: 14px 16px;
            min-height: calc(100vh - var(--total-top));
        }

        /* Page title bar */
        .page-title-bar {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 14px; flex-wrap: wrap; gap: 8px;
        }
        .page-title-bar h4 {
            margin: 0; font-family: var(--font-hd);
            font-size: 1.1rem; font-weight: 800; color: var(--text);
        }
        .page-title-actions { display: flex; gap: 8px; }
        .btn-ns {
            font-family: var(--font); font-size: .75rem; font-weight: 700;
            padding: 5px 12px; border-radius: var(--radius);
            border: 1px solid var(--border); background: var(--card);
            color: var(--text-md); cursor: pointer; transition: all .15s;
            display: flex; align-items: center; gap: 5px;
        }
        .btn-ns:hover { background: var(--bg); border-color: #b0b8c8; }
        .btn-ns.primary { background: var(--brand); color: #fff; border-color: var(--brand); }
        .btn-ns.primary:hover { background: var(--brand-dark); }

        /* ══════════════════ PORTLET WIDGETS */
        .portlet {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-card);
            margin-bottom: 12px;
            overflow: hidden;
        }
        .portlet-header {
            background: linear-gradient(180deg, #f8fafe 0%, #f0f3f9 100%);
            border-bottom: 1px solid var(--border);
            padding: 7px 12px;
            display: flex; align-items: center; justify-content: space-between;
            cursor: default;
        }
        .portlet-title {
            font-family: var(--font-hd);
            font-size: .74rem; font-weight: 700; color: var(--brand);
            display: flex; align-items: center; gap: 6px;
        }
        .portlet-title i { font-size: .8rem; }
        .portlet-actions { display: flex; gap: 4px; }
        .portlet-btn {
            width: 20px; height: 20px; border-radius: 3px;
            display: flex; align-items: center; justify-content: center;
            font-size: .65rem; color: var(--text-sm); cursor: pointer;
            border: 1px solid transparent; transition: all .15s;
        }
        .portlet-btn:hover { background: var(--border-lt); border-color: var(--border); }
        .portlet-body { padding: 10px 12px; }
        .portlet-body.p0 { padding: 0; }

        /* ══════════════════ ALERTS / FLASH */
        .alert {
            border-radius: var(--radius-md); font-size: .8rem;
            border: none; padding: 10px 14px;
        }
        .alert-success { background: var(--green-lt); color: var(--green); border-left: 3px solid var(--green); }
        .alert-danger  { background: var(--red-lt);   color: var(--red);   border-left: 3px solid var(--red); }

        /* ══════════════════ CARDS / TABLES (shared) */
        .card {
            border: 1px solid var(--border) !important;
            border-radius: var(--radius-md) !important;
            box-shadow: var(--shadow-card) !important;
        }
        .card-header {
            background: linear-gradient(180deg,#f8fafe 0%,#f0f3f9 100%) !important;
            border-bottom: 1px solid var(--border) !important;
            font-family: var(--font-hd); font-weight: 700 !important;
            font-size: .76rem !important; color: var(--brand) !important;
            padding: 8px 14px !important;
            border-radius: var(--radius-md) var(--radius-md) 0 0 !important;
        }
        .table thead th {
            font-size: .67rem; text-transform: uppercase; letter-spacing: .5px;
            background: #f6f8fc !important; color: var(--text-sm); font-weight: 700;
            border-bottom: 1px solid var(--border) !important; padding: 8px 12px !important;
            font-family: var(--font-hd);
        }
        .table td { padding: 7px 12px !important; vertical-align: middle; font-size: .8rem; }
        .table tbody tr:hover td { background: #f6f9fe !important; }

        /* Form controls */
        .form-control, .form-select {
            font-family: var(--font); font-size: .8rem;
            border: 1px solid var(--border); border-radius: var(--radius);
            padding: 6px 10px; color: var(--text);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand); box-shadow: 0 0 0 2px rgba(31,78,140,.15); outline: none;
        }

        /* Stat card */
        .stat-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: var(--radius-md); padding: 14px;
            box-shadow: var(--shadow-card);
        }
        .stat-value { font-family: var(--font-mono); font-size: 1.5rem; font-weight: 600; color: var(--text); }
        .stat-label { font-size: .7rem; color: var(--text-sm); margin-top: 3px; }

        /* Breadcrumb */
        .ns-breadcrumb { font-size: .73rem; color: var(--text-sm); margin-bottom: 10px; }
        .ns-breadcrumb a { color: var(--brand); text-decoration: none; }
        .ns-breadcrumb a:hover { text-decoration: underline; }
        .ns-breadcrumb .sep { margin: 0 5px; color: var(--border); }
        .ns-breadcrumb .cur { color: var(--text-md); font-weight: 600; }

        /* Page header */
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 14px; flex-wrap: wrap; gap: 8px;
        }
        .page-header h4 {
            margin: 0; font-family: var(--font-hd);
            font-size: 1rem; font-weight: 800; color: var(--text);
        }

        /* ══════════════════ RESPONSIVE */
        @media(max-width:768px) {
            .nav-items .nav-item-wrap:nth-child(n+5) { display: none; }
            .th-search { max-width: 200px; }
            .page-wrap { padding: 10px; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ══ TOP HEADER ══════════════════════════════════════════════════════ --}}
<div class="top-header">
    <a href="{{ route('dashboard') }}" class="th-brand">
        <div class="th-brand-logo">
            <div class="logo-box"><i class="bi bi-grid-3x3-gap-fill"></i></div>
            <div>
                <div class="logo-name">{{ config('app.name','ERP') }}</div>
                <div class="logo-sub">Management System</div>
            </div>
        </div>
    </a>

    <div class="th-search" id="globalSearch">
        <div class="th-search-input-wrap">
            <i class="bi bi-search si-icon"></i>
            <input type="text" id="searchInput"
                   placeholder="Search reports, forms…"
                   autocomplete="off"
                   aria-label="Search">
            <button class="si-clear" id="searchClear" title="Clear">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="search-dropdown" id="searchDropdown" role="listbox"></div>
    </div>

    <div class="th-right">
{{--        <a href="{{ route('dashboard') }}" class="th-icon-btn" title="Home"><i class="bi bi-house-fill"></i></a>--}}
{{--        <a href="#" class="th-icon-btn" title="Bookmarks"><i class="bi bi-star-fill"></i></a>--}}
{{--        <a href="#" class="th-icon-btn" title="Refresh"><i class="bi bi-arrow-clockwise"></i></a>--}}

        @if(auth()->user()?->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="th-icon-btn" title="Admin"><i class="bi bi-shield-fill"></i></a>
        @endif

        <div class="th-user">
            <div class="th-user-avatar">{{ strtoupper(substr(auth()->user()?->name??'U',0,1)) }}</div>
            <div class="th-user-info d-none d-sm-block">
                <div class="th-user-name">{{ auth()->user()?->name }}</div>
                <div class="th-user-role">{{ auth()->user()?->role?->name ?? 'User' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-2">
                @csrf
                <button type="submit" class="th-icon-btn" title="Sign out" style="border:none;background:none;cursor:pointer;">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ══ HORIZONTAL NAV ══════════════════════════════════════════════════ --}}
<nav class="main-nav">
    <div class="nav-items">

        <div class="nav-item-wrap">
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'current' : '' }}">
                <i class="bi bi-house-fill" style="font-size:.8rem;"></i> Home
            </a>
        </div>

        @foreach($sidebarModules as $module)
            <div class="nav-item-wrap">
                <button>
                    <i class="bi {{ $module->icon }}" style="color:{{ $module->color }};font-size:.82rem;"></i>
                    {{ $module->name }}
                    <i class="bi bi-chevron-down nav-chevron"></i>
                </button>
                <div class="nav-dropdown">
                    @foreach($module->activeMenuItems as $item)
                        @if($item->type === 'divider')
                            <div class="nav-dropdown-divider"></div>
                        @else
                            <a href="{{ $item->url }}"
                               class="{{ $item->isActiveRoute() ? 'fw-bold' : '' }}">
                                <i class="bi {{ $item->icon ?: 'bi-file-text' }}"></i>
                                {{ $item->name }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach

    </div>

    <div class="nav-right">
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('admin.users.index') }}" class="nav-tool"><i class="bi bi-people-fill"></i> Users</a>
            <a href="{{ route('admin.roles.index') }}" class="nav-tool"><i class="bi bi-shield-fill"></i> Roles</a>
            <a href="{{ route('admin.modules.index') }}" class="nav-tool"><i class="bi bi-grid-3x3"></i> Setup</a>
            <a href="{{ route('admin.menu-items.index') }}" class="nav-tool"><i class="bi bi-grid-3x3"></i> Menu Items</a>
        @endif
{{--        <a href="{{ route('admin.company-settings.index') }}" class="nav-tool"><i class="bi bi-gear-fill"></i></a>--}}
    </div>
</nav>

{{-- ══ PAGE CONTENT ════════════════════════════════════════════════════ --}}
<div class="page-wrap">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error') || $errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') ?? $errors->first('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Breadcrumb --}}
    @hasSection('breadcrumb')
        <div class="ns-breadcrumb">
            <a href="{{ route('dashboard') }}">Home</a>
            <span class="sep">›</span>
            @yield('breadcrumb')
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function(){
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el=>new bootstrap.Tooltip(el));
    })();
</script>

@php
    // Fallback: if $searchIndex wasn't passed (e.g. from non-dashboard controllers),
    // build it on the fly from $sidebarModules which the SidebarComposer always provides.
    if (!isset($searchIndex)) {
        $searchIndex = isset($sidebarModules)
            ? $sidebarModules
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
                ->toArray()
            : [];
    }
@endphp

<script>
    /* ═══════════════════════════════════════ LIVE SEARCH ENGINE */
    (function () {
        // Search index pre-built server-side (always a simple array here)
        const menuIndex = @json($searchIndex);

        const input    = document.getElementById('searchInput');
        const dropdown = document.getElementById('searchDropdown');
        const clearBtn = document.getElementById('searchClear');
        let   hiIdx    = -1;
        let   debounce = null;

        if (!input) return;

        /* ── Helpers ──────────────────────────────────────────── */
        function esc(s) {
            return String(s).replace(/[&<>"']/g, c =>
                ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

        function highlight(text, query) {
            if (!query) return esc(text);
            const re = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\]/g,'\$&')})`, 'gi');
        return esc(text).replace(re, '<em>$1</em>');
    }

    function typeIcon(type) {
        return type === 'report' ? 'bi-bar-chart-line'
             : type === 'form'   ? 'bi-pencil-square'
             :                     'bi-link-45deg';
    }

    /* ── Core search ──────────────────────────────────────── */
    function doSearch(q) {
        q = q.trim().toLowerCase();
        dropdown.innerHTML = '';
        hiIdx = -1;

        if (!q) { close(); return; }

        // Filter
        const results = menuIndex.filter(item =>
            item.name.toLowerCase().includes(q) ||
            item.module.toLowerCase().includes(q)
        );

        if (results.length === 0) {
            dropdown.innerHTML = `
                <div class="sd-empty">
                    <i class="bi bi-search"></i>
                    No results for <strong>"${esc(q)}"</strong>
                </div>`;
            open();
            return;
        }

        // Group by module
        const byModule = {};
        results.forEach(r => {
            if (!byModule[r.module]) byModule[r.module] = [];
            byModule[r.module].push(r);
            });

        let html = '';
        Object.entries(byModule).forEach(([mod, items]) => {
            html += `<div class="sd-section-hd">${esc(mod)}</div>`;
            items.forEach((item, idx) => {
                const typeClass = item.type === 'report' ? 'report'
                                : item.type === 'form'   ? 'form' : 'link';
                html += `
            <a class="sd-item" href="${esc(item.url)}" data-idx="${idx}" role="option">
                <div class="sd-item-icon type-${typeClass}">
                <i class="bi ${esc(item.icon)}"></i>
        </div>
            <div class="sd-item-body">
                <div class="sd-item-name">${highlight(item.name, q)}</div>
                <div class="sd-item-meta">${esc(item.module)}</div>
            </div>
            <span class="sd-type-tag ${typeClass}">${esc(item.type)}</span>
        </a>`;
            });
        });

        html += `
            <div class="sd-footer">
                <span class="sd-kbd">↑↓</span> navigate &nbsp;
            <span class="sd-kbd">↵</span> open &nbsp;
            <span class="sd-kbd">Esc</span> close
            &nbsp;·&nbsp; ${results.length} result${results.length !== 1 ? 's' : ''}
        </div>`;

        dropdown.innerHTML = html;
        open();
    }

    /* ── Open / close ─────────────────────────────────────── */
    function open()  { dropdown.classList.add('open'); }
    function close() { dropdown.classList.remove('open'); hiIdx = -1; }

    /* ── Keyboard navigation ───────────────────────────────── */
    input.addEventListener('keydown', e => {
        const items = dropdown.querySelectorAll('.sd-item');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            hiIdx = Math.min(hiIdx + 1, items.length - 1);
            updateHighlight(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            hiIdx = Math.max(hiIdx - 1, 0);
            updateHighlight(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (hiIdx >= 0 && items[hiIdx]) {
                items[hiIdx].click();
            }
        } else if (e.key === 'Escape') {
            close();
            input.blur();
        }
    });

    function updateHighlight(items) {
        items.forEach((el, i) => el.classList.toggle('highlighted', i === hiIdx));
        if (hiIdx >= 0) items[hiIdx].scrollIntoView({ block: 'nearest' });
    }

    /* ── Input events ─────────────────────────────────────── */
    input.addEventListener('input', () => {
        const q = input.value;
        clearBtn.style.display = q ? 'block' : 'none';
        clearTimeout(debounce);
        debounce = setTimeout(() => doSearch(q), 160);
    });

    input.addEventListener('focus', () => {
        if (input.value.trim()) open();
    });

    clearBtn.addEventListener('click', () => {
        input.value = '';
        clearBtn.style.display = 'none';
        close();
        input.focus();
    });

    /* ── Close on outside click ───────────────────────────── */
    document.addEventListener('click', e => {
        if (!document.getElementById('globalSearch')?.contains(e.target)) {
            close();
        }
    });

    /* ── Keyboard shortcut: / to focus search ─────────────── */
    document.addEventListener('keydown', e => {
        if (e.key === '/' && document.activeElement !== input
            && !['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName)) {
            e.preventDefault();
            input.focus();
            input.select();
        }
    });

})();
</script>

@stack('scripts')
</body>
</html>
