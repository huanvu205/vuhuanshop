<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Stats header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Quản lý khách hàng</h4>
        <span class="text-muted small">Xem danh sách đăng ký thành viên, quản lý quyền hạn và trạng thái tài khoản</span>
    </div>
</div>

<!-- Filter card -->
<div class="admin-card mb-4">
    <div class="admin-card-body p-3">
        <form method="GET" action="<?= url('admin/users') ?>" class="row g-2">
            <!-- Search -->
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Tên, Email hoặc SĐT khách hàng..." value="<?= e($search) ?>" style="font-size:0.88rem;">
                </div>
            </div>
            
            <!-- Status -->
            <div class="col-md-3">
                <select name="status" class="form-select bg-light" style="font-size:0.88rem;">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active" <?= $filterStatus === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="banned" <?= $filterStatus === 'banned' ? 'selected' : '' ?>>Bị khóa (Banned)</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-danger w-100 fw-semibold" style="border-radius:8px; background:var(--admin-primary,#E53935); font-size: 0.85rem; border:none;">
                    Tìm kiếm
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Avatar</th>
                    <th>Họ và tên</th>
                    <th>Tên đăng nhập</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th class="text-center">Đăng ký ngày</th>
                    <th class="text-center">Đăng nhập cuối</th>
                    <th class="text-center">Trạng thái</th>
                    <th style="width: 80px;" class="text-center">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-users-slash fs-2 text-muted mb-2 d-block"></i>
                            <span class="text-muted small">Không tìm thấy khách hàng nào phù hợp</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="<?= productImage($user['avatar']) ?>" style="width:38px; height:38px; border-radius:50%; object-fit:cover; border:1px solid #e2e8f0;" alt="">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['full_name'] ?? 'U') ?>&background=E53935&color=fff&size=38" style="width:38px; height:38px; border-radius:50%;" alt="">
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b; font-size: 0.88rem;"><?= e($user['full_name']) ?></div>
                                <span class="badge bg-light text-dark border small mt-1" style="font-size:0.7rem;"><?= e($user['role_name']) ?></span>
                            </td>
                            <td><span style="font-size:0.85rem; font-family:monospace;"><?= e($user['username']) ?></span></td>
                            <td><span style="font-size:0.85rem;"><?= e($user['email']) ?></span></td>
                            <td><span style="font-size:0.85rem;"><?= e($user['phone'] ?? 'Chưa cập nhật') ?></span></td>
                            <td class="text-center" style="font-size:0.8rem; color:#64748b;">
                                <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                            </td>
                            <td class="text-center" style="font-size:0.8rem; color:#64748b;">
                                <?= !empty($user['last_login']) ? date('d/m/Y H:i', strtotime($user['last_login'])) : '<span class="text-muted italic small">Chưa có</span>' ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $user['status'] === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> border" style="font-size:0.75rem; padding: 4px 10px; border-radius:12px;">
                                    <?= $user['status'] === 'active' ? 'Kích hoạt' : 'Bị khóa' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= url('admin/users/' . $user['id']) ?>" class="btn-table-action view" title="Xem hồ sơ & Lịch sử mua hàng">
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

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <div class="mt-4">
        <?php
        $currentUrl = url('admin/users');
        $queryParams = $_GET;
        
        echo '<nav><ul class="pagination justify-content-center">';
        
        if ($currentPage > 1) {
            $queryParams['page'] = $currentPage - 1;
            echo '<li class="page-item"><a class="page-link" href="' . $currentUrl . '?' . http_build_query($queryParams) . '"><i class="fas fa-chevron-left"></i></a></li>';
        }
        
        for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
            $queryParams['page'] = $i;
            $active = $i === $currentPage ? ' active' : '';
            echo '<li class="page-item' . $active . '"><a class="page-link" href="' . $currentUrl . '?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
        }
        
        if ($currentPage < $totalPages) {
            $queryParams['page'] = $currentPage + 1;
            echo '<li class="page-item"><a class="page-link" href="' . $currentUrl . '?' . http_build_query($queryParams) . '"><i class="fas fa-chevron-right"></i></a></li>';
        }
        
        echo '</ul></nav>';
        ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
