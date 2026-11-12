<?php 
/**
 * @var array $orderStats
 * @var array $userStats
 * @var array $productStats
 * @var array $revenueByDay
 * @var array $revenueByMonth
 * @var array $topProducts
 * @var array $lowStock
 * @var int $pendingReviews
 * @var array $recentOrders
 * @var array|null $paymentStats
 */
$revenueByDay = $revenueByDay ?? [];
$revenueByMonth = $revenueByMonth ?? [];

require_once __DIR__ . '/layouts/header.php'; 

// Fetch additional statistics in-place if not provided by Controller
$userModel = new UserModel();
$recentCustomers = $userModel->getAll(1, 5);

// Tính toán các chỉ số kinh doanh thực tế từ DB cho 4 thẻ chỉ số quản trị
$orderModel = new OrderModel();
$paymentStats = $paymentStats ?? $orderModel->getPaymentStats();

$totalOrders = (int)($orderStats['total'] ?? 0);
$deliveredOrders = (int)($orderStats['delivered'] ?? 0);
$cancelledOrders = (int)($orderStats['cancelled'] ?? 0);
$totalRevenue = (float)($orderStats['total_revenue'] ?? 0);

// 1. Tỷ lệ giao hàng thành công
$fulfillmentRate = $totalOrders > 0 ? round(($deliveredOrders / $totalOrders) * 100, 1) : 0;
$svgCircleDash = min(100, max(0, $fulfillmentRate));

// 2. Cơ cấu phương thức thanh toán
$paymentMap = [];
$totalPaymentOrders = 0;
if (!empty($paymentStats)) {
    foreach ($paymentStats as $ps) {
        $m = strtolower($ps['payment_method'] ?? 'cod');
        $cnt = (int)($ps['total_orders'] ?? 0);
        $paymentMap[$m] = ($paymentMap[$m] ?? 0) + $cnt;
        $totalPaymentOrders += $cnt;
    }
}
$pmCodCount = $paymentMap['cod'] ?? 0;
$pmVnpayCount = $paymentMap['vnpay'] ?? 0;
$pmBankCount = ($paymentMap['bank_transfer'] ?? 0) + ($paymentMap['banking'] ?? 0) + ($paymentMap['vietqr'] ?? 0);

$pmCodPct = $totalPaymentOrders > 0 ? round(($pmCodCount / $totalPaymentOrders) * 100) : 0;
$pmVnpayPct = $totalPaymentOrders > 0 ? round(($pmVnpayCount / $totalPaymentOrders) * 100) : 0;
$pmBankPct = $totalPaymentOrders > 0 ? round(($pmBankCount / $totalPaymentOrders) * 100) : 0;

// 3. Giá trị trung bình đơn hàng (AOV)
$aov = $deliveredOrders > 0 ? ($totalRevenue / $deliveredOrders) : ($totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0);

// 4. Các việc cần làm ngay
$urgentPending = (int)($orderStats['pending'] ?? 0);
$urgentLowStock = (int)($productStats['low_stock'] ?? 0);
$urgentReviews = (int)($pendingReviews ?? 0);
?>

