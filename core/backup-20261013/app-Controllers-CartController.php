<?php
/**
 * CartController - Giỏ hàng
 */
class CartController extends Controller {
    private $cartModel;
    private $productModel;
    
    public function __construct() {
        parent::__construct();
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
    }
    
    private function getSessionId() {
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = session_id();
        }
        return $_SESSION['cart_session_id'];
    }
    
    private function getCart() {
        $userId = $this->isLoggedIn() ? $this->getUserId() : null;
        $sessionId = $this->getSessionId();
        return $this->cartModel->getOrCreate($userId, $sessionId);
    }
    
    public function index() {
        // Clear Buy Now state if viewing the shopping cart
        unset($_SESSION['buy_now']);
        
        $cart = $this->getCart();
        $items = $this->cartModel->getItems($cart['id']);
        
        $subtotal = 0;
        foreach ($items as &$item) {
            $price = $item['price_discount'] ?? $item['price_sale'];
            $item['current_price'] = $price;
            $item['item_total'] = $price * $item['quantity'];
            $subtotal += $item['item_total'];
        }
        
        $shippingFee = $subtotal >= SHIPPING_FEE_FREE_AMOUNT ? 0 : SHIPPING_FEE_STANDARD;
        
        // Tự động kiểm tra và tính toán lại hoặc hủy coupon trong session khi giỏ hàng đổi
        $discount = 0;
        if (isset($_SESSION['coupon'])) {
            $couponModel = new CouponModel();
            $coupon = $couponModel->findById($_SESSION['coupon']['id']);
            if ($coupon && $coupon['status'] === 'active' && strtotime($coupon['start_date']) <= time() && strtotime($coupon['end_date']) >= time()) {
                $discountAmt = $couponModel->calculateDiscount($coupon, $subtotal);
                if ($discountAmt > 0) {
                    $_SESSION['coupon']['discount'] = $discountAmt;
                    $discount = $discountAmt;
                } else {
                    unset($_SESSION['coupon']);
                }
            } else {
                unset($_SESSION['coupon']);
            }
        }
        
        $this->view('cart.index', [
            'title' => 'Giỏ hàng - ' . APP_NAME,
            'items' => $items,
            'subtotal' => $subtotal,
            'shippingFee' => $shippingFee,
            'discount' => $discount,
            'total' => $subtotal + $shippingFee - $discount,
            'cart' => $cart,
        ]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid method'], 405);
            return;
        }
        
        $productId = (int)($this->getInput('product_id', 0));
        $quantity = max(1, (int)($this->getInput('quantity', 1)));
        
        if (!$productId) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
            return;
        }
        
        $product = $this->productModel->findById($productId);
        
        if (!$product || $product['status'] !== 'active') {
            $this->json(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
            return;
        }
        
        if ($product['stock_quantity'] < $quantity) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không đủ hàng. Tồn kho: ' . $product['stock_quantity']]);
            return;
        }
        
        $price = $product['price_discount'] ?? $product['price_sale'];
        $cart = $this->getCart();
        $this->cartModel->addItem($cart['id'], $productId, $quantity, $price);
        
        // Cập nhật cart count
        $cartCount = $this->cartModel->getItemCount($cart['id']);
        $_SESSION['cart_count'] = $cartCount;
        
        $this->json([
            'success' => true,
            'message' => 'Đã thêm ' . e($product['name']) . ' vào giỏ hàng',
            'cartCount' => $cartCount,
        ]);
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false], 405);
            return;
        }
        
        $itemId = (int)$this->getInput('item_id', 0);
        $quantity = (int)$this->getInput('quantity', 1);
        
        $cart = $this->getCart();
        $this->cartModel->updateItem($cart['id'], $itemId, $quantity);
        
        // Recalculate
        $items = $this->cartModel->getItems($cart['id']);
        $subtotal = 0;
        $itemTotal = 0;
        foreach ($items as $item) {
            $price = $item['price_discount'] ?? $item['price_sale'];
            $itemSubtotal = $price * $item['quantity'];
            $subtotal += $itemSubtotal;
            if ($item['id'] == $itemId) {
                $itemTotal = $itemSubtotal;
            }
        }
        $shippingFee = $subtotal >= SHIPPING_FEE_FREE_AMOUNT ? 0 : SHIPPING_FEE_STANDARD;
        
        $discount = 0;
        $couponCode = '';
        if (isset($_SESSION['coupon'])) {
            $couponModel = new CouponModel();
            $coupon = $couponModel->findById($_SESSION['coupon']['id']);
            if ($coupon && $coupon['status'] === 'active' && strtotime($coupon['start_date']) <= time() && strtotime($coupon['end_date']) >= time()) {
                $discountAmt = $couponModel->calculateDiscount($coupon, $subtotal);
                if ($discountAmt > 0) {
                    $_SESSION['coupon']['discount'] = $discountAmt;
                    $discount = $discountAmt;
                    $couponCode = $coupon['coupon_code'];
                } else {
                    unset($_SESSION['coupon']);
                }
            } else {
                unset($_SESSION['coupon']);
            }
        }
        
        $cartCount = $this->cartModel->getItemCount($cart['id']);
        $_SESSION['cart_count'] = $cartCount;
        
        $this->json([
            'success' => true,
            'subtotal' => formatPrice($subtotal),
            'shippingFee' => formatPrice($shippingFee),
            'discount' => formatPrice($discount),
            'discountRaw' => $discount,
            'couponCode' => $couponCode,
            'total' => formatPrice($subtotal + $shippingFee - $discount),
            'cartCount' => $cartCount,
            'itemTotal' => formatPrice($itemTotal)
        ]);
    }
    
    public function remove() {
        $itemId = (int)$this->getInput('item_id', 0);
        $cart = $this->getCart();
        $this->cartModel->removeItem($cart['id'], $itemId);
        
        $cartCount = $this->cartModel->getItemCount($cart['id']);
        $_SESSION['cart_count'] = $cartCount;
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->json(['success' => true, 'cartCount' => $cartCount]);
        } else {
            setFlash('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
            $this->redirect(url('cart'));
        }
    }
    
    public function count() {
        $this->json(['count' => getCartCount()]);
    }
}
?>
