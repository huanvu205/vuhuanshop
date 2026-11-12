<?php
class OrderAdminController extends Controller {
    private $orderModel;
    private $productModel;
    
    public function __construct() {
        parent::__construct();
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
    }
    
    public function index() {
        $this->requireStaff();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = sanitize($_GET['q'] ?? '');
        $status = sanitize($_GET['status'] ?? '');
        $payment = sanitize($_GET['payment'] ?? '');
        
        $orders = $this->orderModel->getAll($page, 20, $search, $status, $payment);
        $total = $this->orderModel->count($search, $status, $payment);
        
        $this->view('admin.orders.index', [
            'title' => 'Quản lý đơn hàng - Admin',
            'orders' => $orders,
            'search' => $search,
            'filterStatus' => $status,
            'filterPayment' => $payment,
            'currentPage' => $page,
            'totalPages' => ceil($total / 20),
            'total' => $total,
        ]);
    }
    
    public function detail($id) {
        $this->requireStaff();
        $order = $this->orderModel->findById($id);
        if (!$order) { setFlash('error', 'Đơn hàng không tồn tại'); $this->redirect(url('admin/orders')); return; }
        $orderDetails = $this->orderModel->getDetails($id);
        $vnpayPayment = ($order['payment_method'] ?? '') === 'vnpay' ? $this->orderModel->getVnpayPayment($id) : false;
        $this->view('admin.orders.detail', [
            'title' => 'Chi tiết đơn hàng #' . e($order['order_number']) . ' - Admin',
            'order' => $order,
            'orderDetails' => $orderDetails,
            'statusHistory' => $this->orderModel->getStatusHistory($id),
            'vnpayPayment' => $vnpayPayment,
            'vnpayRefunds' => ($order['payment_method'] ?? '') === 'vnpay' ? $this->orderModel->getVnpayRefunds($id) : [],
            'refundedAmount' => ($order['payment_method'] ?? '') === 'vnpay' ? $this->orderModel->getRefundedAmount($id) : 0,
        ]);
    }
    
    public function updateStatus($id) {
        $this->requireStaff();
        $this->validateCsrf();
        $status = sanitize($this->getInput('status', ''));
        $adminNote = sanitize($this->getInput('admin_note', ''));
        
        if (!$this->orderModel->canTransitionTo($id, $status)) {
            setFlash('error', 'Trạng thái không hợp lệ');
        } else {
            $updated = $this->orderModel->updateStatus($id, $status, $adminNote);
            if ($updated) {
                sendOrderStatusEmail($id, $status);
                setFlash('success', 'Cập nhật trạng thái đơn hàng thành công');
            } else {
                setFlash('error', 'Không thể chuyển đơn hàng sang trạng thái này.');
            }
        }
        
        $this->redirect(url('admin/orders/' . $id));
    }

    public function requestRefund($id) {
        $this->requireStaff();
        $this->validateCsrf();
        $order = $this->orderModel->findById($id);
        $payment = $this->orderModel->getVnpayPayment($id);
        $amountInput = $this->getInput('amount', '');
        $amount = is_numeric($amountInput) ? (float)$amountInput : 0;
        if (!$order || !$payment || !in_array($order['status'], ['return_requested','return_received'], true)
            || $order['payment_status'] !== 'paid' || $amount <= 0 || floor($amount) !== $amount) {
            setFlash('error', 'Đơn không đủ điều kiện hoàn tiền VNPay hoặc số tiền không hợp lệ.');
            $this->redirect(url('admin/orders/' . $id));
            return;
        }
        if (!VnpayService::isConfiguredForSandbox()) {
            setFlash('error', 'VNPay Sandbox chưa sẵn sàng. Không gửi yêu cầu hoàn tiền.');
            $this->redirect(url('admin/orders/' . $id));
            return;
        }
        $requestId = bin2hex(random_bytes(16));
        $reason = sanitize($this->getInput('reason', 'Hoàn tiền theo yêu cầu đổi trả'));
        $refundId = $this->orderModel->createVnpayRefundRequest($id, $amount, $requestId, $this->getUserId(), $reason);
        if (!$refundId) {
            setFlash('error', 'Không tạo được yêu cầu. Có thể đã có yêu cầu đang xử lý hoặc số tiền vượt phần chưa hoàn.');
            $this->redirect(url('admin/orders/' . $id));
            return;
        }
        $result = VnpayService::refund([
            'vnp_RequestId' => $requestId,
            'vnp_TransactionType' => ($this->orderModel->getRefundedAmount($id) <= 0 && $amount >= $payment['amount']) ? '02' : '03',
            'vnp_TxnRef' => $payment['provider_txn_ref'],
            'vnp_Amount' => (int)round($amount * 100),
            'vnp_TransactionNo' => $payment['provider_transaction_no'] ?: '0',
            'vnp_TransactionDate' => $payment['provider_transaction_date'],
            'vnp_CreateBy' => (string)($this->getUser()['full_name'] ?? $this->getUserId()),
            'vnp_OrderInfo' => 'Refund order ' . $order['order_number'] . ': ' . $reason,
        ]);
        if (!$result['verified']) {
            $this->orderModel->updateVnpayRefundResult($refundId, $result);
            setFlash('warning', 'Chưa xác định được kết quả hoàn tiền. Yêu cầu đã khóa chống gửi trùng; hãy đối soát trên cổng VNPay Sandbox trước khi thử lại.');
        } else {
            $this->orderModel->updateVnpayRefundResult($refundId, $result);
            if ($result['state'] === 'succeeded') {
                $updatedOrder = $this->orderModel->findById($id);
                if (($updatedOrder['status'] ?? '') === 'refunded') sendOrderStatusEmail($id, 'refunded');
                setFlash('success', 'VNPay đã xác nhận giao dịch hoàn tiền thành công.');
            } elseif ($result['state'] === 'processing') {
                setFlash('info', 'VNPay đã nhận yêu cầu; tiền hoàn vẫn đang được xử lý.');
            } elseif ($result['state'] === 'unknown') {
                setFlash('warning', 'VNPay báo yêu cầu trùng/đang xử lý. Không gửi lại; cần đối soát giao dịch.');
            } else {
                setFlash('error', 'VNPay từ chối yêu cầu hoàn tiền. Đơn chưa được đánh dấu hoàn tiền.');
            }
        }
        $this->redirect(url('admin/orders/' . $id));
    }
    
    public function invoice($id) {
        $this->requireStaff();
        $order = $this->orderModel->findById($id);
        $orderDetails = $this->orderModel->getDetails($id);
        $this->view('admin.orders.invoice', [
            'title' => 'Hóa đơn #' . e($order['order_number']),
            'order' => $order,
            'orderDetails' => $orderDetails,
            'statusHistory' => $this->orderModel->getStatusHistory($id),
        ]);
    }
}
?>
