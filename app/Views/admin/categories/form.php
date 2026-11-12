<?php require_once __DIR__ . '/../_shared.php'; adminHeader(empty($category) ? 'Them danh muc' : 'Sua danh muc', 'Thong tin danh muc san pham'); ?>
<form method="post" enctype="multipart/form-data" class="admin-card">
    <div class="admin-card-body row g-3">
        <?= csrfToken() ?>
        <div class="col-md-8">
            <label class="form-label">Ten danh muc</label>
            <input name="name" class="form-control" required value="<?= e($category['name'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Trang thai</label>
            <select name="status" class="form-select">
                <option value="active" <?= ($category['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Dang hien thi</option>
                <option value="inactive" <?= ($category['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>An</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Danh muc cha</label>
            <select name="parent_id" class="form-select">
                <option value="">Khong co</option>
                <?php foreach (($categories ?? []) as $cat): if (($cat['id'] ?? null) == ($category['id'] ?? null)) continue; ?>
                    <option value="<?= $cat['id'] ?>" <?= ($category['parent_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Thu tu</label>
            <input type="number" name="display_order" class="form-control" value="<?= e($category['display_order'] ?? 0) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Hinh anh</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        <div class="col-12">
            <label class="form-label">Mo ta</label>
            <textarea name="description" class="form-control" rows="4"><?= e($category['description'] ?? '') ?></textarea>
        </div>
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-danger px-4">Luu</button>
            <a href="<?= url('admin/categories') ?>" class="btn btn-outline-secondary">Huy</a>
        </div>
    </div>
</form>
<?php adminFooter(); ?>
