<?php
class ReviewController extends Controller {
    private $reviewModel;
    private $orderModel;
    
    public function __construct() {
        parent::__construct();
        $this->reviewModel = new ReviewModel();
        $this->orderModel = new OrderModel();
    }
    
    public function create() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(url());
            return;
        }
        
        $productId = (int)$this->getInput('product_id', 0);
        $rating = (int)$this->getInput('rating', 5);
        $comment = sanitize($this->getInput('comment', ''));
        $title = sanitize($this->getInput('title', ''));
        $orderId = (int)$this->getInput('order_id', 0);

        if ($productId < 1 || !$this->reviewModel->hasDeliveredPurchase($this->getUserId(), $productId, $orderId ?: null)) {
            $this->json(['success' => false, 'message' => 'Chỉ khách đã nhận sản phẩm mới có thể đánh giá']);
            return;
        }
        
        if ($rating < 1 || $rating > 5) $rating = 5;
        
        if ($this->reviewModel->hasReviewed($this->getUserId(), $productId)) {
            $this->json(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi']);
            return;
        }
        
        $reviewId = $this->reviewModel->create([
            'product_id' => $productId,
            'user_id' => $this->getUserId(),
            'order_id' => $orderId ?: null,
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment,
        ]);
        
        if ($reviewId) {
            $this->json(['success' => true, 'message' => 'Đánh giá của bạn đã được gửi và đang chờ duyệt']);
        } else {
            $this->json(['success' => false, 'message' => 'Gửi đánh giá thất bại']);
        }
    }
}
?>
