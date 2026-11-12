<?php
/** @var string $keyword */
/** @var array $results */
/** @var bool $searched */
$keyword  = $keyword ?? '';
$results  = $results ?? [];
$searched = $searched ?? false;
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.warranty-hero {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: 50px 0 40px;
    color: #fff;
    margin-bottom: 35px;
    position: relative;
    border-bottom: 3px solid #E53935;
}
.warranty-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #f1f5f9;
    transition: all 0.25s ease;
    overflow: hidden;
    margin-bottom: 24px;
}
.warranty-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}
.warranty-status-badge {
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.status-active {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}
.status-expired {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #ffcdd2;
}
.status-pending {
    background: #fff8e1;
    color: #b78103;
    border: 1px solid #ffe082;
}
.warranty-timeline {
    position: relative;
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
}
.warranty-info-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed #e2e8f0;
    font-size: 14px;
}
.warranty-info-row:last-child {
    border-bottom: none;
}
</style>

<!-- Hero Section -->
<div class="warranty-hero">
    <div class="container text-center">
        <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:60px; height:60px; border-radius:50%; background:rgba(229,57,53,0.15); color:#E53935; font-size:26px;">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h1 class="fw-bold mb-2" style="font-size: 2rem;">Tra Cứu Bảo Hành Điện Tử</h1>
        <p class="text-white-50 mx-auto" style="max-width: 650px; font-size: 0.95rem;">
            Nhập <strong>Số điện thoại mua hàng</strong> hoặc <strong>Mã đơn hàng / Mã bảo hành</strong> để kiểm tra chi tiết hạn bảo hành thiết bị của bạn tại Vũ Huân Shop.
        </p>

        <!-- Search Form -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-7 col-md-9">
                <form action="<?= url('warranty') ?>" method="GET" class="d-flex gap-2 p-2 bg-white rounded-pill shadow-lg">
                    <div class="d-flex align-items-center ps-3 text-muted">
                        <i class="fas fa-search fs-5"></i>
                    </div>
                    <input type="text" name="keyword" class="form-control border-0 shadow-none px-2" 
                           placeholder="Nhập số điện thoại (VD: 0918841790) hoặc mã đơn..." 
                           value="<?= e($keyword) ?>" required style="font-size: 15px;">
                    <button type="submit" class="btn btn-danger px-4 rounded-pill fw-bold" style="background:#E53935; white-space:nowrap;">
                        <i class="fas fa-search me-1"></i> Tra cứu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <?php if ($searched): ?>
        <?php if (!empty($results)): ?>
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <h4 class="fw-bold text-dark mb-0">
                    <i class="fas fa-check-circle text-success me-2"></i>Tìm thấy <?= count($results) ?> sản phẩm bảo hành
                </h4>
                <span class="text-muted small">Từ khóa: <strong><?= e($keyword) ?></strong></span>
            </div>

            <div class="row">
                <?php foreach ($results as $item): ?>
                    <div class="col-lg-6">
                        <div class="warranty-card p-4">
                            <!-- Header card -->
                            <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom flex-wrap gap-2">
                                <div>
                                    <span class="text-muted small d-block">Mã bảo hành điện tử</span>
                                    <strong class="text-danger fs-6"><?= e($item['warranty_code']) ?></strong>
                                </div>
                                <div>
                                    <?php if ($item['warranty_status'] === 'active'): ?>
                                        <span class="warranty-status-badge status-active">
                                            <i class="fas fa-check-circle"></i> Đang bảo hành (còn <?= $item['days_left'] ?> ngày)
                                        </span>
                                    <?php elseif ($item['warranty_status'] === 'pending_delivery'): ?>
                                        <span class="warranty-status-badge status-pending" title="Đơn hàng đang xử lý hoặc đang giao">
                                            <i class="fas fa-clock"></i> Chờ kích hoạt (<?= e($item['order_status_label']) ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="warranty-status-badge status-expired">
                                            <i class="fas fa-times-circle"></i> Hết hạn bảo hành
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="d-flex gap-3 align-items-center mb-3">
                                <div style="width: 70px; height: 70px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?= productImage($item['image']) ?>" alt="Ảnh" style="width:100%; height:100%; object-fit:contain;">
                                    <?php else: ?>
                                        <i class="fas fa-mobile-alt fs-2 text-danger"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;"><?= e($item['product_name']) ?></h5>
                                    <?php if (!empty($item['variant'])): ?>
                                        <span class="badge bg-light text-dark border me-1"><?= e($item['variant']) ?></span>
                                    <?php endif; ?>
                                    <span class="badge bg-light text-muted border">SL: <?= (int)$item['quantity'] ?></span>
                                </div>
                            </div>

                            <?php if ($item['warranty_status'] === 'pending_delivery'): ?>
                                <div class="p-2 px-3 mb-3 rounded-3" style="background:#fffde7; border:1px solid #ffe082; font-size:12.5px; color:#856404; line-height: 1.5;">
                                    <i class="fas fa-info-circle me-1 text-warning"></i><strong>Chính sách kích hoạt:</strong> Bảo hành chính hãng 12 tháng sẽ được hệ thống tự động kích hoạt ngay khi bưu tá giao hàng thành công và quý khách nhận máy.
                                </div>
                            <?php endif; ?>

                            <!-- Warranty Details -->
                            <div class="warranty-timeline mb-3">
                                <div class="warranty-info-row">
                                    <span class="text-muted"><i class="fas fa-user me-2 text-primary"></i>Khách hàng</span>
                                    <strong class="text-dark"><?= e($item['customer_name']) ?> (<?= e($item['customer_phone']) ?>)</strong>
                                </div>
                                <div class="warranty-info-row">
                                    <span class="text-muted"><i class="fas fa-calendar-check me-2 <?= $item['warranty_status'] === 'active' ? 'text-success' : 'text-warning' ?>"></i>Ngày kích hoạt</span>
                                    <span class="fw-semibold <?= $item['warranty_status'] === 'pending_delivery' ? 'text-warning' : 'text-dark' ?>"><?= e($item['start_date']) ?></span>
                                </div>
                                <div class="warranty-info-row">
                                    <span class="text-muted"><i class="fas fa-calendar-times me-2 text-danger"></i>Ngày hết hạn</span>
                                    <strong class="<?= $item['is_valid'] ? 'text-danger' : 'text-muted' ?>"><?= e($item['end_date']) ?></strong>
                                </div>
                                <div class="warranty-info-row">
                                    <span class="text-muted"><i class="fas fa-award me-2 text-warning"></i>Thời hạn</span>
                                    <span class="fw-bold text-dark"><?= e($item['duration']) ?></span>
                                </div>
                            </div>

                            <!-- Support Hotlines -->
                            <div class="d-flex justify-content-between align-items-center pt-2">
                                <span class="text-muted small"><i class="fas fa-headset me-1 text-danger"></i> Hỗ trợ kỹ thuật: <strong>0336.120.405</strong></span>
                                <a href="https://zalo.me/0918841790" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="fas fa-comment-dots me-1"></i> Chat Zalo
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm my-4">
                <div class="text-muted mb-3" style="font-size: 50px;">
                    <i class="fas fa-search-minus text-warning"></i>
                </div>
                <h4 class="fw-bold text-dark">Không tìm thấy thông tin bảo hành</h4>
                <p class="text-muted mx-auto" style="max-width: 450px;">
                    Không tìm thấy sản phẩm nào khớp với từ khóa <strong>"<?= e($keyword) ?>"</strong>. Vui lòng kiểm tra lại số điện thoại đặt hàng hoặc liên hệ hotline để được hỗ trợ.
                </p>
                <a href="tel:0336120405" class="btn btn-danger rounded-pill px-4 mt-2">
                    <i class="fas fa-phone-alt me-1"></i> Gọi Hotline 0336.120.405
                </a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Cam kết bảo hành -->
        <div class="row g-4 my-2">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 border h-100 shadow-sm text-center">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:50px; height:50px; border-radius:50%; background:#e8f5e9; color:#2e7d32; font-size:22px;">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-2">1 Đổi 1 Trong 30 Ngày</h5>
                    <p class="text-muted small mb-0">Lỗi do nhà sản xuất là đổi ngay máy mới tương đương, không sửa chữa chắp vá, yên tâm tuyệt đối.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 border h-100 shadow-sm text-center">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:50px; height:50px; border-radius:50%; background:#e3f2fd; color:#1976d2; font-size:22px;">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Bảo Hành 12 Tháng Phần Cứng</h5>
                    <p class="text-muted small mb-0">Bảo hành toàn diện cả nguồn, màn hình và mainboard với linh kiện chính hãng tại Vũ Huân Shop.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 border h-100 shadow-sm text-center">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:50px; height:50px; border-radius:50%; background:#fff3e0; color:#f57c00; font-size:22px;">
                        <i class="fas fa-mobile"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Tra Cứu Online Tiện Lợi</h5>
                    <p class="text-muted small mb-0">Hệ thống bảo hành điện tử lưu trữ trực tuyến, không lo mất phiếu hay rách giấy bảo hành.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

