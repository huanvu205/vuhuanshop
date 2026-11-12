<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Chỉnh sửa mã giảm giá: <?= e($coupon['coupon_code']) ?></h4>
        <span class="text-muted small">Cập nhật thiết lập chiết khấu và giới hạn sử dụng cho mã giảm giá</span>
    </div>
    <div>
        <a href="<?= url('admin/coupons') ?>" class="btn btn-light border px-4 fw-semibold" style="border-radius:10px;">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-ticket-alt text-primary"></i> Thông tin mã giảm giá</h5>
            </div>
            <div class="admin-card-body p-4">
                <form action="<?= url('admin/coupons/edit/' . $coupon['id']) ?>" method="POST" id="couponForm">
                    <?= csrfToken() ?>

                    <div class="row g-3">
                        <!-- Code -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="coupon_code">Mã giảm giá (Code)</label>
                            <input type="text" id="coupon_code" class="form-control font-monospace text-uppercase" value="<?= e($coupon['coupon_code']) ?>" style="border-radius:8px; background:#f8fafc; cursor:not-allowed;" readonly>
                            <small class="text-muted" style="font-size:0.75rem;">Mã giảm giá không thể thay đổi sau khi tạo</small>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="status_select">Trạng thái phát hành</label>
                            <select id="status_select" name="status" class="form-select" style="border-radius:8px;">
                                <option value="active" <?= $coupon['status'] === 'active' ? 'selected' : '' ?>>Hiệu lực ngay (Active)</option>
                                <option value="inactive" <?= $coupon['status'] === 'inactive' ? 'selected' : '' ?>>Tạm tắt (Inactive)</option>
                            </select>
                        </div>

                        <!-- Discount Type -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="discount_type">Hình thức chiết khấu</label>
                            <select id="discount_type" name="discount_type" class="form-select" style="border-radius:8px;" onchange="toggleDiscountFields(this.value)">
                                <option value="percent" <?= $coupon['discount_type'] === 'percent' ? 'selected' : '' ?>>Giảm theo tỷ lệ phần trăm (%)</option>
                                <option value="fixed" <?= $coupon['discount_type'] === 'fixed' ? 'selected' : '' ?>>Giảm số tiền cố định (₫)</option>
                            </select>
                        </div>

                        <!-- Discount Value -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="discount_value">Giá trị giảm <span class="text-danger">*</span></label>
                            <input type="number" id="discount_value" name="discount_value" class="form-control" placeholder="Ví dụ: 10 hoặc 50000" value="<?= e($coupon['discount_value']) ?>" style="border-radius:8px;" required min="1">
                        </div>

                        <!-- Max Discount -->
                        <div class="col-md-6" id="max_discount_wrapper" style="display: <?= $coupon['discount_type'] === 'percent' ? 'block' : 'none' ?>;">
                            <label class="form-label fw-semibold text-dark small" for="max_discount">Mức giảm tối đa (₫)</label>
                            <input type="number" id="max_discount" name="max_discount" class="form-control" placeholder="Để trống nếu không giới hạn mức giảm" value="<?= e($coupon['max_discount'] ?? '') ?>" style="border-radius:8px;">
                            <small class="text-muted" style="font-size:0.75rem;">Có hiệu lực khi chọn giảm theo %</small>
                        </div>

                        <!-- Min Order Amount -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="min_order_amount">Giá trị đơn hàng tối thiểu (₫)</label>
                            <input type="number" id="min_order_amount" name="min_order_amount" class="form-control" placeholder="Ví dụ: 200000" value="<?= e($coupon['min_order_amount'] ?? '') ?>" style="border-radius:8px;">
                            <small class="text-muted" style="font-size:0.75rem;">Đơn hàng đạt giá trị này mới được áp dụng mã</small>
                        </div>

                        <!-- Max Uses -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="max_uses">Giới hạn số lần sử dụng (Lượt)</label>
                            <input type="number" id="max_uses" name="max_uses" class="form-control" placeholder="Để trống nếu không giới hạn lượt dùng" value="<?= e($coupon['max_uses'] ?? '') ?>" style="border-radius:8px;">
                            <small class="text-muted" style="font-size:0.75rem;">Tổng số lần mã này được sử dụng trên toàn hệ thống (Hiện đã dùng: <?= $coupon['used_count'] ?? 0 ?> lượt)</small>
                        </div>

                        <!-- Empty space for layout balance -->
                        <div class="col-md-6"></div>

                        <!-- Start Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="start_date">Ngày bắt đầu áp dụng <span class="text-danger">*</span></label>
                            <input type="date" id="start_date" name="start_date" class="form-control" value="<?= date('Y-m-d', strtotime($coupon['start_date'])) ?>" style="border-radius:8px;" required>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="end_date">Ngày hết hạn <span class="text-danger">*</span></label>
                            <input type="date" id="end_date" name="end_date" class="form-control" value="<?= date('Y-m-d', strtotime($coupon['end_date'])) ?>" style="border-radius:8px;" required>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="coupon_desc">Mô tả chương trình khuyến mãi</label>
                            <textarea id="coupon_desc" name="description" class="form-control" rows="3" placeholder="Ví dụ: Giảm ngay 10% tối đa 100k cho đơn hàng từ 500k..." style="border-radius:8px;"><?= e($coupon['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="<?= url('admin/coupons') ?>" class="btn btn-light border px-4" style="border-radius:8px;">Hủy bỏ</a>
                        <button type="submit" class="btn btn-danger px-4 fw-semibold" style="border-radius:8px; background:var(--admin-primary,#E53935); border:none;">
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDiscountFields(type) {
    const maxDiscountWrapper = document.getElementById('max_discount_wrapper');
    const maxDiscountInput = document.getElementById('max_discount');
    if (type === 'percent') {
        maxDiscountWrapper.style.display = 'block';
    } else {
        maxDiscountWrapper.style.display = 'none';
        maxDiscountInput.value = '';
    }
}

document.getElementById('couponForm').addEventListener('submit', function(e) {
    const start = new Date(document.getElementById('start_date').value);
    const end = new Date(document.getElementById('end_date').value);
    if (end < start) {
        e.preventDefault();
        alert('Ngày hết hạn không thể trước ngày bắt đầu!');
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
