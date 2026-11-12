<?php
/** @var array $banner */
$banner = $banner ?? [
    'id' => 0,
    'title' => '',
    'link' => '',
    'image' => '',
    'position' => 'home_slider',
    'display_order' => 0,
    'status' => 'active',
    'start_date' => null,
    'end_date' => null
];
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.image-preview-box {
    width: 250px;
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
    object-fit: cover;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Chỉnh sửa banner quảng cáo</h4>
        <span class="text-muted small">Cập nhật thiết lập và hình ảnh cho banner: <?= e($banner['title']) ?></span>
    </div>
    <div>
        <a href="<?= url('admin/banners') ?>" class="btn btn-light border px-4 fw-semibold" style="border-radius:10px;">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-image text-primary"></i> Thông tin banner</h5>
            </div>
            <div class="admin-card-body p-4">
                <form action="<?= url('admin/banners/edit/' . $banner['id']) ?>" method="POST" enctype="multipart/form-data">
                    <?= csrfToken() ?>

                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="banner_title">Tên / Tiêu đề banner <span class="text-danger">*</span></label>
                            <input type="text" id="banner_title" name="title" class="form-control" placeholder="Ví dụ: Banner Slider iPhone 15" value="<?= e($banner['title']) ?>" style="border-radius:8px;" required>
                        </div>

                        <!-- Link -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="banner_link">Đường dẫn liên kết khi Click (Link)</label>
                            <input type="url" id="banner_link" name="link" class="form-control" placeholder="Ví dụ: https://vuhuanshop.vn/products/iphone-15" value="<?= e($banner['link'] ?? '') ?>" style="border-radius:8px;">
                        </div>

                        <!-- Position -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="banner_position">Vị trí hiển thị <span class="text-danger">*</span></label>
                            <select id="banner_position" name="position" class="form-select" style="border-radius:8px;">
                                <option value="home_slider" <?= $banner['position'] === 'home_slider' ? 'selected' : '' ?>>1. Slider lớn bên trái trang chủ (Khuyên dùng: 800x400px hoặc 1200x500px, tỷ lệ 2:1)</option>
                                <option value="home_sidebar" <?= $banner['position'] === 'home_sidebar' ? 'selected' : '' ?>>2. Hai banner phụ bên phải Slider (Khuyên dùng: 400x192px hoặc 600x300px, tỷ lệ 2:1)</option>
                                <option value="promo_middle" <?= $banner['position'] === 'promo_middle' ? 'selected' : '' ?>>3. Banner ngang lớn giữa trang (Khuyên dùng: 1200x250px)</option>
                                <option value="popup_center" <?= $banner['position'] === 'popup_center' ? 'selected' : '' ?>>4. Popup ảnh giữa màn hình (Khuyên dùng: ảnh dọc hoặc vuông, tối đa rộng 520px)</option>
                            </select>
                            <small class="text-muted" style="font-size:0.75rem;">Chọn đúng vị trí để banner xuất hiện ở khu vực tương ứng ngoài trang chủ</small>
                        </div>

                        <!-- Display Order -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="display_order">Thứ tự hiển thị</label>
                            <input type="number" id="display_order" name="display_order" class="form-control" placeholder="0" value="<?= e($banner['display_order']) ?>" style="border-radius:8px;">
                            <small class="text-muted" style="font-size:0.75rem;">Số nhỏ hiển thị trước</small>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="status_select">Trạng thái phát hành</label>
                            <select id="status_select" name="status" class="form-select" style="border-radius:8px;">
                                <option value="active" <?= $banner['status'] === 'active' ? 'selected' : '' ?>>Hiển thị hoạt động (Active)</option>
                                <option value="inactive" <?= $banner['status'] === 'inactive' ? 'selected' : '' ?>>Tạm ngưng ẩn đi (Inactive)</option>
                            </select>
                        </div>

                        <!-- Empty column for layout balance -->
                        <div class="col-md-6"></div>

                        <!-- Start Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="start_date">Ngày bắt đầu hiển thị</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" value="<?= !empty($banner['start_date']) ? date('Y-m-d', strtotime($banner['start_date'])) : '' ?>" style="border-radius:8px;">
                            <small class="text-muted" style="font-size:0.75rem;">Để trống nếu muốn hiển thị ngay lập tức</small>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="end_date">Ngày kết thúc hiển thị</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" value="<?= !empty($banner['end_date']) ? date('Y-m-d', strtotime($banner['end_date'])) : '' ?>" style="border-radius:8px;">
                            <small class="text-muted" style="font-size:0.75rem;">Để trống nếu muốn hiển thị vô hạn</small>
                        </div>

                        <!-- Image File -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small">Hình ảnh banner quảng cáo hiện tại</label>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="image-preview-box">
                                    <img id="banner-preview" src="<?= productImage($banner['image']) ?>" alt="Preview">
                                </div>
                                <div class="flex-grow-1" style="min-width: 250px;">
                                    <input type="file" id="banner_image" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'banner-preview')">
                                    <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Chọn file mới nếu muốn thay hình ảnh hiện tại (tối đa 2MB).</small>
                                    <div class="alert alert-light border py-2 px-3 mt-2 mb-0" style="font-size:0.75rem; border-radius:8px;">
                                        <i class="fas fa-info-circle text-primary me-1"></i> <strong>Kích thước chuẩn (tỷ lệ 2:1):</strong>
                                        <ul class="mb-0 ps-3 mt-1">
                                            <li><strong>Slider chính bên trái:</strong> <code>800 x 400 px</code> hoặc <code>1200 x 500 px</code></li>
                                            <li><strong>2 Banner phụ bên phải:</strong> <code>400 x 192 px</code> hoặc <code>600 x 300 px</code></li>
                                            <li><strong>Banner giữa trang:</strong> <code>1200 x 250 px</code></li>
                                        </ul>
                                        <span class="text-secondary mt-1 d-block"><i>* Giao diện đã cài tự động co giãn (`object-fit: cover`) nên ảnh sẽ không bao giờ bị méo hay vỡ khung.</i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="<?= url('admin/banners') ?>" class="btn btn-light border px-4" style="border-radius:8px;">Hủy bỏ</a>
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
