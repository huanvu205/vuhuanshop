<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Stats header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Quản lý mã giảm giá</h4>
        <span class="text-muted small">Tạo và thiết lập các chương trình khuyến mãi bằng mã voucher giảm giá</span>
    </div>
    <div>
        <a href="<?= url('admin/coupons/create') ?>" class="btn btn-danger text-white px-4 fw-semibold" style="border-radius:10px; background: var(--admin-primary, #E53935); border: none;">
            <i class="fas fa-plus me-1"></i>Thêm mã giảm giá
        </a>
    </div>
</div>

<!-- Coupons Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã giảm giá (Code)</th>
                    <th>Mô tả chi tiết</th>
                    <th>Loại giảm</th>
                    <th class="text-end">Giá trị giảm</th>
                    <th class="text-end">Đơn hàng tối thiểu</th>
                    <th class="text-center">Đã dùng / Tối đa</th>
                    <th class="text-center">Thời hạn áp dụng</th>
                    <th class="text-center">Trạng thái</th>
                    <th style="width: 110px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($coupons)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-ticket-alt fs-2 text-muted mb-2 d-block"></i>
                            <span class="text-muted small">Chưa có mã giảm giá nào được tạo</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($coupons as $coup): ?>
                        <tr>
                            <td>
                                <strong style="font-family:monospace; font-size:1rem; color:#E53935;"><?= e($coup['coupon_code']) ?></strong>
                            </td>
                            <td>
                                <span class="small" style="font-size:0.85rem;"><?= e($coup['description']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border small" style="font-size:0.75rem;">
                                    <?= $coup['discount_type'] === 'percent' ? 'Giảm %' : 'Số tiền cố định' ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <span style="font-weight:700; color:#1e293b;">
                                    <?= $coup['discount_type'] === 'percent' ? $coup['discount_value'] . '%' : formatPrice($coup['discount_value']) ?>
                                </span>
                                <?php if ($coup['discount_type'] === 'percent' && !empty($coup['max_discount'])): ?>
                                    <div class="text-muted small" style="font-size:0.7rem;">(Tối đa: <?= formatPrice($coup['max_discount']) ?>)</div>
                                <?php endif; ?>
                            </td>
                            <td class="text-end" style="font-size:0.85rem;">
                                <?= !empty($coup['min_order_amount']) ? formatPrice($coup['min_order_amount']) : 'Không giới hạn' ?>
                            </td>
                            <td class="text-center" style="font-size:0.85rem;">
                                <strong class="text-primary"><?= $coup['used_count'] ?? 0 ?></strong> / <?= $coup['max_uses'] ?? '∞' ?>
                            </td>
                            <td class="text-center small" style="font-size:0.8rem; color:#64748b; line-height:1.3;">
                                <div>Từ: <?= date('d/m/Y', strtotime($coup['start_date'])) ?></div>
                                <div>Đến: <?= date('d/m/Y', strtotime($coup['end_date'])) ?></div>
                                <?php if (strtotime($coup['end_date']) < time()): ?>
                                    <span class="badge bg-danger-subtle text-danger small mt-1" style="font-size:0.65rem; padding: 2px 6px;">Hết hạn</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $coup['status'] === 'active' && strtotime($coup['end_date']) >= time() ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> border" style="font-size:0.75rem; padding: 4px 10px; border-radius:12px;">
                                    <?= $coup['status'] === 'active' && strtotime($coup['end_date']) >= time() ? 'Có hiệu lực' : 'Đang tắt' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= url('admin/coupons/edit/' . $coup['id']) ?>" class="btn-table-action edit" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn-table-action delete border-0 bg-transparent text-danger ms-1" title="Xóa" onclick="confirmDelete('<?= url('admin/coupons/delete/' . $coup['id']) ?>', 'mã <?= addslashes($coup['coupon_code']) ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
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
        $currentUrl = url('admin/coupons');
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
