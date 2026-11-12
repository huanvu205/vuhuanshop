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
        ]);
    }
    
    public function cancel($id) {
        $this->requireAuth();
        
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
    
    public function track() {
        $orderNumber = sanitize($_GET['order_number'] ?? '');
        $order = null;
        
        if ($orderNumber) {
            $cleanedOrderNumber = ltrim(trim($orderNumber), '#');
            $order = $this->orderModel->findByOrderNumber($cleanedOrderNumber);
        }
        
        $this->view('orders.track', [
            'title' => 'Theo dõi đơn hàng - ' . APP_NAME,
            'order' => $order,
            'orderNumber' => $orderNumber,
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
