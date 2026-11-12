<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Stats header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Quản lý danh mục</h4>
        <span class="text-muted small">Cơ cấu ngành hàng (Điện thoại, Laptop, Phụ kiện...) và danh mục con</span>
    </div>
    <div>
        <a href="<?= url('admin/categories/create') ?>" class="btn btn-danger text-white px-4 fw-semibold" style="border-radius:10px; background: var(--admin-primary, #E53935); border: none;">
            <i class="fas fa-plus me-1"></i>Thêm danh mục mới
        </a>
    </div>
</div>

<!-- Search box -->
<div class="admin-card mb-4">
    <div class="admin-card-body p-3">
        <form method="GET" action="<?= url('admin/categories') ?>" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Tìm tên danh mục..." value="<?= e($search) ?>" style="font-size:0.88rem;">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-danger w-100 fw-semibold" style="border-radius:8px; background:var(--admin-primary,#E53935); font-size: 0.85rem; border:none;">
                    Tìm kiếm
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Categories Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:70px;">Hình ảnh</th>
                    <th>Tên danh mục</th>
                    <th>Đường dẫn tĩnh (Slug)</th>
                    <th>Danh mục cha</th>
                    <th class="text-center">Thứ tự hiển thị</th>
                    <th class="text-center">Số sản phẩm</th>
                    <th class="text-center">Trạng thái</th>
                    <th style="width: 110px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-th-large fs-2 text-muted mb-2 d-block"></i>
                            <span class="text-muted small">Không tìm thấy danh mục nào</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>
                                <img src="<?= categoryImage($cat['image']) ?>" class="table-product-img" style="width:40px; height:40px; object-fit:contain; background:#F8F9FA; border-radius:6px;" alt="">
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b; font-size: 0.9rem;"><?= e($cat['name']) ?></div>
                                <span class="text-muted small" style="font-size:0.75rem;"><?= e(truncate($cat['description'], 40)) ?></span>
                            </td>
                            <td><code style="font-size:0.8rem;"><?= e($cat['slug']) ?></code></td>
                            <td>
                                <?= !empty($cat['parent_name']) ? '<span class="badge bg-light text-dark border">' . e($cat['parent_name']) . '</span>' : '<span class="text-muted small">— (Cấp cha)</span>' ?>
                            </td>
                            <td class="text-center"><?= $cat['display_order'] ?></td>
                            <td class="text-center fw-bold text-dark"><?= number_format($cat['product_count'] ?? 0) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $cat['status'] === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> border" style="font-size:0.75rem; padding: 4px 10px; border-radius:12px;">
                                    <?= $cat['status'] === 'active' ? 'Hiển thị' : 'Ẩn' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= url('admin/categories/edit/' . $cat['id']) ?>" class="btn-table-action edit" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn-table-action delete border-0 bg-transparent text-danger ms-1" title="Xóa" onclick="confirmDelete('<?= url('admin/categories/delete/' . $cat['id']) ?>', '<?= addslashes($cat['name']) ?>')">
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
