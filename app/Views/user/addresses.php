<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<main class="container py-5" style="max-width:1000px">
    <h1 class="h3 mb-4">Sổ địa chỉ giao hàng</h1>
    <div class="row g-4">
        <div class="col-lg-7"><div class="list-group">
            <?php foreach (($addresses ?? []) as $address): ?>
                <div class="list-group-item p-3 mb-2 rounded border">
                    <strong><?= e($address['label']) ?><?= !empty($address['is_default']) ? ' · Mặc định' : '' ?></strong>
                    <div><?= e($address['recipient_name']) ?> · <?= e($address['phone']) ?></div>
                    <div><?= e(implode(', ', array_filter([$address['address_line'],$address['ward'],$address['district'],$address['city']]))) ?></div>
                    <form class="mt-2" action="<?= url('user/deleteAddress/' . $address['id']) ?>" method="post"><?= csrfToken() ?><button class="btn btn-sm btn-outline-danger">Xóa</button></form>
                </div>
            <?php endforeach; ?>
            <?php if (empty($addresses)): ?><p class="text-muted">Bạn chưa lưu địa chỉ nào.</p><?php endif; ?>
        </div></div>
        <div class="col-lg-5"><form class="card card-body" action="<?= url('user/saveAddress') ?>" method="post">
            <?= csrfToken() ?><h2 class="h5">Thêm địa chỉ</h2>
            <label class="form-label">Tên địa chỉ<input class="form-control" name="label" value="Nhà riêng"></label>
            <label class="form-label">Người nhận<input class="form-control" name="recipient_name" required></label>
            <label class="form-label">Điện thoại<input class="form-control" name="phone" required></label>
            <label class="form-label">Địa chỉ<input class="form-control" name="address_line" required></label>
            <label class="form-label">Tỉnh/Thành phố<input class="form-control" name="city" required></label>
            <label class="form-label">Quận/Huyện<input class="form-control" name="district"></label>
            <label class="form-label">Phường/Xã<input class="form-control" name="ward"></label>
            <label class="form-check mb-3"><input class="form-check-input" type="checkbox" name="is_default" value="1"> Đặt làm địa chỉ mặc định</label>
            <button class="btn btn-primary">Lưu địa chỉ</button>
        </form></div>
    </div>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
