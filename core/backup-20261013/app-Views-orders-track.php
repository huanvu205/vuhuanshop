<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
/* Tracking Specific Styles */
.track-wrapper {
    max-width: 800px;
    margin: 50px auto;
}
.track-card {
    background-color: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.06);
    border: 1px solid #f1f5f9;
    overflow: hidden;
}
.track-card-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: 30px 40px;
    color: white;
}
.track-card-body {
    padding: 40px;
}

/* Timeline */
.track-timeline {
    position: relative;
    padding-left: 50px;
    margin-top: 30px;
}
.track-timeline::before {
    content: '';
    position: absolute;
    top: 5px;
    left: 18px;
    bottom: 5px;
    width: 3px;
    background-color: #e2e8f0;
}
.track-timeline-item {
    position: relative;
    margin-bottom: 30px;
}
.track-timeline-item:last-child {
    margin-bottom: 0;
}
.track-timeline-circle {
    position: absolute;
    left: -42px;
    top: 0;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background-color: #fff;
    border: 3px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 0.75rem;
    z-index: 1;
    transition: all 0.3s;
}
.track-timeline-item.active .track-timeline-circle {
    border-color: var(--primary);
    color: var(--primary);
    background-color: #fff;
    box-shadow: 0 0 0 6px rgba(229,57,53,0.15);
}
.track-timeline-item.done .track-timeline-circle {
    border-color: #22c55e;
    background-color: #22c55e;
    color: white;
}
.track-timeline-content {
    padding-top: 2px;
}
.track-timeline-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 4px;
}
.track-timeline-item.active .track-timeline-title { color: var(--primary); }
.track-timeline-item.done .track-timeline-title { color: #22c55e; }
.track-timeline-time {
    font-size: 0.76rem;
    color: #64748b;
}

/* Products small list */
.track-product-item {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px dashed #f1f5f9;
}
.track-product-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}
.track-product-img {
    width: 45px;
    height: 45px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid #f1f5f9;
}
.track-product-name {
    font-size: 0.88rem;
    font-weight: 600;
    color: #334155;
}
.track-product-qty {
    font-size: 0.82rem;
    color: #64748b;
}
</style>