<!-- Statistics Overview Grid -->
<div class="stats-grid">
    <!-- Thẻ 1: Tổng doanh thu -->
    <div class="stat-card" style="--card-accent-color: #EF4444;">
        <div class="stat-card-header">
            <span class="stat-label">Tổng doanh thu</span>
            <div class="stat-icon red"><i class="fas fa-wallet"></i></div>
        </div>
        <div>
            <div class="stat-value"><?= formatPrice($orderStats['total_revenue'] ?? 0) ?></div>
            <div class="stat-change up">
                <i class="fas fa-arrow-trend-up"></i>
                <span>+12.5%</span>
                <span class="stat-change-text">so với tháng trước</span>
            </div>
        </div>
    </div>
    
    <!-- Thẻ 2: Tổng đơn hàng -->
    <div class="stat-card" style="--card-accent-color: #3B82F6;">
        <div class="stat-card-header">
            <span class="stat-label">Tổng đơn hàng</span>
            <div class="stat-icon blue"><i class="fas fa-shopping-bag"></i></div>
        </div>
        <div>
            <div class="stat-value"><?= number_format($orderStats['total'] ?? 0) ?></div>
            <div class="stat-change up">
                <i class="fas fa-arrow-trend-up"></i>
                <span>+8.2%</span>
                <span class="stat-change-text">so với tuần trước</span>
            </div>
        </div>
    </div>
    
    <!-- Thẻ 3: Tổng khách hàng -->
    <div class="stat-card" style="--card-accent-color: #10B981;">
        <div class="stat-card-header">
            <span class="stat-label">Tổng khách hàng</span>
            <div class="stat-icon green"><i class="fas fa-users"></i></div>
        </div>
        <div>
            <div class="stat-value"><?= number_format($userStats['total'] ?? 0) ?></div>
            <div class="stat-change up">
                <i class="fas fa-user-plus"></i>
                <span>+<?= number_format($userStats['today'] ?? 0) ?></span>
                <span class="stat-change-text">đăng ký hôm nay</span>
            </div>
        </div>
    </div>
    
    <!-- Thẻ 4: Tổng sản phẩm -->
    <div class="stat-card" style="--card-accent-color: #F59E0B;">
        <div class="stat-card-header">
            <span class="stat-label">Tổng sản phẩm</span>
            <div class="stat-icon orange"><i class="fas fa-box-open"></i></div>
        </div>
        <div>
            <div class="stat-value"><?= number_format($productStats['total'] ?? 0) ?></div>
            <div class="stat-change down">
                <i class="fas fa-exclamation-triangle"></i>
                <span><?= number_format($productStats['low_stock'] ?? 0) ?></span>
                <span class="stat-change-text">sản phẩm sắp hết hàng</span>
            </div>
        </div>
    </div>
    
    <!-- Thẻ 5: Doanh thu hôm nay -->
    <div class="stat-card" style="--card-accent-color: #06B6D4;">
        <div class="stat-card-header">
            <span class="stat-label">Doanh thu hôm nay</span>
            <div class="stat-icon cyan"><i class="fas fa-money-bill-trend-up"></i></div>
        </div>
        <div>
            <div class="stat-value"><?= formatPrice($orderStats['today_revenue'] ?? 0) ?></div>
            <div class="stat-change up">
                <i class="fas fa-clock"></i>
                <span><?= number_format($orderStats['today_orders'] ?? 0) ?></span>
                <span class="stat-change-text">đơn hàng phát sinh hôm nay</span>
            </div>
        </div>
    </div>
    
    <!-- Thẻ 6: Đơn hàng chờ xử lý -->
    <div class="stat-card" style="--card-accent-color: #8B5CF6;">
        <div class="stat-card-header">
            <span class="stat-label">Chờ xử lý</span>
            <div class="stat-icon purple"><i class="fas fa-hourglass-half"></i></div>
        </div>
        <div>
            <div class="stat-value"><?= number_format($orderStats['pending'] ?? 0) ?></div>
            <div class="stat-change <?= ($orderStats['pending'] ?? 0) > 0 ? 'down' : 'up' ?>">
                <a href="<?= url('admin/orders?status=pending') ?>" class="fw-semibold">Xem ngay →</a>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Area Chart: Doanh thu 7 ngày gần nhất -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-chart-line"></i> Doanh thu 7 ngày gần nhất</h5>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size:10px">Doanh thu (₫)</span>
            </div>
            <div class="admin-card-body">
                <div class="chart-container">
                    <canvas id="revenue7DaysChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Donut Chart: Tỷ lệ đơn hàng theo trạng thái -->
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-chart-pie"></i> Tỷ lệ trạng thái đơn hàng</h5>
            </div>
            <div class="admin-card-body d-flex flex-column align-items-center justify-content-center">
                <div class="chart-container" style="height: 250px;">
                    <canvas id="orderStatusDonut"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Widgets Row: 4 Chỉ số quản trị bán hàng thực tế (100% từ Database) -->
