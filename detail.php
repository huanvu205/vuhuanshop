<?php require_once __DIR__ . '/../layouts/header.php';

// Các biến $order / $orderDetails do OrderController truyền qua Controller::view() (extract).
// Khởi tạo mặc định phòng khi view được gọi trực tiếp, tránh lỗi "undefined variable".
$order = $order ?? [];
$orderDetails = $orderDetails ?? [];
if (empty($order)) {
    echo '<div class="container py-5"><div class="alert alert-warning">Không tìm thấy đơn hàng.</div></div>';
    require __DIR__ . '/../layouts/footer.php';
    return;
}
?>

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

/* Breadcrumb */
.breadcrumb-wrap {
    padding: 12px 0;
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
}
.breadcrumb-wrap .breadcrumb { margin: 0; font-size: 0.85rem; }
.breadcrumb-item a { color: var(--primary); text-decoration: none; }
.breadcrumb-item.active { color: #64748b; }

/* Order Detail Timeline */
.timeline-wrap {
    background-color: #fafafa;
    border-radius: 12px;
    padding: 30px 20px;
    margin-bottom: 24px;
    border: 1px solid #f0f0f0;
}
.order-timeline {
    display: flex;
    justify-content: space-between;
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}
.order-timeline::before {
    content: '';
    position: absolute;
    top: 25px;
    left: 40px;
    right: 40px;
    height: 3px;
    background-color: #e0e0e0;
    z-index: 0;
}
.timeline-step {
    text-align: center;
    position: relative;
    z-index: 1;
    flex: 1;
}
.timeline-icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #fff;
    border: 3px solid #e0e0e0;
    color: #999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin: 0 auto 10px;
    transition: all 0.3s;
}
.timeline-step.active .timeline-icon-circle {
    border-color: var(--primary);
    color: var(--primary);
    background-color: #fff;
    box-shadow: 0 0 0 8px rgba(229,57,53,0.1);
}
.timeline-step.done .timeline-icon-circle {
    border-color: #22c55e;
    color: #fff;
    background-color: #22c55e;
}
.timeline-step.done::before {
    content: '';
    position: absolute;
    top: 25px;
    left: 50%;
    width: 100%;
    height: 3px;
    background-color: #22c55e;
    z-index: -1;
}
.timeline-title { font-size: 0.82rem; font-weight: 700; color: #666; margin-bottom: 4px; }
.timeline-step.active .timeline-title { color: var(--primary); }
.timeline-step.done .timeline-title { color: #22c55e; }
.timeline-time { font-size: 0.72rem; color: #999; }

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

/* Order Summary */
.info-label { font-size: 0.85rem; color: #777; margin-bottom: 4px; }
.info-value { font-size: 0.95rem; font-weight: 600; color: #1a1a2e; margin-bottom: 12px; }

/* Products Table */
.order-products-table th { background-color: #f8f9fa; border-bottom: 2px solid #eee; padding: 12px; font-size: 0.85rem; color: #666; }
.order-products-table td { padding: 16px 12px; vertical-align: middle; }
.product-item-meta { display: flex; gap: 12px; align-items: center; }
.product-item-img { width: 50px; height: 50px; border-radius: 6px; border: 1px solid #eee; object-fit: cover; }
.product-item-name { font-weight: 600; color: #1a1a2e; text-decoration: none; }
.product-item-name:hover { color: var(--primary); }

.summary-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: #666; }
.summary-row.total { font-size: 1.15rem; font-weight: 800; color: var(--primary); border-top: 1px solid #eee; padding-top: 12px; margin-top: 12px; }
</style>

<!-- Profile Hero -->
<div class="profile-hero">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="h4 mb-1"><i class="fas fa-file-invoice me-2"></i>Đơn hàng #<?= e($order['order_number']) ?></h1>
                <p>Đặt lúc: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
            </div>
            <div>
                <span class="badge-status status-<?= $order['status'] ?> fs-6">
                    <?= orderStatusLabel($order['status']) ?>
                </span>
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
            <!-- Thông báo flash đã được layouts/header.php hiển thị -->

            <!-- Timeline -->
            <?php if ($order['status'] !== 'cancelled'): ?>
                <div class="timeline-wrap d-none d-md-block">
                    <div class="order-timeline">
                        <?php 
                        $statusIndex = 0;
                        if ($order['status'] === 'pending') $statusIndex = 1;
                        elseif ($order['status'] === 'confirmed') $statusIndex = 2;
                        elseif ($order['status'] === 'processing') $statusIndex = 3;
                        elseif ($order['status'] === 'shipping') $statusIndex = 4;
                        elseif ($order['status'] === 'delivered') $statusIndex = 5;
                        ?>
                        <div class="timeline-step <?= $statusIndex >= 1 ? 'done' : '' ?> <?= $statusIndex == 1 ? 'active' : '' ?>">
                            <div class="timeline-icon-circle"><i class="fas fa-receipt"></i></div>
                            <div class="timeline-title">Chờ duyệt</div>
                            <div class="timeline-time"><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                        </div>
                        <div class="timeline-step <?= $statusIndex >= 2 ? 'done' : '' ?> <?= $statusIndex == 2 ? 'active' : '' ?>">
                            <div class="timeline-icon-circle"><i class="fas fa-clipboard-check"></i></div>
                            <div class="timeline-title">Xác nhận</div>
                        </div>
                        <div class="timeline-step <?= $statusIndex >= 3 ? 'done' : '' ?> <?= $statusIndex == 3 ? 'active' : '' ?>">
                            <div class="timeline-icon-circle"><i class="fas fa-box-open"></i></div>
                            <div class="timeline-title">Đóng gói</div>
                        </div>
                        <div class="timeline-step <?= $statusIndex >= 4 ? 'done' : '' ?> <?= $statusIndex == 4 ? 'active' : '' ?>">
                            <div class="timeline-icon-circle"><i class="fas fa-truck"></i></div>
                            <div class="timeline-title">Đang giao</div>
                        </div>
                        <div class="timeline-step <?= $statusIndex >= 5 ? 'done' : '' ?> <?= $statusIndex == 5 ? 'active' : '' ?>">
                            <div class="timeline-icon-circle"><i class="fas fa-check-circle"></i></div>
                            <div class="timeline-title">Đã giao</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Info Grid -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="profile-card h-100">
                        <div class="card-header-custom" style="padding: 16px 20px;">
                            <div class="icon" style="width:30px;height:30px;font-size:0.9rem;"><i class="fas fa-map-marker-alt"></i></div>
                            <h6 class="mb-0 fw-bold">Thông tin nhận hàng</h6>
                        </div>
                        <div class="card-body-custom" style="padding: 20px;">
                            <div class="info-label">Người nhận hàng</div>
                            <div class="info-value"><?= e($order['ship_name']) ?></div>
                            
                            <div class="info-label">Số điện thoại</div>
                            <div class="info-value"><?= e($order['ship_phone']) ?></div>

                            <div class="info-label">Địa chỉ nhận hàng</div>
                            <div class="info-value">
                                <?= e(formatOrderAddress($order)) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="profile-card h-100">
                        <div class="card-header-custom" style="padding: 16px 20px;">
                            <div class="icon" style="width:30px;height:30px;font-size:0.9rem;"><i class="fas fa-wallet"></i></div>
                            <h6 class="mb-0 fw-bold">Thanh toán & Vận chuyển</h6>
                        </div>
                        <div class="card-body-custom" style="padding: 20px;">
                            <div class="info-label">Phương thức thanh toán</div>
                            <div class="info-value">
                                <?php 
                                $payLabel = [
                                    'cod' => 'Thanh toán trực tiếp khi nhận hàng (COD)',
                                    'banking' => 'Chuyển khoản ngân hàng',
                                    'momo' => 'Ví điện tử MoMo',
                                    'vnpay' => 'Cổng thanh toán VNPAY'
                                ];
                                echo e($payLabel[$order['payment_method']] ?? $order['payment_method']);
                                ?>
                            </div>
                            
                            <div class="info-label">Trạng thái thanh toán</div>
                            <div class="info-value">
                                <span class="badge <?= $order['payment_status'] === 'paid' ? 'bg-success' : 'bg-warning' ?>">
                                    <?= $order['payment_status'] === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' ?>
                                </span>
                            </div>

                            <div class="info-label">Ghi chú khách hàng</div>
                            <div class="info-value text-muted" style="font-size: 0.85rem; font-style: italic;">
                                <?= !empty($order['customer_note']) ? e($order['customer_note']) : 'Không có ghi chú' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products List -->
            <div class="profile-card mb-4">
                <div class="card-header-custom" style="padding: 16px 20px;">
                    <div class="icon" style="width:30px;height:30px;font-size:0.9rem;"><i class="fas fa-shopping-bag"></i></div>
                    <h6 class="mb-0 fw-bold">Danh sách sản phẩm</h6>
                </div>
                <div class="table-responsive">
                    <table class="table order-products-table mb-0">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Đơn giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Tạm tính</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderDetails as $item): ?>
                                <tr>
                                    <td>
                                        <div class="product-item-meta">
                                            <img src="<?= productImage($item['main_image']) ?>" class="product-item-img" alt="Product" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/images/no-image.png'">
                                            <div>
                                                <a href="<?= url('products/' . $item['product_slug']) ?>" class="product-item-name"><?= e($item['product_name']) ?></a>
                                                <?php if (!empty($item['variant'])): ?>
                                                    <div class="mt-1">
                                                        <span class="badge bg-light text-dark border" style="font-size:11px; font-weight:500;">
                                                            <i class="fas fa-tag me-1 text-danger"></i><?= e($item['variant']) ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($order['status'] === 'delivered'): ?>
                                                    <!-- Nút review nếu đã giao -->
                                                    <div class="mt-1">
                                                        <a href="<?= url('products/' . $item['product_slug'] . '#reviews-tab') ?>" class="text-danger small" style="text-decoration:none;">
                                                            <i class="far fa-star me-1"></i>Viết đánh giá
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= formatPrice($item['price']) ?></td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end fw-bold"><?= formatPrice($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Financial calculations -->
                <div class="card-body-custom border-top" style="padding: 24px 28px; background-color:#fafafa;">
                    <div class="row justify-content-end">
                        <div class="col-md-5">
                            <div class="summary-row">
                                <span>Tạm tính:</span>
                                <span class="fw-bold text-dark"><?= formatPrice($order['total_amount']) ?></span>
                            </div>
                            <?php if ($order['discount_amount'] > 0): ?>
                                <div class="summary-row text-success">
                                    <span>Giảm giá mã giảm:</span>
                                    <span>-<?= formatPrice($order['discount_amount']) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="summary-row">
                                <span>Phí vận chuyển:</span>
                                <span><?= $order['shipping_fee'] > 0 ? formatPrice($order['shipping_fee']) : 'Miễn phí' ?></span>
                            </div>
                            <div class="summary-row total">
                                <span>Thành tiền:</span>
                                <span><?= formatPrice($order['final_amount']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action footer -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-4">
                <a href="<?= url('orders') ?>" class="btn btn-outline-secondary btn-sm px-4" style="border-radius:8px;">
                    <i class="fas fa-chevron-left me-1"></i>Trở lại danh sách
                </a>

                <?php 
                $orderModel = new OrderModel();
                if ($orderModel->canCancel($order['id'], $_SESSION['user_id'])): 
                ?>
                    <button class="btn btn-danger btn-sm px-4" style="border-radius:8px; background:var(--primary);" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                        <i class="fas fa-times me-1"></i>Hủy đơn hàng
                    </button>
                <?php endif; ?>
            </div>

        </div><!-- /col -->
    </div>
</div>

<!-- Modal Hủy Đơn Hàng -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Xác nhận hủy đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('orders/cancel/' . $order['id']) ?>" method="POST">
                <div class="modal-body py-3">
                    <p class="text-muted small mb-3">Bạn chắc chắn muốn hủy đơn hàng này? Một khi đã hủy, bạn không thể khôi phục lại trạng thái cũ.</p>
                    <div class="form-group mb-0">
                        <label class="form-label small fw-bold mb-2">Lý do hủy đơn hàng <span class="text-danger">*</span></label>
                        <select name="reason" class="form-select" style="border-radius:8px;" required>
                            <option value="Tôi muốn thay đổi sản phẩm khác">Tôi muốn thay đổi sản phẩm khác</option>
                            <option value="Tôi tìm thấy giá rẻ hơn ở nơi khác">Tôi tìm thấy giá rẻ hơn ở nơi khác</option>
                            <option value="Tôi thay đổi quyết định không mua nữa">Tôi thay đổi quyết định không mua nữa</option>
                            <option value="Địa chỉ giao hàng bị sai">Địa chỉ giao hàng bị sai</option>
                            <option value="Thời gian giao nhận hàng quá lâu">Thời gian giao nhận hàng quá lâu</option>
                            <option value="Khác">Lý do khác...</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm px-3" style="border-radius:8px;" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4" style="border-radius:8px; background:var(--primary);">Hủy đơn hàng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
