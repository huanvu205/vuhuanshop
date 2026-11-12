<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Stats header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Quản lý kho hàng</h4>
        <span class="text-muted small">Xem mức tồn kho hiện tại, thiết lập cảnh báo và thực hiện nhập/xuất kho</span>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/inventory/logs') ?>" class="btn btn-outline-primary px-3 fw-semibold" style="border-radius:10px;">
            <i class="fas fa-history me-1"></i>Lịch sử kho
        </a>
        <a href="<?= url('admin/inventory/import') ?>" class="btn btn-danger text-white px-4 fw-semibold" style="border-radius:10px; background: var(--admin-primary, #E53935); border: none;">
            <i class="fas fa-plus me-1"></i>Nhập kho sản phẩm
        </a>
    </div>
</div>

<!-- Filter card -->
<div class="admin-card mb-4">
    <div class="admin-card-body p-3">
        <form method="GET" action="<?= url('admin/inventory') ?>" class="row g-2 align-items-center">
            <!-- Search -->
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Tên hoặc mã sản phẩm..." value="<?= e($search) ?>" style="font-size:0.88rem;">
                </div>
            </div>
            
            <!-- Low stock check -->
            <div class="col-md-4">
                <div class="form-check form-switch ms-md-3">
                    <input class="form-check-input" type="checkbox" id="low_stock_switch" name="low_stock" value="1" <?= $lowStock ? 'checked' : '' ?> onchange="this.form.submit()">
                    <label class="form-check-label text-dark small fw-semibold" for="low_stock_switch" style="cursor:pointer;">Chỉ hiển thị sản phẩm tồn kho thấp</label>
                </div>
            </div>

            <!-- Submit -->
            <div class="col-md-3 text-md-end">
                <div class="d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-danger px-4 fw-semibold" style="border-radius:8px; background:var(--admin-primary,#E53935); font-size: 0.85rem; border:none;">
                        Tìm kiếm
                    </button>
                    <a href="<?= url('admin/inventory') ?>" class="btn btn-outline-secondary" title="Quay lại ban đầu" style="border-radius:8px; font-size: 0.85rem;">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Inventory Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Thương hiệu</th>
                    <th class="text-center">Ngưỡng cảnh báo</th>
                    <th class="text-center">Số lượng trong kho</th>
                    <th class="text-center">Đánh giá tồn kho</th>
                    <th style="width: 100px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inventory)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-warehouse fs-2 text-muted mb-2 d-block"></i>
                            <span class="text-muted small">Không tìm thấy thông tin sản phẩm nào trong kho</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inventory as $item): ?>
                        <?php 
                        $isLow = $item['quantity'] <= $item['reorder_level'];
                        ?>
                        <tr style="<?= $isLow ? 'background-color: #fffdf5;' : '' ?>">
                            <td><span style="font-size:0.85rem; font-family:monospace;"><?= e($item['product_code']) ?></span></td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b; font-size: 0.88rem;"><?= e($item['name']) ?></div>
                            </td>
                            <td><span style="font-size:0.85rem;"><?= e($item['category_name']) ?></span></td>
                            <td><span style="font-size:0.85rem;"><?= e($item['brand_name']) ?></span></td>
                            <td class="text-center" style="font-size:0.85rem; color:#64748b; font-weight:600;">
                                <?= $item['reorder_level'] ?>
                            </td>
                            <td class="text-center">
                                <strong style="font-size:0.95rem; color: <?= $isLow ? '#E53935' : '#1e293b' ?>;">
                                    <?= $item['quantity'] ?>
                                </strong>
                            </td>
                            <td class="text-center">
                                <?php if ($item['quantity'] == 0): ?>
                                    <span class="badge bg-danger-subtle text-danger border" style="font-size:0.75rem; padding: 4px 10px; border-radius:12px;">Hết hàng</span>
                                <?php elseif ($isLow): ?>
                                    <span class="badge bg-warning-subtle text-warning border" style="font-size:0.75rem; padding: 4px 10px; border-radius:12px;">Sắp hết hàng</span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border" style="font-size:0.75rem; padding: 4px 10px; border-radius:12px;">Đầy đủ</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= url('admin/inventory/import?product_id=' . $item['product_id']) ?>" class="btn btn-outline-danger btn-sm py-1 px-2 fw-semibold" style="border-radius:6px; font-size:0.75rem;" title="Nhập thêm hàng">
                                    <i class="fas fa-plus me-1"></i>Nhập kho
                                </a>
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
        $currentUrl = url('admin/inventory');
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
