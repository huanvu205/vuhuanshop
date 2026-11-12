<?php require_once __DIR__ . '/../_shared.php'; adminHeader(empty($brand) ? 'Them thuong hieu' : 'Sua thuong hieu', 'Thong tin thuong hieu'); ?>
<form method="post" enctype="multipart/form-data" class="admin-card"><div class="admin-card-body row g-3">
<?= csrfToken() ?>
<div class="col-md-6"><label class="form-label">Ten thuong hieu</label><input name="name" class="form-control" required value="<?= e($brand['name'] ?? '') ?>"></div>
<div class="col-md-3"><label class="form-label">Quoc gia</label><input name="country" class="form-control" value="<?= e($brand['country'] ?? '') ?>"></div>
<div class="col-md-3"><label class="form-label">Trang thai</label><select name="status" class="form-select"><option value="active" <?= ($brand['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Dang hien thi</option><option value="inactive" <?= ($brand['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>An</option></select></div>
<div class="col-md-6"><label class="form-label">Website</label><input name="website" class="form-control" value="<?= e($brand['website'] ?? '') ?>"></div>
<div class="col-md-3"><label class="form-label">Thu tu</label><input type="number" name="display_order" class="form-control" value="<?= e($brand['display_order'] ?? 0) ?>"></div>
<div class="col-md-3"><label class="form-label">Logo</label><input type="file" name="logo" class="form-control" accept="image/*"></div>
<div class="col-12"><label class="form-label">Mo ta</label><textarea name="description" class="form-control" rows="4"><?= e($brand['description'] ?? '') ?></textarea></div>
<div class="col-12 d-flex gap-2"><button class="btn btn-danger px-4">Luu</button><a href="<?= url('admin/brands') ?>" class="btn btn-outline-secondary">Huy</a></div>
</div></form><?php adminFooter(); ?>
