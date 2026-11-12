<?php
class UserAdminController extends Controller {
    private $userModel;
    private $orderModel;
    public function __construct() { parent::__construct(); $this->userModel = new UserModel(); $this->orderModel = new OrderModel(); }
    
    public function index() {
        $this->requireAdmin();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = sanitize($_GET['q'] ?? '');
        $status = sanitize($_GET['status'] ?? '');
        $total = $this->userModel->count($search, $status);
        $this->view('admin.users.index', ['title' => 'Quản lý khách hàng - Admin', 'users' => $this->userModel->getAll($page, 20, $search, $status), 'search' => $search, 'filterStatus' => $status, 'currentPage' => $page, 'totalPages' => ceil($total / 20), 'total' => $total]);
    }
    
    public function detail($id) {
        $this->requireAdmin();
        $user = $this->userModel->findById($id);
        if (!$user) { setFlash('error', 'Người dùng không tồn tại'); $this->redirect(url('admin/users')); return; }
        $orders = $this->orderModel->getUserOrders($id);
        $this->view('admin.users.detail', ['title' => 'Chi tiết khách hàng - Admin', 'user' => $user, 'orders' => $orders]);
    }
    
    public function setStatus($id) {
        $this->requireAdmin();
        $status = sanitize($this->getInput('status', 'active'));
        $this->userModel->setStatus($id, $status);
        $msg = $status === 'banned' ? 'Đã khóa tài khoản' : 'Đã kích hoạt tài khoản';
        setFlash('success', $msg);
        $this->redirect(url('admin/users/' . $id));
    }
}
?>
