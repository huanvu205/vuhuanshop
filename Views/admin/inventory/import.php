<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Nhập thêm hàng vào kho</h4>
        <span class="text-muted small">Tăng số lượng sản phẩm thực tế trong kho và lưu lịch sử giao dịch</span>
    </div>
    <div>
        <a href="<?= url('admin/inventory') ?>" class="btn btn-light border px-4 fw-semibold" style="border-radius:10px;">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-plus text-primary"></i> Phiếu nhập kho</h5>
            </div>
            <div class="admin-card-body p-4">
                <form action="<?= url('admin/inventory/import') ?>" method="POST" id="importForm">
                    <?= csrfToken() ?>

                    <?php 
                    $preSelectedId = (int)($_GET['product_id'] ?? 0);
                    ?>

                    <div class="row g-3">
                        <!-- Product Selection -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="product_select">Chọn sản phẩm <span class="text-danger">*</span></label>
                            <select id="product_select" name="product_id" class="form-select" style="border-radius:8px;" required>
                                <option value="">-- Click để chọn sản phẩm --</option>
                                <?php foreach ($products as $prod): ?>
                                    <option value="<?= $prod['id'] ?>" <?= $preSelectedId === $prod['id'] ? 'selected' : '' ?>>
                                        <?= e($prod['name']) ?> (Mã: <?= e($prod['product_code']) ?> | Tồn cũ: <?= $prod['stock_quantity'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="import_qty">Số lượng nhập kho <span class="text-danger">*</span></label>
                            <input type="number" id="import_qty" name="quantity" class="form-control" placeholder="Nhập số lượng lớn hơn 0" min="1" style="border-radius:8px;" required>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small" for="import_notes">Ghi chú phiếu nhập (Nhà cung cấp, số chứng từ...)</label>
                            <textarea id="import_notes" name="notes" class="form-control" rows="4" placeholder="Ví dụ: Nhập hàng từ nhà phân phối FPT, chứng từ số 9821..." style="border-radius:8px;"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="<?= url('admin/inventory') ?>" class="btn btn-light border px-4" style="border-radius:8px;">Hủy bỏ</a>
                        <button type="submit" class="btn btn-danger px-4 fw-semibold" style="border-radius:8px; background:var(--admin-primary,#E53935); border:none;">
                            Xác nhận nhập kho
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
