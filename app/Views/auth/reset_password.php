<?php
    $valid = $valid ?? false;
?><?php
$errors = $errors ?? [];
$token = $token ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Đặt lại mật khẩu tài khoản <?= APP_NAME ?>">
    <title>Đặt lại mật khẩu - <?= APP_NAME ?></title>

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
            background: linear-gradient(135deg, #fff5f5 0%, #fef2f2 50%, #fff1f2 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow: hidden;
        }

        /* Background decorations */
        body.auth-page::before {
            content: '';
            position: fixed;
            top: -150px; right: -150px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(198,40,40,0.06) 0%, transparent 70%);
            pointer-events: none;
        }
        body.auth-page::after {
            content: '';
            position: fixed;
            bottom: -100px; left: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(198,40,40,0.04) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Floating bubbles */
        .fp-bubble {
            position: fixed;
            border-radius: 50%;
            background: rgba(198,40,40,0.04);
            animation: floatBubble linear infinite;
            pointer-events: none;
        }
        @keyframes floatBubble {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        /* ===== TOP BRAND ===== */
        .fp-brand-top {
            position: fixed;
            top: 0; left: 0; right: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 32px;
            z-index: 10;
        }

        .fp-brand {
            font-size: 1.4rem;
            font-weight: 800;
            color: #c62828;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 7px;
            letter-spacing: -0.5px;
        }
        .fp-brand span { color: #7f0000; }

        .fp-back-link {
            font-size: 13px;
            color: #6b7280;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
        }
        .fp-back-link:hover { color: #c62828; }

        /* ===== MAIN CARD ===== */
        .fp-card {
            width: 100%;
            max-width: 480px;
            background: white;
            border-radius: 24px;
            box-shadow:
                0 4px 6px rgba(0,0,0,0.04),
                0 20px 60px rgba(0,0,0,0.08),
                0 0 0 1px rgba(0,0,0,0.04);
            overflow: hidden;
            position: relative;
            z-index: 1;
            animation: scaleIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9) translateY(20px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* ===== CARD TOP ===== */
        .fp-card-top {
            background: linear-gradient(135deg, #c62828 0%, #9b1c1c 100%);
            padding: 36px 40px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .fp-card-top::before {
            content: '';
            position: absolute;
            top: -40px; left: -40px;
            width: 160px; height: 160px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .fp-icon-wrap {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            margin: 0 auto 20px;
            border: 2px solid rgba(255,255,255,0.2);
            animation: iconPulse 2.5s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255,255,255,0.2); }
            50%       { box-shadow: 0 0 0 12px rgba(255,255,255,0); }
        }

        .fp-icon-wrap i {
            font-size: 2rem;
            color: white;
        }

        .fp-card-top h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .fp-card-top p {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.72);
            margin: 0;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        /* ===== STEPS INDICATOR ===== */
        .fp-steps {
            display: flex;
            justify-content: center;
            gap: 0;
            padding: 0 40px;
            position: relative;
            margin-top: -1px;
        }

        .fp-step-item {
            flex: 1;
            text-align: center;
            padding: 16px 8px;
            position: relative;
        }

        .fp-step-item::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            right: -50%;
            height: 2px;
            background: #e5e7eb;
            z-index: 0;
        }
        .fp-step-item:last-child::before { display: none; }

        .fp-step-circle {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #f3f4f6;
            border: 2px solid #e5e7eb;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #9ca3af;
            margin: 0 auto 4px;
            position: relative;
            z-index: 1;
            transition: all 0.3s;
        }
        .fp-step-item.done .fp-step-circle {
            background: #22c55e;
            border-color: #22c55e;
            color: white;
        }
        .fp-step-item.active .fp-step-circle {
            background: #c62828;
            border-color: #c62828;
            color: white;
        }
        .fp-step-label {
            font-size: 10px;
            color: #9ca3af;
            font-weight: 500;
        }
        .fp-step-item.active .fp-step-label { color: #c62828; font-weight: 600; }
        .fp-step-item.done .fp-step-label { color: #22c55e; }

        /* ===== FORM AREA ===== */
        .fp-card-body {
            padding: 32px 40px 36px;
        }

        /* Alert */
        .alert-auth {
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 0.855rem;
            margin-bottom: 22px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            border: none;
            animation: slideDown 0.3s ease both;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .alert-auth.error {
            background: #fff5f5;
            color: #9b1c1c;
            border-left: 4px solid #ef4444;
        }

        .form-group-auth { margin-bottom: 20px; }

        .form-label-auth {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .input-wrapper { position: relative; }

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
        .input-wrapper:focus-within .input-icon { color: #c62828; }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 14px;
            cursor: pointer;
            transition: color 0.2s;
            background: none;
            border: none;
            padding: 0;
        }
        .password-toggle:hover { color: #c62828; }

        .form-control-auth {
            width: 100%;
            padding: 13px 44px 13px 44px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            background: #fafafa;
            color: #1f2937;
            outline: none;
            transition: all 0.2s;
        }
        .form-control-auth:focus {
            border-color: #c62828;
            background: white;
            box-shadow: 0 0 0 3px rgba(198,40,40,0.09);
        }
        .form-control-auth.is-invalid {
            border-color: #ef4444;
            background: #fff8f8;
        }

        .invalid-feedback-auth {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.775rem;
            color: #ef4444;
            margin-top: 6px;
        }

        /* Password strength meter */
        .strength-meter {
            height: 4px;
            background-color: #e5e7eb;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
            display: flex;
        }
        .strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
            border-radius: 2px;
        }
        .strength-text {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
            display: block;
        }

        /* Submit button */
        .btn-auth-primary {
            width: 100%;
            padding: 14px 24px;
            font-size: 0.9375rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e53935 0%, #c62828 100%);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.3px;
        }
        .btn-auth-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(198,40,40,0.35);
        }
        .btn-auth-primary:disabled { opacity: 0.7; cursor: not-allowed; }

        .btn-spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .fp-footer {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
        }
        .fp-footer a {
            font-size: 0.8125rem;
            color: #9ca3af;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
        }
        .fp-footer a:hover { color: #c62828; }

        @media (max-width: 575.98px) {
            body.auth-page { padding: 80px 12px 24px; }
            .fp-card-top { padding: 28px 24px 32px; }
            .fp-card-body { padding: 24px 24px 28px; }
            .fp-steps { padding: 0 24px; }
            .fp-brand-top { padding: 12px 16px; }
        }
    </style>
</head>
<body class="auth-page">

    <!-- Floating bubbles -->
    <div class="fp-bubble" style="width:60px;height:60px;left:10%;animation-duration:8s;"></div>
    <div class="fp-bubble" style="width:40px;height:40px;left:30%;animation-duration:12s;animation-delay:3s;"></div>
    <div class="fp-bubble" style="width:80px;height:80px;left:60%;animation-duration:10s;animation-delay:1s;"></div>

    <!-- Top brand bar -->
    <div class="fp-brand-top">
        <a href="<?= url() ?>" class="fp-brand">
            <i class="fas fa-mobile-alt"></i>Vũ Huân <span>Shop</span>
        </a>
    </div>

    <!-- Main Card -->
    <div class="fp-card">

        <!-- Card Top -->
        <div class="fp-card-top">
            <div class="fp-icon-wrap">
                <i class="fas fa-shield-halved"></i>
            </div>
            <h1>Đặt lại mật khẩu</h1>
            <p>Vui lòng nhập mật khẩu mới và xác nhận mật khẩu để khôi phục tài khoản của bạn.</p>
        </div>

        <!-- Steps -->
        <div class="fp-steps">
            <div class="fp-step-item done">
                <div class="fp-step-circle"><i class="fas fa-check" style="font-size:11px"></i></div>
                <div class="fp-step-label">Nhập email</div>
            </div>
            <div class="fp-step-item done">
                <div class="fp-step-circle"><i class="fas fa-check" style="font-size:11px"></i></div>
                <div class="fp-step-label">Kiểm tra hộp thư</div>
            </div>
            <div class="fp-step-item active">
                <div class="fp-step-circle">3</div>
                <div class="fp-step-label">Đặt lại mật khẩu</div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="fp-card-body">

            <?php $flash = getFlash(); if (!empty($flash) && $flash['type'] === 'error'): ?>
                <div class="alert-auth error">
                    <i class="fas fa-circle-exclamation alert-auth-icon"></i>
                    <div class="alert-auth-body">
                        <strong>Lỗi đặt lại mật khẩu</strong>
                        <?= e($flash['message']) ?>
                    </div>
                </div>
            <?php endif; ?>

            <form id="reset-form" action="<?= url('auth/reset-password') ?>" method="POST" novalidate>
                <?= csrfToken() ?>
                <input type="hidden" name="token" value="<?= e($token ?? '') ?>">

                <!-- Password Input -->
                <div class="form-group-auth">
                    <label class="form-label-auth" for="password">Mật khẩu mới <span style="color:#ef4444">*</span></label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control-auth"
                            placeholder="Tối thiểu 6 ký tự"
                            required
                            autofocus
                        >
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="strength-meter">
                        <div id="strength-bar" class="strength-bar"></div>
                    </div>
                    <span id="strength-text" class="strength-text">Mức độ bảo mật</span>
                </div>

                <!-- Confirm Password Input -->
                <div class="form-group-auth">
                    <label class="form-label-auth" for="confirm_password">Xác nhận mật khẩu mới <span style="color:#ef4444">*</span></label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            class="form-control-auth"
                            placeholder="Nhập lại mật khẩu mới"
                            required
                        >
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirm_password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <span id="confirm-feedback" class="invalid-feedback-auth d-none">
                        <i class="fas fa-circle-exclamation"></i>
                        Xác nhận mật khẩu không khớp.
                    </span>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-auth-primary" id="reset-submit-btn">
                    <span class="btn-spinner" id="reset-spinner"></span>
                    <i class="fas fa-check-double" id="reset-btn-icon"></i>
                    <span>Cập nhật mật khẩu mới</span>
                </button>
            </form>

            <!-- Footer Links -->
            <div class="fp-footer">
                <a href="<?= url('auth/login') ?>">
                    <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
                </a>
                <a href="<?= url() ?>">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function togglePasswordVisibility(fieldId, toggleBtn) {
        var input = document.getElementById(fieldId);
        var icon = toggleBtn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }

    (function () {
        'use strict';

        var form = document.getElementById('reset-form');
        var passInput = document.getElementById('password');
        var confirmInput = document.getElementById('confirm_password');
        var strengthBar = document.getElementById('strength-bar');
        var strengthText = document.getElementById('strength-text');
        var confirmFeedback = document.getElementById('confirm-feedback');

        // Check password strength
        passInput.addEventListener('input', function() {
            var val = this.value;
            var score = 0;
            
            if (val.length >= 6) score++;
            if (val.length >= 10) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            var colors = ['#e53935', '#ff9800', '#ffc107', '#4caf50', '#2e7d32'];
            var texts = ['Rất yếu (Tối thiểu 6 ký tự)', 'Yếu', 'Trung bình', 'Mạnh', 'Rất mạnh'];
            
            if(val.length === 0) {
                strengthBar.style.width = '0%';
                strengthText.textContent = 'Mức độ bảo mật';
            } else {
                var pct = (score / 5) * 100;
                strengthBar.style.width = pct + '%';
                strengthBar.style.backgroundColor = colors[score - 1] || colors[0];
                strengthText.textContent = 'Mức độ bảo mật: ' + (texts[score - 1] || texts[0]);
            }
        });

        // Validate on submit
        form.addEventListener('submit', function(e) {
            var hasErr = false;
            
            if (passInput.value.length < 6) {
                passInput.classList.add('is-invalid');
                hasErr = true;
            } else {
                passInput.classList.remove('is-invalid');
            }

            if (confirmInput.value !== passInput.value) {
                confirmInput.classList.add('is-invalid');
                confirmFeedback.classList.remove('d-none');
                hasErr = true;
            } else {
                confirmInput.classList.remove('is-invalid');
                confirmFeedback.classList.add('d-none');
            }

            if (hasErr) {
                e.preventDefault();
                return;
            }

            var submitBtn = document.getElementById('reset-submit-btn');
            var spinner = document.getElementById('reset-spinner');
            var btnIcon = document.getElementById('reset-btn-icon');

            if (submitBtn) {
                submitBtn.disabled = true;
                if (spinner) spinner.style.display = 'block';
                if (btnIcon) btnIcon.style.display = 'none';
            }
        });

        // Clear validation
        passInput.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
        confirmInput.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            confirmFeedback.classList.add('d-none');
        });

        // Auto dismiss errors
        setTimeout(function () {
            document.querySelectorAll('.alert-auth').forEach(function (el) {
                el.style.transition = 'opacity 0.5s, transform 0.5s';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-8px)';
                setTimeout(function () { el.remove(); }, 500);
            });
        }, 5000);
    })();
    </script>
</body>
</html>
