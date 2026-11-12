<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
.image-preview-box {
    width: 200px;
    height: 110px;
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
        <h4 class="mb-0 text-dark fw-bold">Chỉnh sửa bài viết</h4>
        <span class="text-muted small">Cập nhật nội dung và chuyên mục của bài viết</span>
    </div>
    <div>
        <a href="<?= url('admin/news') ?>" class="btn btn-light border px-4 fw-semibold" style="border-radius:10px;">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<form action="<?= url('admin/news/edit/' . $news['id']) ?>" method="POST" enctype="multipart/form-data">
    <?= csrfToken() ?>

    <div class="row g-4">
        <!-- Main editor content (Left) -->
        <div class="col-lg-8">
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title"><i class="fas fa-edit text-primary"></i> Nội dung bài viết</h5>
                </div>
                <div class="admin-card-body p-4">
                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="news_title">Tiêu đề bài viết <span class="text-danger">*</span></label>
                            <input type="text" id="news_title" name="title" class="form-control form-control-lg" placeholder="Nhập tiêu đề hấp dẫn..." value="<?= e($news['title']) ?>" style="border-radius:10px;" required>
                        </div>

                        <!-- Excerpt -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="news_excerpt">Tóm tắt ngắn (Excerpt) <span class="text-danger">*</span></label>
                            <textarea id="news_excerpt" name="excerpt" class="form-control" rows="3" placeholder="Nhập một đoạn tóm tắt ngắn..." style="border-radius:8px;" required><?= e($news['excerpt']) ?></textarea>
                        </div>

                        <!-- Content -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="news_content">Nội dung chi tiết bài viết <span class="text-danger">*</span></label>
                            <textarea id="news_content" name="content" class="form-control" rows="15" placeholder="Soạn thảo nội dung bài viết..." style="border-radius:8px;" required><?= e($news['content']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta settings Sidebar (Right) -->
        <div class="col-lg-4">
            <!-- Classification card -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title"><i class="fas fa-folder text-primary"></i> Phân mục & Đăng tải</h5>
                </div>
                <div class="admin-card-body p-4">
                    <div class="row g-3">
                        <!-- Category -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="news_category">Chuyên mục bài viết</label>
                            <select id="news_category" name="category" class="form-select" style="border-radius:8px;">
                                <option value="cong-nghe" <?= $news['category'] === 'cong-nghe' ? 'selected' : '' ?>>Tin Công nghệ (Technology)</option>
                                <option value="danh-gia" <?= $news['category'] === 'danh-gia' ? 'selected' : '' ?>>Đánh giá sản phẩm (Reviews)</option>
                                <option value="khuyen-mai" <?= $news['category'] === 'khuyen-mai' ? 'selected' : '' ?>>Khuyến mãi cực hot (Promotions)</option>
                                <option value="meo-vat" <?= $news['category'] === 'meo-vat' ? 'selected' : '' ?>>Mẹo vặt công nghệ (Tips & Tricks)</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="news_status">Trạng thái xuất bản</label>
                            <select id="news_status" name="status" class="form-select" style="border-radius:8px;">
                                <option value="draft" <?= $news['status'] === 'draft' ? 'selected' : '' ?>>Bản nháp (Draft - Lưu tạm ẩn)</option>
                                <option value="published" <?= $news['status'] === 'published' ? 'selected' : '' ?>>Xuất bản (Published - Hiển thị ngay)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Cover card -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title"><i class="fas fa-image text-primary"></i> Ảnh đại diện bài viết</h5>
                </div>
                <div class="admin-card-body p-4">
                    <label class="form-label fw-semibold text-dark small">Ảnh đại diện hiện tại</label>
                    <div class="image-preview-box mx-auto">
                        <img id="news-preview" src="<?= productImage($news['featured_image']) ?>" alt="Preview">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small" for="featured_image">Thay thế ảnh đại diện mới</label>
                        <input type="file" id="featured_image" name="featured_image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImage(this, 'news-preview')" <?= empty($news['featured_image']) ? 'required' : '' ?>>
                        <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Ảnh sẽ hiển thị trọn vẹn, không bị cắt. Tỷ lệ 16:9 (khoảng 800x450px) giúp ảnh lấp khung đẹp nhất; để trống nếu muốn giữ ảnh hiện tại.</small>
                    </div>
                </div>
            </div>

            <!-- Submit action -->
            <div class="admin-card">
                <div class="admin-card-body p-4">
                    <button type="submit" class="btn btn-danger w-100 py-2.5 fw-bold" style="border-radius:10px; background:var(--admin-primary,#E53935); border:none;">
                        <i class="fas fa-save me-1"></i>Cập nhật bài viết
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
