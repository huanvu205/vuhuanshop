<?php
/**
 * WarrantyController - Tra cứu bảo hành điện tử
 * Cho phép khách hàng tra cứu thời hạn bảo hành theo Số điện thoại hoặc Mã bảo hành / Mã đơn hàng
 */
class WarrantyController extends Controller {
    private $orderModel;
    
    public function __construct() {
        parent::__construct();
        $this->orderModel = new OrderModel();
    }
    
    public function index() {
        $keyword = trim(sanitize($_GET['keyword'] ?? ''));
        $results = [];
        $searched = !empty($keyword);
        
        if ($searched) {
            $results = $this->searchWarranty($keyword);
        }
        
        $this->view('warranty.index', [
            'title' => 'Tra cứu bảo hành điện tử - ' . APP_NAME,
            'keyword' => $keyword,
            'results' => $results,
            'searched' => $searched,
        ]);
    }
    
    private function searchWarranty($keyword) {
        $cleanPhone = preg_replace('/\D+/', '', $keyword);
        $cleanCode = ltrim(trim($keyword), '#');
        $cleanCode = preg_replace('/^VH-?BH-?/i', '', $cleanCode);
        $cleanCode = preg_replace('/^VH-?/i', '', $cleanCode);
        
        $db = new Database();
        $orders = [];
        
        if (strlen($cleanPhone) >= 9) {
            // Tìm theo số điện thoại
            $orders = $db->query("SELECT * FROM orders WHERE REPLACE(REPLACE(ship_phone, ' ', ''), '.', '') LIKE :phone ORDER BY created_at DESC LIMIT 10")
                ->bind(':phone', '%' . $cleanPhone . '%')
                ->resultSet();
        }
        
        if (empty($orders) && !empty($cleanCode)) {
            // Tìm theo mã đơn hàng hoặc order_number
            $orders = $db->query("SELECT * FROM orders WHERE order_number LIKE :code OR id = :id LIMIT 5")
                ->bind(':code', '%' . $cleanCode . '%')
                ->bind(':id', is_numeric($cleanCode) ? (int)$cleanCode : 0)
                ->resultSet();
        }
        
        $warrantyItems = [];
        $orderStatusLabels = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang chuẩn bị hàng',
            'shipping' => 'Đang giao hàng',
            'delivered' => 'Đã giao thành công',
        ];

        foreach ($orders as $order) {
            // Không tính các đơn đã hủy
            if ($order['status'] === 'cancelled') {
                continue;
            }
            
            $details = $this->orderModel->getDetails($order['id']);
            $isDelivered = ($order['status'] === 'delivered');
            $now = time();

            if ($isDelivered) {
                // Đã giao hàng thành công -> Kích hoạt bảo hành từ ngày nhận máy
                $deliveryDate = !empty($order['delivery_date']) ? $order['delivery_date'] : ($order['updated_at'] ?? $order['created_at']);
                $startDate = strtotime($deliveryDate);
                // Mặc định bảo hành 12 tháng (365 ngày) tính từ ngày nhận hàng
                $endDate = strtotime('+12 months', $startDate);
                $isValid = ($now <= $endDate);
                $daysLeft = max(0, ceil(($endDate - $now) / 86400));
                $warrantyStatus = $isValid ? 'active' : 'expired';
                $startDateStr = date('d/m/Y', $startDate);
                $endDateStr = date('d/m/Y', $endDate);
            } else {
                // Đơn hàng chưa giao xong -> Chờ giao hàng để kích hoạt bảo hành
                $warrantyStatus = 'pending_delivery';
                $isValid = false;
                $daysLeft = 365;
                $startDateStr = 'Chờ nhận máy (Tự động kích hoạt)';
                $endDateStr = '12 tháng sau khi nhận hàng';
            }
            
            // Mask số điện thoại bảo mật
            $rawPhone = $order['ship_phone'];
            $maskedPhone = substr($rawPhone, 0, 3) . '****' . substr($rawPhone, -3);
            
            foreach ($details as $detail) {
                $warrantyCode = 'VH-BH' . str_pad($order['id'], 5, '0', STR_PAD_LEFT) . '-' . $detail['product_id'];
                
                $warrantyItems[] = [
                    'warranty_code' => $warrantyCode,
                    'order_number' => $order['order_number'],
                    'order_id' => $order['id'],
                    'product_name' => $detail['product_name'],
                    'variant' => $detail['variant'] ?? '',
                    'quantity' => $detail['quantity'],
                    'customer_name' => $order['ship_name'],
                    'customer_phone' => $maskedPhone,
                    'start_date' => $startDateStr,
                    'end_date' => $endDateStr,
                    'duration' => '12 Tháng Chính Hãng',
                    'is_valid' => $isValid,
                    'days_left' => $daysLeft,
                    'warranty_status' => $warrantyStatus, // 'active', 'expired', 'pending_delivery'
                    'order_status' => $order['status'],
                    'order_status_label' => $orderStatusLabels[$order['status']] ?? $order['status'],
                    'image' => $detail['main_image'] ?? $detail['image'] ?? '',
                ];
            }
        }
        
        return $warrantyItems;
    }
}

