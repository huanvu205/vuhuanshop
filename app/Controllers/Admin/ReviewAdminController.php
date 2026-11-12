<?php
class ReviewAdminController extends Controller {
    private $reviewModel;
    public function __construct() { parent::__construct(); $this->reviewModel = new ReviewModel(); }
    
    public function index() {
        $this->requireStaff();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = sanitize($_GET['q'] ?? '');
        $status = sanitize($_GET['status'] ?? '');
        $rating = (int)($_GET['rating'] ?? 0);
        $total = $this->reviewModel->count($search, $status, $rating ?: '');
        $this->view('admin.reviews.index', ['title' => 'Quản lý đánh giá - Admin', 'reviews' => $this->reviewModel->getAll($page, 20, $search, $status, $rating ?: ''), 'search' => $search, 'filterStatus' => $status, 'filterRating' => $rating, 'currentPage' => $page, 'totalPages' => ceil($total / 20), 'total' => $total]);
    }
    
    public function approve($id) {
        $this->requireStaff();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $this->reviewModel->updateStatus($id, 'approved');
        setFlash('success', 'Đã duyệt đánh giá');
        $this->redirect(url('admin/reviews'));
    }
    
    public function reject($id) {
        $this->requireStaff();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $this->reviewModel->updateStatus($id, 'rejected');
        setFlash('success', 'Đã từ chối đánh giá');
        $this->redirect(url('admin/reviews'));
    }
    
    public function delete($id) {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $this->reviewModel->delete($id);
        setFlash('success', 'Đã xóa đánh giá');
        $this->redirect(url('admin/reviews'));
    }
}
?>
