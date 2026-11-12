<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
.spec-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}
.spec-row input {
    flex: 1;
}
.btn-remove-spec {
    background: #FFEBEE;
    color: #E53935;
    border: none;
    width: 38px;
    height: 38px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.btn-remove-spec:hover {
    background: #E53935;
    color: white;
}
.image-preview-box {
    width: 120px;
    height: 120px;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background-color: #f8fafc;
    margin-bottom: 12px;
}
.image-preview-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Thêm sản phẩm mới</h4>
        <span class="text-muted small">Cung cấp đầy đủ thông tin để đăng bán sản phẩm mới lên website</span>
    </div>
    <div>
        <a href="<?= url('admin/products') ?>" class="btn btn-light border px-4 fw-semibold" style="border-radius:10px;">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger border-0 p-3 rounded-3 mb-4">
        <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-circle me-1"></i>Vui lòng sửa các lỗi sau:</h6>
        <ul class="mb-0 small ps-3">
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= url('admin/products/create') ?>" method="POST" enctype="multipart/form-data">
    <?= csrfToken() ?>

    <div class="row g-4">
        <!-- Main Form (Left) -->
        <div class="col-lg-8">
            <!-- Basic Info Card -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title"><i class="fas fa-info-circle text-primary"></i> Thông tin cơ bản</h5>
                </div>
                <div class="admin-card-body p-4">
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="product_name">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" id="product_name" name="name" class="form-control form-control-lg" placeholder="Ví dụ: iPhone 15 Pro Max 256GB" value="<?= e($old['name'] ?? '') ?>" style="border-radius:10px;" required>
                        </div>

                        <!-- Code -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="product_code">Mã sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" id="product_code" name="product_code" class="form-control" placeholder="Ví dụ: IP15PM-256" value="<?= e($old['product_code'] ?? '') ?>" style="border-radius:8px;" required>
                        </div>

                        <!-- Stock -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="stock_quantity">Số lượng tồn kho ban đầu</label>
                            <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" placeholder="0" min="0" value="<?= e($old['stock_quantity'] ?? '0') ?>" style="border-radius:8px;">
                        </div>

                        <!-- Short Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="short_description">Mô tả ngắn (Hiển thị ở trang danh sách)</label>
                            <textarea id="short_description" name="short_description" class="form-control" rows="3" placeholder="Nhập tóm tắt nhanh về sản phẩm..." style="border-radius:8px;"><?= e($old['short_description'] ?? '') ?></textarea>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="description">Mô tả chi tiết sản phẩm</label>
                            <textarea id="description" name="description" class="form-control" rows="8" placeholder="Viết mô tả đầy đủ thông tin sản phẩm..." style="border-radius:8px;"><?= e($old['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Specs Card -->
            <div class="admin-card mb-4">
                <div class="admin-card-header d-flex justify-content-between align-items-center">
                    <h5 class="admin-card-title mb-0"><i class="fas fa-sliders-h text-primary"></i> Thông số kỹ thuật</h5>
                    <button type="button" class="btn btn-outline-danger btn-sm fw-semibold" style="border-radius:8px;" onclick="addSpec()">
                        <i class="fas fa-plus me-1"></i>Thêm thông số
                    </button>
                </div>
                <div class="admin-card-body p-4" id="specs-container">
                    <?php if (!empty($old['spec_name'])): ?>
                        <?php foreach ($old['spec_name'] as $i => $sName): ?>
                            <div class="spec-row">
                                <input type="text" name="spec_name[]" class="form-control" placeholder="Tên thông số (VD: CPU)" value="<?= e($sName) ?>">
                                <input type="text" name="spec_value[]" class="form-control" placeholder="Giá trị (VD: Apple A17 Pro)" value="<?= e($old['spec_value'][$i] ?? '') ?>">
                                <button type="button" class="btn-remove-spec" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Sample row -->
                        <div class="spec-row">
                            <input type="text" name="spec_name[]" class="form-control" placeholder="Tên thông số (VD: Màn hình)">
                            <input type="text" name="spec_value[]" class="form-control" placeholder="Giá trị (VD: OLED 6.7 inch)">
                            <button type="button" class="btn-remove-spec" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Variants Card (Dung lượng & Màu sắc) -->
            <div class="admin-card mb-4">
                <div class="admin-card-header d-flex justify-content-between align-items-center">
                    <h5 class="admin-card-title mb-0">
                        <i class="fas fa-cubes text-primary"></i> Biến thể sản phẩm (Dung lượng & Màu sắc)
                    </h5>
                </div>
                <div class="admin-card-body p-4">
                    <!-- Capacities -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-dark small mb-0">
                                <i class="fas fa-microchip me-1 text-danger"></i> Các phiên bản bộ nhớ / dung lượng
                            </label>
                            <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" onclick="addVariantCapacity()" style="border-radius:8px;">
                                <i class="fas fa-plus me-1"></i>Thêm phiên bản
                            </button>
                        </div>
                        <small class="text-muted d-block mb-3">Nhập tên phiên bản và số tiền chênh lệch cộng thêm so với giá bán gốc (nhập 0 nếu là bản tiêu chuẩn).</small>
                        <div id="variant-capacity-container">
                            <div class="d-flex gap-2 mb-2 align-items-center variant-row">
                                <input type="text" name="variant_capacity_name[]" class="form-control" placeholder="Tên phiên bản (VD: 128GB Tiêu chuẩn)" value="Tiêu chuẩn" style="border-radius:8px;">
                                <input type="number" name="variant_capacity_diff[]" class="form-control" placeholder="Giá cộng thêm (₫)" value="0" style="border-radius:8px;">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()" style="border-radius:8px;"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Colors -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-dark small mb-0">
                                <i class="fas fa-palette me-1 text-danger"></i> Các màu sắc sản phẩm
                            </label>
                            <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" onclick="addVariantColor()" style="border-radius:8px;">
                                <i class="fas fa-plus me-1"></i>Thêm màu sắc
                            </button>
                        </div>
                        <small class="text-muted d-block mb-3">Nhập tên màu sắc và chọn màu đại diện hiển thị.</small>
                        <div id="variant-color-container">
                            <div class="d-flex gap-2 mb-2 align-items-center variant-row">
                                <input type="text" name="variant_color_name[]" class="form-control" placeholder="Tên màu (VD: Đen)" value="Đen" style="border-radius:8px;">
                                <input type="color" name="variant_color_hex[]" class="form-control form-control-color" value="#222222" title="Chọn mã màu" style="width:50px; height:38px; border-radius:8px; padding:2px;">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()" style="border-radius:8px;"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Form (Right) -->
        <div class="col-lg-4">
            <!-- Taxonomy Card -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title"><i class="fas fa-tag text-primary"></i> Phân loại & Tổ chức</h5>
                </div>
                <div class="admin-card-body p-4">
                    <div class="row g-3">
                        <!-- Category -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="category_id">Danh mục <span class="text-danger">*</span></label>
                            <select id="category_id" name="category_id" class="form-select" style="border-radius:8px;" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($old['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Brand -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="brand_id">Thương hiệu <span class="text-danger">*</span></label>
                            <select id="brand_id" name="brand_id" class="form-select" style="border-radius:8px;" required>
                                <option value="">-- Chọn thương hiệu --</option>
                                <?php foreach ($brands as $br): ?>
                                    <option value="<?= $br['id'] ?>" <?= ($old['brand_id'] ?? '') == $br['id'] ? 'selected' : '' ?>><?= e($br['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price Card -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title"><i class="fas fa-wallet text-primary"></i> Giá sản phẩm</h5>
                </div>
                <div class="admin-card-body p-4">
                    <div class="row g-3">
                        <!-- Price cost -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="price_cost">Giá nhập kho (₫)</label>
                            <input type="number" id="price_cost" name="price_cost" class="form-control" placeholder="0" min="0" value="<?= e($old['price_cost'] ?? '') ?>" style="border-radius:8px;">
                        </div>

                        <!-- Price sale -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="price_sale">Giá bán niêm yết (₫) <span class="text-danger">*</span></label>
                            <input type="number" id="price_sale" name="price_sale" class="form-control" placeholder="0" min="0" value="<?= e($old['price_sale'] ?? '') ?>" style="border-radius:8px;" required>
                        </div>

                        <!-- Price discount -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="price_discount">Giá khuyến mãi (₫)</label>
                            <input type="number" id="price_discount" name="price_discount" class="form-control" placeholder="Để trống nếu không giảm giá" min="0" value="<?= e($old['price_discount'] ?? '') ?>" style="border-radius:8px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Card -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title"><i class="fas fa-image text-primary"></i> Hình ảnh</h5>
                </div>
                <div class="admin-card-body p-4">
                    <!-- Main image preview -->
                    <div class="image-preview-box">
                        <img id="main-preview" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiB2aWV3Qm94PSIwIDAgNDAwIDQwMCI+PHJlY3Qgd2lkdGg9IjQwMCIgaGVpZ2h0PSI0MDAiIGZpbGw9IiNmNWY1ZjUiLz48Y2lyY2xlIGN4PSIyMDAiIGN5PSIxNjAiIHI9IjUwIiBmaWxsPSIjZTBlMGUwIi8+PGNpcmNsZSBjeD0iMjAwIiBjeT0iMTYwIiByPSIzMCIgZmlsbD0iIzk5OTk5OSIvPjxwYXRoIGQ9Ik0xNDAgMjIwSDI2MlYyODBoLTEyMlYyMjB6IiBmaWxsPSIjOTk5OTk5Ii8+PC9zdmc+" alt="Preview">
                    </div>
                    <!-- Main Image input -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small" for="main_image">Ảnh đại diện chính <span class="text-danger">*</span></label>
                        <input type="file" id="main_image" name="main_image" class="form-control" accept="image/*" onchange="previewImage(this, 'main-preview')" required>
                    </div>

                    <!-- Sub Images input -->
                    <div>
                        <label class="form-label fw-semibold text-dark small" for="sub_images">Ảnh chi tiết phụ (Chọn nhiều ảnh)</label>
                        <input type="file" id="sub_images" name="sub_images[]" class="form-control" accept="image/*" multiple>
                    </div>
                </div>
            </div>

            <!-- Settings Card -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title"><i class="fas fa-cog text-primary"></i> Thiết lập & Đăng bán</h5>
                </div>
                <div class="admin-card-body p-4">
                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small" for="status_select">Trạng thái bán</label>
                        <select id="status_select" name="status" class="form-select" style="border-radius:8px;">
                            <option value="active" <?= ($old['status'] ?? '') === 'active' ? 'selected' : '' ?>>Kích hoạt (Hiển thị ngay)</option>
                            <option value="inactive" <?= ($old['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Nháp (Tạm ẩn)</option>
                        </select>
                    </div>

                    <!-- Featured checkbox -->
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= !empty($old['is_featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label text-dark small fw-semibold" for="is_featured" style="cursor:pointer;">Sản phẩm nổi bật</label>
                    </div>

                    <!-- Hot Deal checkbox -->
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="is_hot_deal" name="is_hot_deal" value="1" <?= !empty($old['is_hot_deal']) ? 'checked' : '' ?>>
                        <label class="form-check-label text-dark small fw-semibold" for="is_hot_deal" style="cursor:pointer;">Hot Deal / Flash Sale hôm nay</label>
                    </div>

                    <!-- Best Selling checkbox -->
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="is_best_selling" name="is_best_selling" value="1" <?= !empty($old['is_best_selling']) ? 'checked' : '' ?>>
                        <label class="form-check-label text-dark small fw-semibold" for="is_best_selling" style="cursor:pointer;">Bán chạy nhất</label>
                    </div>

                    <!-- Newest checkbox -->
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="is_newest" name="is_newest" value="1" <?= !empty($old['is_newest']) ? 'checked' : '' ?>>
                        <label class="form-check-label text-dark small fw-semibold" for="is_newest" style="cursor:pointer;">Sản phẩm mới nhất</label>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2.5 fw-bold" style="border-radius:10px; background:var(--admin-primary,#E53935); border:none;">
                        <i class="fas fa-save me-1"></i>Lưu và Đăng bán
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