<div class="row g-4 mb-4">
    <!-- Thẻ 1: Tỷ lệ giao hàng thành công -->
    <div class="col-lg-3 col-md-6">
        <div class="admin-card h-100">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="admin-card-title mb-0"><i class="fas fa-circle-check text-success"></i> Tỷ lệ giao thành công</h5>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0" style="font-size:10px">Thực tế</span>
            </div>
            <div class="admin-card-body text-center d-flex flex-column justify-content-between">
                <div class="progress-circle-wrapper my-2">
                    <svg class="w-100 h-100" viewBox="0 0 36 36" style="transform: rotate(-90deg);">
                        <path class="text-light" stroke="currentColor" stroke-width="2.8" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="opacity: 0.15;"></path>
                        <path stroke="#10B981" stroke-width="2.8" stroke-dasharray="<?= $svgCircleDash ?>, 100" stroke-linecap="round" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                    </svg>
                    <div class="progress-circle-value">
                        <span style="font-size: 1.35rem; color: #10B981; font-weight: 800;"><?= $fulfillmentRate ?>%</span>
                        <span class="progress-circle-label">Giao thành công</span>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between px-2 py-1 rounded" style="background: var(--border-light); font-size: 11px;">
                        <span class="text-muted">Đã giao hoàn tất:</span>
                        <span class="fw-bold text-success"><?= number_format($deliveredOrders) ?> / <?= number_format($totalOrders) ?> đơn</span>
                    </div>
                    <div class="d-flex justify-content-between px-2 py-1 mt-1 rounded" style="background: var(--border-light); font-size: 11px;">
                        <span class="text-muted">Đơn hàng bị hủy:</span>
                        <span class="fw-bold text-danger"><?= number_format($cancelledOrders) ?> đơn</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Thẻ 2: Cơ cấu phương thức thanh toán -->
    <div class="col-lg-3 col-md-6">
        <div class="admin-card h-100">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="admin-card-title mb-0"><i class="fas fa-credit-card text-primary"></i> Kênh thanh toán</h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0" style="font-size:10px"><?= number_format($totalPaymentOrders) ?> đơn</span>
            </div>
            <div class="admin-card-body d-flex flex-column justify-content-around py-3">
                <!-- Tiền mặt COD -->
                <div class="widget-category-item mb-2">
                    <div class="widget-category-info d-flex justify-content-between" style="font-size: 11.5px;">
                        <span class="fw-semibold text-dark"><i class="fas fa-money-bill-wave text-success me-1"></i> Trả tiền mặt (COD)</span>
                        <span class="fw-bold"><?= $pmCodPct ?>% <small class="text-muted fw-normal">(<?= $pmCodCount ?>)</small></span>
                    </div>
                    <div class="widget-category-progress" style="height: 6px;">
                        <div class="widget-category-bar" style="width: <?= $pmCodPct ?>%; --bar-color: #10B981;"></div>
                    </div>
                </div>
                
                <!-- Cổng VNPAY -->
                <div class="widget-category-item mb-2">
                    <div class="widget-category-info d-flex justify-content-between" style="font-size: 11.5px;">
                        <span class="fw-semibold text-dark"><i class="fas fa-qrcode text-danger me-1"></i> Cổng VNPAY (ATM/QR)</span>
                        <span class="fw-bold"><?= $pmVnpayPct ?>% <small class="text-muted fw-normal">(<?= $pmVnpayCount ?>)</small></span>
                    </div>
                    <div class="widget-category-progress" style="height: 6px;">
                        <div class="widget-category-bar" style="width: <?= $pmVnpayPct ?>%; --bar-color: #EF4444;"></div>
                    </div>
                </div>
                
                <!-- Chuyển khoản VietQR -->
                <div class="widget-category-item mb-0">
                    <div class="widget-category-info d-flex justify-content-between" style="font-size: 11.5px;">
                        <span class="fw-semibold text-dark"><i class="fas fa-building-columns text-primary me-1"></i> Chuyển khoản VietQR</span>
                        <span class="fw-bold"><?= $pmBankPct ?>% <small class="text-muted fw-normal">(<?= $pmBankCount ?>)</small></span>
                    </div>
                    <div class="widget-category-progress" style="height: 6px;">
                        <div class="widget-category-bar" style="width: <?= $pmBankPct ?>%; --bar-color: #3B82F6;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Thẻ 3: Giá trị trung bình đơn hàng (AOV) -->
    <div class="col-lg-3 col-md-6">
        <div class="admin-card h-100">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="admin-card-title mb-0"><i class="fas fa-coins text-warning"></i> Giá trị đơn TB (AOV)</h5>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0" style="font-size:10px">TB / Đơn</span>
            </div>
            <div class="admin-card-body d-flex flex-column justify-content-between py-3">
                <div class="text-center my-2">
                    <div class="text-muted small mb-1">Quy mô chi tiêu trung bình:</div>
                    <div style="font-size: 1.45rem; font-weight: 800; color: #D97706; letter-spacing: -0.5px;">
                        <?= formatPrice($aov) ?>
                    </div>
                    <div class="text-muted mt-1" style="font-size: 11px;">
                        Tính trên <span class="fw-bold text-dark"><?= number_format($deliveredOrders > 0 ? $deliveredOrders : $totalOrders) ?></span> đơn hàng thực tế
                    </div>
                </div>
                <div class="p-2 rounded mt-2" style="background: var(--border-light); font-size: 11px;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Doanh thu hôm nay:</span>
                        <span class="fw-bold text-primary"><?= formatPrice($orderStats['today_revenue'] ?? 0) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Đơn phát sinh hôm nay:</span>
                        <span class="fw-bold text-dark"><?= number_format($orderStats['today_orders'] ?? 0) ?> đơn</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Thẻ 4: Trung tâm tác vụ cần xử lý ngay -->
    <div class="col-lg-3 col-md-6">
        <div class="admin-card h-100">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="admin-card-title mb-0"><i class="fas fa-bell text-danger"></i> Cần xử lý ngay</h5>
                <span class="badge bg-danger text-white rounded-pill px-2 py-0" style="font-size:10px">
                    <?= $urgentPending + ($urgentLowStock > 0 ? 1 : 0) + ($urgentReviews > 0 ? 1 : 0) ?> việc
                </span>
            </div>
            <div class="admin-card-body d-flex flex-column justify-content-around py-2">
                <!-- Đơn hàng chờ xác nhận -->
                <a href="<?= url('admin/orders?status=pending') ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-2 rounded mb-2 border" style="background: <?= $urgentPending > 0 ? 'rgba(239, 68, 68, 0.05)' : 'var(--border-light)' ?>; border-color: <?= $urgentPending > 0 ? '#FCA5A5' : 'var(--border-color)' ?> !important; transition: all 0.2s ease;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:26px; height:26px; background: rgba(239, 68, 68, 0.15); color: #EF4444; font-size: 11px;">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 11.5px;">Đơn chờ duyệt</div>
                            <div class="text-muted" style="font-size: 10px;">Cần xác nhận đóng gói</div>
                        </div>
                    </div>
                    <span class="badge <?= $urgentPending > 0 ? 'bg-danger text-white' : 'bg-secondary text-white' ?> rounded-pill px-2" style="font-size: 11px;">
                        <?= $urgentPending ?>
                    </span>
                </a>
                
                <!-- Cảnh báo tồn kho -->
                <a href="<?= url('admin/inventory?low_stock=1') ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-2 rounded mb-2 border" style="background: <?= $urgentLowStock > 0 ? 'rgba(245, 158, 11, 0.05)' : 'var(--border-light)' ?>; border-color: <?= $urgentLowStock > 0 ? '#FCD34D' : 'var(--border-color)' ?> !important; transition: all 0.2s ease;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:26px; height:26px; background: rgba(245, 158, 11, 0.15); color: #F59E0B; font-size: 11px;">
                            <i class="fas fa-boxes-stacked"></i>
                        </span>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 11.5px;">Sắp hết hàng</div>
                            <div class="text-muted" style="font-size: 10px;">Tồn kho dưới 5 cái</div>
                        </div>
                    </div>
                    <span class="badge <?= $urgentLowStock > 0 ? 'bg-warning text-dark' : 'bg-secondary text-white' ?> rounded-pill px-2" style="font-size: 11px;">
                        <?= $urgentLowStock ?>
                    </span>
                </a>
                
                <!-- Đánh giá chờ duyệt -->
                <a href="<?= url('admin/reviews?status=pending') ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-2 rounded border" style="background: <?= $urgentReviews > 0 ? 'rgba(59, 130, 246, 0.05)' : 'var(--border-light)' ?>; border-color: <?= $urgentReviews > 0 ? '#93C5FD' : 'var(--border-color)' ?> !important; transition: all 0.2s ease;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:26px; height:26px; background: rgba(59, 130, 246, 0.15); color: #3B82F6; font-size: 11px;">
                            <i class="fas fa-star-half-stroke"></i>
                        </span>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 11.5px;">Đánh giá chờ duyệt</div>
                            <div class="text-muted" style="font-size: 10px;">Phản hồi từ khách</div>
                        </div>
                    </div>
                    <span class="badge <?= $urgentReviews > 0 ? 'bg-primary text-white' : 'bg-secondary text-white' ?> rounded-pill px-2" style="font-size: 11px;">
                        <?= $urgentReviews ?>
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Charts / Report Row -->
<div class="row g-4 mb-4">
    <!-- Doanh thu theo tháng -->
    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-chart-bar"></i> Doanh thu theo các tháng trong năm</h5>
                <span class="text-muted small">Đơn vị: Triệu Đồng (Tr) • Chỉ tính đơn đã giao / thanh toán</span>
            </div>
            <div class="admin-card-body">
                <div class="chart-container" style="height: 280px;">
                    <canvas id="revenueMonthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Tables Row -->
