<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f2540 0%, #1a3a5c 50%, #2d6a9f 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .login-card {
            width: 100%; max-width: 400px;
            background: #fff;
            border-radius: .75rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #0f2540, #1a3a5c);
            color: #fff;
            padding: 2rem;
            text-align: center;
        }
        .login-header .brand-icon {
            width: 60px; height: 60px;
            background: rgba(255,255,255,.15);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.75rem;
        }
        .login-header h4 { margin: 0; font-weight: 700; letter-spacing: .5px; }
        .login-header p  { margin: .3rem 0 0; color: rgba(255,255,255,.7); font-size: .85rem; }
        .login-body { padding: 2rem; }
        .form-control:focus { border-color: #2d6a9f; box-shadow: 0 0 0 .2rem rgba(45,106,159,.25); }
        .btn-login {
            background: linear-gradient(135deg, #1a3a5c, #2d6a9f);
            color: #fff; border: none; width: 100%;
            padding: .65rem; font-weight: 600; border-radius: .4rem;
            transition: opacity .2s;
        }
        .btn-login:hover { opacity: .9; color: #fff; }
        .login-footer {
            background: #f8f9fa;
            padding: 1rem 2rem;
            text-align: center;
            font-size: .78rem;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="brand-icon">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </div>
            <h4>{{ config('app.name', 'ERP Reports') }}</h4>
            <p>Sign in to access your reports</p>
        </div>

        <div class="login-body">
            @if(session('success'))
                <div class="alert alert-success alert-sm py-2">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email or Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
                               value="{{ old('login') }}" placeholder="usmanyounas@gmail.com" autofocus required>
                        @error('login')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" id="passwordInput"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Enter password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </form>
        </div>

        <div class="login-footer">
            {{ config('app.name') }} &copy; {{ date('Y') }} &nbsp;·&nbsp; All rights reserved
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePwd')?.addEventListener('click', () => {
            const input = document.getElementById('passwordInput');
            const icon  = document.getElementById('eyeIcon');
            input.type  = input.type === 'password' ? 'text' : 'password';
            icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        });
    </script>
</body>
</html>
