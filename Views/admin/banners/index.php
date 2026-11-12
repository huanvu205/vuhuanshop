<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Stats header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Quản lý banner quảng cáo</h4>
        <span class="text-muted small">Cài đặt các banner trượt ở trang chủ, khu vực quảng cáo phụ hoặc sự kiện khuyến mãi</span>
    </div>
    <div>
        <a href="<?= url('admin/banners/create') ?>" class="btn btn-danger text-white px-4 fw-semibold" style="border-radius:10px; background: var(--admin-primary, #E53935); border: none;">
            <i class="fas fa-plus me-1"></i>Thêm banner mới
        </a>
    </div>
</div>

<!-- Banners Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:180px;">Hình ảnh banner</th>
                    <th>Tiêu đề / Tên banner</th>
                    <th>Đường dẫn liên kết (Link)</th>
                    <th>Vị trí hiển thị</th>
                    <th class="text-center">Thứ tự hiển thị</th>
                    <th class="text-center">Thời hạn hiển thị</th>
                    <th class="text-center">Trạng thái</th>
                    <th style="width: 110px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($banners)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-image fs-2 text-muted mb-2 d-block"></i>
                            <span class="text-muted small">Chưa có banner nào được thiết lập</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($banners as $ban): ?>
                        <tr>
                            <td>
                                <img src="<?= productImage($ban['image']) ?>" style="width:150px; height:60px; object-fit:cover; background:#F8F9FA; border-radius:6px; border:1px solid #e2e8f0;" alt="">
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b; font-size: 0.9rem;"><?= e($ban['title']) ?></div>
                            </td>
                            <td>
                                <?php if (!empty($ban['link'])): ?>
                                    <a href="<?= e($ban['link']) ?>" target="_blank" class="text-decoration-none text-primary small">
                                        <i class="fas fa-link me-1"></i><?= e(truncate($ban['link'], 35)) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border small" style="font-size:0.75rem;">
                                    <?php 
                                    if ($ban['position'] === 'home_slider') echo 'Slider trang chủ';
                                    elseif ($ban['position'] === 'home_sidebar') echo 'Sidebar trang chủ';
                                    elseif ($ban['position'] === 'promo_middle') echo 'Quảng cáo giữa trang';
                                    else echo e($ban['position']);
                                    ?>
                                </span>
                            </td>
                            <td class="text-center"><?= $ban['display_order'] ?></td>
                            <td class="text-center small" style="font-size:0.8rem; color:#64748b; line-height:1.3;">
                                <div>Từ: <?= !empty($ban['start_date']) ? date('d/m/Y', strtotime($ban['start_date'])) : 'Không đặt' ?></div>
                                <div>Đến: <?= !empty($ban['end_date']) ? date('d/m/Y', strtotime($ban['end_date'])) : 'Không giới hạn' ?></div>
                                <?php if (!empty($ban['end_date']) && strtotime($ban['end_date']) < time()): ?>
                                    <span class="badge bg-danger-subtle text-danger small mt-1" style="font-size:0.65rem; padding: 2px 6px;">Hết hạn</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $ban['status'] === 'active' && (empty($ban['end_date']) || strtotime($ban['end_date']) >= time()) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> border" style="font-size:0.75rem; padding: 4px 10px; border-radius:12px;">
                                    <?= $ban['status'] === 'active' && (empty($ban['end_date']) || strtotime($ban['end_date']) >= time()) ? 'Đang chạy' : 'Tạm ẩn' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= url('admin/banners/edit/' . $ban['id']) ?>" class="btn-table-action edit" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn-table-action delete border-0 bg-transparent text-danger ms-1" title="Xóa" onclick="confirmDelete('<?= url('admin/banners/delete/' . $ban['id']) ?>', 'banner <?= addslashes($ban['title']) ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
