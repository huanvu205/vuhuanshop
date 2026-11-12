<?php
class InventoryController extends Controller {
    private $inventoryModel;
    private $productModel;
    public function __construct() { parent::__construct(); $this->inventoryModel = new InventoryModel(); $this->productModel = new ProductModel(); }
    
    public function index() {
        $this->requireStaff();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = sanitize($_GET['q'] ?? '');
        $lowStock = !empty($_GET['low_stock']);
        $total = $this->inventoryModel->count($search, $lowStock);
        $this->view('admin.inventory.index', ['title' => 'Quản lý kho - Admin', 'inventory' => $this->inventoryModel->getAll($page, 20, $search, $lowStock), 'search' => $search, 'lowStock' => $lowStock, 'currentPage' => $page, 'totalPages' => ceil($total / 20), 'total' => $total, 'lowStockProducts' => $this->productModel->getLowStock(10)]);
    }
    
    public function import() {
        $this->requireStaff();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $productId = (int)$this->getInput('product_id', 0);
            $quantity = (int)$this->getInput('quantity', 0);
            $notes = sanitize($this->getInput('notes', ''));
            if ($productId && $quantity > 0) {
                $this->inventoryModel->import($productId, $quantity, $notes, $this->getUserId());
                setFlash('success', 'Nhập kho thành công: ' . $quantity . ' sản phẩm');
            } else {
                setFlash('error', 'Thông tin không hợp lệ');
            }
            $this->redirect(url('admin/inventory'));
        }
        $products = $this->productModel->getAllAdmin(1, 1000);
        $this->view('admin.inventory.import', ['title' => 'Nhập kho - Admin', 'products' => $products]);
    }
    
    public function logs() {
        $this->requireStaff();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $productId = (int)($_GET['product_id'] ?? 0);
        $logs = $this->inventoryModel->getLogs($productId ?: null, $page, 30);
        $this->view('admin.inventory.logs', ['title' => 'Lịch sử kho - Admin', 'logs' => $logs]);
    }
}
?>
