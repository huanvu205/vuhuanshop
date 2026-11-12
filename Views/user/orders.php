<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
/* Sidebar and layout reuse */
.profile-hero {
    background: linear-gradient(135deg, var(--primary) 0%, #b71c1c 100%);
    padding: 40px 0 80px;
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

.profile-main-wrap { margin-top: -50px; }

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

/* Order list styles */
.order-filter-nav {
    display: flex;
    border-bottom: 2px solid #f0f0f0;
    margin-bottom: 24px;
    overflow-x: auto;
    white-space: nowrap;
}
.order-filter-nav::-webkit-scrollbar { display: none; }
.order-filter-btn {
    padding: 12px 20px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #666;
    text-decoration: none;
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
    cursor: pointer;
}
.order-filter-btn:hover { color: var(--primary); }
.order-filter-btn.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.order-item-card {
    border: 1px solid #eee;
    border-radius: 12px;
    margin-bottom: 18px;
    transition: all 0.2s;
    overflow: hidden;
}
.order-item-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border-color: #ddd;
}
.order-card-header {
    background-color: #fafafa;
    padding: 14px 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.order-code { font-weight: 700; color: #1a1a2e; }
.order-date { font-size: 0.82rem; color: #888; }
.order-card-body { padding: 20px; }
.order-card-footer {
    padding: 14px 20px;
    border-top: 1px solid #eee;
    background-color: #fafafa;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.order-total-price {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--primary);
}

/* Status Labels */
.badge-status {
    font-size: 0.76rem;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 20px;
    text-transform: capitalize;
}
.status-pending { background-color: #fff3cd; color: #856404; }
.status-confirmed { background-color: #cfe2ff; color: #084298; }
.status-processing { background-color: #e0cffc; color: #6f42c1; }
.status-shipping { background-color: #cff4fc; color: #055160; }
.status-delivered { background-color: #d1e7dd; color: #0f5132; }
.status-cancelled { background-color: #f8d7da; color: #842029; }
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>"><i class="fas fa-home me-1"></i>Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= url('user/profile') ?>">Tài khoản</a></li>
                <li class="breadcrumb-item active">Đơn hàng của tôi</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Profile Hero -->
<div class="profile-hero">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <div>
                <h1 class="h4 mb-1"><i class="fas fa-box me-2"></i>Lịch sử mua hàng</h1>
                <p>Theo dõi tình trạng vận chuyển và xem lại các đơn hàng đã đặt</p>
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
                        <a href="<?= url('orders') ?>" class="active">
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
                    <div class="icon"><i class="fas fa-receipt"></i></div>
                    <div>
                        <h5>Danh sách đơn hàng</h5>
                        <p>Thông tin chi tiết và tiến trình xử lý đơn hàng của bạn</p>
                    </div>
                </div>

                <div class="card-body-custom">
                    <!-- Filters -->
                    <div class="order-filter-nav" id="orderFilterNav">
                        <a href="?status=" class="order-filter-btn <?= empty($currentStatus) ? 'active' : '' ?>">Tất cả</a>
                        <a href="?status=pending" class="order-filter-btn <?= ($currentStatus ?? '') === 'pending' ? 'active' : '' ?>">Chờ xử lý</a>
                        <a href="?status=confirmed" class="order-filter-btn <?= ($currentStatus ?? '') === 'confirmed' ? 'active' : '' ?>">Đã xác nhận</a>
                        <a href="?status=shipping" class="order-filter-btn <?= ($currentStatus ?? '') === 'shipping' ? 'active' : '' ?>">Đang giao</a>
                        <a href="?status=delivered" class="order-filter-btn <?= ($currentStatus ?? '') === 'delivered' ? 'active' : '' ?>">Đã giao</a>
                        <a href="?status=cancelled" class="order-filter-btn <?= ($currentStatus ?? '') === 'cancelled' ? 'active' : '' ?>">Đã hủy</a>
                    </div>

                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/107/107831.png" alt="No orders" style="width: 80px; opacity: 0.3; margin-bottom: 20px;">
                            <h6 style="color:#555; font-weight:600;">Bạn chưa có đơn hàng nào trong mục này.</h6>
                            <p class="text-muted small">Hãy tiếp tục mua sắm để nhận nhiều ưu đãi hấp dẫn!</p>
                            <a href="<?= url('products') ?>" class="btn btn-danger btn-sm px-4 mt-2" style="border-radius:8px; background:var(--primary);">Mua sắm ngay</a>
                        </div>
                    <?php else: ?>
                        <div class="orders-list">
                            <?php foreach ($orders as $order): ?>
                                <div class="order-item-card">
                                    <div class="order-card-header">
                                        <div>
                                            <span class="order-code">Đơn hàng #<?= e($order['order_number']) ?></span>
                                            <span class="order-date ms-3"><i class="far fa-calendar-alt me-1"></i><?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></span>
                                        </div>
                                        <div>
                                            <span class="badge-status status-<?= $order['status'] ?>">
                                                <?= orderStatusLabel($order['status']) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="order-card-body">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div>
                                                <p class="mb-1 text-muted small"><i class="fas fa-user me-2"></i>Người nhận: <strong><?= e($order['ship_name']) ?></strong></p>
                                                <p class="mb-1 text-muted small"><i class="fas fa-phone me-2"></i>Điện thoại: <?= e($order['ship_phone']) ?></p>
                                                <p class="mb-0 text-muted small"><i class="fas fa-map-marker-alt me-2"></i>Địa chỉ: <?= e(formatOrderAddress($order)) ?></p>
                                            </div>
                                            <div class="text-md-end">
                                                <p class="mb-1 text-muted small">Số mặt hàng: <strong><?= $order['item_count'] ?></strong></p>
                                                <p class="mb-0 text-muted small">Thanh toán: <span class="badge bg-secondary"><?= strtoupper(e($order['payment_method'])) ?></span></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="order-card-footer">
                                        <div>
                                            <span class="text-muted small">Tổng tiền thanh toán:</span>
                                            <span class="order-total-price ms-2"><?= formatPrice($order['final_amount']) ?></span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="<?= url('orders/' . $order['id']) ?>" class="btn btn-outline-danger btn-sm" style="border-radius:8px; font-weight:600;">
                                                <i class="fas fa-eye me-1"></i>Chi tiết đơn
                                            </a>
                                            <?php if (in_array($order['status'], ['shipping', 'delivered'])): ?>
                                                <a href="<?= url('orders/track?order_number=' . $order['order_number']) ?>" class="btn btn-danger btn-sm" style="border-radius:8px; font-weight:600; background:var(--primary);">
                                                    <i class="fas fa-truck me-1"></i>Theo dõi
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
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
