<?php
class CouponController extends Controller {
    private $couponModel;
    public function __construct() { parent::__construct(); $this->couponModel = new CouponModel(); }
    
    public function index() {
        $this->requireAdmin();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $this->view('admin.coupons.index', ['title' => 'Quản lý mã giảm giá - Admin', 'coupons' => $this->couponModel->getAll($page, 20), 'currentPage' => $page, 'totalPages' => ceil($this->couponModel->count() / 20)]);
    }
    
    public function create() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $data = ['coupon_code' => sanitize($this->getInput('coupon_code', '')), 'description' => sanitize($this->getInput('description', '')), 'discount_type' => $this->getInput('discount_type', 'percent'), 'discount_value' => (float)$this->getInput('discount_value', 0), 'max_discount' => $this->getInput('max_discount') ? (float)$this->getInput('max_discount') : null, 'min_order_amount' => $this->getInput('min_order_amount') ? (float)$this->getInput('min_order_amount') : null, 'max_uses' => $this->getInput('max_uses') ? (int)$this->getInput('max_uses') : null, 'start_date' => $this->getInput('start_date'), 'end_date' => $this->getInput('end_date'), 'status' => $this->getInput('status', 'active')];
            $this->couponModel->create($data);
            setFlash('success', 'Tạo mã giảm giá thành công');
            $this->redirect(url('admin/coupons'));
        }
        $this->view('admin.coupons.create', ['title' => 'Tạo mã giảm giá - Admin']);
    }
    
    public function edit($id) {
        $this->requireAdmin();
        $coupon = $this->couponModel->findById($id);
        if (!$coupon) { setFlash('error', 'Mã không tồn tại'); $this->redirect(url('admin/coupons')); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $data = ['description' => sanitize($this->getInput('description', '')), 'discount_type' => $this->getInput('discount_type', 'percent'), 'discount_value' => (float)$this->getInput('discount_value', 0), 'max_discount' => $this->getInput('max_discount') ? (float)$this->getInput('max_discount') : null, 'min_order_amount' => $this->getInput('min_order_amount') ? (float)$this->getInput('min_order_amount') : null, 'max_uses' => $this->getInput('max_uses') ? (int)$this->getInput('max_uses') : null, 'start_date' => $this->getInput('start_date'), 'end_date' => $this->getInput('end_date'), 'status' => $this->getInput('status', 'active')];
            $this->couponModel->update($id, $data);
            setFlash('success', 'Cập nhật mã giảm giá thành công');
            $this->redirect(url('admin/coupons'));
        }
        $this->view('admin.coupons.edit', ['title' => 'Sửa mã giảm giá - Admin', 'coupon' => $coupon]);
    }
    
    public function delete($id) {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $this->couponModel->delete($id);
        setFlash('success', 'Đã xóa mã giảm giá');
        $this->redirect(url('admin/coupons'));
    }
}
?>
