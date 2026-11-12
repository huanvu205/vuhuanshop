<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
/* ===== PROFILE PAGE STYLES ===== */
.profile-hero {
    background: linear-gradient(135deg, var(--primary) 0%, #b71c1c 100%);
    padding: 35px 0 65px;
    border-radius: 0 0 24px 24px;
    box-shadow: 0 4px 20px rgba(229,57,53,0.12);
    position: relative;
    overflow: hidden;
}
.profile-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
}
.profile-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: 5%;
    width: 250px;
    height: 250px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
}
.profile-hero .container { position: relative; z-index: 1; }
.profile-hero h1 { color: #fff; font-weight: 700; margin: 0; }
.profile-hero p { color: rgba(255,255,255,0.8); margin: 0; }

.profile-main-wrap { margin-top: -35px; }

/* Sidebar */
.profile-sidebar {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.04);
    border: 1px solid #f1f5f9;
    overflow: hidden;
    position: sticky;
    top: 90px;
}
.profile-sidebar .user-info {
    background: #fff;
    padding: 30px 24px 20px;
    text-align: center;
    border-bottom: 1px solid #f1f5f9;
}
.profile-sidebar .avatar-wrap {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto 12px;
}
.profile-sidebar .avatar-wrap img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(229,57,53,0.15);
}
.profile-sidebar .avatar-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 18px;
    height: 18px;
    background: #4CAF50;
    border-radius: 50%;
    border: 2px solid #fff;
}
.profile-sidebar .user-name {
    color: var(--text-dark);
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 4px;
}
.profile-sidebar .user-email {
    color: var(--text-light);
    font-size: 0.8rem;
}
.profile-sidebar .nav-menu { padding: 12px 0; }
.profile-sidebar .nav-item a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 24px;
    color: #475569;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
    border-left: 3px solid transparent;
}
.profile-sidebar .nav-item a:hover {
    background: #fff5f5;
    color: var(--primary);
    border-left-color: var(--primary);
}
.profile-sidebar .nav-item a.active {
    background: #fff5f5;
    color: var(--primary);
    border-left-color: var(--primary);
    font-weight: 600;
}
.profile-sidebar .nav-item a i {
    width: 20px;
    text-align: center;
    font-size: 1rem;
}
.profile-sidebar .nav-item a.text-danger:hover { background: #fff5f5; }

/* Cards */
.profile-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.04);
    border: 1px solid #f1f5f9;
    overflow: hidden;
    margin-bottom: 24px;
}
.profile-card .card-header-custom {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 12px;
}
.profile-card .card-header-custom .icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    background: linear-gradient(135deg, var(--primary) 0%, #ff6f00 100%);
    color: #fff;
}
.profile-card .card-header-custom h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-dark);
}
.profile-card .card-header-custom p {
    margin: 0;
    font-size: 0.8rem;
    color: #64748b;
}
.profile-card .card-body-custom { padding: 24px; }

