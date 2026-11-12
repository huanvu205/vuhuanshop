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
                            <option value="return_requested" <?= $order['status'] === 'return_requested' ? 'selected' : '' ?>>Yêu cầu đổi trả</option>
                            <option value="return_received" <?= $order['status'] === 'return_received' ? 'selected' : '' ?>>Đã nhận hàng trả về</option>
                            <option value="refunded" <?= $order['status'] === 'refunded' ? 'selected' : '' ?>>Đã hoàn tiền</option>
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
                <?php if (($order['payment_method'] ?? '') === 'vnpay' && ($order['payment_status'] ?? '') === 'paid' && in_array($order['status'], ['return_requested','return_received'], true) && !empty($vnpayPayment)): ?>
                    <?php $refundableRemaining = max(0, (float)$vnpayPayment['amount'] - (float)($refundedAmount ?? 0)); ?>
                    <hr class="my-4">
                    <h6 class="fw-bold">Hoàn tiền qua VNPay Sandbox</h6>
                    <p class="small text-muted">Đã xác nhận hoàn: <?= formatPrice($refundedAmount ?? 0) ?> · Có thể yêu cầu thêm: <?= formatPrice($refundableRemaining) ?>. Kết quả đang xử lý không được gửi lại.</p>
                    <?php if ($refundableRemaining > 0): ?>
                    <form action="<?= url('admin/orders/requestRefund/' . $order['id']) ?>" method="POST" onsubmit="return confirm('Gửi yêu cầu hoàn tiền tới VNPay Sandbox?')">
                        <?= csrfToken() ?>
                        <label class="form-label small" for="refund-amount">Số tiền hoàn (VND)</label>
                        <input id="refund-amount" class="form-control mb-2" type="number" name="amount" min="1" max="<?= (int)$refundableRemaining ?>" step="1" value="<?= (int)$refundableRemaining ?>" required>
                        <input class="form-control mb-2" type="text" name="reason" maxlength="500" placeholder="Lý do hoàn tiền" value="Hoàn tiền theo yêu cầu đổi trả">
                        <button class="btn btn-warning w-100" type="submit">Gửi yêu cầu hoàn Sandbox</button>
                    </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($vnpayRefunds)): ?>
<div class="admin-card mt-4">
    <div class="admin-card-header"><h5 class="admin-card-title">Lịch sử yêu cầu hoàn VNPay</h5></div>
    <div class="admin-card-body table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Request ID</th><th>Số tiền</th><th>Loại</th><th>Trạng thái</th><th>Người tạo</th><th>Thời gian</th><th>Mã trả lời</th></tr></thead><tbody>
    <?php foreach ($vnpayRefunds as $refund): ?>
        <tr><td><?= e($refund['request_id']) ?></td><td><?= formatPrice($refund['amount']) ?></td><td><?= $refund['transaction_type'] === '02' ? 'Toàn phần' : 'Một phần' ?></td><td><?= e($refund['state']) ?></td><td><?= e($refund['requester_name'] ?? '') ?></td><td><?= e($refund['created_at']) ?></td><td><?= e($refund['response_code'] ?? '—') ?> / <?= e($refund['transaction_status'] ?? '—') ?></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
</div>
<?php endif; ?>

<style>
.status-timeline{position:relative;margin:0;padding-left:2px}
.status-timeline-item{display:grid;grid-template-columns:38px 1fr;gap:12px;position:relative;padding-bottom:18px}
.status-timeline-item:not(:last-child)::before{content:'';position:absolute;left:18px;top:36px;bottom:1px;width:2px;background:#e5e7eb}
.status-timeline-icon{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#64748b;color:#fff;z-index:1;box-shadow:0 0 0 4px #fff}
.status-timeline-icon.success{background:#10b981}.status-timeline-icon.warning{background:#f59e0b}.status-timeline-icon.info{background:#3b82f6}.status-timeline-icon.danger{background:#ef4444}.status-timeline-icon.return{background:#a855f7}
.status-timeline-card{background:#fff;border:1px solid #e9edf3;border-radius:12px;padding:12px 14px;box-shadow:0 2px 10px rgba(15,23,42,.035)}
</style>
<div class="admin-card mt-4">
   <div class="admin-card-header"><h5 class="admin-card-title">Lịch sử trạng thái</h5></div>
   <div class="admin-card-body">
       <div class="status-timeline">
       <?php foreach (array_reverse($statusHistory ?? []) as $event):
           $status = (string)($event['to_status'] ?? '');
           $iconClass = in_array($status,['delivered','refunded'],true) ? 'success' : (in_array($status,['pending'],true) ? 'warning' : (in_array($status,['confirmed','processing','shipping'],true) ? 'info' : ($status === 'cancelled' ? 'danger' : (strpos($status,'return') === 0 ? 'return' : ''))));
           $icons = ['pending'=>'fa-clock','confirmed'=>'fa-circle-check','processing'=>'fa-gears','shipping'=>'fa-truck-fast','delivered'=>'fa-box-open','cancelled'=>'fa-circle-xmark','return_requested'=>'fa-rotate-left','return_received'=>'fa-boxes-packing','refunded'=>'fa-money-bill-transfer'];
       ?>
           <div class="status-timeline-item">
               <div class="status-timeline-icon <?= $iconClass ?>"><i class="fas <?= $icons[$status] ?? 'fa-circle' ?>"></i></div>
               <div class="status-timeline-card">
                   <div class="d-flex justify-content-between gap-2">
                       <strong><?= e(orderStatusLabel($status)) ?></strong>
                       <small class="text-muted text-nowrap"><?= e(!empty($event['created_at']) ? date('d/m/Y - H:i',strtotime($event['created_at'])) : '') ?></small>
                   </div>
                   <div class="small text-muted mt-1"><?= e($event['actor_name'] ?? 'Hệ thống') ?></div>
                   <?php if (!empty($event['note'])): ?><div class="small mt-2 p-2 rounded bg-light"><?= e($event['note']) ?></div><?php endif; ?>
               </div>
           </div>
       <?php endforeach; ?>
       </div>
       <?php if (empty($statusHistory)): ?><p class="text-muted mb-0">Chưa có lịch sử trạng thái.</p><?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
