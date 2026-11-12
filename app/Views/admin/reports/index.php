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

<style>
.report-page{--report-ink:#172033;--report-muted:#718096;--report-line:#e7edf5;max-width:1600px;margin:0 auto;padding-bottom:28px}
.report-heading{position:relative;overflow:hidden;display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:24px;padding:25px 28px;border:1px solid #e5ebf4;border-radius:20px;background:linear-gradient(115deg,#fff 0%,#f7f9ff 70%,#eef4ff 100%);box-shadow:0 8px 26px rgba(31,48,79,.05)}
.report-heading:after{content:"";position:absolute;width:210px;height:210px;right:8%;top:-135px;border-radius:50%;background:rgba(59,130,246,.08);pointer-events:none}
.report-heading-copy,.report-actions{position:relative;z-index:1}.report-heading h1{margin:0 0 7px;color:var(--report-ink);font-size:clamp(1.25rem,2vw,1.65rem);font-weight:800;letter-spacing:-.035em}.report-heading p{max-width:700px;margin:0;color:var(--report-muted);font-size:.91rem;line-height:1.55}.report-actions .btn{border-radius:11px;padding:11px 16px;box-shadow:0 5px 12px rgba(22,101,52,.14)}
.report-page .stats-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:22px}
.report-page .stat-card{min-width:0;display:flex;align-items:center;gap:15px;padding:20px;border:1px solid var(--report-line);border-radius:17px;background:#fff;box-shadow:0 5px 18px rgba(31,48,79,.045);transition:transform .18s ease,box-shadow .18s ease}
.report-page .stat-card:hover{transform:translateY(-2px);box-shadow:0 12px 25px rgba(31,48,79,.09)}.report-page .stat-icon{flex:0 0 46px;width:46px;height:46px;display:grid;place-items:center;border-radius:14px;font-size:1rem}.report-page .stat-label{margin-bottom:5px;color:var(--report-muted);font-size:.77rem;font-weight:650}.report-page .stat-value{color:var(--report-ink);font-size:1.35rem!important;font-weight:800;letter-spacing:-.035em;white-space:nowrap}.report-page .stat-change{margin-top:6px;font-size:.75rem;font-weight:600}
.report-page .admin-card{height:100%;overflow:hidden;border:1px solid var(--report-line);border-radius:17px;background:#fff;box-shadow:0 5px 18px rgba(31,48,79,.045)}.report-page .admin-card-header{min-height:62px;padding:16px 20px;border-bottom:1px solid #edf1f6;background:#fff}.report-page .admin-card-title{margin:0;color:var(--report-ink);font-size:.94rem;font-weight:750}.report-page .admin-card-title i{margin-right:7px;color:#4f6b95}.report-page .admin-card-body{padding:18px 20px}.report-page .admin-table-wrapper{overflow-x:hidden}.report-page .admin-table{width:100%;min-width:0;margin:0;table-layout:fixed}.report-page .admin-table thead th{padding:10px 7px;background:#f8fafd;color:#6c7b91;font-size:.62rem;font-weight:750;letter-spacing:.025em;text-transform:uppercase;white-space:normal;line-height:1.25}.report-page .admin-table tbody td{padding:9px 7px;border-color:#eef2f6;vertical-align:middle;font-size:.73rem}.report-page .admin-table tbody tr:hover{background:#fafcff}.report-page .admin-table th:nth-child(1),.report-page .admin-table td:nth-child(1){width:8%}.report-page .admin-table th:nth-child(2),.report-page .admin-table td:nth-child(2){width:31%}.report-page .admin-table th:nth-child(3),.report-page .admin-table td:nth-child(3){width:20%}.report-page .admin-table th:nth-child(4),.report-page .admin-table td:nth-child(4){width:16%}.report-page .admin-table th:nth-child(5),.report-page .admin-table td:nth-child(5){width:25%}.report-page .admin-table td:nth-child(2) img{width:30px!important;height:30px!important;flex:0 0 30px}.report-page .admin-table td:nth-child(2)>div{gap:6px!important}.report-page .admin-table td:nth-child(2)>div>div{max-width:calc(100% - 36px)!important}.report-page .admin-table td:nth-child(2)>div>div>div:first-child{max-width:100%!important;font-size:.75rem!important}.report-page .admin-table td:nth-child(3),.report-page .admin-table td:nth-child(5){font-size:.69rem}.report-page .admin-table td:nth-child(3) strong,.report-page .admin-table td:nth-child(5) strong{font-size:.72rem!important;overflow-wrap:anywhere}.report-page .admin-table td:nth-child(3) div,.report-page .admin-table td:nth-child(3) span{overflow-wrap:anywhere}.report-page .admin-table .badge{font-size:.67rem!important;padding:.3rem .42rem!important;white-space:nowrap}.report-page .rank-medal{width:26px;height:26px;display:inline-grid;place-items:center;border:1px solid #e8edf4;border-radius:9px;background:#f8fafc;color:#51627a;font-size:.68rem;font-weight:800}.report-page .report-empty{padding:30px 14px!important;color:#8290a5;text-align:center}.report-page .report-section-gap{margin-top:20px}
.report-page .report-products-table th:nth-child(1),.report-page .report-products-table td:nth-child(1){width:8%}.report-page .report-products-table th:nth-child(2),.report-page .report-products-table td:nth-child(2){width:31%}.report-page .report-products-table th:nth-child(3),.report-page .report-products-table td:nth-child(3){width:18%}.report-page .report-products-table th:nth-child(4),.report-page .report-products-table td:nth-child(4){width:13%}.report-page .report-products-table th:nth-child(5),.report-page .report-products-table td:nth-child(5){width:30%}
.report-page .report-customers-table th:nth-child(1),.report-page .report-customers-table td:nth-child(1){width:8%}.report-page .report-customers-table th:nth-child(2),.report-page .report-customers-table td:nth-child(2){width:20%}.report-page .report-customers-table th:nth-child(3),.report-page .report-customers-table td:nth-child(3){width:36%}.report-page .report-customers-table th:nth-child(4),.report-page .report-customers-table td:nth-child(4){width:14%}.report-page .report-customers-table th:nth-child(5),.report-page .report-customers-table td:nth-child(5){width:22%}
@media(max-width:1100px){.report-page .stats-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:767px){.report-page .admin-table-wrapper{overflow-x:auto}.report-page .admin-table{min-width:620px;table-layout:auto}}
@media(max-width:640px){.report-heading{align-items:flex-start;flex-direction:column;padding:20px}.report-actions,.report-actions .btn{width:100%}.report-page .stats-grid{grid-template-columns:1fr;gap:11px}.report-page .stat-card{padding:16px}.report-page .admin-card-header{align-items:flex-start!important;flex-direction:column;gap:8px}.report-page .admin-card-body{padding:12px}}
</style>

<div class="report-page">

<!-- Stats header -->
<div class="report-heading">
    <div class="report-heading-copy">
        <h1>Báo cáo &amp; thống kê kinh doanh</h1>
        <p>Doanh thu, đơn hàng, khách hàng và hiệu suất bán hàng theo dữ liệu ghi nhận trên cửa hàng.</p>
    </div>
    <div class="report-actions">
        <a href="<?= url('admin/reports/exportOrders') ?>" class="btn btn-success text-white fw-semibold">
            <i class="fas fa-file-excel me-2"></i>Tải báo cáo đơn hàng Excel (.xlsx)
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
            <div class="admin-table-wrapper report-products-table">
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
                            <span class="rank-medal" style="background:#fff8df;color:#b7791f">01</span>
                                        <?php elseif ($i === 1): ?>
                            <span class="rank-medal">02</span>
                                        <?php elseif ($i === 2): ?>
                            <span class="rank-medal" style="background:#fff1e8;color:#b45309">03</span>
                                        <?php else: ?>
                            <span class="rank-medal"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
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
            <div class="admin-table-wrapper report-customers-table">
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
                                            <span class="rank-medal" style="background:#fff8df;color:#b7791f">01</span>
                                        <?php elseif ($rank === 2): ?>
                                            <span class="rank-medal">02</span>
                                        <?php elseif ($rank === 3): ?>
                                            <span class="rank-medal" style="background:#fff1e8;color:#b45309">03</span>
                                        <?php else: ?>
                                            <span class="rank-medal"><?= str_pad((string)$rank, 2, '0', STR_PAD_LEFT) ?></span>
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

 </div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
