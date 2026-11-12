<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Chi tiết khách hàng: <?= e($user['full_name']) ?></h4>
        <span class="text-muted small">Hồ sơ cá nhân, lịch sử giao dịch và phân quyền tài khoản</span>
    </div>
    <div>
        <a href="<?= url('admin/users') ?>" class="btn btn-light border px-4 fw-semibold" style="border-radius:10px;">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: User Card & Status Action -->
    <div class="col-lg-4">
        <!-- User summary -->
        <div class="admin-card mb-4 text-center">
            <div class="admin-card-body p-4">
                <div class="mb-3">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= productImage($user['avatar']) ?>" style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:3px solid #f1f5f9;" alt="">
                    <?php else: ?>
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['full_name'] ?? 'U') ?>&background=E53935&color=fff&size=100" style="width:100px; height:100px; border-radius:50%; border:3px solid #f1f5f9;" alt="">
                    <?php endif; ?>
                </div>
                <h5 class="fw-bold mb-1"><?= e($user['full_name']) ?></h5>
                <span class="badge bg-light text-dark border mb-3">Quyền: <?= e($user['role_name']) ?></span>
                
                <hr class="my-3">

                <div class="text-start small text-muted">
                    <p class="mb-2"><i class="fas fa-envelope me-2"></i>Email: <strong class="text-dark"><?= e($user['email']) ?></strong></p>
                    <p class="mb-2"><i class="fas fa-phone me-2"></i>SĐT: <strong class="text-dark"><?= e($user['phone'] ?? 'Chưa cung cấp') ?></strong></p>
                    <p class="mb-2"><i class="fas fa-calendar-alt me-2"></i>Đăng ký ngày: <span class="text-dark"><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></span></p>
                    <p class="mb-2"><i class="fas fa-history me-2"></i>Đăng nhập cuối: <span class="text-dark"><?= !empty($user['last_login']) ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Chưa từng đăng nhập' ?></span></p>
                    <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Địa chỉ mặc định: <span class="text-dark"><?= e($user['address'] ?? '') ?> <?= e($user['city'] ?? '') ?></span></p>
                </div>
            </div>
        </div>

        <!-- Status manager card -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-user-lock text-primary me-2"></i>Trạng thái tài khoản</h6>
            </div>
            <div class="admin-card-body p-4">
                <!-- Status Badge -->
                <div class="mb-4 text-center">
                    <span class="badge-status status-<?= $user['status'] ?> fs-6 py-2 px-3 d-inline-block">
                        <?= $user['status'] === 'active' ? 'Đang hoạt động' : 'Bị khóa tài khoản' ?>
                    </span>
                </div>

                <form action="<?= url('admin/users/setStatus/' . $user['id']) ?>" method="POST">
                    <?= csrfToken() ?>
                    <?php if ($user['status'] === 'active'): ?>
                        <input type="hidden" name="status" value="banned">
                        <button type="submit" class="btn btn-danger w-100 fw-bold" style="border-radius:8px; background:#e53935; border:none;" onclick="return confirm('Bạn có chắc chắn muốn KHÓA tài khoản của người dùng này?')">
                            <i class="fas fa-ban me-1"></i>Khóa tài khoản (Ban)
                        </button>
                    <?php else: ?>
                        <input type="hidden" name="status" value="active">
                        <button type="submit" class="btn btn-success w-100 fw-bold" style="border-radius:8px; background:#2e7d32; border:none;">
                            <i class="fas fa-check-circle me-1"></i>Mở khóa tài khoản
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Purchase History -->
    <div class="col-lg-8">
        <div class="admin-card h-100">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="admin-card-title mb-0"><i class="fas fa-shopping-cart text-primary"></i> Lịch sử mua hàng</h5>
                <span class="badge bg-danger rounded-pill"><?= count($orders) ?> đơn hàng</span>
            </div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th class="text-end">Tổng tiền</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Ngày đặt hàng</th>
                            <th style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small">
                                    Khách hàng này chưa thực hiện giao dịch nào
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong style="color:#0f172a;">#<?= e($order['order_number']) ?></strong></td>
                                    <td class="text-end fw-bold text-danger"><?= formatPrice($order['final_amount']) ?></td>
                                    <td class="text-center">
                                        <span class="badge-status status-<?= $order['status'] ?>" style="font-size:0.75rem;">
                                            <?= orderStatusLabel($order['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center" style="font-size:0.8rem; color:#64748b;">
                                        <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= url('admin/orders/' . $order['id']) ?>" class="btn-table-action view" title="Xem chi tiết đơn">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