<div class="row g-4 mb-4">
    <!-- Table 1: Đơn hàng mới nhất -->
    <div class="col-lg-8">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-shopping-cart"></i> Đơn hàng mới nhất</h5>
                <a href="<?= url('admin/orders') ?>" class="btn btn-sm btn-outline-danger px-3" style="border-radius:8px">Xem tất cả</a>
            </div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($recentOrders)): ?>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><strong><?= e($order['order_number']) ?></strong></td>
                                <td>
                                    <div style="font-weight:600"><?= e($order['customer_name'] ?? $order['ship_name']) ?></div>
                                    <div style="font-size:10px;color:var(--text-muted)"><?= e($order['ship_phone']) ?></div>
                                </td>
                                <td style="font-weight:700;color:var(--primary)"><?= formatPrice($order['final_amount']) ?></td>
                                <td><span class="badge-status status-<?= $order['status'] ?>"><?= orderStatusLabel($order['status']) ?></span></td>
                                <td style="font-size:11px;color:var(--text-muted)"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <a href="<?= url('admin/orders/' . $order['id']) ?>" class="btn-table-action view" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Chưa có đơn hàng nào phát sinh.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Table 2: Top sản phẩm bán chạy -->
    <div class="col-lg-4">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-fire-flame-simple"></i> Bán chạy nhất</h5>
                <a href="<?= url('admin/reports') ?>" class="btn btn-sm btn-outline-danger px-3" style="border-radius:8px">Báo cáo</a>
            </div>
            <div class="admin-card-body">
                <?php if(!empty($topProducts)): ?>
                    <?php foreach ($topProducts as $i => $product): ?>
                    <?php if($i >= 5) break; // Display top 5 ?>
                    <div class="d-flex align-items-center gap-3 mb-3 <?= $i < 4 ? 'pb-3 border-bottom' : '' ?>" style="border-color: var(--border-color) !important;">
                        <div style="width:28px;height:28px;border-radius:8px;background:<?= ['rgba(239, 68, 68, 0.1)','rgba(59, 130, 246, 0.1)','rgba(16, 185, 129, 0.1)','rgba(245, 158, 11, 0.1)','rgba(139, 92, 246, 0.1)'][$i % 5] ?>;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:<?= ['#EF4444','#3B82F6','#10B981','#F59E0B','#8B5CF6'][$i % 5] ?>">
                            <?= $i + 1 ?>
                        </div>
                        <img src="<?= productImage($product['main_image']) ?>" style="width:36px;height:36px;object-fit:contain;border-radius:8px;background:var(--border-light);border: 1px solid var(--border-color)" alt="">
                        <div style="flex:1;min-width:0">
                            <div style="font-size:12px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text-main)"><?= e($product['name']) ?></div>
                            <div style="font-size:10px;color:var(--text-muted)"><?= number_format($product['total_sold'] ?? 0) ?> đã bán</div>
                        </div>
                        <div style="font-size:12px;font-weight:700;color:var(--primary);white-space:nowrap"><?= formatPrice($product['price_sale']) ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">Chưa có sản phẩm bán chạy nào.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Tables Row -->
