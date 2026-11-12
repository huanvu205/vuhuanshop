<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
.image-preview-box {
    width: 100px;
    height: 100px;
    border: 1px solid #e2e8f0;
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
        <h4 class="mb-0 text-dark fw-bold">Chỉnh sửa danh mục</h4>
        <span class="text-muted small">Cập nhật thông tin chi tiết và cơ cấu của danh mục</span>
    </div>
    <div>
        <a href="<?= url('admin/categories') ?>" class="btn btn-light border px-4 fw-semibold" style="border-radius:10px;">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-edit text-primary"></i> Thông tin danh mục: <?= e($category['name']) ?></h5>
            </div>
            <div class="admin-card-body p-4">
                <form action="<?= url('admin/categories/edit/' . $category['id']) ?>" method="POST" enctype="multipart/form-data">
                    <?= csrfToken() ?>

                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="cat_name">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" id="cat_name" name="name" class="form-control" placeholder="Ví dụ: Điện thoại" value="<?= e($category['name']) ?>" style="border-radius:8px;" required>
                        </div>

                        <!-- Parent Category -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="parent_id">Danh mục cha (Nếu có)</label>
                            <select id="parent_id" name="parent_id" class="form-select" style="border-radius:8px;">
                                <option value="">-- Không có (Đây là danh mục cha) --</option>
                                <?php foreach ($categories as $parent): ?>
                                    <?php if ($parent['id'] != $category['id']): ?>
                                        <option value="<?= $parent['id'] ?>" <?= $category['parent_id'] == $parent['id'] ? 'selected' : '' ?>><?= e($parent['name']) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Display Order -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="display_order">Thứ tự hiển thị</label>
                            <input type="number" id="display_order" name="display_order" class="form-control" placeholder="0" value="<?= e($category['display_order']) ?>" style="border-radius:8px;">
                            <small class="text-muted" style="font-size:0.75rem;">Số nhỏ hơn sẽ hiển thị trước</small>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="status_select">Trạng thái hiển thị</label>
                            <select id="status_select" name="status" class="form-select" style="border-radius:8px;">
                                <option value="active" <?= $category['status'] === 'active' ? 'selected' : '' ?>>Hiển thị (Active)</option>
                                <option value="inactive" <?= $category['status'] === 'inactive' ? 'selected' : '' ?>>Tạm ẩn (Inactive)</option>
                            </select>
                        </div>

                        <!-- Image Cover -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small">Hình ảnh đại diện danh mục</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="image-preview-box">
                                    <img id="cat-preview" src="<?= categoryImage($category['image']) ?>" alt="Preview">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" id="cat_image" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'cat-preview')">
                                    <small class="text-muted" style="font-size:0.75rem;">Chọn file mới nếu muốn thay đổi ảnh đại diện hiện tại.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="cat_description">Mô tả ngắn danh mục</label>
                            <textarea id="cat_description" name="description" class="form-control" rows="4" placeholder="Nhập một vài thông tin mô tả danh mục..." style="border-radius:8px;"><?= e($category['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="<?= url('admin/categories') ?>" class="btn btn-light border px-4" style="border-radius:8px;">Hủy bỏ</a>
                        <button type="submit" class="btn btn-danger px-4 fw-semibold" style="border-radius:8px; background:var(--admin-primary,#E53935); border:none;">
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
