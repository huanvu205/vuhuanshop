<?php
class WishlistController extends Controller {
    private $wishlistModel;
    
    public function __construct() {
        parent::__construct();
        $this->wishlistModel = new WishlistModel();
    }
    
    public function index() {
        $this->requireAuth();
        $items = $this->wishlistModel->getUserWishlist($this->getUserId());
        $this->view('user.wishlist', [
            'title' => 'Sản phẩm yêu thích - ' . APP_NAME,
            'items' => $items,
        ]);
    }
    
    public function toggle() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->json(['success'=>false], 405); return; }
        $this->validateCsrf();
        $productId = (int)$this->getInput('product_id', 0);
        if ($productId < 1) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ'], 400);
            return;
        }
        $added = $this->wishlistModel->toggle($this->getUserId(), $productId);
        $this->json([
            'success' => true,
            'added' => $added,
            'message' => $added ? 'Đã thêm vào yêu thích' : 'Đã xóa khỏi yêu thích',
        ]);
    }
    
    /**
     * Xóa 1 sản phẩm khỏi wishlist.
     * Hỗ trợ:
     *   - AJAX POST với JSON body { product_id: ... }  (từ trang wishlist)
     *   - GET /wishlist/remove/{productId}             (fallback cũ)
     */
    public function remove($productId = null) {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();

        // Ưu tiên product_id từ body (AJAX JSON)
        if ($productId === null) {
            $productId = (int)$this->getInput('product_id', 0);
        } else {
            $productId = (int)$productId;
        }

        if ($productId < 1) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                $this->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ'], 400);
            } else {
                setFlash('error', 'Sản phẩm không hợp lệ');
                $this->redirect(url('wishlist'));
            }
            return;
        }

        $this->wishlistModel->remove($this->getUserId(), $productId);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            // Trả về số lượng còn lại trong wishlist
            $remaining = count($this->wishlistModel->getUserWishlist($this->getUserId()));
            $this->json(['success' => true, 'remaining' => $remaining]);
        } else {
            setFlash('success', 'Đã xóa khỏi danh sách yêu thích');
            $this->redirect(url('wishlist'));
        }
    }

    /**
     * Xóa toàn bộ wishlist (form POST "Xóa tất cả")
     */
    public function clear() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $userId = $this->getUserId();
        $this->wishlistModel->clearAll($userId);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->json(['success' => true]);
        } else {
            setFlash('success', 'Đã xóa toàn bộ danh sách yêu thích');
            $this->redirect(url('wishlist'));
        }
    }
}
?>
