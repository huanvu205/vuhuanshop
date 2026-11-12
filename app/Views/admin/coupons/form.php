<?php require_once __DIR__ . '/../_shared.php'; adminHeader(empty($coupon) ? 'Tao ma giam gia' : 'Sua ma giam gia', 'Thiet lap dieu kien ap dung'); ?>
<form method="post" class="admin-card"><div class="admin-card-body row g-3"><?= csrfToken() ?>
<div class="col-md-4"><label class="form-label">Ma giam gia</label><input name="coupon_code" class="form-control" <?= empty($coupon) ? 'required' : 'readonly' ?> value="<?= e($coupon['coupon_code'] ?? '') ?>"></div>
<div class="col-md-4"><label class="form-label">Loai</label><select name="discount_type" class="form-select"><option value="percent" <?= ($coupon['discount_type'] ?? 'percent') === 'percent' ? 'selected' : '' ?>>Phan tram</option><option value="fixed" <?= ($coupon['discount_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>So tien</option></select></div>
<div class="col-md-4"><label class="form-label">Gia tri</label><input type="number" step="0.01" name="discount_value" class="form-control" required value="<?= e($coupon['discount_value'] ?? 0) ?>"></div>
<div class="col-md-3"><label class="form-label">Giam toi da</label><input type="number" name="max_discount" class="form-control" value="<?= e($coupon['max_discount'] ?? '') ?>"></div>
<div class="col-md-3"><label class="form-label">Don toi thieu</label><input type="number" name="min_order_amount" class="form-control" value="<?= e($coupon['min_order_amount'] ?? '') ?>"></div>
<div class="col-md-3"><label class="form-label">So luot dung</label><input type="number" name="max_uses" class="form-control" value="<?= e($coupon['max_uses'] ?? '') ?>"></div>
<div class="col-md-3"><label class="form-label">Trang thai</label><select name="status" class="form-select"><option value="active" <?= ($coupon['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= ($coupon['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
<div class="col-md-6"><label class="form-label">Ngay bat dau</label><input type="datetime-local" name="start_date" class="form-control" value="<?= !empty($coupon['start_date']) ? date('Y-m-d\TH:i', strtotime($coupon['start_date'])) : '' ?>"></div>
<div class="col-md-6"><label class="form-label">Ngay ket thuc</label><input type="datetime-local" name="end_date" class="form-control" value="<?= !empty($coupon['end_date']) ? date('Y-m-d\TH:i', strtotime($coupon['end_date'])) : '' ?>"></div>
<div class="col-12"><label class="form-label">Mo ta</label><textarea name="description" class="form-control" rows="3"><?= e($coupon['description'] ?? '') ?></textarea></div>
<div class="col-12 d-flex gap-2"><button class="btn btn-danger px-4">Luu</button><a href="<?= url('admin/coupons') ?>" class="btn btn-outline-secondary">Huy</a></div>
</div></form><?php adminFooter(); ?>
