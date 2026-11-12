<?php 
/**
 * @var string $period
 * @var int $year
 * @var array $orderStats
 * @var array $userStats
 * @var array $productStats
 * @var array $revenueByDay
 * @var array $revenueByMonth
 * @var array $revenueByYear
 * @var array $topProducts
 * @var array $topCustomers
 */
$period = $period ?? 'month';
$year = $year ?? (int)date('Y');
$revenueByDay = $revenueByDay ?? [];
$revenueByMonth = $revenueByMonth ?? [];
$revenueByYear = $revenueByYear ?? [];
$topProducts = $topProducts ?? [];
$topCustomers = $topCustomers ?? [];
$orderStats = $orderStats ?? [];
$userStats = $userStats ?? [];
$productStats = $productStats ?? [];

require_once __DIR__ . '/../layouts/header.php'; 
?>

<!-- Stats header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 text-dark fw-bold">Báo cáo & Thống kê kinh doanh</h4>
        <span class="text-muted small">Phân tích doanh thu, tăng trưởng thành viên và hiệu suất bán hàng của từng dòng thiết bị</span>
    </div>
    <div>
        <a href="<?= url('admin/reports/exportOrders') ?>" class="btn btn-success text-white px-4 fw-semibold" style="border-radius:10px; border:none; background:#2e7d32;">
            <i class="fas fa-file-excel me-1"></i>Xuất Excel (.CSV)
        </a>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="stats-grid mb-4">
    <!-- Revenue -->
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-dollar-sign"></i></div>
        <div>
            <div class="stat-label">Tổng doanh thu bán lẻ</div>
            <div class="stat-value" style="font-size:1.15rem;"><?= formatPrice($orderStats['total_revenue'] ?? 0) ?></div>
            <div class="stat-change up"><i class="fas fa-calendar-day me-1"></i>Hôm nay: <?= formatPrice($orderStats['today_revenue'] ?? 0) ?></div>
        </div>
    </div>
    <!-- Total Orders -->
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-shopping-bag"></i></div>
        <div>
            <div class="stat-label">Tổng số lượng đơn đặt</div>
            <div class="stat-value"><?= number_format($orderStats['total'] ?? 0) ?> đơn</div>
            <div class="stat-change up"><i class="fas fa-check-circle me-1"></i>Hoàn thành: <?= number_format($orderStats['delivered'] ?? 0) ?></div>
        </div>
    </div>
    <!-- Customers -->
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-label">Khách hàng đăng ký</div>
            <div class="stat-value"><?= number_format($userStats['total'] ?? 0) ?> thành viên</div>
            <div class="stat-change up"><i class="fas fa-user-plus me-1"></i>Mới hôm nay: <?= number_format($userStats['today'] ?? 0) ?></div>
        </div>
    </div>
    <!-- Active products -->
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-boxes-stacked"></i></div>
        <div>
            <div class="stat-label">Sản phẩm đang bán</div>
            <div class="stat-value"><?= number_format($productStats['active'] ?? $productStats['total'] ?? 0) ?> sản phẩm</div>
            <div class="stat-change down"><i class="fas fa-exclamation-triangle me-1"></i>Tồn kho cảnh báo: <?= number_format($productStats['low_stock'] ?? 0) ?></div>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="row g-4 mb-4">
    <!-- Line Chart (Daily) -->
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title"><i class="fas fa-chart-line"></i> Tăng trưởng doanh thu 30 ngày gần đây</h5>
            </div>
            <div class="admin-card-body">
                <div class="chart-container" style="position: relative; height: 320px; width: 100%;">
                    <canvas id="dailyRevenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bar Chart (Monthly) -->
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="admin-card-title mb-0"><i class="fas fa-chart-bar"></i> Doanh thu theo tháng</h5>
                <form method="GET" action="" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="period" value="<?= e($period) ?>">
                    <select name="year" class="form-select form-select-sm" style="width: 100px; border-radius: 6px;" onchange="this.form.submit()">
                        <?php 
                        $currY = (int)date('Y');
                        for ($y = $currY; $y >= $currY - 4; $y--):
                        ?>
                            <option value="<?= $y ?>" <?= $year === $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </form>
            </div>
            <div class="admin-card-body">
                <div class="chart-container" style="position: relative; height: 320px; width: 100%;">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rankings Grid -->
