<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
.image-preview-box {
    width: 120px;
    height: 70px;
    border: 2px dashed #cbd5e1;
    border-radius: 8px;
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
        <h4 class="mb-0 text-dark fw-bold">Thêm thương hiệu mới</h4>
        <span class="text-muted small">Đăng ký thêm thương hiệu sản xuất mới vào hệ thống</span>
    </div>
    <div>
        <a href="<?= url('admin/brands') ?>" class="btn btn-light border px-4 fw-semibold" style="border-radius:10px;">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-edit text-primary"></i> Thông tin thương hiệu</h5>
            </div>
            <div class="admin-card-body p-4">
                <form action="<?= url('admin/brands/create') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrfToken() ?>

                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="brand_name">Tên thương hiệu <span class="text-danger">*</span></label>
                            <input type="text" id="brand_name" name="name" class="form-control" placeholder="Ví dụ: Apple, Samsung..." style="border-radius:8px;" required>
                        </div>

                        <!-- Country -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="country">Quốc gia xuất xứ</label>
                            <input type="text" id="country" name="country" class="form-control" placeholder="Ví dụ: Mỹ, Hàn Quốc, Trung Quốc..." style="border-radius:8px;">
                        </div>

                        <!-- Website -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="website">Website chính thức</label>
                            <input type="url" id="website" name="website" class="form-control" placeholder="Ví dụ: https://apple.com" style="border-radius:8px;">
                        </div>

                        <!-- Display Order -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="display_order">Thứ tự hiển thị</label>
                            <input type="number" id="display_order" name="display_order" class="form-control" placeholder="0" value="0" style="border-radius:8px;">
                            <small class="text-muted" style="font-size:0.75rem;">Số nhỏ hơn hiển thị trước</small>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="status_select">Trạng thái hoạt động</label>
                            <select id="status_select" name="status" class="form-select" style="border-radius:8px;">
                                <option value="active">Kích hoạt (Active)</option>
                                <option value="inactive">Tạm ngưng (Inactive)</option>
                            </select>
                        </div>

                        <!-- Logo -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small">Logo thương hiệu</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="image-preview-box">
                                    <img id="logo-preview" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiB2aWV3Qm94PSIwIDAgNDAwIDQwMCI+PHJlY3Qgd2lkdGg9IjQwMCIgaGVpZ2h0PSI0MDAiIGZpbGw9IiNmNWY1ZjUiLz48Y2lyY2xlIGN4PSIyMDAiIGN5PSIxNjAiIHI9IjUwIiBmaWxsPSIjZTBlMGUwIi8+PGNpcmNsZSBjeD0iMjAwIiBjeT0iMTYwIiByPSIzMCIgZmlsbD0iIzk5OTk5OSIvPjxwYXRoIGQ9Ik0xNDAgMjIwSDI2MlYyODBoLTEyMlYyMjB6IiBmaWxsPSIjOTk5OTk5Ii8+PC9zdmc+" alt="Preview">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" id="logo_image" name="logo" class="form-control" accept="image/*" onchange="previewImage(this, 'logo-preview')">
                                    <small class="text-muted" style="font-size:0.75rem;">Chọn file logo PNG, JPG hoặc WebP. Tối đa 2MB.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="brand_desc">Mô tả tóm tắt thương hiệu</label>
                            <textarea id="brand_desc" name="description" class="form-control" rows="4" placeholder="Nhập một vài nét sơ lược về thương hiệu..." style="border-radius:8px;"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="<?= url('admin/brands') ?>" class="btn btn-light border px-4" style="border-radius:8px;">Hủy bỏ</a>
                        <button type="submit" class="btn btn-danger px-4 fw-semibold" style="border-radius:8px; background:var(--admin-primary,#E53935); border:none;">
                            Tạo thương hiệu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
