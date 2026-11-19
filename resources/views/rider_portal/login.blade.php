<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rider Portal Login | Munch Fried Chicken</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --rider-primary: #f97316;
            --rider-dark: #0f172a;
            --rider-bg: #f8fafc;
        }
        body {
            background-color: var(--rider-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            color: #1e293b;
        }
        .rider-login-card {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .rider-header {
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
            position: relative;
        }
        .rider-icon-bubble {
            width: 72px;
            height: 72px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 12px;
            border: 2px solid rgba(255, 255, 255, 0.4);
            animation: bounceIn 0.6s ease;
        }
        @keyframes bounceIn {
            0% { transform: scale(0.6); opacity: 0; }
            70% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }
        .form-control-rider {
            height: 48px;
            border-radius: 12px;
            border: 1.5px solid #cbd5e1;
            padding: 10px 14px;
            font-size: 15px;
            transition: all 0.2s;
        }
        .form-control-rider:focus {
            border-color: var(--rider-primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
        }
        .btn-rider-login {
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn-rider-login:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

<div class="rider-login-card">
    <div class="rider-header">
        <div class="rider-icon-bubble">
            <i class="fas fa-motorcycle"></i>
        </div>
        <h4 class="fw-bold mb-1">Rider Delivery Portal</h4>
        <p class="mb-0 opacity-75 small">Munch Fried Chicken • Delivery Fleet</p>
    </div>

    <div class="p-4">
        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small rounded-3 mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('rider.login.submit') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary mb-1">
                    <i class="fas fa-phone-alt me-1 text-warning"></i> Mobile / Phone Number
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 12px 0 0 12px;">
                        <i class="fas fa-user-circle"></i>
                    </span>
                    <input type="text" name="login" class="form-control form-control-rider border-start-0" 
                           placeholder="03xx-xxxxxxx" value="{{ old('login') }}" required autofocus style="border-radius: 0 12px 12px 0;">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary mb-1">
                    <i class="fas fa-key me-1 text-warning"></i> PIN / Password
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 12px 0 0 12px;">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="riderPasswordInput" class="form-control form-control-rider border-start-0 border-end-0" 
                           placeholder="Enter PIN / Password" required>
                    <button class="btn btn-outline-secondary border-start-0 text-muted" type="button" id="togglePasswordBtn" style="border-radius: 0 12px 12px 0;">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
                        <label class="form-check-label text-muted small" for="rememberMe">Remember me</label>
                    </div>
                    <small class="text-muted">Default PIN: <strong>123456</strong></small>
                </div>
            </div>

            <button type="submit" class="btn btn-rider-login w-100 d-flex align-items-center justify-content-center gap-2">
                <span>Open Delivery Screen</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="text-center mt-4 pt-2 border-top">
            <small class="text-muted">Manager / Staff? <a href="{{ route('software') }}" class="text-decoration-none fw-bold" style="color: #ea580c;">POS Login</a></small>
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePasswordBtn').addEventListener('click', function() {
        const input = document.getElementById('riderPasswordInput');
        const eye = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.remove('fa-eye');
            eye.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            eye.classList.remove('fa-eye-slash');
            eye.classList.add('fa-eye');
        }
    });
</script>
</body>
</html>
