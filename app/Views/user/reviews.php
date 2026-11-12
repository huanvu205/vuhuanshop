<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
/* Layout styling */
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
.profile-hero h1 { color: #fff; font-weight: 700; margin: 0; }
.profile-hero p { color: rgba(255,255,255,0.8); margin: 0; }

.profile-main-wrap { margin-top: -35px; position: relative; z-index: 2; }

/* Sidebar */
.profile-sidebar {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    position: sticky;
    top: 90px;
}
.profile-sidebar .user-info {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    padding: 30px 24px;
    text-align: center;
}
.profile-sidebar .avatar-wrap {
    position: relative;
    width: 90px;
    height: 90px;
    margin: 0 auto 14px;
}
.profile-sidebar .avatar-wrap img {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,0.2);
}
.profile-sidebar .avatar-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 22px;
    height: 22px;
    background: #4CAF50;
    border-radius: 50%;
    border: 2px solid #16213e;
}
.profile-sidebar .user-name {
    color: #fff;
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 4px;
}
.profile-sidebar .user-email {
    color: rgba(255,255,255,0.55);
    font-size: 0.78rem;
}
.profile-sidebar .nav-menu { padding: 12px 0; }
.profile-sidebar .nav-item a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 24px;
    color: #555;
    font-size: 0.92rem;
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

/* Cards */
.profile-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 30px rgba(0,0,0,0.07);
    overflow: hidden;
    margin-bottom: 24px;
}
.profile-card .card-header-custom {
    padding: 22px 28px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 12px;
}
.profile-card .card-header-custom .icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    background: linear-gradient(135deg, var(--primary), #ff6f00);
    color: #fff;
}
.profile-card .card-header-custom h5 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    color: #1a1a2e;
}
.profile-card .card-header-custom p {
    margin: 0;
    font-size: 0.8rem;
    color: #999;
}
.profile-card .card-body-custom { padding: 28px; }

/* Breadcrumb */
.breadcrumb-wrap {
    padding: 14px 0;
    background: #f8f8f8;
    border-bottom: 1px solid #efefef;
}
.breadcrumb-wrap .breadcrumb { margin: 0; font-size: 0.85rem; }
.breadcrumb-item a { color: var(--primary); text-decoration: none; }
.breadcrumb-item.active { color: #888; }

/* Review item */
.review-item-card {
    border: 1px solid #eee;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 18px;
    transition: all 0.2s;
}
.review-item-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border-color: #ddd;
}
.review-product-info {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px dashed #eee;
    align-items: center;
}
.review-product-img {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #eee;
}
.review-product-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: #1a1a2e;
    text-decoration: none;
    transition: color 0.2s;
}
.review-product-name:hover { color: var(--primary); }

.review-stars { color: #ffc107; font-size: 0.9rem; margin-bottom: 6px; }
.review-date { font-size: 0.78rem; color: #999; margin-left: 10px; }
.review-title { font-weight: 700; color: #1a1a2e; font-size: 0.95rem; margin-bottom: 8px; }
.review-comment { font-size: 0.9rem; color: #555; line-height: 1.5; margin-bottom: 0; }

.badge-review-status {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 12px;
}
.badge-status-approved { background-color: #d1e7dd; color: #0f5132; }
.badge-status-pending { background-color: #fff3cd; color: #856404; }
.badge-status-rejected { background-color: #f8d7da; color: #842029; }
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>"><i class="fas fa-home me-1"></i>Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= url('user/profile') ?>">Tài khoản</a></li>
                <li class="breadcrumb-item active">Đánh giá của tôi</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Profile Hero -->
<div class="profile-hero">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <div>
                <h1 class="h4 mb-1"><i class="fas fa-star me-2"></i>Đánh giá của tôi</h1>
                <p>Xem và quản lý các đánh giá sản phẩm bạn đã đăng tải</p>
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
                        <?php if (!empty($_SESSION['user']['avatar'])): ?>
                            <img src="<?= productImage($_SESSION['user']['avatar']) ?>" alt="Avatar">
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user']['full_name'] ?? 'U') ?>&background=E53935&color=fff&size=90" alt="Avatar">
                        <?php endif; ?>
                        <div class="avatar-badge"></div>
                    </div>
                    <div class="user-name"><?= e($_SESSION['user']['full_name'] ?? 'Người dùng') ?></div>
                    <div class="user-email"><?= e($_SESSION['user']['email'] ?? '') ?></div>
                </div>

                <!-- Navigation -->
                <nav class="nav-menu">
                    <div class="nav-item">
                        <a href="<?= url('user/profile') ?>">
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
                        <a href="<?= url('user/reviews') ?>" class="active">
                            <i class="fas fa-star"></i> Đánh giá của tôi
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="<?= url('user/profile#change-password') ?>">
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
            <div class="profile-card">
                <div class="card-header-custom">
                    <div class="icon" style="background:linear-gradient(135deg,#ff9800,#f57c00);"><i class="fas fa-star"></i></div>
                    <div>
                        <h5>Lịch sử đánh giá</h5>
                        <p>Các ý kiến đóng góp và nhận xét của bạn về chất lượng sản phẩm</p>
                    </div>
                </div>

                <div class="card-body-custom">
                    <?php if (empty($reviews)): ?>
                        <div class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/1486/1486433.png" alt="No reviews" style="width: 80px; opacity: 0.3; margin-bottom: 20px;">
                            <h6 style="color:#555; font-weight:600;">Bạn chưa viết đánh giá nào.</h6>
                            <p class="text-muted small">Hãy mua hàng và đánh giá sản phẩm để chia sẻ trải nghiệm với cộng đồng.</p>
                            <a href="<?= url('orders') ?>" class="btn btn-danger btn-sm px-4 mt-2" style="border-radius:8px; background:var(--primary);">Xem lịch sử mua hàng</a>
                        </div>
                    <?php else: ?>
                        <div class="reviews-list">
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item-card">
                                    <!-- Product Info -->
                                    <div class="review-product-info">
                                        <img src="<?= productImage($review['main_image']) ?>" class="review-product-img" alt="Product" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/images/no-image.png'">
                                        <div class="flex-grow-1">
                                            <a href="<?= url('products/' . $review['product_slug']) ?>" class="review-product-name">
                                                <?= e($review['product_name']) ?>
                                            </a>
                                        </div>
                                        <div>
                                            <span class="badge-review-status badge-status-<?= $review['status'] ?>">
                                                <?php 
                                                if ($review['status'] === 'approved') echo 'Đã duyệt';
                                                elseif ($review['status'] === 'pending') echo 'Chờ duyệt';
                                                else echo 'Từ chối';
                                                ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Rating details -->
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="review-stars">
                                            <?= renderStars($review['rating']) ?>
                                        </div>
                                        <span class="review-date">
                                            <i class="far fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?>
                                        </span>
                                    </div>

                                    <!-- Content -->
                                    <?php if (!empty($review['title'])): ?>
                                        <div class="review-title"><?= e($review['title']) ?></div>
                                    <?php endif; ?>
                                    <p class="review-comment"><?= nl2br(e($review['comment'])) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
