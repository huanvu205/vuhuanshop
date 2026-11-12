<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Đăng nhập tài khoản <?= APP_NAME ?> - Mua sắm điện thoại, laptop chính hãng">
    <title>Đăng nhập - <?= APP_NAME ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= APP_URL ?>/public/css/style.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body.auth-page {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            background: #f8f9fa;
            overflow-x: hidden;
        }

        /* ===== LEFT PANEL ===== */
        .auth-left {
            flex: 0 0 50%;
            position: relative;
            background: linear-gradient(135deg, #c62828 0%, #b71c1c 30%, #7f0000 70%, #4a0000 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 50px;
            overflow: hidden;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,0.05) 0%, transparent 50%);
        }

        .auth-left-blob {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .auth-left-blob.b1 { width: 300px; height: 300px; top: -80px; left: -80px; }
        .auth-left-blob.b2 { width: 200px; height: 200px; bottom: -50px; right: -50px; }
        .auth-left-blob.b3 { width: 150px; height: 150px; top: 50%; left: 75%; transform: translate(-50%,-50%); background: rgba(255,255,255,0.03); }

        .auth-left-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            max-width: 380px;
        }

        .auth-brand {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            text-decoration: none;
            color: white;
            display: inline-block;
        }
        .auth-brand span { color: rgba(255,220,0,0.9); }
        .auth-brand i { font-size: 1.7rem; margin-right: 6px; }

        .auth-left-tagline {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.65);
            margin-bottom: 48px;
            letter-spacing: 0.5px;
        }

        .auth-illustration {
            width: 100%;
            max-width: 320px;
            margin: 0 auto 48px;
        }

        .auth-illustration svg {
            width: 100%;
            height: auto;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
        }

        .auth-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
            text-align: left;
            width: 100%;
            max-width: 320px;
        }

        .auth-feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            background: rgba(255,255,255,0.08);
            border-radius: 12px;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.1);
            transition: background 0.2s;
        }
        .auth-feature-item:hover { background: rgba(255,255,255,0.12); }

        .auth-feature-icon {
            width: 38px;
            height: 38px;
            background: rgba(255,220,0,0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .auth-feature-icon i { color: #FFD600; font-size: 15px; }

        .auth-feature-text h6 {
            font-size: 13px;
            font-weight: 600;
            color: rgba(255,255,255,0.95);
            margin: 0 0 2px;
        }
        .auth-feature-text p {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            margin: 0;
        }

        /* ===== RIGHT PANEL ===== */
        .auth-right {
            flex: 0 0 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 50px;
            background: #fff;
            position: relative;
        }

        .auth-right::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #c62828, #e53935, #c62828);
        }

        .auth-form-container {
            width: 100%;
            max-width: 400px;
        }

        .auth-form-header {
            margin-bottom: 36px;
        }

        .auth-form-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .auth-form-header p {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .auth-form-header p a {
            color: #c62828;
            font-weight: 600;
            text-decoration: none;
        }
        .auth-form-header p a:hover { text-decoration: underline; }

        /* Form Elements */
        .form-group-auth {
            margin-bottom: 20px;
        }

        .form-label-auth {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
            letter-spacing: 0.2px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 14px;
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-control-auth {
            width: 100%;
            padding: 12px 14px 12px 42px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            background: #fafafa;
            color: #1f2937;
            outline: none;
            transition: all 0.2s ease;
            -webkit-appearance: none;
            appearance: none;
        }

        .form-control-auth:focus {
            border-color: #c62828;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(198,40,40,0.1);
        }

        .form-control-auth.is-invalid {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .form-control-auth:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: #c62828;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            color: #9ca3af;
            font-size: 14px;
            transition: color 0.2s;
        }
        .password-toggle:hover { color: #c62828; }

        .form-control-auth.has-toggle { padding-right: 42px; }

        .invalid-feedback-auth {
            display: block;
            font-size: 0.78rem;
            color: #dc3545;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .invalid-feedback-auth i { font-size: 11px; }

        .form-row-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .form-check-auth {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .form-check-auth input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #c62828;
            cursor: pointer;
        }

        .form-check-auth label {
            font-size: 0.8125rem;
            color: #6b7280;
            cursor: pointer;
            user-select: none;
        }

        .forgot-link {
            font-size: 0.8125rem;
            color: #c62828;
            font-weight: 600;
            text-decoration: none;
        }
        .forgot-link:hover { text-decoration: underline; }

        .btn-auth-primary {
            width: 100%;
            padding: 13px 24px;
            font-size: 0.9375rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e53935 0%, #c62828 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-auth-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.25s;
        }

        .btn-auth-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(198,40,40,0.4);
        }
        .btn-auth-primary:hover::before { opacity: 1; }
        .btn-auth-primary:active { transform: translateY(0); }

        .btn-auth-primary .btn-spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #d1d5db;
            font-size: 0.8rem;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .social-login-btns {
            display: block;
            width: 100%;
            margin-bottom: 24px;
        }

        .btn-google-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            height: 48px;
            padding: 0 20px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            font-size: 0.9375rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .btn-google-login:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            color: #0f172a;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }
        .btn-google-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .google-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
        }

        .auth-footer-links {
            text-align: center;
            font-size: 0.8125rem;
            color: #9ca3af;
            margin-top: 8px;
        }
        .auth-footer-links a {
            color: #c62828;
            font-weight: 600;
            text-decoration: none;
        }
        .auth-footer-links a:hover { text-decoration: underline; }

        .alert-auth {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border: none;
        }
        .alert-auth.success {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid #22c55e;
        }
        .alert-auth.error {
            background: #fff5f5;
            color: #9b1c1c;
            border-left: 4px solid #ef4444;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .auth-left { display: none; }
            .auth-right {
                flex: 0 0 100%;
                padding: 40px 24px;
            }
        }

        @media (max-width: 575.98px) {
            .auth-right { padding: 32px 20px; }
            .social-login-btns { grid-template-columns: 1fr; }
        }

        /* Entrance animations */
        .auth-form-container {
            animation: fadeInUp 0.5s ease both;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .auth-left-content {
            animation: fadeInLeft 0.6s ease both;
        }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body class="auth-page">

    <!-- Left Panel -->
    <div class="auth-left">
        <div class="auth-left-blob b1"></div>
        <div class="auth-left-blob b2"></div>
        <div class="auth-left-blob b3"></div>

        <div class="auth-left-content">
            <a href="<?= url() ?>" class="auth-brand">
                <i class="fas fa-mobile-alt"></i>Vũ Huân <span>Shop</span>
            </a>
            <p class="auth-left-tagline">Thiết bị công nghệ chính hãng – Giá tốt nhất Việt Nam</p>

            <!-- SVG Illustration -->
            <div class="auth-illustration">
                <svg viewBox="0 0 320 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Shopping bag -->
                    <rect x="80" y="90" width="160" height="120" rx="16" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
                    <path d="M120 90 C120 65 140 48 160 48 C180 48 200 65 200 90" stroke="rgba(255,255,255,0.4)" stroke-width="3" fill="none" stroke-linecap="round"/>
                    <!-- Handle details -->
                    <circle cx="130" cy="90" r="5" fill="rgba(255,220,0,0.6)"/>
                    <circle cx="190" cy="90" r="5" fill="rgba(255,220,0,0.6)"/>
                    <!-- Phone in bag -->
                    <rect x="130" y="108" width="60" height="84" rx="8" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/>
                    <rect x="135" y="116" width="50" height="62" rx="4" fill="rgba(255,255,255,0.08)"/>
                    <circle cx="160" cy="184" r="4" fill="rgba(255,255,255,0.4)"/>
                    <!-- Stars -->
                    <text x="60" y="75" fill="rgba(255,220,0,0.6)" font-size="14">★</text>
                    <text x="240" y="110" fill="rgba(255,220,0,0.4)" font-size="10">★</text>
                    <text x="250" y="70" fill="rgba(255,220,0,0.5)" font-size="18">★</text>
                    <text x="45" y="150" fill="rgba(255,220,0,0.3)" font-size="12">★</text>
                    <!-- Discount badge -->
                    <circle cx="226" cy="95" r="22" fill="rgba(255,220,0,0.15)" stroke="rgba(255,220,0,0.3)" stroke-width="1.5"/>
                    <text x="214" y="90" fill="#FFD600" font-size="8" font-weight="bold">SALE</text>
                    <text x="212" y="102" fill="#FFD600" font-size="11" font-weight="bold">50%</text>
                </svg>
            </div>

            <div class="auth-features">
                <div class="auth-feature-item">
                    <div class="auth-feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="auth-feature-text">
                        <h6>Hàng chính hãng 100%</h6>
                        <p>Bảo hành toàn quốc, có hoá đơn VAT</p>
                    </div>
                </div>
                <div class="auth-feature-item">
                    <div class="auth-feature-icon"><i class="fas fa-tags"></i></div>
                    <div class="auth-feature-text">
                        <h6>Ưu đãi độc quyền thành viên</h6>
                        <p>Tích điểm, hoàn tiền và flash sale hàng ngày</p>
                    </div>
                </div>
                <div class="auth-feature-item">
                    <div class="auth-feature-icon"><i class="fas fa-truck"></i></div>
                    <div class="auth-feature-text">
                        <h6>Giao hàng siêu tốc</h6>
                        <p>Nhận hàng trong ngày tại TP.HCM & Hà Nội</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="auth-right">
        <div class="auth-form-container">

            <!-- Header -->
            <div class="auth-form-header">
                <h1>Chào mừng trở lại! 👋</h1>
                <p>Chưa có tài khoản? <a href="<?= url('auth/register') ?>">Đăng ký ngay</a></p>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash'])): ?>
                <?php showFlash(); ?>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert-auth error">
                    <i class="fas fa-exclamation-circle mt-1"></i>
                    <div><?= e($error) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert-auth success">
                    <i class="fas fa-check-circle mt-1"></i>
                    <div><?= e($success) ?></div>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="login-form" action="<?= url('auth/login') ?>" method="POST" novalidate>
                <?= csrfToken() ?>

                <!-- Email / Username -->
                <div class="form-group-auth">
                    <label class="form-label-auth" for="login-email">Email hoặc số điện thoại</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input
                            type="text"
                            id="login-email"
                            name="login"
                            class="form-control-auth <?= !empty($errors['login']) ? 'is-invalid' : '' ?>"
                            placeholder="example@email.com"
                            value="<?= e($_POST['login'] ?? '') ?>"
                            autocomplete="username"
                            required
                        >
                    </div>
                    <?php if (!empty($errors['login'])): ?>
                        <span class="invalid-feedback-auth">
                            <i class="fas fa-circle-exclamation"></i>
                            <?= e($errors['login']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="form-group-auth">
                    <label class="form-label-auth" for="login-password">Mật khẩu</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input
                            type="password"
                            id="login-password"
                            name="password"
                            class="form-control-auth has-toggle <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                            placeholder="Nhập mật khẩu của bạn"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="password-toggle" id="login-pwd-toggle" title="Hiện/Ẩn mật khẩu">
                            <i class="fas fa-eye" id="login-pwd-icon"></i>
                        </button>
                    </div>
                    <?php if (!empty($errors['password'])): ?>
                        <span class="invalid-feedback-auth">
                            <i class="fas fa-circle-exclamation"></i>
                            <?= e($errors['password']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Remember + Forgot -->
                <div class="form-row-flex">
                    <label class="form-check-auth">
                        <input type="checkbox" id="login-remember" name="remember" <?= !empty($_POST['remember']) ? 'checked' : '' ?>>
                        <label for="login-remember">Ghi nhớ đăng nhập</label>
                    </label>
                    <a href="<?= url('auth/forgot-password') ?>" class="forgot-link">Quên mật khẩu?</a>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-auth-primary" id="login-submit-btn">
                    <span class="btn-spinner" id="login-spinner"></span>
                    <i class="fas fa-sign-in-alt" id="login-btn-icon"></i>
                    <span>Đăng nhập</span>
                </button>
            </form>

            <!-- Divider -->
            <div class="auth-divider">hoặc tiếp tục với</div>

            <!-- Social Login -->
            <div class="social-login-btns">
                <a href="<?= url('auth/google') ?>" class="btn-google-login">
                    <svg class="google-icon" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Đăng nhập nhanh với Google</span>
                </a>
            </div>

            <!-- Footer -->
            <div class="auth-footer-links">
                <a href="<?= url() ?>"><i class="fas fa-arrow-left me-1"></i>Quay lại trang chủ</a>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    (function () {
        'use strict';

        // ── Password toggle ──
        const pwdInput  = document.getElementById('login-password');
        const pwdToggle = document.getElementById('login-pwd-toggle');
        const pwdIcon   = document.getElementById('login-pwd-icon');

        if (pwdToggle) {
            pwdToggle.addEventListener('click', function () {
                const isHidden = pwdInput.type === 'password';
                pwdInput.type  = isHidden ? 'text' : 'password';
                pwdIcon.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
            });
        }

        // ── Form submit with spinner ──
        const loginForm   = document.getElementById('login-form');
        const submitBtn   = document.getElementById('login-submit-btn');
        const spinner     = document.getElementById('login-spinner');
        const btnIcon     = document.getElementById('login-btn-icon');

        if (loginForm) {
            loginForm.addEventListener('submit', function (e) {
                const email    = document.getElementById('login-email').value.trim();
                const password = pwdInput.value;
                let valid = true;

                // Simple client-side check
                if (!email) {
                    e.preventDefault();
                    valid = false;
                    document.getElementById('login-email').classList.add('is-invalid');
                }
                if (!password) {
                    e.preventDefault();
                    valid = false;
                    pwdInput.classList.add('is-invalid');
                }

                if (valid) {
                    submitBtn.disabled = true;
                    spinner.style.display = 'block';
                    btnIcon.style.display = 'none';
                }
            });
        }

        // ── Remove is-invalid on input ──
        document.querySelectorAll('.form-control-auth').forEach(function (el) {
            el.addEventListener('input', function () {
                this.classList.remove('is-invalid');
            });
        });

        // ── Auto-dismiss alerts ──
        setTimeout(function () {
            document.querySelectorAll('.alert-auth').forEach(function (el) {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 500);
            });
        }, 5000);

    })();
    </script>
</body>
</html>
