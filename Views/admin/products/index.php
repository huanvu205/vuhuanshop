<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Stats header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Danh sách sản phẩm</h4>
        <span class="text-muted small">Quản lý kho hàng, giá bán và thông số kỹ thuật của từng sản phẩm</span>
    </div>
    <div>
        <a href="<?= url('admin/products/create') ?>" class="btn btn-danger text-white px-4 fw-semibold" style="border-radius:10px; background: var(--admin-primary, #E53935); border: none;">
            <i class="fas fa-plus me-1"></i>Thêm sản phẩm mới
        </a>
    </div>
</div>

<!-- Filter card -->
<div class="admin-card mb-4">
    <div class="admin-card-body p-3">
        <form method="GET" action="<?= url('admin/products') ?>" class="row g-2">
            <!-- Search -->
            <div class="col-md-3 col-sm-6">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Tên hoặc mã sản phẩm..." value="<?= e($search) ?>" style="font-size:0.88rem;">
                </div>
            </div>
            
            <!-- Category -->
            <div class="col-md-3 col-sm-6">
                <select name="category" class="form-select bg-light" style="font-size:0.88rem;">
                    <option value="">-- Tất cả danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $filterCategory == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Brand -->
            <div class="col-md-2 col-sm-6">
                <select name="brand" class="form-select bg-light" style="font-size:0.88rem;">
                    <option value="">-- Tất cả thương hiệu --</option>
                    <?php foreach ($brands as $br): ?>
                        <option value="<?= $br['id'] ?>" <?= $filterBrand == $br['id'] ? 'selected' : '' ?>><?= e($br['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status -->
            <div class="col-md-2 col-sm-6">
                <select name="status" class="form-select bg-light" style="font-size:0.88rem;">
                    <option value="">-- Trạng thái --</option>
                    <option value="active" <?= $filterStatus === 'active' ? 'selected' : '' ?>>Đang bán</option>
                    <option value="inactive" <?= $filterStatus === 'inactive' ? 'selected' : '' ?>>Ngừng bán</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="col-md-2 col-sm-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger w-100 fw-semibold" style="border-radius:8px; background:var(--admin-primary,#E53935); font-size: 0.85rem; border:none;">
                        Lọc kết quả
                    </button>
                    <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary" title="Xóa bộ lọc" style="border-radius:8px; font-size: 0.85rem;">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Hình ảnh</th>
                    <th>Thông tin sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Thương hiệu</th>
                    <th class="text-end">Giá bán</th>
                    <th class="text-center">Tồn kho</th>
                    <th class="text-center">Trạng thái</th>
                    <th style="width: 110px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-box-open fs-2 text-muted mb-2 d-block"></i>
                            <span class="text-muted small">Không tìm thấy sản phẩm nào phù hợp</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <img src="<?= productImage($product['main_image']) ?>" class="table-product-img" style="width:50px; height:50px; object-fit:contain; background:#F8F9FA; border-radius:6px;" alt="">
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b; font-size: 0.9rem;"><?= e($product['name']) ?></div>
                                <span class="badge bg-light text-dark border small mt-1" style="font-size:0.75rem;">Mã: <?= e($product['product_code']) ?></span>
                            </td>
                            <td><span style="font-size:0.85rem;"><?= e($product['category_name']) ?></span></td>
                            <td><span style="font-size:0.85rem;"><?= e($product['brand_name']) ?></span></td>
                            <td class="text-end">
                                <?php if (!empty($product['price_discount'])): ?>
                                    <div style="font-weight:700; color:#E53935; font-size:0.9rem;"><?= formatPrice($product['price_discount']) ?></div>
                                    <div class="text-muted text-decoration-line-through" style="font-size:0.75rem;"><?= formatPrice($product['price_sale']) ?></div>
                                <?php else: ?>
                                    <div style="font-weight:700; color:#1e293b; font-size:0.9rem;"><?= formatPrice($product['price_sale']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span style="font-weight:700; color: <?= $product['stock_quantity'] <= 5 ? '#E53935' : '#1e293b' ?>;">
                                    <?= $product['stock_quantity'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $product['status'] === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> border" style="font-size:0.75rem; padding: 4px 10px; border-radius: 12px;">
                                    <?= $product['status'] === 'active' ? 'Đang bán' : 'Ngừng bán' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= url('admin/products/edit/' . $product['id']) ?>" class="btn-table-action edit" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn-table-action delete border-0 bg-transparent text-danger ms-1" title="Xóa sản phẩm" onclick="confirmDelete('<?= url('admin/products/delete/' . $product['id']) ?>', '<?= addslashes($product['name']) ?>')">
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
        $currentUrl = url('admin/products');
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
