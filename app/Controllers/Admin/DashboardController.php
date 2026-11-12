<?php
/**
 * Admin DashboardController - Bảng điều khiển quản trị
 */
class DashboardController extends Controller {
    /** @var OrderModel */
    private $orderModel;
    /** @var UserModel */
    private $userModel;
    /** @var ProductModel */
    private $productModel;
    /** @var ReviewModel */
    private $reviewModel;
    /** @var InventoryModel */
    private $inventoryModel;
    /** @var NotificationModel */
    private $notificationModel;
    
    public function __construct() {
        parent::__construct();
        $this->orderModel = new OrderModel();
        $this->userModel = new UserModel();
        $this->productModel = new ProductModel();
        $this->reviewModel = new ReviewModel();
        $this->inventoryModel = new InventoryModel();
        $this->notificationModel = new NotificationModel();
    }
    
    public function index() {
        $this->requireStaff();
        
        $orderStats = $this->orderModel->getStats();
        $userStats = $this->userModel->getStats();
        $productStats = $this->productModel->getStats();
        $revenueByDay = $this->orderModel->getRevenueByDay(30);
        $revenueByMonth = $this->orderModel->getRevenueByMonth();
        $topProducts = $this->productModel->getTopSelling(10);
        $lowStock = $this->productModel->getLowStock(10);
        $pendingReviews = $this->reviewModel->count('', 'pending');
        $recentOrders = $this->orderModel->getAll(1, 10);
        $paymentStats = $this->orderModel->getPaymentStats();
        
        $this->view('admin.dashboard', [
            'title' => 'Dashboard - Admin',
            'orderStats' => $orderStats,
            'userStats' => $userStats,
            'productStats' => $productStats,
            'revenueByDay' => $revenueByDay,
            'revenueByMonth' => $revenueByMonth,
            'topProducts' => $topProducts,
            'lowStock' => $lowStock,
            'pendingReviews' => $pendingReviews,
            'recentOrders' => $recentOrders,
            'paymentStats' => $paymentStats,
        ]);
    }

    public function markRead() {
        $this->requireStaff();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Yêu cầu không hợp lệ'], 400);
            return;
        }
        
        $this->validateCsrf();
        
        $id = (int)$this->getInput('id');
        $adminUserId = $this->getUserId();
        
        if (!$id || !$adminUserId) {
            $this->json(['success' => false, 'message' => 'Thiếu dữ liệu'], 400);
            return;
        }
        
        $result = $this->notificationModel->markRead($id, $adminUserId);
        
        if ($result) {
            $this->json(['success' => true]);
        } else {
            $this->json(['success' => false, 'message' => 'Không thể cập nhật trạng thái'], 500);
        }
    }
    
    public function markAllRead() {
        $this->requireStaff();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Yêu cầu không hợp lệ'], 400);
            return;
        }
        
        $this->validateCsrf();
        
        $adminUserId = $this->getUserId();
        if (!$adminUserId) {
            $this->json(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
            return;
        }
        
        $result = $this->notificationModel->markAllRead($adminUserId);
        
        if ($result) {
            $this->json(['success' => true]);
        } else {
            $this->json(['success' => false, 'message' => 'Không thể cập nhật trạng thái'], 500);
        }
    }
}
?>