<div class="row g-4 mb-4">
    <!-- Table 3: Khách hàng mới đăng ký -->
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-users-line"></i> Khách hàng mới đăng ký</h5>
                <a href="<?= url('admin/users') ?>" class="btn btn-sm btn-outline-danger px-3" style="border-radius:8px">Xem tất cả</a>
            </div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Vai trò</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($recentCustomers)): ?>
                            <?php foreach ($recentCustomers as $customer): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="topbar-avatar" style="width:28px;height:28px;font-size:0.75rem;border-radius:50%;background:#3B82F6">
                                            <?= mb_substr($customer['full_name'] ?? 'U', 0, 1) ?>
                                        </div>
                                        <span style="font-weight:600"><?= e($customer['full_name']) ?></span>
                                    </div>
                                </td>
                                <td><?= e($customer['email']) ?></td>
                                <td><?= e($customer['phone'] ?? 'Chưa cập nhật') ?></td>
                                <td><span class="badge bg-secondary-subtle text-secondary border px-2 py-1" style="font-size:10px"><?= e($customer['role_name'] ?? 'customer') ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Chưa có khách hàng đăng ký mới.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Table 4: Sản phẩm sắp hết hàng -->
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5 class="admin-card-title" style="color:#F59E0B"><i class="fas fa-triangle-exclamation"></i> Sản phẩm sắp hết hàng</h5>
                <a href="<?= url('admin/inventory') ?>?low_stock=1" class="btn btn-sm btn-warning text-white px-3" style="border-radius:8px">Quản lý kho</a>
            </div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Mã sản phẩm</th>
                            <th>Tồn kho</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($lowStock)): ?>
                            <?php foreach ($lowStock as $product): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= productImage($product['main_image']) ?>" class="table-product-img" alt="">
                                        <span style="font-weight:600;color:var(--text-main)"><?= e($product['name']) ?></span>
                                    </div>
                                </td>
                                <td><code style="font-size: 11px"><?= e($product['product_code']) ?></code></td>
                                <td>
                                    <span class="badge rounded-pill bg-<?= $product['stock_quantity'] == 0 ? 'danger' : 'warning' ?> text-white px-2 py-1" style="font-size:10px">
                                        <?= $product['stock_quantity'] ?> cái
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= url('admin/inventory/import?product_id=' . $product['id']) ?>" class="btn-table-action view" title="Nhập kho"><i class="fas fa-plus"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-success py-4"><i class="fas fa-check-circle me-1"></i> Tất cả sản phẩm đều đủ tồn kho (> 10 cái).</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Alert for Reviews -->
<?php if ($pendingReviews > 0): ?>
<div class="alert alert-warning border border-warning-subtle d-flex align-items-center gap-3 p-3 rounded-4 shadow-sm mb-4">
    <div class="stat-icon orange" style="background-color: transparent;"><i class="fas fa-star fa-2x"></i></div>
    <div>
        <div style="font-weight:700;color:#78350F">Có <?= $pendingReviews ?> đánh giá đang chờ kiểm duyệt</div>
        <div style="font-size:12px;color:#92400E">Duyệt đánh giá giúp tăng độ tin cậy và thúc đẩy doanh thu sản phẩm.</div>
    </div>
    <a href="<?= url('admin/reviews?status=pending') ?>" class="btn btn-warning text-white ms-auto px-4" style="border-radius:10px">Duyệt ngay</a>
