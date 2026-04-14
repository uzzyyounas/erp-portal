<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ERP') — {{ config('app.name') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sidebar-w: 240px;
            --topbar-h: 56px;
            --brand:    #1a3a5c;
            --brand2:   #2d6a9f;
            --accent:   #e8a020;
            --sidebar-bg: #0f2236;
            --sidebar-text: rgba(255,255,255,.75);
            --sidebar-hover: rgba(255,255,255,.08);
            --sidebar-active-bg: rgba(255,255,255,.13);
        }

        /* ── Layout ─────────────────────────────────────────── */
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f0f4f8; }

        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 1040;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-brand {
            padding: 0 16px;
            height: var(--topbar-h);
            display: flex; align-items: center; gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            flex-shrink: 0;
            text-decoration: none;
        }
        .sidebar-brand .brand-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: #fff;
        }
        .sidebar-brand .brand-text {
            font-size: .88rem; font-weight: 700;
            color: #fff; letter-spacing: .3px;
        }
        .sidebar-brand .brand-sub {
            font-size: .65rem; color: rgba(255,255,255,.45);
            line-height: 1; display: block;
        }

        /* ── Module sections ─────────────────────────────────── */
        .sidebar-nav { flex: 1; padding: 8px 0 16px; }

        .module-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 12px 6px 14px;
            cursor: pointer;
            user-select: none;
            color: rgba(255,255,255,.45);
            font-size: .62rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .8px;
            margin-top: 4px;
            transition: color .15s;
        }
        .module-header:hover { color: rgba(255,255,255,.75); }
        .module-header .mod-icon {
            display: inline-flex; align-items: center; gap: 6px;
        }
        .module-header .mod-icon i {
            font-size: .9rem;
        }
        .module-header .chevron {
            font-size: .7rem;
            transition: transform .2s;
        }
        .module-header.collapsed .chevron { transform: rotate(-90deg); }

        .module-items { padding: 0 6px; }
        .module-items .nav-item a {
            display: flex; align-items: center; gap: 9px;
            padding: 7px 10px;
            border-radius: 7px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: .8rem;
            margin-bottom: 2px;
            transition: background .15s, color .15s;
            white-space: nowrap; overflow: hidden;
        }
        .module-items .nav-item a i { font-size: .9rem; flex-shrink: 0; }
        .module-items .nav-item a:hover {
            background: var(--sidebar-hover); color: #fff;
        }
        .module-items .nav-item a.active {
            background: var(--sidebar-active-bg); color: #fff;
            font-weight: 600;
        }
        .module-items .nav-item a .item-type-badge {
            margin-left: auto;
            font-size: .55rem; padding: 1px 5px;
            border-radius: 4px; flex-shrink: 0;
            font-weight: 600; text-transform: uppercase; letter-spacing: .3px;
        }
        .type-report  { background: rgba(29,115,209,.3); color: #7ec8f7; }
        .type-form    { background: rgba(232,160,32,.25); color: #f9c95c; }
        .type-link    { background: rgba(255,255,255,.1); color: rgba(255,255,255,.55); }

        /* ── Sidebar footer ─────────────────────────────────── */
        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,.08);
            padding: 10px 12px;
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 9px;
        }
        .sidebar-user .avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--brand2);
            display: flex; align-items: center; justify-content: center;
            font-size: .82rem; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .sidebar-user .user-info { flex: 1; min-width: 0; }
        .sidebar-user .user-name {
            font-size: .78rem; font-weight: 600; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-user .user-role {
            font-size: .64rem; color: rgba(255,255,255,.45);
        }

        /* ── Topbar ──────────────────────────────────────────── */
        .topbar {
            position: fixed; top: 0; left: var(--sidebar-w); right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center;
            padding: 0 20px; gap: 12px;
            z-index: 1030;
        }
        .topbar .breadcrumb { margin: 0; font-size: .78rem; }
        .topbar .breadcrumb-item + .breadcrumb-item::before { color: #94a3b8; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 10px; }

        /* ── Content area ────────────────────────────────────── */
        .main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 24px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* ── Component styles ────────────────────────────────── */
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px; flex-wrap: wrap; gap: 10px;
        }
        .page-header h4 { margin: 0; font-size: 1.1rem; font-weight: 700; color: #1a3a5c; }

        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.07); border-radius: 10px; }
        .card-header {
            background: #fff; border-bottom: 1px solid #f0f4f8;
            font-weight: 600; font-size: .85rem; color: #1a3a5c;
            border-radius: 10px 10px 0 0 !important;
        }
        .card-header.light { background: #f8fafc; }

        .stat-card {
            background: #fff; border-radius: 10px; padding: 16px 18px;
            border-left: 4px solid #1a3a5c;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
        }
        .stat-value { font-size: 1.6rem; font-weight: 700; color: #1a3a5c; line-height: 1; }
        .stat-label { font-size: .72rem; color: #64748b; margin-top: 4px; }

        .btn-erp {
            background: #1a3a5c; color: #fff; border: none;
        }
        .btn-erp:hover { background: #2d6a9f; color: #fff; }

        .btn-xs { padding: 2px 8px; font-size: .75rem; }

        .table thead th {
            font-size: .72rem; text-transform: uppercase;
            letter-spacing: .4px; background: #f8fafc;
            color: #64748b; font-weight: 700; border-bottom: 1px solid #e2e8f0;
        }
        .table tbody tr:hover { background: #f8fafc !important; }

        /* ── Admin sidebar divider ───────────────────────────── */
        .admin-link {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 10px 8px 16px;
            color: rgba(255,255,255,.35);
            text-decoration: none; font-size: .76rem;
            transition: color .15s;
        }
        .admin-link:hover { color: rgba(255,255,255,.75); }
        .admin-link i { font-size: .85rem; }
        .sidebar-divider {
            border: none; border-top: 1px solid rgba(255,255,255,.06);
            margin: 8px 12px;
        }

        /* ── Scrollbar ────────────────────────────────────────── */
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 2px; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .25s; }
            .sidebar.open { transform: translateX(0); }
            .topbar { left: 0; }
            .main-content { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ══ Sidebar ════════════════════════════════════════════════════════════ --}}
<nav class="sidebar" id="sidebar">

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
        <div>
            <div class="brand-text">{{ config('app.name', 'ERP') }}</div>
            <span class="brand-sub">Management System</span>
        </div>
    </a>

    {{-- Dynamic module nav --}}
    <div class="sidebar-nav">

        {{-- Dashboard link --}}
        <div class="module-items">
            <div class="nav-item">
                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-fill"></i> Dashboard
                </a>
            </div>
        </div>

        <hr class="sidebar-divider">

        {{-- Modules injected by SidebarComposer --}}
        @foreach($sidebarModules as $module)
            @php
                $isOpen = $module->activeMenuItems->contains(fn($item) => $item->isActiveRoute());
                $collapseId = 'mod_' . $module->id;
            @endphp

            <div class="module-header {{ $isOpen ? '' : '' }}"
                 data-bs-toggle="collapse"
                 data-bs-target="#{{ $collapseId }}"
                 aria-expanded="{{ $isOpen ? 'true' : 'true' }}">
                <span class="mod-icon">
                    <i class="bi {{ $module->icon }}" style="color:{{ $module->color }};"></i>
                    {{ $module->name }}
                </span>
                <i class="bi bi-chevron-down chevron"></i>
            </div>

{{--            {{ dd($item->route_name, $item->url) }}--}}

            <div class="collapse {{ $isOpen ? 'show' : 'show' }}" id="{{ $collapseId }}">
                <div class="module-items">
                    @foreach($module->activeMenuItems as $item)
                        @if($item->type === 'divider')
                            <hr class="sidebar-divider">
                        @else
                            <div class="nav-item">
                                <a href="{{ $item->url }}"
                                   class="{{ $item->isActiveRoute() ? 'active' : '' }}">
                                    <i class="bi {{ $item->icon ?: 'bi-file-text' }}"></i>
                                    {{ $item->name }}
                                    <span class="item-type-badge type-{{ $item->type }}">
                                        {{ substr($item->type, 0, 1) }}
                                    </span>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach

        @if(auth()->user()?->isAdmin())
            <hr class="sidebar-divider">
            <div class="module-items" style="margin-top:2px;">
                <div style="padding:4px 10px 2px;font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.25);">
                    Administration
                </div>
                @foreach([
                    ['route'=>'admin.dashboard',          'icon'=>'bi-speedometer2',   'label'=>'Dashboard'],
                    ['route'=>'admin.modules.index',       'icon'=>'bi-grid-3x3',       'label'=>'Modules'],
                    ['route'=>'admin.menu-items.index',    'icon'=>'bi-list-ul',         'label'=>'Menu Items'],
                    ['route'=>'admin.roles.index',         'icon'=>'bi-shield-fill',     'label'=>'Roles & Permissions'],
                    ['route'=>'admin.users.index',         'icon'=>'bi-people-fill',     'label'=>'Users'],
                ] as $al)
                    <div class="nav-item">
                        <a href="{{ route($al['route']) }}"
                           class="admin-link {{ request()->routeIs($al['route'].'*') ? 'active' : '' }}"
                           style="{{ request()->routeIs($al['route'].'*') ? 'color:#fff;background:rgba(255,255,255,.1);border-radius:7px;' : '' }}">
                            <i class="bi {{ $al['icon'] }}"></i> {{ $al['label'] }}
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    {{-- Sidebar footer: user info + logout --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()?->name }}</div>
                <div class="user-role">{{ auth()->user()?->role?->name }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                @csrf
                <button type="submit" class="btn btn-link p-0"
                        title="Logout" style="color:rgba(255,255,255,.4);font-size:1rem;">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- ══ Topbar ══════════════════════════════════════════════════════════════ --}}
<header class="topbar">
    <button class="btn btn-sm d-md-none" id="sidebarToggle" style="color:#64748b;">
        <i class="bi bi-list fs-5"></i>
    </button>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" style="color:#1a3a5c;text-decoration:none;">
                    <i class="bi bi-house-fill"></i>
                </a>
            </li>
            @yield('breadcrumb')
        </ol>
    </nav>
    <div class="topbar-right">
        <small class="text-muted d-none d-sm-block" style="font-size:.72rem;">
            {{ now()->format('d M Y') }}
        </small>
    </div>
</header>

{{-- ══ Main Content ═════════════════════════════════════════════════════════ --}}
<main class="main-content">

    {{-- Global flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error') || $errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle me-2"></i>
            {{ session('error') ?? $errors->first('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mobile sidebar toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('open');
    });

    // Collapse chevron rotation
    document.querySelectorAll('.module-header').forEach(header => {
        const targetId = header.getAttribute('data-bs-target');
        const target   = document.querySelector(targetId);
        if (!target) return;
        target.addEventListener('show.bs.collapse',  () => header.querySelector('.chevron')?.classList.remove('collapsed'));
        target.addEventListener('hide.bs.collapse',  () => header.querySelector('.chevron')?.classList.add('collapsed'));
    });

    // Bootstrap tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
</script>

@stack('scripts')
</body>
</html>
