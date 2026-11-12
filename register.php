<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tạo tài khoản <?= APP_NAME ?> - Đăng ký để nhận ưu đãi độc quyền và mua sắm dễ dàng hơn">
    <title>Đăng ký tài khoản - <?= APP_NAME ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= APP_URL ?>/public/css/style.css" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body.auth-page {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #f3f4f6;
            display: flex;
            flex-direction: column;
        }

        /* ===== TOP BAR ===== */
        .reg-topbar {
            background: linear-gradient(135deg, #c62828 0%, #b71c1c 100%);
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(198,40,40,0.3);
        }

        .reg-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 32px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .reg-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.5px;
        }
        .reg-brand span { color: #FFD600; }

        .reg-topbar-steps {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .reg-step {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: rgba(255,255,255,0.55);
            padding: 6px 12px;
        }
        .reg-step.active { color: white; }
        .reg-step.done { color: rgba(255,220,0,0.8); }

        .reg-step-num {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .reg-step.active .reg-step-num {
            background: white;
            color: #c62828;
        }
        .reg-step.done .reg-step-num {
            background: #FFD600;
            color: #7f0000;
        }

        .reg-step-sep { color: rgba(255,255,255,0.25); font-size: 10px; }

        .topbar-login-link {
            font-size: 13px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .topbar-login-link:hover { color: white; }

        /* ===== MAIN CONTENT ===== */
        .reg-main {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 40px 16px 60px;
        }

        .reg-wrapper {
            display: flex;
            gap: 28px;
            width: 100%;
            max-width: 1060px;
            align-items: flex-start;
        }

        /* ===== FORM CARD ===== */
        .reg-card {
            flex: 1;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            animation: fadeInUp 0.5s ease both;
        }

        .reg-card-header {
            background: linear-gradient(135deg, #c62828 0%, #9b1c1c 100%);
            padding: 28px 36px;
            position: relative;
            overflow: hidden;
        }
        .reg-card-header::before {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 120px; height: 120px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }
        .reg-card-header::after {
            content: '';
            position: absolute;
            bottom: -20px; left: 60%;
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }

        .reg-card-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 4px;
            position: relative;
            z-index: 1;
        }
        .reg-card-header p {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
            margin: 0;
            position: relative;
            z-index: 1;
        }
        .reg-card-header p a {
            color: #FFD600;
            font-weight: 600;
            text-decoration: none;
        }
        .reg-card-header p a:hover { text-decoration: underline; }

        .reg-card-body {
            padding: 32px 36px 36px;
        }

        /* Form elements */
        .form-row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group-auth {
            margin-bottom: 18px;
        }

        .form-label-auth {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .input-wrapper { position: relative; }

        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 13px;
            pointer-events: none;
            transition: color 0.2s;
        }
        .input-wrapper:focus-within .input-icon { color: #c62828; }

        .form-control-auth {
            width: 100%;
            padding: 11px 13px 11px 38px;
            font-size: 0.875rem;
            font-family: 'Inter', sans-serif;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            background: #fafafa;
            color: #1f2937;
            outline: none;
            transition: all 0.2s;
            -webkit-appearance: none;
        }
        .form-control-auth:focus {
            border-color: #c62828;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(198,40,40,0.08);
        }
        .form-control-auth.is-invalid {
            border-color: #ef4444;
            background: #fff8f8;
        }
        .form-control-auth.is-valid {
            border-color: #22c55e;
            background: #f0fdf4;
        }
        .form-control-auth.has-toggle { padding-right: 40px; }

        .form-control-auth.no-icon {
            padding-left: 13px;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            color: #9ca3af;
            font-size: 13px;
            transition: color 0.2s;
        }
        .password-toggle:hover { color: #c62828; }

        .invalid-feedback-auth {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.775rem;
            color: #ef4444;
            margin-top: 5px;
        }
        .valid-feedback-auth {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.775rem;
            color: #22c55e;
            margin-top: 5px;
        }

        /* Password strength */
        .pwd-strength {
            margin-top: 8px;
        }
        .pwd-strength-bar {
            display: flex;
            gap: 4px;
            margin-bottom: 5px;
        }
        .pwd-strength-segment {
            flex: 1;
            height: 4px;
            border-radius: 2px;
            background: #e5e7eb;
            transition: background 0.3s;
        }
        .pwd-strength-label {
            font-size: 11px;
            color: #9ca3af;
        }

        /* Terms */
        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
            padding: 14px 16px;
            background: #fafafa;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
        }
        .terms-checkbox input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #c62828;
            margin-top: 2px;
            flex-shrink: 0;
            cursor: pointer;
        }
        .terms-checkbox label {
            font-size: 0.8rem;
            color: #6b7280;
            cursor: pointer;
            line-height: 1.5;
        }
        .terms-checkbox label a {
            color: #c62828;
            font-weight: 600;
            text-decoration: none;
        }
        .terms-checkbox label a:hover { text-decoration: underline; }
        .terms-checkbox.has-error { border-color: #ef4444; background: #fff8f8; }

        /* Submit btn */
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
            transition: all 0.25s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }
        .btn-auth-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(198,40,40,0.35);
        }
        .btn-auth-primary:active { transform: translateY(0); }
        .btn-auth-primary:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .btn-spinner {
            display: none;
            width: 18px; height: 18px;
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
            margin: 20px 0;
            color: #9ca3af;
            font-size: 12px;
        }
        .auth-divider::before, .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .social-login-btns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .btn-social {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            background: white;
            font-size: 13px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-social:hover {
            border-color: #d1d5db;
            background: #f9fafb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            color: #374151;
        }
        .btn-social.google i { color: #ea4335; }
        .btn-social.facebook i { color: #1877f2; }

        /* ===== SIDE PANEL ===== */
        .reg-side {
            width: 280px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 16px;
            animation: fadeInRight 0.5s ease 0.1s both;
        }

        .side-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .side-card h6 {
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .side-card h6 i { color: #c62828; }

        .benefit-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .benefit-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12px;
            color: #6b7280;
            line-height: 1.5;
        }
        .benefit-icon {
            width: 26px; height: 26px;
            border-radius: 8px;
            background: #fee2e2;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .benefit-icon i { color: #c62828; font-size: 11px; }

        .promo-card {
            background: linear-gradient(135deg, #c62828 0%, #7f0000 100%);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .promo-card::before {
            content: '';
            position: absolute;
            top: -20px; right: -20px;
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }
        .promo-badge {
            display: inline-block;
            background: #FFD600;
            color: #7f0000;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .promo-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }
        .promo-value small { font-size: 0.9rem; font-weight: 500; }
        .promo-desc {
            font-size: 11px;
            color: rgba(255,255,255,0.7);
            margin: 0;
        }

        /* Alert */
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
        .alert-auth.success { background: #f0fdf4; color: #166534; border-left: 4px solid #22c55e; }
        .alert-auth.error   { background: #fff5f5; color: #9b1c1c; border-left: 4px solid #ef4444; }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .reg-side { display: none; }
            .reg-topbar-steps { display: none; }
        }
        @media (max-width: 575.98px) {
            .reg-topbar-inner { padding: 12px 16px; }
            .reg-card-body { padding: 24px 20px; }
            .reg-card-header { padding: 22px 20px; }
            .form-row-2col { grid-template-columns: 1fr; }
            .social-login-btns { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="auth-page">

    <!-- Top Bar -->
    <div class="reg-topbar">
        <div class="reg-topbar-inner">
            <a href="<?= url() ?>" class="reg-brand">
                <i class="fas fa-mobile-alt"></i>Vũ Huân <span>Shop</span>
            </a>

            <!-- Progress Steps -->
            <div class="reg-topbar-steps">
                <div class="reg-step active">
                    <div class="reg-step-num">1</div>
                    <span>Thông tin</span>
                </div>
                <span class="reg-step-sep"><i class="fas fa-chevron-right"></i></span>
                <div class="reg-step">
                    <div class="reg-step-num">2</div>
                    <span>Xác nhận</span>
                </div>
                <span class="reg-step-sep"><i class="fas fa-chevron-right"></i></span>
                <div class="reg-step">
                    <div class="reg-step-num">3</div>
                    <span>Hoàn tất</span>
                </div>
            </div>

            <a href="<?= url('auth/login') ?>" class="topbar-login-link">
                <i class="fas fa-sign-in-alt"></i> Đăng nhập
            </a>
        </div>
    </div>

    <!-- Main -->
    <div class="reg-main">
        <div class="reg-wrapper">

            <!-- Form Card -->
            <div class="reg-card">
                <div class="reg-card-header">
                    <h1>Tạo tài khoản mới 🎉</h1>
                    <p>Đã có tài khoản? <a href="<?= url('auth/login') ?>">Đăng nhập ngay</a> để tiếp tục mua sắm</p>
                </div>

                <div class="reg-card-body">

                    <!-- Flash / Errors -->
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
                    <?php $flash = getFlash(); if ($flash): ?>
                        <div class="alert-auth <?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
                            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check' : 'exclamation' ?>-circle mt-1"></i>
                            <div><?= e($flash['message']) ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($errors) && is_array($errors)): ?>
                        <div class="alert-auth error">
                            <i class="fas fa-exclamation-circle mt-1"></i>
                            <div>
                                <ul style="margin:0;padding-left:18px;">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= e($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Use $old variable from controller, fallback to $_POST
                    $oldData = $old ?? $_POST ?? [];
                    ?>

                    <form id="register-form" action="<?= url('auth/register') ?>" method="POST" novalidate>
                        <?= csrfToken() ?>

                        <!-- Full Name + Username -->
                        <div class="form-row-2col">
                            <div class="form-group-auth">
                                <label class="form-label-auth" for="reg-fullname">Họ và tên <span style="color:#ef4444">*</span></label>
                                <div class="input-wrapper">
                                    <span class="input-icon"><i class="fas fa-user"></i></span>
                                    <input
                                        type="text"
                                        id="reg-fullname"
                                        name="full_name"
                                        class="form-control-auth <?= !empty($errors['full_name']) ? 'is-invalid' : '' ?>"
                                        placeholder="Nguyễn Văn A"
                                        value="<?= e($oldData['full_name'] ?? '') ?>"
                                        autocomplete="name"
                                        required
                                    >
                                </div>
                                <?php if (!empty($errors['full_name'])): ?>
                                    <span class="invalid-feedback-auth">
                                        <i class="fas fa-circle-exclamation"></i>
                                        <?= e($errors['full_name']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group-auth">
                                <label class="form-label-auth" for="reg-username">Tên đăng nhập <span style="color:#ef4444">*</span></label>
                                <div class="input-wrapper">
                                    <span class="input-icon"><i class="fas fa-at"></i></span>
                                    <input
                                        type="text"
                                        id="reg-username"
                                        name="username"
                                        class="form-control-auth <?= !empty($errors['username']) ? 'is-invalid' : '' ?>"
                                        placeholder="nguyenvana"
                                        value="<?= e($oldData['username'] ?? '') ?>"
                                        autocomplete="username"
                                        required
                                    >
                                </div>
                                <?php if (!empty($errors['username'])): ?>
                                    <span class="invalid-feedback-auth">
                                        <i class="fas fa-circle-exclamation"></i>
                                        <?= e($errors['username']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group-auth">
                            <label class="form-label-auth" for="reg-email">Địa chỉ email <span style="color:#ef4444">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon"><i class="fas fa-envelope"></i></span>
                                <input
                                    type="email"
                                    id="reg-email"
                                    name="email"
                                    class="form-control-auth <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                                    placeholder="example@email.com"
                                    value="<?= e($oldData['email'] ?? '') ?>"
                                    autocomplete="email"
                                    required
                                >
                            </div>
                            <?php if (!empty($errors['email'])): ?>
                                <span class="invalid-feedback-auth">
                                    <i class="fas fa-circle-exclamation"></i>
                                    <?= e($errors['email']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Phone -->
                        <div class="form-group-auth">
                            <label class="form-label-auth" for="reg-phone">Số điện thoại</label>
                            <div class="input-wrapper">
                                <span class="input-icon"><i class="fas fa-phone"></i></span>
                                <input
                                    type="tel"
                                    id="reg-phone"
                                    name="phone"
                                    class="form-control-auth <?= !empty($errors['phone']) ? 'is-invalid' : '' ?>"
                                    placeholder="0912 345 678"
                                    value="<?= e($oldData['phone'] ?? '') ?>"
                                    autocomplete="tel"
                                >
                            </div>
                            <?php if (!empty($errors['phone'])): ?>
                                <span class="invalid-feedback-auth">
                                    <i class="fas fa-circle-exclamation"></i>
                                    <?= e($errors['phone']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Password + Confirm -->
                        <div class="form-row-2col">
                            <div class="form-group-auth">
                                <label class="form-label-auth" for="reg-password">Mật khẩu <span style="color:#ef4444">*</span></label>
                                <div class="input-wrapper">
                                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                                    <input
                                        type="password"
                                        id="reg-password"
                                        name="password"
                                        class="form-control-auth has-toggle <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                                        placeholder="Ít nhất 8 ký tự"
                                        autocomplete="new-password"
                                        required
                                    >
                                    <button type="button" class="password-toggle" id="reg-pwd-toggle" title="Hiện mật khẩu">
                                        <i class="fas fa-eye" id="reg-pwd-icon"></i>
                                    </button>
                                </div>
                                <?php if (!empty($errors['password'])): ?>
                                    <span class="invalid-feedback-auth">
                                        <i class="fas fa-circle-exclamation"></i>
                                        <?= e($errors['password']) ?>
                                    </span>
                                <?php endif; ?>
                                <!-- Strength bar -->
                                <div class="pwd-strength" id="pwd-strength-box" style="display:none">
                                    <div class="pwd-strength-bar">
                                        <div class="pwd-strength-segment" id="ps1"></div>
                                        <div class="pwd-strength-segment" id="ps2"></div>
                                        <div class="pwd-strength-segment" id="ps3"></div>
                                        <div class="pwd-strength-segment" id="ps4"></div>
                                    </div>
                                    <div class="pwd-strength-label" id="pwd-strength-label"></div>
                                </div>
                            </div>

                            <div class="form-group-auth">
                                <label class="form-label-auth" for="reg-password-confirm">Xác nhận mật khẩu <span style="color:#ef4444">*</span></label>
                                <div class="input-wrapper">
                                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                                    <input
                                        type="password"
                                        id="reg-password-confirm"
                                        name="password_confirm"
                                        class="form-control-auth has-toggle <?= !empty($errors['password_confirm']) ? 'is-invalid' : '' ?>"
                                        placeholder="Nhập lại mật khẩu"
                                        autocomplete="new-password"
                                        required
                                    >
                                    <button type="button" class="password-toggle" id="reg-pwd-confirm-toggle" title="Hiện mật khẩu">
                                        <i class="fas fa-eye" id="reg-pwd-confirm-icon"></i>
                                    </button>
                                </div>
                                <?php if (!empty($errors['password_confirm'])): ?>
                                    <span class="invalid-feedback-auth">
                                        <i class="fas fa-circle-exclamation"></i>
                                        <?= e($errors['password_confirm']) ?>
                                    </span>
                                <?php endif; ?>
                                <div id="pwd-match-msg" style="display:none;font-size:11px;margin-top:5px;"></div>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="terms-checkbox <?= !empty($errors['terms']) ? 'has-error' : '' ?>">
                            <input type="checkbox" id="reg-terms" name="terms" <?= !empty($oldData['terms']) ? 'checked' : '' ?> required>
                            <label for="reg-terms">
                                Tôi đã đọc và đồng ý với
                                <a href="<?= url('terms') ?>" target="_blank">Điều khoản dịch vụ</a>
                                và
                                <a href="<?= url('privacy') ?>" target="_blank">Chính sách bảo mật</a>
                                của <?= APP_NAME ?>.
                            </label>
                        </div>
                        <?php if (!empty($errors['terms'])): ?>
                            <span class="invalid-feedback-auth" style="margin-top:-12px;margin-bottom:14px">
                                <i class="fas fa-circle-exclamation"></i>
                                <?= e($errors['terms']) ?>
                            </span>
                        <?php endif; ?>

                        <!-- Submit -->
                        <button type="submit" class="btn-auth-primary" id="reg-submit-btn">
                            <span class="btn-spinner" id="reg-spinner"></span>
                            <i class="fas fa-user-plus" id="reg-btn-icon"></i>
                            <span>Tạo tài khoản ngay</span>
                        </button>

                        <!-- Social Register -->
                        <div class="auth-divider">hoặc đăng ký với</div>
                        <div class="social-login-btns">
                            <a href="<?= url('auth/google') ?>" class="btn-social google">
                                <i class="fab fa-google"></i> Google
                            </a>
                            <a href="<?= url('auth/facebook') ?>" class="btn-social facebook">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Side Panel -->
            <div class="reg-side">
                <!-- Promo Card -->
                <div class="promo-card">
                    <div class="promo-badge">🎁 QUÀ TẶNG MỚI</div>
                    <div class="promo-value">200K <small>VNĐ</small></div>
                    <p class="promo-desc">Voucher chào mừng dành riêng cho thành viên mới đăng ký hôm nay</p>
                </div>

                <!-- Benefits -->
                <div class="side-card">
                    <h6><i class="fas fa-star"></i> Quyền lợi thành viên</h6>
                    <ul class="benefit-list">
                        <li>
                            <div class="benefit-icon"><i class="fas fa-gift"></i></div>
                            <span>Voucher 200K ngay khi đăng ký thành công</span>
                        </li>
                        <li>
                            <div class="benefit-icon"><i class="fas fa-coins"></i></div>
                            <span>Tích điểm thưởng với mỗi đơn hàng, đổi quà hấp dẫn</span>
                        </li>
                        <li>
                            <div class="benefit-icon"><i class="fas fa-bolt"></i></div>
                            <span>Ưu tiên truy cập Flash Sale và deal độc quyền</span>
                        </li>
                        <li>
                            <div class="benefit-icon"><i class="fas fa-headset"></i></div>
                            <span>Hỗ trợ khách hàng VIP 24/7 qua chat & hotline</span>
                        </li>
                        <li>
                            <div class="benefit-icon"><i class="fas fa-truck"></i></div>
                            <span>Miễn phí giao hàng cho mọi đơn từ 500K</span>
                        </li>
                    </ul>
                </div>

                <!-- Trust -->
                <div class="side-card">
                    <h6><i class="fas fa-shield-alt"></i> An toàn & Bảo mật</h6>
                    <ul class="benefit-list">
                        <li>
                            <div class="benefit-icon"><i class="fas fa-lock"></i></div>
                            <span>Dữ liệu mã hoá SSL 256-bit, an toàn tuyệt đối</span>
                        </li>
                        <li>
                            <div class="benefit-icon"><i class="fas fa-user-shield"></i></div>
                            <span>Không chia sẻ thông tin với bên thứ 3</span>
                        </li>
                        <li>
                            <div class="benefit-icon"><i class="fas fa-eye-slash"></i></div>
                            <span>Quyền kiểm soát hoàn toàn dữ liệu cá nhân</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    (function () {
        'use strict';

        // ── Password toggle helper ──
        function makeToggle(inputId, toggleId, iconId) {
            var input  = document.getElementById(inputId);
            var toggle = document.getElementById(toggleId);
            var icon   = document.getElementById(iconId);
            if (!toggle) return;
            toggle.addEventListener('click', function () {
                var isHidden = input.type === 'password';
                input.type   = isHidden ? 'text' : 'password';
                icon.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
            });
        }
        makeToggle('reg-password', 'reg-pwd-toggle', 'reg-pwd-icon');
        makeToggle('reg-password-confirm', 'reg-pwd-confirm-toggle', 'reg-pwd-confirm-icon');

        // ── Password strength ──
        var pwdInput    = document.getElementById('reg-password');
        var strengthBox = document.getElementById('pwd-strength-box');
        var strengthLbl = document.getElementById('pwd-strength-label');
        var segments    = [
            document.getElementById('ps1'),
            document.getElementById('ps2'),
            document.getElementById('ps3'),
            document.getElementById('ps4')
        ];

        function calcStrength(pwd) {
            var score = 0;
            if (pwd.length >= 8)  score++;
            if (pwd.length >= 12) score++;
            if (/[A-Z]/.test(pwd) && /[a-z]/.test(pwd)) score++;
            if (/\d/.test(pwd))   score++;
            if (/[^a-zA-Z0-9]/.test(pwd)) score++;
            return Math.min(score, 4);
        }

        var strengthColors = ['#ef4444','#f97316','#eab308','#22c55e'];
        var strengthLabels = ['Rất yếu','Yếu','Trung bình','Mạnh'];

        if (pwdInput) {
            pwdInput.addEventListener('input', function () {
                var val = this.value;
                if (!val) { strengthBox.style.display = 'none'; return; }
                strengthBox.style.display = 'block';
                var score = calcStrength(val);
                var lvl = Math.max(score - 1, 0);
                segments.forEach(function (s, i) {
                    s.style.background = i < score ? strengthColors[lvl] : '#e5e7eb';
                });
                strengthLbl.textContent = 'Độ mạnh: ' + strengthLabels[lvl];
                strengthLbl.style.color = strengthColors[lvl];
            });
        }

        // ── Password match ──
        var pwdConfirm = document.getElementById('reg-password-confirm');
        var matchMsg   = document.getElementById('pwd-match-msg');

        if (pwdConfirm) {
            pwdConfirm.addEventListener('input', function () {
                var pass1 = pwdInput ? pwdInput.value : '';
                var pass2 = this.value;
                if (!pass2) { matchMsg.style.display = 'none'; return; }
                matchMsg.style.display = 'block';
                if (pass1 === pass2) {
                    matchMsg.innerHTML = '<i class="fas fa-check" style="color:#22c55e"></i> <span style="color:#22c55e">Mật khẩu khớp</span>';
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    matchMsg.innerHTML = '<i class="fas fa-xmark" style="color:#ef4444"></i> <span style="color:#ef4444">Mật khẩu không khớp</span>';
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        }

        // ── Form submit ──
        var form      = document.getElementById('register-form');
        var submitBtn = document.getElementById('reg-submit-btn');
        var spinner   = document.getElementById('reg-spinner');
        var btnIcon   = document.getElementById('reg-btn-icon');

        if (form) {
            form.addEventListener('submit', function (e) {
                var isValid = true;
                var required = ['reg-fullname','reg-username','reg-email','reg-password','reg-password-confirm'];
                required.forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el && !el.value.trim()) {
                        el.classList.add('is-invalid');
                        isValid = false;
                    }
                });

                // Password match check
                if (pwdInput && pwdConfirm && pwdInput.value !== pwdConfirm.value) {
                    pwdConfirm.classList.add('is-invalid');
                    isValid = false;
                }

                // Terms
                var terms = document.getElementById('reg-terms');
                if (terms && !terms.checked) {
                    terms.closest('.terms-checkbox').classList.add('has-error');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    var first = form.querySelector('.is-invalid, .has-error');
                    if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                submitBtn.disabled = true;
                spinner.style.display = 'block';
                btnIcon.style.display = 'none';
            });
        }

        // ── Clear validation on input ──
        document.querySelectorAll('.form-control-auth').forEach(function (el) {
            el.addEventListener('input', function () {
                this.classList.remove('is-invalid');
            });
        });

        document.getElementById('reg-terms') && document.getElementById('reg-terms').addEventListener('change', function () {
            this.closest('.terms-checkbox').classList.remove('has-error');
        });

    })();
    </script>
</body>
</html>
