<?php
class OrderController extends Controller {
    private $orderModel;
    
    public function __construct() {
        parent::__construct();
        $this->orderModel = new OrderModel();
    }
    
    public function index() {
        $this->requireAuth();
        $status = sanitize($_GET['status'] ?? '');
        $orders = $this->orderModel->getUserOrders($this->getUserId(), $status);
        
        $this->view('orders.index', [
            'title' => 'Đơn hàng của tôi - ' . APP_NAME,
            'orders' => $orders,
            'currentStatus' => $status,
        ]);
    }
    
    public function detail($id) {
        $this->requireAuth();
        $order = $this->orderModel->findById($id);
        
        if (!$order || $order['user_id'] != $this->getUserId()) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect(url('orders'));
            return;
        }
        
        $orderDetails = $this->orderModel->getDetails($id);
        
        $this->view('orders.detail', [
            'title' => 'Chi tiết đơn hàng #' . e($order['order_number']) . ' - ' . APP_NAME,
            'order' => $order,
            'orderDetails' => $orderDetails,
            'statusHistory' => $this->orderModel->getStatusHistory($id),
        ]);
    }
    
    public function cancel($id) {
        $this->requireAuth();
        $this->validateCsrf();
        
        if (!$this->orderModel->canCancel($id, $this->getUserId())) {
            setFlash('error', 'Không thể hủy đơn hàng này');
            $this->redirect(url('orders/' . $id));
            return;
        }
        
        $reason = sanitize($this->getInput('reason', 'Khách hàng hủy đơn'));
        $this->orderModel->cancelOrder($id, $reason);
        
        setFlash('success', 'Đã hủy đơn hàng thành công');
        $this->redirect(url('orders/' . $id));
    }

    public function returnRequest($id) {
        $this->requireAuth();
        $this->validateCsrf();
        $requestType = sanitize($this->getInput('request_type', 'Đổi trả'));
        $detail = sanitize($this->getInput('reason_detail', $this->getInput('reason', '')));
        $allowedTypes = ['Đổi sản phẩm', 'Trả hàng hoàn tiền', 'Hoàn tiền', 'Đổi trả'];
        if (!in_array($requestType, $allowedTypes, true)) {
            $requestType = 'Đổi trả';
        }
        $reason = trim($requestType . ($detail !== '' ? ': ' . $detail : ''));
        if ($this->orderModel->requestReturn($id, $this->getUserId(), $reason)) {
            sendOrderStatusEmail($id, 'return_requested');
            $order = $this->orderModel->findById($id);
            $notification = new NotificationModel();
            $notification->createForStaff(
                'return_request',
                'Yêu cầu đổi trả mới',
                '#' . ($order['order_number'] ?? $id) . ' - ' . $reason,
                ['order_id' => (int)$id, 'url' => url('admin/orders/detail/' . $id), 'reason' => $reason]
            );
            sendReturnRequestEmail($id, $reason);
            setFlash('success', 'Yêu cầu đổi trả/hoàn tiền đã được ghi nhận.');
        } else {
            setFlash('error', 'Chỉ đơn đã giao mới có thể yêu cầu đổi trả.');
        }
        $this->redirect(url('orders/' . $id));
    }
    
    public function track() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
        }

        $lookupSubmitted = $_SERVER['REQUEST_METHOD'] === 'POST';
        $orderNumber = sanitize($this->getInput('order_number', ''));
        $shipPhone = sanitize($this->getInput('ship_phone', ''));
        $order = null;

        $now = time();
        $attempts = array_filter(
            $_SESSION['order_track_attempts'] ?? [],
            static function ($attemptedAt) use ($now) {
                return is_int($attemptedAt) && $attemptedAt > $now - 600;
            }
        );
        $rateLimited = count($attempts) >= 5;

        if ($lookupSubmitted && !$rateLimited) {
            $attempts[] = $now;
            $_SESSION['order_track_attempts'] = array_values($attempts);
        }

        if ($lookupSubmitted && !$rateLimited && $orderNumber !== '' && $shipPhone !== '') {
            $cleanedOrderNumber = ltrim(trim($orderNumber), '#');
            $order = $this->orderModel->findByOrderNumber($cleanedOrderNumber);
            $submittedPhone = preg_replace('/\D+/', '', $shipPhone);
            $storedPhone = preg_replace('/\D+/', '', $order['ship_phone'] ?? '');
            if (!$order || $submittedPhone === '' || !hash_equals($storedPhone, $submittedPhone)) {
                $order = null;
            }
        }
        
        $this->view('orders.track', [
            'title' => 'Theo dõi đơn hàng - ' . APP_NAME,
            'order' => $order,
            'orderNumber' => $orderNumber,
            'shipPhone' => $shipPhone,
            'lookupSubmitted' => $lookupSubmitted,
            'rateLimited' => $rateLimited,
        ]);
    }
    
    public function invoice($id) {
        $this->requireAuth();
        $order = $this->orderModel->findById($id);
        
        if (!$order || $order['user_id'] != $this->getUserId()) {
            setFlash('error', 'Đơn hàng không tồn tại hoặc bạn không có quyền xem hóa đơn này.');
            $this->redirect(url('orders'));
            return;
        }
        
        $orderDetails = $this->orderModel->getDetails($id);
        $this->view('admin.orders.invoice', [
            'title' => 'Hóa đơn #' . e($order['order_number']),
            'order' => $order,
            'orderDetails' => $orderDetails
        ]);
    }
}
?>