<div class="row g-4">
    <!-- Top Selling Products -->
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="admin-card-title mb-0"><i class="fas fa-fire text-danger me-2"></i>Top 10 sản phẩm bán chạy</h5>
                <span class="badge bg-danger-subtle text-danger px-2.5 py-1" style="font-size:11px;">Chỉ tính đơn thành công</span>
            </div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:50px;" class="text-center">Hạng</th>
                            <th>Sản phẩm</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-center">Đã bán</th>
                            <th class="text-end">Doanh số</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topProducts)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">Chưa có giao dịch sản phẩm nào hoàn tất</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($topProducts as $i => $prod): ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if ($i === 0): ?>
                                            <span class="badge rounded-circle" style="background:#FEF3C7; color:#D97706; font-size:13px; width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center;">🥇</span>
                                        <?php elseif ($i === 1): ?>
                                            <span class="badge rounded-circle" style="background:#F1F5F9; color:#64748B; font-size:13px; width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center;">🥈</span>
                                        <?php elseif ($i === 2): ?>
                                            <span class="badge rounded-circle" style="background:#FFEDD5; color:#C2410C; font-size:13px; width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center;">🥉</span>
                                        <?php else: ?>
                                            <span class="fw-bold text-muted small" style="width:28px; display:inline-block;"><?= $i + 1 ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="<?= productImage($prod['main_image']) ?>" style="width:38px; height:38px; object-fit:contain; background:#f8f9fa; border-radius:8px; border:1px solid #e2e8f0;" alt="">
                                            <div style="min-width:0;">
                                                <div style="font-weight:600; color:var(--text-main); font-size:0.85rem; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= e($prod['name']) ?>"><?= e($prod['name']) ?></div>
                                                <div class="text-muted" style="font-size:0.75rem;">Mã: <?= e($prod['product_code'] ?? 'SP' . $prod['id']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-semibold" style="font-size:0.82rem;">
                                        <?= formatPrice($prod['price_sale']) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger-subtle text-danger px-2.5 py-1" style="border-radius:10px; font-weight:700; font-size:0.8rem;">
                                            <?= number_format($prod['total_sold']) ?> cái
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-success" style="font-size:0.85rem;">
                                        <?= formatPrice($prod['total_revenue'] ?? 0) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Spending Customers -->
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="admin-card-title mb-0"><i class="fas fa-crown text-warning me-2"></i>Top 10 khách hàng chi tiêu lớn</h5>
                <span class="badge bg-primary-subtle text-primary px-2.5 py-1" style="font-size:11px;">Thực tế chi trả</span>
            </div>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:50px;" class="text-center">Hạng</th>
                            <th>Khách hàng</th>
                            <th>Thông tin liên hệ</th>
                            <th class="text-center">Đơn hoàn tất</th>
                            <th class="text-end">Tổng tiền chi tiêu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $activeCustomers = array_filter($topCustomers ?? [], fn($c) => ($c['order_count'] ?? 0) > 0 || ($c['total_spent'] ?? 0) > 0);
                        if (empty($activeCustomers)): 
                        ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">Chưa có khách hàng phát sinh đơn hàng thành công</td>
                            </tr>
                        <?php else: ?>
                            <?php $rank = 1; foreach ($activeCustomers as $cust): ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if ($rank === 1): ?>
                                            <span class="badge rounded-circle" style="background:#FEF3C7; color:#D97706; font-size:13px; width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center;">🥇</span>
                                        <?php elseif ($rank === 2): ?>
                                            <span class="badge rounded-circle" style="background:#F1F5F9; color:#64748B; font-size:13px; width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center;">🥈</span>
                                        <?php elseif ($rank === 3): ?>
                                            <span class="badge rounded-circle" style="background:#FFEDD5; color:#C2410C; font-size:13px; width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center;">🥉</span>
                                        <?php else: ?>
                                            <span class="fw-bold text-muted small" style="width:28px; display:inline-block;"><?= $rank ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle fw-bold text-primary" style="width:32px; height:32px; background: rgba(59, 130, 246, 0.15); font-size:12px;">
                                                <?= mb_substr($cust['full_name'] ?? 'K', 0, 1) ?>
                                            </div>
                                            <div style="font-weight:600; color:var(--text-main); font-size:0.85rem;"><?= e($cust['full_name']) ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size:0.8rem; color:var(--text-muted);"><?= e($cust['email']) ?></div>
                                        <span class="text-muted small" style="font-size:0.75rem;"><i class="fas fa-phone me-1"></i><?= e($cust['phone'] ?? 'Chưa có SĐT') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary px-2.5 py-1" style="border-radius:10px; font-weight:700; font-size:0.8rem;">
                                            <?= number_format($cust['order_count']) ?> đơn
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success" style="font-size:0.9rem;"><?= formatPrice($cust['total_spent'] ?? 0) ?></strong>
                                    </td>
                                </tr>
                            <?php $rank++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Load Chart JS and render -->
<script>
window.addEventListener('DOMContentLoaded', () => {
    // 1. Daily Revenue Chart (Line)
    const dailyDataRawInput = <?= json_encode(array_values(array_map(fn($d) => ['date' => $d['date'] ?? '', 'revenue' => (float)($d['revenue'] ?? 0)], $revenueByDay ?? []))) ?>;
    
    // Construct last 30 days ending today
    const dailyData = [];
    const todayForReport = new Date();
    for (let i = 29; i >= 0; i--) {
        const d = new Date();
        d.setDate(todayForReport.getDate() - i);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const dateVal = String(d.getDate()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${dateVal}`;
        
        const found = dailyDataRawInput.find(item => item.date.startsWith(formattedDate));
        dailyData.push({
            date: formattedDate,
            revenue: found ? found.revenue : 0
        });
    }
    
    new Chart(document.getElementById('dailyRevenueChart'), {
        type: 'line',
        data: {
            labels: dailyData.map(d => {
                const parts = d.date.split('-');
                return parseInt(parts[2]) + '/' + parseInt(parts[1]);
            }),
            datasets: [{
                label: 'Doanh thu ngày (₫)',
                data: dailyData.map(d => d.revenue),
                borderColor: '#e53935',
                backgroundColor: 'rgba(229, 57, 53, 0.06)',
                borderWidth: 2.5,
                pointBackgroundColor: '#e53935',
                pointHoverRadius: 6,
                fill: true,
                tension: 0.45
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
                    ticks: {
                        callback: v => v >= 1000000 ? (v / 1000000).toFixed(1) + ' Tr' : v.toLocaleString('vi-VN')
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Monthly Revenue Chart (Bar)
    // Pre-populate months array [1..12]
    const monthlyDB = <?= json_encode($revenueByMonth ?? []) ?>;
    const monthsArray = Array.from({length: 12}, (_, i) => i + 1);
    const monthlyRevenue = monthsArray.map(m => {
        const found = monthlyDB.find(db => parseInt(db.month) === m);
        return found ? parseFloat(found.revenue) : 0;
    });

    new Chart(document.getElementById('monthlyRevenueChart'), {
        type: 'bar',
        data: {
            labels: monthsArray.map(m => 'Tháng ' + m),
            datasets: [{
                label: 'Doanh thu tháng (₫)',
                data: monthlyRevenue,
                backgroundColor: '#1e293b',
                hoverBackgroundColor: '#e53935',
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
                            const fullVnd = Math.round(val).toLocaleString('vi-VN');
                            const valInTr = (val / 1000000).toFixed(2);
                            return 'Doanh thu: ' + fullVnd + ' ₫ (' + valInTr + ' Tr)';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => v >= 1000000 ? (v / 1000000).toFixed(1) + ' Tr' : v.toLocaleString('vi-VN')
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
