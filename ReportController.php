<?php
class ReportController extends Controller {
    private $orderModel;
    private $productModel;
    private $userModel;
    private $brandModel;
    
    public function __construct() {
        parent::__construct();
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
        $this->brandModel = new BrandModel();
    }
    
    public function index() {
        $this->requireAdmin();
        $period = sanitize($_GET['period'] ?? 'month');
        $year = (int)($_GET['year'] ?? date('Y'));
        
        $revenueByDay = $this->orderModel->getRevenueByDay(30);
        $revenueByMonth = $this->orderModel->getRevenueByMonth($year);
        $revenueByYear = $this->orderModel->getRevenueByYear(5);
        $topProducts = $this->productModel->getTopSelling(10);
        $topCustomers = $this->userModel->getTopCustomers(10);
        $orderStats = $this->orderModel->getStats();
        $userStats = $this->userModel->getStats();
        $productStats = $this->productModel->getStats();
        
        $this->view('admin.reports.index', [
            'title' => 'Báo cáo thống kê - Admin',
            'period' => $period,
            'year' => $year,
            'revenueByDay' => $revenueByDay,
            'revenueByMonth' => $revenueByMonth,
            'revenueByYear' => $revenueByYear,
            'topProducts' => $topProducts,
            'topCustomers' => $topCustomers,
            'orderStats' => $orderStats,
            'userStats' => $userStats,
            'productStats' => $productStats,
        ]);
    }
    
    public function exportOrders() {
        $this->requireAdmin();
        $orders = $this->orderModel->getAll(1, 10000);
        
        $excelEncoding = function_exists('iconv') ? 'UTF-16LE' : 'UTF-8';
        header('Content-Type: text/csv; charset=' . $excelEncoding);
        header('Content-Disposition: attachment; filename="orders_' . date('Y-m-d') . '.csv"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        $output = fopen('php://output', 'wb');
        // UTF-16LE with BOM is detected reliably by older Excel versions using Windows code pages.
        fwrite($output, $excelEncoding === 'UTF-16LE' ? "\xFF\xFE" : "\xEF\xBB\xBF");
        $separatorHint = "sep=;\r\n";
        fwrite($output, $excelEncoding === 'UTF-16LE' ? iconv('UTF-8', 'UTF-16LE', $separatorHint) : $separatorHint);
        $writeRow = static function ($stream, array $row) use ($excelEncoding) {
            $buffer = fopen('php://temp', 'w+b');
            fputcsv($buffer, $row, ';', '"', '\\');
            rewind($buffer);
            $line = stream_get_contents($buffer);
            fclose($buffer);
            if ($excelEncoding === 'UTF-16LE') {
                $line = iconv('UTF-8', 'UTF-16LE', $line);
            }
            fwrite($stream, $line);
        };
        $safeText = static function ($value) {
            $value = (string)($value ?? '');
            // Prevent spreadsheet formula execution for text imported from user data.
            if (preg_match('/^[\\t\\r ]*[=+@-]/u', $value)) $value = "'" . $value;
            return $value;
        };

        $writeRow($output, ['Mã đơn hàng', 'Khách hàng', 'Điện thoại', 'Địa chỉ', 'Tổng tiền', 'Trạng thái', 'Thanh toán', 'Ngày đặt']);
        foreach ($orders as $order) {
            $writeRow($output, [
                $safeText($order['order_number'] ?? ''), $safeText($order['customer_name'] ?? ''),
                $safeText($order['ship_phone'] ?? ''), $safeText($order['ship_address'] ?? ''),
                $order['final_amount'] ?? 0, $safeText($order['status'] ?? ''),
                $safeText($order['payment_status'] ?? ''), $safeText($order['created_at'] ?? ''),
            ]);
        }
        fclose($output);
        exit;
    }
}
?>
