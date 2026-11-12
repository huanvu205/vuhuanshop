<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Stats header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Quản lý đơn hàng</h4>
        <span class="text-muted small">Kiểm tra thông tin giao dịch, trạng thái vận chuyển và duyệt đơn hàng</span>
    </div>
</div>

<!-- Filter card -->
<div class="admin-card mb-4">
    <div class="admin-card-body p-3">
        <form method="GET" action="<?= url('admin/orders') ?>" class="row g-2">
            <!-- Search -->
            <div class="col-md-4 col-sm-6">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Mã đơn hàng, tên, SĐT nhận..." value="<?= e($search) ?>" style="font-size:0.88rem;">
                </div>
            </div>
            
            <!-- Status -->
            <div class="col-md-3 col-sm-6">
                <select name="status" class="form-select bg-light" style="font-size:0.88rem;">
                    <option value="">-- Tất cả trạng thái đơn --</option>
                    <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                    <option value="confirmed" <?= $filterStatus === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                    <option value="processing" <?= $filterStatus === 'processing' ? 'selected' : '' ?>>Đang đóng gói</option>
                    <option value="shipping" <?= $filterStatus === 'shipping' ? 'selected' : '' ?>>Đang giao hàng</option>
                    <option value="delivered" <?= $filterStatus === 'delivered' ? 'selected' : '' ?>>Đã hoàn thành</option>
                    <option value="cancelled" <?= $filterStatus === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>
            
            <!-- Payment status -->
            <div class="col-md-3 col-sm-6">
                <select name="payment" class="form-select bg-light" style="font-size:0.88rem;">
                    <option value="">-- Trạng thái thanh toán --</option>
                    <option value="unpaid" <?= $filterPayment === 'unpaid' ? 'selected' : '' ?>>Chưa thanh toán</option>
                    <option value="paid" <?= $filterPayment === 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="col-md-2 col-sm-6">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger w-100 fw-semibold" style="border-radius:8px; background:var(--admin-primary,#E53935); font-size: 0.85rem; border:none;">
                        Lọc kết quả
                    </button>
                    <a href="<?= url('admin/orders') ?>" class="btn btn-outline-secondary" title="Xóa bộ lọc" style="border-radius:8px; font-size: 0.85rem;">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="min-width: 130px;">Mã đơn hàng</th>
                    <th style="min-width: 140px;">Khách hàng / Người nhận</th>
                    <th style="width: 85px;" class="text-center">Phương thức</th>
                    <th style="width: 95px;" class="text-center">Thanh toán</th>
                    <th style="width: 105px;" class="text-end">Tổng tiền</th>
                    <th style="width: 105px;" class="text-center">Trạng thái</th>
                    <th style="width: 95px;" class="text-center">Ngày đặt</th>
                    <th style="width: 80px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-receipt fs-2 text-muted mb-2 d-block"></i>
                            <span class="text-muted small">Không tìm thấy đơn hàng nào phù hợp</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <div class="fw-bold font-monospace text-dark" style="font-size:0.83rem; word-break:break-all; max-width:145px; line-height:1.25;" title="#<?= e($order['order_number']) ?>">
                                    #<?= e($order['order_number']) ?>
                                </div>
                                <span class="d-block text-muted mt-1" style="font-size:0.75rem;">
                                    <i class="fas fa-box-open me-1"></i><?= $order['item_count'] ?> món
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b; font-size: 0.88rem;"><?= e($order['ship_name']) ?></div>
                                <a href="tel:<?= e($order['ship_phone']) ?>" class="text-muted text-decoration-none" style="font-size:0.78rem;">
                                    <i class="fas fa-phone-alt me-1 text-primary"></i><?= e($order['ship_phone']) ?>
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary border uppercase" style="font-size: 0.72rem;"><?= strtoupper(e($order['payment_method'])) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $order['payment_status'] === 'paid' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border-warning-subtle' ?> border" style="font-size: 0.73rem; padding: 3px 8px; border-radius:12px;">
                                    <?= $order['payment_status'] === 'paid' ? 'Đã thanh toán' : 'Chưa trả' ?>
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <span style="font-weight:700; color:#E53935; font-size:0.88rem;"><?= formatPrice($order['final_amount']) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge-status status-<?= $order['status'] ?>" style="font-size:0.73rem;">
                                    <?= orderStatusLabel($order['status']) ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap" style="font-size:0.78rem; color:#64748b; line-height: 1.25;">
                                <?= date('d/m/Y', strtotime($order['created_at'])) ?><br>
                                <span class="text-muted" style="font-size:0.72rem;"><?= date('H:i', strtotime($order['created_at'])) ?></span>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-inline-flex align-items-center gap-1 justify-content-center">
                                    <a href="<?= url('admin/orders/' . $order['id']) ?>" class="btn-table-action view" title="Xem chi tiết & Cập nhật">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= url('admin/orders/invoice/' . $order['id']) ?>" class="btn-table-action view" style="color:#0f3460; background:#e1f5fe; border-color:#b3e5fc;" title="In hóa đơn" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <div class="mt-4">
        <?php
        $currentUrl = url('admin/orders');
        $queryParams = $_GET;
        
        echo '<nav><ul class="pagination justify-content-center">';
        
        if ($currentPage > 1) {
            $queryParams['page'] = $currentPage - 1;
            echo '<li class="page-item"><a class="page-link" href="' . $currentUrl . '?' . http_build_query($queryParams) . '"><i class="fas fa-chevron-left"></i></a></li>';
        }
        
        for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
            $queryParams['page'] = $i;
            $active = $i === $currentPage ? ' active' : '';
            echo '<li class="page-item' . $active . '"><a class="page-link" href="' . $currentUrl . '?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
        }
        
        if ($currentPage < $totalPages) {
            $queryParams['page'] = $currentPage + 1;
            echo '<li class="page-item"><a class="page-link" href="' . $currentUrl . '?' . http_build_query($queryParams) . '"><i class="fas fa-chevron-right"></i></a></li>';
        }
        
        echo '</ul></nav>';
        ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
