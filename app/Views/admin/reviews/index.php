<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Stats header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Quản lý đánh giá</h4>
        <span class="text-muted small">Kiểm duyệt và quản lý các nhận xét, bình luận đánh giá chất lượng sản phẩm từ khách hàng</span>
    </div>
</div>

<!-- Filter card -->
<div class="admin-card mb-4">
    <div class="admin-card-body p-3">
        <form method="GET" action="<?= url('admin/reviews') ?>" class="row g-2">
            <!-- Search -->
            <div class="col-md-3 col-sm-6">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Tên sản phẩm, nội dung..." value="<?= e($search) ?>" style="font-size:0.88rem;">
                </div>
            </div>
            
            <!-- Status -->
            <div class="col-md-3 col-sm-6">
                <select name="status" class="form-select bg-light" style="font-size:0.88rem;">
                    <option value="">-- Trạng thái duyệt --</option>
                    <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>Chờ kiểm duyệt</option>
                    <option value="approved" <?= $filterStatus === 'approved' ? 'selected' : '' ?>>Đã hiển thị</option>
                    <option value="rejected" <?= $filterStatus === 'rejected' ? 'selected' : '' ?>>Bị từ chối</option>
                </select>
            </div>

            <!-- Rating -->
            <div class="col-md-3 col-sm-6">
                <select name="rating" class="form-select bg-light" style="font-size:0.88rem;">
                    <option value="">-- Số sao đánh giá --</option>
                    <option value="5" <?= $filterRating == 5 ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ (5 sao)</option>
                    <option value="4" <?= $filterRating == 4 ? 'selected' : '' ?>>⭐⭐⭐⭐ (4 sao)</option>
                    <option value="3" <?= $filterRating == 3 ? 'selected' : '' ?>>⭐⭐⭐ (3 sao)</option>
                    <option value="2" <?= $filterRating == 2 ? 'selected' : '' ?>>⭐⭐ (2 sao)</option>
                    <option value="1" <?= $filterRating == 1 ? 'selected' : '' ?>>⭐ (1 sao)</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="col-md-3 col-sm-6">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger w-100 fw-semibold" style="border-radius:8px; background:var(--admin-primary,#E53935); font-size: 0.85rem; border:none;">
                        Lọc đánh giá
                    </button>
                    <a href="<?= url('admin/reviews') ?>" class="btn btn-outline-secondary" title="Xóa bộ lọc" style="border-radius:8px; font-size: 0.85rem;">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reviews Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Khách hàng</th>
                    <th>Sản phẩm</th>
                    <th class="text-center">Đánh giá</th>
                    <th>Nhận xét & Bình luận</th>
                    <th class="text-center">Ngày gửi</th>
                    <th class="text-center">Trạng thái</th>
                    <th style="width: 140px;" class="text-center">Thao tác duyệt</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-comment-slash fs-2 text-muted mb-2 d-block"></i>
                            <span class="text-muted small">Không tìm thấy đánh giá nào</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                        <tr>
                            <td>
                                <strong style="color: #1e293b; font-size: 0.88rem;"><?= e($rev['full_name']) ?></strong>
                            </td>
                            <td style="max-width: 180px;">
                                <div style="font-weight: 500; font-size:0.82rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= e($rev['product_name']) ?>">
                                    <?= e($rev['product_name']) ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="small" style="font-size:0.8rem;">
                                    <?= renderStars($rev['rating']) ?>
                                </div>
                            </td>
                            <td style="max-width: 300px;">
                                <?php if (!empty($rev['title'])): ?>
                                    <div class="fw-bold mb-1" style="font-size:0.85rem;"><?= e($rev['title']) ?></div>
                                <?php endif; ?>
                                <span class="small" style="font-size:0.82rem; line-height:1.4; display:block;"><?= nl2br(e($rev['comment'])) ?></span>
                            </td>
                            <td class="text-center" style="font-size:0.8rem; color:#64748b;">
                                <?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?>
                            </td>
                            <td class="text-center">
                                <?php if ($rev['status'] === 'approved'): ?>
                                    <span class="badge bg-success-subtle text-success border" style="font-size:0.75rem;">Hiển thị</span>
                                <?php elseif ($rev['status'] === 'pending'): ?>
                                    <span class="badge bg-warning-subtle text-warning border" style="font-size:0.75rem;">Chờ duyệt</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border" style="font-size:0.75rem;">Từ chối</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <?php if ($rev['status'] !== 'approved'): ?>
                                        <a href="<?= url('admin/reviews/approve/' . $rev['id']) ?>" class="btn btn-success btn-sm py-1 px-2" style="border-radius:6px; font-size:0.75rem;" title="Phê duyệt cho hiển thị">
                                            <i class="fas fa-check"></i> Duyệt
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($rev['status'] === 'pending'): ?>
                                        <a href="<?= url('admin/reviews/reject/' . $rev['id']) ?>" class="btn btn-warning btn-sm py-1 px-2 text-white" style="border-radius:6px; font-size:0.75rem;" title="Từ chối nhận xét">
                                            <i class="fas fa-times"></i> Ẩn
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2" style="border-radius:6px; font-size:0.75rem;" title="Xóa vĩnh viễn" onclick="confirmDelete('<?= url('admin/reviews/delete/' . $rev['id']) ?>', 'nhận xét của <?= addslashes($rev['full_name']) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
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
        $currentUrl = url('admin/reviews');
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
