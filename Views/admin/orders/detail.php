<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9rem;
    color: #475569;
}
.summary-row.total {
    font-size: 1.1rem;
    font-weight: 800;
    color: #e53935;
    border-top: 1px solid #e2e8f0;
    padding-top: 12px;
    margin-top: 12px;
}
.order-product-meta {
    display: flex;
    gap: 12px;
    align-items: center;
}
.order-product-img {
    width: 50px;
    height: 50px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    object-fit: cover;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Chi tiết đơn hàng #<?= e($order['order_number']) ?></h4>
        <span class="text-muted small">Cập nhật tiến độ giao nhận, thông tin thanh toán và ghi chú vận đơn</span>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/orders/invoice/' . $order['id']) ?>" class="btn btn-outline-primary px-3 fw-semibold" style="border-radius:10px;" target="_blank">
            <i class="fas fa-print me-1"></i>In hóa đơn
        </a>
        <a href="<?= url('admin/orders') ?>" class="btn btn-light border px-4 fw-semibold" style="border-radius:10px;">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Main content (Left) -->
    <div class="col-lg-8">
        <!-- Products list -->
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-shopping-bag text-primary"></i> Sản phẩm trong đơn</h5>
            </div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
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
                                    <div class="order-product-meta">
                                        <img src="<?= productImage($item['main_image']) ?>" class="order-product-img" alt="">
                                        <div>
                                            <span style="font-weight:600; color:#1e293b;"><?= e($item['product_name']) ?></span>
                                            <?php if (!empty($item['variant'])): ?>
                                                <div class="mt-1">
                                                    <span class="badge bg-light text-dark border" style="font-size:11px; font-weight:500;">
                                                        <i class="fas fa-tag me-1 text-danger"></i><?= e($item['variant']) ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center"><?= formatPrice($item['price']) ?></td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-end fw-bold" style="color:#0f172a;"><?= formatPrice($item['subtotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Total Calc -->
            <div class="admin-card-body border-top p-4 bg-light-subtle">
                <div class="row justify-content-end">
                    <div class="col-md-6 col-lg-5">
                        <div class="summary-row">
                            <span>Tạm tính mặt hàng:</span>
                            <span class="fw-semibold text-dark"><?= formatPrice($order['total_amount']) ?></span>
                        </div>
                        <?php if ($order['discount_amount'] > 0): ?>
                            <div class="summary-row text-success">
                                <span>Giảm giá khuyến mãi:</span>
                                <span>-<?= formatPrice($order['discount_amount']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span><?= $order['shipping_fee'] > 0 ? formatPrice($order['shipping_fee']) : 'Miễn phí' ?></span>
                        </div>
                        <div class="summary-row total">
                            <span>Tổng tiền thanh toán:</span>
                            <span><?= formatPrice($order['final_amount']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recipient & Payment info -->
        <div class="row g-3">
            <div class="col-md-6">
                <div class="admin-card h-100">
                    <div class="admin-card-header p-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-map-marker-alt text-primary me-2"></i>Địa chỉ nhận hàng</h6>
                    </div>
                    <div class="admin-card-body p-4">
                        <table class="table table-borderless table-sm small mb-0">
                            <tr>
                                <td class="text-muted" style="width: 110px;">Họ tên nhận:</td>
                                <td class="fw-bold"><?= e($order['ship_name']) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Số điện thoại:</td>
                                <td><?= e($order['ship_phone']) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email:</td>
                                <td><?= e($order['ship_email'] ?? 'Không cung cấp') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Địa chỉ nhận:</td>
                                <td><?= e(formatOrderAddress($order)) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ghi chú giao:</td>
                                <td class="text-danger font-monospace"><?= !empty($order['customer_note']) ? e($order['customer_note']) : 'Không có' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="admin-card h-100">
                    <div class="admin-card-header p-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-credit-card text-primary me-2"></i>Thanh toán & Vận chuyển</h6>
                    </div>
                    <div class="admin-card-body p-4">
                        <table class="table table-borderless table-sm small mb-0">
                            <tr>
                                <td class="text-muted" style="width: 120px;">Phương thức:</td>
                                <td class="fw-bold text-uppercase"><?= e($order['payment_method']) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">TT Thanh toán:</td>
                                <td>
                                    <span class="badge <?= $order['payment_status'] === 'paid' ? 'bg-success' : 'bg-warning' ?>">
                                        <?= $order['payment_status'] === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ngày đặt đơn:</td>
                                <td><?= date('d/m/Y H:i:s', strtotime($order['created_at'])) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Cập nhật gần nhất:</td>
                                <td><?= date('d/m/Y H:i:s', strtotime($order['updated_at'])) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status control Sidebar (Right) -->
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-edit text-primary"></i> Trạng thái đơn hàng</h5>
            </div>
            <div class="admin-card-body p-4">
                <form action="<?= url('admin/orders/updateStatus/' . $order['id']) ?>" method="POST">
                    <?= csrfToken() ?>

                    <!-- Current badge -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small d-block">Trạng thái hiện tại</label>
                        <span class="badge-status status-<?= $order['status'] ?> fs-6 py-2 px-3 d-inline-block">
                            <?= orderStatusLabel($order['status']) ?>
                        </span>
                    </div>

                    <!-- Status select -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small" for="status_update">Cập nhật trạng thái mới</label>
                        <select id="status_update" name="status" class="form-select" style="border-radius:8px;" required>
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý (Pending)</option>
                            <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận (Confirmed)</option>
                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Đang đóng gói (Processing)</option>
                            <option value="shipping" <?= $order['status'] === 'shipping' ? 'selected' : '' ?>>Đang giao hàng (Shipping)</option>
                            <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Hoàn thành giao (Delivered)</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Hủy đơn hàng (Cancelled)</option>
                        </select>
                    </div>

                    <!-- Admin Note -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark small" for="admin_note">Ghi chú quản lý (Lý do hủy/Thông tin ship...)</label>
                        <textarea id="admin_note" name="admin_note" class="form-control" rows="4" placeholder="Nhập ghi chú cho đơn hàng..." style="border-radius:8px;"><?= e($order['admin_note'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2.5 fw-bold" style="border-radius:10px; background:var(--admin-primary,#E53935); border:none;">
                        <i class="fas fa-check-circle me-1"></i>Lưu trạng thái mới
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
