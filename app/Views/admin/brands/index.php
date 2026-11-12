<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Stats header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Quản lý thương hiệu</h4>
        <span class="text-muted small">Các nhà sản xuất (Apple, Samsung, Xiaomi...) thiết bị công nghệ</span>
    </div>
    <div>
        <a href="<?= url('admin/brands/create') ?>" class="btn btn-danger text-white px-4 fw-semibold" style="border-radius:10px; background: var(--admin-primary, #E53935); border: none;">
            <i class="fas fa-plus me-1"></i>Thêm thương hiệu mới
        </a>
    </div>
</div>

<!-- Brands Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:70px;">Logo</th>
                    <th>Tên thương hiệu</th>
                    <th>Trang web chính thức</th>
                    <th>Quốc gia</th>
                    <th class="text-center">Thứ tự hiển thị</th>
                    <th class="text-center">Số sản phẩm</th>
                    <th class="text-center">Trạng thái</th>
                    <th style="width: 110px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($brands)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-tags fs-2 text-muted mb-2 d-block"></i>
                            <span class="text-muted small">Không tìm thấy thương hiệu nào</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($brands as $brand): ?>
                        <tr>
                            <td>
                                <img src="<?= productImage($brand['logo']) ?>" class="table-product-img" style="width:50px; height:30px; object-fit:contain; background:#F8F9FA; border-radius:4px;" alt="">
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b; font-size: 0.9rem;"><?= e($brand['name']) ?></div>
                                <span class="text-muted small" style="font-size:0.75rem;"><?= e(truncate($brand['description'], 40)) ?></span>
                            </td>
                            <td>
                                <?php if (!empty($brand['website'])): ?>
                                    <a href="<?= e($brand['website']) ?>" target="_blank" class="text-decoration-none text-primary small">
                                        <i class="fas fa-external-link-alt me-1"></i><?= e(str_replace(['https://','http://','www.'], '', $brand['website'])) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                            <td><span style="font-size:0.85rem;"><?= e($brand['country'] ?? 'N/A') ?></span></td>
                            <td class="text-center"><?= $brand['display_order'] ?></td>
                            <td class="text-center fw-bold text-dark"><?= number_format($brand['product_count'] ?? 0) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $brand['status'] === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> border" style="font-size:0.75rem; padding: 4px 10px; border-radius:12px;">
                                    <?= $brand['status'] === 'active' ? 'Kích hoạt' : 'Ẩn' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= url('admin/brands/edit/' . $brand['id']) ?>" class="btn-table-action edit" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn-table-action delete border-0 bg-transparent text-danger ms-1" title="Xóa" onclick="confirmDelete('<?= url('admin/brands/delete/' . $brand['id']) ?>', '<?= addslashes($brand['name']) ?>')">
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