/* Avatar Upload */
.avatar-upload-section {
    display: flex;
    align-items: center;
    gap: 24px;
    padding: 20px 28px;
    background: #fafafa;
    border-bottom: 1px solid #f0f0f0;
}
.current-avatar {
    position: relative;
    width: 90px;
    height: 90px;
    flex-shrink: 0;
}
.current-avatar img {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #eee;
}
.avatar-info h6 { font-weight: 600; margin-bottom: 4px; font-size: 0.95rem; }
.avatar-info p { color: #888; font-size: 0.8rem; margin-bottom: 10px; }
.btn-upload {
    background: linear-gradient(135deg, var(--primary), #c62828);
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-upload:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(229,57,53,0.3); }

/* Form Styles */
.form-label { font-weight: 600; font-size: 0.87rem; color: #444; margin-bottom: 6px; }
.form-control-custom {
    border: 1.5px solid #e8e8e8;
    border-radius: 10px;
    padding: 11px 15px;
    font-size: 0.92rem;
    transition: all 0.2s;
    background: #fafafa;
    width: 100%;
}
.form-control-custom:focus {
    outline: none;
    border-color: var(--primary);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(229,57,53,0.1);
}
.form-control-custom.is-invalid { border-color: #dc3545; }
.form-group { margin-bottom: 20px; }
.input-icon-wrap { position: relative; }
.input-icon-wrap .form-control-custom { padding-left: 40px; }
.input-icon-wrap .input-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #bbb;
    font-size: 0.95rem;
    pointer-events: none;
}

/* Password strength */
.password-strength { margin-top: 6px; }
.strength-bar {
    height: 4px;
    border-radius: 2px;
    background: #eee;
    margin-bottom: 4px;
    overflow: hidden;
}
.strength-bar-fill {
    height: 100%;
    border-radius: 2px;
    width: 0;
    transition: all 0.4s;
}
.strength-label { font-size: 0.75rem; color: #999; }

/* Save Button */
.btn-save {
    background: linear-gradient(135deg, var(--primary) 0%, #c62828 100%);
    color: #fff;
    border: none;
    padding: 12px 32px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.25s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(229,57,53,0.35); color: #fff; }

/* Stats */
.profile-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #f5f5f5;
}
.stat-card .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    margin: 0 auto 10px;
}
.stat-card .stat-value { font-size: 1.5rem; font-weight: 800; color: #1a1a2e; line-height: 1; margin-bottom: 4px; }
.stat-card .stat-label { font-size: 0.78rem; color: #999; }

/* Breadcrumb */
.breadcrumb-wrap {
    padding: 14px 0;
    background: #f8f8f8;
    border-bottom: 1px solid #efefef;
}
.breadcrumb-wrap .breadcrumb { margin: 0; font-size: 0.85rem; }
.breadcrumb-item a { color: var(--primary); text-decoration: none; }
.breadcrumb-item.active { color: #888; }
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>"><i class="fas fa-home me-1"></i>Trang chủ</a></li>
                <li class="breadcrumb-item active">Hồ sơ cá nhân</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Profile Hero -->
<div class="profile-hero">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <div>
                <h1 class="h4 mb-1"><i class="fas fa-user-circle me-2"></i>Tài khoản của tôi</h1>
                <p>Quản lý thông tin cá nhân và bảo mật tài khoản</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container profile-main-wrap pb-5">
    <div class="row g-4">

        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="profile-sidebar">
                <!-- User Info -->
                <div class="user-info">
                    <div class="avatar-wrap">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="<?= productImage($user['avatar']) ?>" alt="Avatar">
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['full_name'] ?? 'U') ?>&background=E53935&color=fff&size=90" alt="Avatar">
                        <?php endif; ?>
                        <div class="avatar-badge"></div>
                    </div>
                    <div class="user-name"><?= e($user['full_name'] ?? 'Người dùng') ?></div>
                    <div class="user-email"><?= e($user['email'] ?? '') ?></div>
                </div>

                <!-- Navigation -->
                <nav class="nav-menu">
                    <div class="nav-item">
                        <a href="<?= url('user/profile') ?>" class="active">
                            <i class="fas fa-user"></i> Hồ sơ cá nhân
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="<?= url('orders') ?>">
                            <i class="fas fa-box"></i> Đơn hàng của tôi
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="<?= url('wishlist') ?>">
                            <i class="fas fa-heart"></i> Sản phẩm yêu thích
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="<?= url('user/reviews') ?>">
                            <i class="fas fa-star"></i> Đánh giá của tôi
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#change-password" onclick="document.getElementById('change-password').scrollIntoView({behavior:'smooth'}); return false;">
                            <i class="fas fa-lock"></i> Đổi mật khẩu
                        </a>
                    </div>
                    <hr class="my-2 mx-4">
                    <div class="nav-item">
                        <a href="<?= url('auth/logout') ?>" class="text-danger">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Content -->
        <div class="col-lg-9">

            <!-- Stats -->
            <div class="profile-stats">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#E53935,#ff8f00);">
                        <i class="fas fa-box" style="color:#fff;"></i>
                    </div>
                    <div class="stat-value"><?= $user['order_count'] ?? 0 ?></div>
                    <div class="stat-label">Đơn hàng</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#e91e63,#c2185b);">
                        <i class="fas fa-heart" style="color:#fff;"></i>
                    </div>
                    <div class="stat-value"><?= $user['wishlist_count'] ?? 0 ?></div>
                    <div class="stat-label">Yêu thích</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#ff9800,#f57c00);">
                        <i class="fas fa-star" style="color:#fff;"></i>
                    </div>
                    <div class="stat-value"><?= $user['review_count'] ?? 0 ?></div>
                    <div class="stat-label">Đánh giá</div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="profile-card">
                <div class="card-header-custom">
                    <div class="icon"><i class="fas fa-user-edit"></i></div>
                    <div>
                        <h5>Thông tin cá nhân</h5>
                        <p>Cập nhật ảnh đại diện và thông tin của bạn</p>
                    </div>
                </div>

                <form action="<?= url('user/profile/update') ?>" method="POST" enctype="multipart/form-data" id="profileForm">
                    <?= csrfToken() ?>

                    <!-- Avatar Upload -->
                    <div class="avatar-upload-section">
                        <div class="current-avatar">
                            <img src="<?= !empty($user['avatar']) ? productImage($user['avatar']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['full_name'] ?? 'U') . '&background=E53935&color=fff&size=90' ?>"
                                 alt="Avatar" id="avatarPreview">
                        </div>
                        <div class="avatar-info">
                            <h6>Ảnh đại diện</h6>
                            <p>Hỗ trợ JPG, PNG, GIF. Kích thước tối đa 2MB</p>
                            <label class="btn-upload" for="avatarInput">
                                <i class="fas fa-camera"></i> Thay đổi ảnh
                            </label>
                            <input type="file" id="avatarInput" name="avatar" accept="image/*" class="d-none">
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="full_name">
                                        <i class="fas fa-user me-1 text-muted"></i> Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap">
                                        <i class="input-icon fas fa-user"></i>
                                        <input type="text" id="full_name" name="full_name"
                                               class="form-control-custom"
                                               value="<?= e($user['full_name'] ?? '') ?>"
                                               placeholder="Nhập họ và tên đầy đủ" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="email">
                                        <i class="fas fa-envelope me-1 text-muted"></i> Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap">
                                        <i class="input-icon fas fa-envelope"></i>
                                        <input type="email" id="email" name="email"
                                               class="form-control-custom"
                                               value="<?= e($user['email'] ?? '') ?>"
                                               placeholder="Email của bạn" required>
                                    </div>
                                    <small class="text-muted" style="font-size:0.75rem;">Email dùng để đăng nhập, có thể thay đổi</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="phone">
                                        <i class="fas fa-phone me-1 text-muted"></i> Số điện thoại
                                    </label>
                                    <div class="input-icon-wrap">
                                        <i class="input-icon fas fa-phone"></i>
                                        <input type="tel" id="phone" name="phone"
                                               class="form-control-custom"
                                               value="<?= e($user['phone'] ?? '') ?>"
                                               placeholder="Ví dụ: 0901234567">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="city">
                                        <i class="fas fa-city me-1 text-muted"></i> Tỉnh / Thành phố
                                    </label>
                                    <div class="input-icon-wrap">
                                        <i class="input-icon fas fa-map-marker-alt"></i>
                                        <select id="city" name="city" class="form-control-custom">
                                            <option value="">-- Chọn tỉnh/thành --</option>
                                            <?php
                                            $cities = ['Hà Nội','TP. Hồ Chí Minh','Đà Nẵng','Hải Phòng','Cần Thơ',
                                                       'An Giang','Bà Rịa – Vũng Tàu','Bắc Giang','Bắc Kạn','Bạc Liêu',
                                                       'Bắc Ninh','Bến Tre','Bình Định','Bình Dương','Bình Phước',
                                                       'Bình Thuận','Cà Mau','Cao Bằng','Đắk Lắk','Đắk Nông',
                                                       'Điện Biên','Đồng Nai','Đồng Tháp','Gia Lai','Hà Giang',
                                                       'Hà Nam','Hà Tĩnh','Hải Dương','Hậu Giang','Hòa Bình',
                                                       'Hưng Yên','Khánh Hòa','Kiên Giang','Kon Tum','Lai Châu',
                                                       'Lâm Đồng','Lạng Sơn','Lào Cai','Long An','Nam Định',
                                                       'Nghệ An','Ninh Bình','Ninh Thuận','Phú Thọ','Phú Yên',
                                                       'Quảng Bình','Quảng Nam','Quảng Ngãi','Quảng Ninh','Quảng Trị',
                                                       'Sóc Trăng','Sơn La','Tây Ninh','Thái Bình','Thái Nguyên',
                                                       'Thanh Hóa','Thừa Thiên Huế','Tiền Giang','Trà Vinh','Tuyên Quang',
                                                       'Vĩnh Long','Vĩnh Phúc','Yên Bái'];
                                            foreach ($cities as $c):
                                                $sel = ($user['city'] ?? '') === $c ? 'selected' : '';
                                            ?>
                                                <option value="<?= e($c) ?>" <?= $sel ?>><?= e($c) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="address">
                                        <i class="fas fa-home me-1 text-muted"></i> Địa chỉ chi tiết
                                    </label>
                                    <div class="input-icon-wrap">
                                        <i class="input-icon fas fa-map-pin"></i>
                                        <input type="text" id="address" name="address"
                                               class="form-control-custom"
                                               value="<?= e($user['address'] ?? '') ?>"
                                               placeholder="Số nhà, tên đường, phường/xã...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="profile-card" id="change-password">
                <div class="card-header-custom">
                    <div class="icon" style="background:linear-gradient(135deg,#1a1a2e,#16213e);">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h5>Đổi mật khẩu</h5>
                        <p>Để bảo mật tài khoản, hãy sử dụng mật khẩu mạnh</p>
                    </div>
                </div>
                <div class="card-body-custom">
                    <form action="<?= url('user/profile/password') ?>" method="POST" id="passwordForm">
                        <?= csrfToken() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="current_password">
                                        <i class="fas fa-key me-1 text-muted"></i> Mật khẩu hiện tại <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap" style="position:relative;">
                                        <i class="input-icon fas fa-lock"></i>
                                        <input type="password" id="current_password" name="current_password"
                                               class="form-control-custom"
                                               placeholder="Nhập mật khẩu hiện tại" required
                                               style="padding-right:44px;">
                                        <button type="button" class="toggle-pw" onclick="togglePassword('current_password',this)"
                                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#bbb;cursor:pointer;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="new_password">
                                        <i class="fas fa-key me-1 text-muted"></i> Mật khẩu mới <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap" style="position:relative;">
                                        <i class="input-icon fas fa-lock"></i>
                                        <input type="password" id="new_password" name="new_password"
                                               class="form-control-custom"
                                               placeholder="Tối thiểu 8 ký tự" required
                                               style="padding-right:44px;"
                                               oninput="checkStrength(this.value)">
                                        <button type="button" class="toggle-pw" onclick="togglePassword('new_password',this)"
                                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#bbb;cursor:pointer;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength" id="strengthWrap" style="display:none;">
                                        <div class="strength-bar"><div class="strength-bar-fill" id="strengthFill"></div></div>
                                        <span class="strength-label" id="strengthLabel"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="confirm_password">
                                        <i class="fas fa-check-circle me-1 text-muted"></i> Xác nhận mật khẩu <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap" style="position:relative;">
                                        <i class="input-icon fas fa-lock"></i>
                                        <input type="password" id="confirm_password" name="confirm_password"
                                               class="form-control-custom"
                                               placeholder="Nhập lại mật khẩu mới" required
                                               style="padding-right:44px;">
                                        <button type="button" class="toggle-pw" onclick="togglePassword('confirm_password',this)"
                                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#bbb;cursor:pointer;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, số và ký tự đặc biệt
                            </small>
                            <button type="submit" class="btn-save" style="background:linear-gradient(135deg,#1a1a2e,#0f3460);">
                                <i class="fas fa-shield-alt"></i> Đổi mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Account (Quyền được lãng quên / Xóa dữ liệu) -->
            <div class="profile-card mt-4" style="border: 1px solid #fee2e2;">
                <div class="card-header-custom" style="border-bottom: 1px solid #fee2e2;">
                    <div class="icon" style="background:#fee2e2; color:#dc2626;"><i class="fas fa-user-slash"></i></div>
                    <div>
                        <h5 class="text-danger mb-0">Xóa tài khoản</h5>
                        <small class="text-muted">Quyền được xóa dữ liệu cá nhân theo quy định pháp luật (Nghị định 13/2023/NĐ-CP)</small>
                    </div>
                </div>
                <div class="card-body-custom p-4">
                    <p class="text-muted small mb-3">
                        Khi bạn xóa tài khoản, tất cả dữ liệu cá nhân (họ tên, số điện thoại, địa chỉ) sẽ bị xóa vĩnh viễn hoặc ẩn danh theo quy định bảo vệ dữ liệu người dùng. Hành động này không thể hoàn tác.
                    </p>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="showDeleteAccountModal()">
                        <i class="fas fa-trash-alt me-1"></i> Yêu cầu xóa tài khoản của tôi
                    </button>
                </div>
            </div>

            <!-- Modal Xác nhận xóa tài khoản -->
            <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
                    <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Xác nhận xóa tài khoản</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="<?= url('user/delete-account') ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn 100% muốn xóa tài khoản không? Hành động này không thể khôi phục!')">
                            <?= csrfToken() ?>
                            <div class="modal-body py-3">
                                <p class="text-muted small">Để bảo mật, vui lòng nhập mật khẩu tài khoản của bạn để xác nhận yêu cầu xóa vĩnh viễn tài khoản:</p>
                                <div class="form-group mb-2">
                                    <input type="password" name="confirm_delete_password" class="form-control" placeholder="Nhập mật khẩu hiện tại của bạn" required style="border-radius: 10px; padding: 10px 14px;">
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 8px;">Hủy bỏ</button>
                                <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 8px;">Xác nhận xóa vĩnh viễn</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div><!-- /col -->
    </div>
</div>

<script>
// Avatar preview
document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) {
        alert('Ảnh quá lớn! Vui lòng chọn ảnh dưới 2MB.');
        return;
    }
    const reader = new FileReader();
    reader.onload = function(ev) {
        document.getElementById('avatarPreview').src = ev.target.result;
    };
    reader.readAsDataURL(file);
});

// Toggle password visibility
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// Password strength
function checkStrength(pw) {
    const wrap = document.getElementById('strengthWrap');
    const fill = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');

    if (!pw) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'block';

    let score = 0;
    if (pw.length >= 8) score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;

    const levels = [
        { pct: '25%', color: '#f44336', text: 'Rất yếu' },
        { pct: '50%', color: '#ff9800', text: 'Yếu' },
        { pct: '75%', color: '#2196F3', text: 'Trung bình' },
        { pct: '100%', color: '#4CAF50', text: 'Mạnh' }
    ];
    const lv = levels[Math.max(score - 1, 0)];
    fill.style.width = lv.pct;
    fill.style.background = lv.color;
    label.textContent = '💪 Độ mạnh: ' + lv.text;
    label.style.color = lv.color;
}

// Form validation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const np = document.getElementById('new_password').value;
    const cp = document.getElementById('confirm_password').value;
    if (np !== cp) {
        e.preventDefault();
        alert('Mật khẩu xác nhận không khớp!');
        document.getElementById('confirm_password').style.borderColor = '#dc3545';
    }
});

function showDeleteAccountModal() {
    var myModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
    myModal.show();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
