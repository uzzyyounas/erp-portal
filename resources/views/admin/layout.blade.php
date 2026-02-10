<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Admin CSS -->
    <style>
        :root {
            --admin-primary: #ff6b18;
            --admin-secondary: #1a1a1a;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f8f9fa;
        }

        /* Sidebar */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--admin-secondary);
            color: white;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }

        .admin-sidebar .brand {
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--admin-primary);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            text-decoration: none;
        }

        .admin-sidebar .nav-link:hover {
            background: rgba(255,107,24,0.1);
            color: white;
        }

        .admin-sidebar .nav-link.active {
            background: var(--admin-primary);
            color: white;
        }

        .admin-sidebar .nav-link i {
            width: 24px;
            margin-right: 0.75rem;
        }

        /* Main Content */
        .admin-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Top Bar */
        .admin-topbar {
            background: white;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .admin-content {
            padding: 2rem;
        }

        /* Cards */
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid var(--admin-primary);
        }

        .stat-card .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255,107,24,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--admin-primary);
            font-size: 1.5rem;
        }

        /* Tables */
        .table-responsive {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            color: #6c757d;
        }

        /* Badges */
        .badge-active {
            background: #28a745;
        }

        .badge-inactive {
            background: #6c757d;
        }

        .badge-unread {
            background: #dc3545;
        }

        /* Buttons */
        .btn-primary {
            background: var(--admin-primary);
            border-color: var(--admin-primary);
        }

        .btn-primary:hover {
            background: #e55f15;
            border-color: #e55f15;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .admin-sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            .admin-sidebar.show {
                margin-left: 0;
            }

            .admin-main {
                margin-left: 0;
            }
        }

        /* Form */
        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 0.2rem rgba(255,107,24,0.25);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="brand">
            <i class="fas fa-hard-hat"></i> WeldPro Admin
        </div>
        <nav class="mt-3">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <i class="fas fa-fire"></i> Services
            </a>
            <a href="{{ route('admin.portfolios.index') }}" class="nav-link {{ request()->routeIs('admin.portfolios.*') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i> Portfolio
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> Categories
            </a>
            <a href="{{ route('admin.testimonials.index') }}" class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
                <i class="fas fa-star"></i> Testimonials
            </a>
            <a href="{{ route('admin.blog.index') }}" class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
                <i class="fas fa-blog"></i> Blog Posts
            </a>
            <a href="{{ route('admin.contacts.index') }}" class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i> Contacts
                @if(isset($unreadCount) && $unreadCount > 0)
                <span class="badge bg-danger ms-auto">{{ $unreadCount }}</span>
                @endif
            </a>
            <hr class="my-3" style="border-color: rgba(255,255,255,0.1);">
            <a href="{{ route('home') }}" class="nav-link" target="_blank">
                <i class="fas fa-eye"></i> View Website
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Bar -->
        <div class="admin-topbar">
            <div>
                <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-3">
                    <i class="far fa-user me-2"></i>{{ Auth::user()->name }}
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="admin-content">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>