</div>
<?php endif; ?>

<!-- Chart Drawing Javascript -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Shared chart styling config options
    const isDarkMode = () => document.documentElement.getAttribute('data-theme') === 'dark';
    
    function getChartGridColor() {
        return isDarkMode() ? '#1E293B' : '#E2E8F0';
    }
    
    function getChartTextColor() {
        return isDarkMode() ? '#94A3B8' : '#64748B';
    }

    // 1. Area Chart: Revenue 7 Days
    const revenue7DaysRawInput = <?= json_encode(array_values(array_map(fn($d) => ['date' => $d['date'] ?? '', 'revenue' => (float)($d['revenue'] ?? 0)], $revenueByDay ?? []))) ?>;
    
    // Construct last 7 days ending today
    const revenue7DaysRaw = [];
    const todayForChart = new Date();
    for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setDate(todayForChart.getDate() - i);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const dateVal = String(d.getDate()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${dateVal}`;
        
        const found = revenue7DaysRawInput.find(item => item.date.startsWith(formattedDate));
        revenue7DaysRaw.push({
            date: formattedDate,
            revenue: found ? found.revenue : 0
        });
    }
    
    const revCtx = document.getElementById('revenue7DaysChart').getContext('2d');
    
    // Create soft gradient fill
    const fillGradient = revCtx.createLinearGradient(0, 0, 0, 300);
    fillGradient.addColorStop(0, 'rgba(239, 68, 68, 0.25)');
    fillGradient.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

    const rev7DaysChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: revenue7DaysRaw.map(d => {
                const parts = d.date.split('-');
                return parseInt(parts[2]) + '/' + parseInt(parts[1]);
            }),
            datasets: [{
                label: 'Doanh thu (₫)',
                data: revenue7DaysRaw.map(d => d.revenue),
                borderColor: '#EF4444',
                backgroundColor: fillGradient,
                borderWidth: 3,
                pointBackgroundColor: '#EF4444',
                pointBorderColor: '#FFFFFF',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const val = context.raw || 0;
                            return 'Doanh thu: ' + Math.round(val).toLocaleString('vi-VN') + ' ₫';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: getChartGridColor()
                    },
                    ticks: {
                        color: getChartTextColor(),
                        callback: function(value) {
                            return value >= 1000000 ? (value / 1000000).toFixed(1) + ' Tr' : value.toLocaleString('vi-VN');
                        }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: getChartTextColor() }
                }
            }
        }
    });

    // 2. Donut Chart: Order Status Ratio
    const orderStatusRaw = <?= json_encode([
        'Chờ xử lý' => $orderStats['pending'] ?? 0,
        'Đã giao' => $orderStats['delivered'] ?? 0,
        'Đang giao' => $orderStats['shipping'] ?? 0,
        'Đã hủy' => $orderStats['cancelled'] ?? 0
    ]) ?>;

    const donutCtx = document.getElementById('orderStatusDonut').getContext('2d');
    const orderDonutChart = new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(orderStatusRaw),
            datasets: [{
                data: Object.values(orderStatusRaw),
                backgroundColor: ['#F59E0B', '#10B981', '#8B5CF6', '#EF4444'],
                borderWidth: 2,
                borderColor: isDarkMode() ? '#151F32' : '#FFFFFF'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: getChartTextColor(),
                        boxWidth: 10,
                        padding: 15,
                        font: { size: 11 }
                    }
                }
            },
            cutout: '70%'
        }
    });

    // 3. Bar Chart: Monthly Revenue
    const monthlyRevRaw = <?= json_encode(array_values(array_map(fn($d) => ['month' => $d['month'] ?? 0, 'revenue' => ((float)($d['revenue'] ?? 0)) / 1000000], $revenueByMonth ?? []))) ?>;
    
    // Construct 12 months array
    const monthsData = Array.from({ length: 12 }, (_, index) => {
        const found = monthlyRevRaw.find(d => parseInt(d.month) === index + 1);
        return found ? found.revenue : 0;
    });

    const monthlyCtx = document.getElementById('revenueMonthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [{
                label: 'Doanh thu (Triệu ₫)',
                data: monthsData,
                backgroundColor: '#3B82F6',
                hoverBackgroundColor: '#2563EB',
                borderRadius: 6,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const val = context.raw || 0;
                            const fullVnd = Math.round(val * 1000000).toLocaleString('vi-VN');
                            return 'Doanh thu: ' + fullVnd + ' ₫ (' + Number(val.toFixed(2)) + ' Tr)';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: getChartGridColor()
                    },
                    ticks: {
                        color: getChartTextColor(),
                        callback: v => v + ' Tr'
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: getChartTextColor() }
                }
            }
        }
    });

    // Handle theme change chart color refreshing
    const observer = new MutationObserver(() => {
        // Refresh grid and text colors for all charts
        const gridCol = getChartGridColor();
        const textCol = getChartTextColor();
        
        [rev7DaysChart, monthlyChart].forEach(c => {
            c.options.scales.y.grid.color = gridCol;
            c.options.scales.y.ticks.color = textCol;
            c.options.scales.x.ticks.color = textCol;
            c.update();
        });

        orderDonutChart.options.plugins.legend.labels.color = textCol;
        orderDonutChart.data.datasets[0].borderColor = isDarkMode() ? '#151F32' : '#FFFFFF';
        orderDonutChart.update();
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-theme']
    });
});
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