<div class="container pb-5">
    <div class="track-wrapper">
        <div class="track-card">
            <!-- Header -->
            <div class="track-card-header text-center">
                <h1 class="h3 fw-bold mb-2"><i class="fas fa-truck-fast me-2 text-danger"></i>Theo dõi vận đơn</h1>
                <p class="text-slate-400 mb-0 small">Nhập mã đơn hàng của bạn để kiểm tra trực tiếp trạng thái vận chuyển</p>
            </div>

            <!-- Body -->
            <div class="track-card-body">
                <!-- Search Input Form -->
                <form action="<?= url('orders/track') ?>" method="GET" class="mb-4">
                    <div class="row g-2 justify-content-center">
                        <div class="col-sm-8 col-md-9">
                            <div class="position-relative">
                                <span class="position-absolute top-50 translate-middle-y text-muted fs-5" style="left:15px;"><i class="fas fa-barcode"></i></span>
                                <input type="text" name="order_number" class="form-control form-control-lg" placeholder="Ví dụ: ORD20260609..." value="<?= e($orderNumber) ?>" style="padding-left:45px; border-radius:10px;" required>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-3">
                            <button type="submit" class="btn btn-danger btn-lg w-100 fw-bold" style="border-radius:10px; background:var(--primary); font-size: 0.95rem;">
                                <i class="fas fa-search me-1"></i>Tra cứu
                            </button>
                        </div>
                    </div>
                </form>

                <?php if ($orderNumber && empty($order)): ?>
                    <!-- Not Found State -->
                    <div class="alert alert-danger border-0 p-3 rounded-3 text-center mb-0">
                        <i class="fas fa-circle-xmark fs-4 mb-2 d-block"></i>
                        <h6 class="fw-bold mb-1">Không tìm thấy đơn hàng</h6>
                        <p class="mb-0 small text-muted">Mã đơn hàng <strong><?= e($orderNumber) ?></strong> không chính xác hoặc không tồn tại. Vui lòng kiểm tra lại.</p>
                    </div>
                <?php elseif (!empty($order)): ?>
                    <!-- Found State -->
                    <div class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="text-muted small d-block">Vận đơn:</span>
                            <strong class="text-dark fs-5">#<?= e($order['order_number']) ?></strong>
                        </div>
                        <div class="text-md-end">
                            <span class="text-muted small d-block">Trạng thái:</span>
                            <span class="badge bg-danger rounded-pill" style="font-size:0.8rem; background:var(--primary)!important;"><?= orderStatusLabel($order['status']) ?></span>
                        </div>
                    </div>

                    <!-- Shipping Address and Payment Info -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-bold text-dark small mb-2"><i class="fas fa-user me-2 text-muted"></i>Thông tin người nhận</h6>
                                <p class="mb-1 small"><strong><?= e($order['ship_name']) ?></strong></p>
                                <p class="mb-1 small text-muted">SĐT: <?= e($order['ship_phone']) ?></p>
                                <p class="mb-0 small text-muted">Địa chỉ: <?= e($order['ship_address']) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-bold text-dark small mb-2"><i class="fas fa-wallet me-2 text-muted"></i>Hình thức thanh toán</h6>
                                <p class="mb-1 small">Phương thức: <strong><?= strtoupper(e($order['payment_method'])) ?></strong></p>
                                <p class="mb-1 small text-muted">Trạng thái: <span class="badge <?= $order['payment_status'] === 'paid' ? 'bg-success' : 'bg-warning' ?>"><?= $order['payment_status'] === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' ?></span></p>
                                <p class="mb-0 small text-muted">Số tiền: <strong class="text-danger"><?= formatPrice($order['final_amount']) ?></strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Logistics Status Timeline -->
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-truck-loading me-2 text-muted"></i>Hành trình đơn hàng</h6>
                    <div class="p-4 border rounded-3 bg-white mb-4">
                        <?php if ($order['status'] === 'cancelled'): ?>
                            <div class="alert alert-danger border-0 p-3 rounded-3 text-center mb-0">
                                <i class="fas fa-ban fs-5 mb-2 d-block"></i>
                                <h6 class="fw-bold mb-1">Đơn hàng đã bị hủy</h6>
                                <p class="mb-0 small text-muted">Đơn hàng đã được hủy bỏ và không thể tiếp tục vận chuyển.</p>
                            </div>
                        <?php else: ?>
                            <div class="track-timeline">
                                <?php 
                                $trackIndex = 0;
                                if ($order['status'] === 'pending') $trackIndex = 1;
                                elseif ($order['status'] === 'confirmed') $trackIndex = 2;
                                elseif ($order['status'] === 'processing') $trackIndex = 3;
                                elseif ($order['status'] === 'shipping') $trackIndex = 4;
                                elseif ($order['status'] === 'delivered') $trackIndex = 5;
                                ?>
                                <div class="track-timeline-item <?= $trackIndex >= 1 ? 'done' : '' ?> <?= $trackIndex == 1 ? 'active' : '' ?>">
                                    <div class="track-timeline-circle"><i class="fas fa-check"></i></div>
                                    <div class="track-timeline-content">
                                        <div class="track-timeline-title">Đơn đặt hàng thành công</div>
                                        <div class="track-timeline-time"><?= date('H:i d/m/Y', strtotime($order['created_at'])) ?> - Hệ thống đã ghi nhận vận đơn</div>
                                    </div>
                                </div>
                                <div class="track-timeline-item <?= $trackIndex >= 2 ? 'done' : '' ?> <?= $trackIndex == 2 ? 'active' : '' ?>">
                                    <div class="track-timeline-circle"><i class="fas fa-check"></i></div>
                                    <div class="track-timeline-content">
                                        <div class="track-timeline-title">Đã được xác nhận</div>
                                        <div class="track-timeline-time"><?= $trackIndex >= 2 ? 'Nhân viên cửa hàng đã duyệt thông tin' : 'Đang chờ cửa hàng duyệt xác nhận đơn hàng' ?></div>
                                    </div>
                                </div>
                                <div class="track-timeline-item <?= $trackIndex >= 3 ? 'done' : '' ?> <?= $trackIndex == 3 ? 'active' : '' ?>">
                                    <div class="track-timeline-circle"><i class="fas fa-check"></i></div>
                                    <div class="track-timeline-content">
                                        <div class="track-timeline-title">Đang đóng gói và gửi kho</div>
                                        <div class="track-timeline-time"><?= $trackIndex >= 3 ? 'Hàng đã được bọc và bàn giao cho đơn vị chuyển phát' : 'Đang chuẩn bị sản phẩm' ?></div>
                                    </div>
                                </div>
                                <div class="track-timeline-item <?= $trackIndex >= 4 ? 'done' : '' ?> <?= $trackIndex == 4 ? 'active' : '' ?>">
                                    <div class="track-timeline-circle"><i class="fas fa-check"></i></div>
                                    <div class="track-timeline-content">
                                        <div class="track-timeline-title">Đang vận chuyển giao hàng</div>
                                        <div class="track-timeline-time"><?= $trackIndex >= 4 ? 'Đơn hàng đang trên đường đi đến địa chỉ của bạn' : 'Hàng chuẩn bị xuất xưởng vận chuyển' ?></div>
                                    </div>
                                </div>
                                <div class="track-timeline-item <?= $trackIndex >= 5 ? 'done' : '' ?> <?= $trackIndex == 5 ? 'active' : '' ?>">
                                    <div class="track-timeline-circle"><i class="fas fa-check"></i></div>
                                    <div class="track-timeline-content">
                                        <div class="track-timeline-title">Đã giao hàng thành công</div>
                                        <div class="track-timeline-time"><?= $trackIndex >= 5 ? 'Người mua đã ký xác nhận và thanh toán thành công' : 'Đang chờ shipper liên hệ giao hàng' ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Items list -->
                    <?php
                    $orderModel = new OrderModel();
                    $items = $orderModel->getDetails($order['id']);
                    if (!empty($items)):
                    ?>
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-shopping-bag me-2 text-muted"></i>Chi tiết các sản phẩm</h6>
                        <div class="p-3 border rounded-3 bg-white">
                            <?php foreach ($items as $item): ?>
                                <div class="track-product-item">
                                    <img src="<?= productImage($item['main_image']) ?>" class="track-product-img" alt="Product">
                                    <div class="flex-grow-1">
                                        <div class="track-product-name"><?= e($item['product_name']) ?></div>
                                    </div>
                                    <div class="text-end text-muted small">
                                        <span class="track-product-qty">SL: <?= $item['quantity'] ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- General Guidance -->
                    <div class="text-center py-4 text-muted small">
                        <i class="fas fa-info-circle me-1 text-danger"></i> Mã số đơn hàng được gửi kèm trong email xác nhận hoặc ở trang Lịch sử đơn hàng của bạn.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
