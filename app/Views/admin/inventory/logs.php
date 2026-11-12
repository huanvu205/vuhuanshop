<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Stats header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Lịch sử xuất nhập kho</h4>
        <span class="text-muted small">Nhật ký theo dõi chi tiết từng đợt thay đổi tồn kho sản phẩm</span>
    </div>
    <div>
        <a href="<?= url('admin/inventory') ?>" class="btn btn-light border px-4 fw-semibold" style="border-radius:10px;">
            <i class="fas fa-arrow-left me-1"></i>Quay lại kho
        </a>
    </div>
</div>

<!-- Logs Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Sản phẩm</th>
                    <th class="text-center">Loại giao dịch</th>
                    <th class="text-center">Số lượng biến động</th>
                    <th>Hình thức</th>
                    <th>Ghi chú</th>
                    <th>Người thực hiện</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-history fs-2 text-muted mb-2 d-block"></i>
                            <span class="text-muted small">Không tìm thấy nhật ký kho nào</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td style="font-size:0.8rem; color:#64748b;">
                                <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #1e293b; font-size:0.88rem;"><?= e($log['product_name']) ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($log['transaction_type'] === 'in'): ?>
                                    <span class="badge bg-success-subtle text-success border" style="font-size:0.72rem; padding:4px 8px; border-radius:8px;">
                                        <i class="fas fa-arrow-down me-1"></i> Nhập kho
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border" style="font-size:0.72rem; padding:4px 8px; border-radius:8px;">
                                        <i class="fas fa-arrow-up me-1"></i> Xuất kho
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center fw-bold fs-6">
                                <?php if ($log['transaction_type'] === 'in'): ?>
                                    <span class="text-success">+<?= $log['quantity'] ?></span>
                                <?php else: ?>
                                    <span class="text-danger">-<?= $log['quantity'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary small border" style="font-size: 0.72rem;">
                                    <?php 
                                    if ($log['reference_type'] === 'manual_import') echo 'Nhập thủ công';
                                    elseif ($log['reference_type'] === 'order') echo 'Đơn mua hàng';
                                    elseif ($log['reference_type'] === 'manual_adjustment') echo 'Điều chỉnh';
                                    else echo e($log['reference_type']);
                                    ?>
                                </span>
                            </td>
                            <td>
                                <span class="small" style="font-size:0.82rem; color:#475569;"><?= e($log['notes']) ?></span>
                            </td>
                            <td>
                                <span class="small fw-semibold" style="font-size:0.85rem; color:#1e293b;"><i class="far fa-user me-1 text-muted"></i><?= e($log['created_by_name'] ?? 'Hệ thống') ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
