<?php
if (!defined('VNPAY_TMN_CODE')) define('VNPAY_TMN_CODE', '');
if (!defined('VNPAY_HASH_SECRET')) define('VNPAY_HASH_SECRET', '');
if (!defined('VNPAY_URL')) define('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');

/**
 * CheckoutController - Thanh toán
 */
class CheckoutController extends Controller {
    /** @var CartModel */
    private $cartModel;
    /** @var OrderModel */
    private $orderModel;
    /** @var ProductModel */
    private $productModel;
    /** @var CouponModel */
    private $couponModel;
    /** @var UserModel */
    private $userModel;
    private $addressModel;
    
    public function __construct() {
        parent::__construct();
        $this->cartModel = new CartModel();
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->couponModel = new CouponModel();
        $this->userModel = new UserModel();
        $this->addressModel = new AddressModel();
    }
    
    private function getCart() {
        $userId = $this->isLoggedIn() ? $this->getUserId() : null;
        $sessionId = $_SESSION['cart_session_id'] ?? session_id();
        return $this->cartModel->getOrCreate($userId, $sessionId);
    }

    private function getSyntheticVariantAdjustment($product, $variantName) {
        if (!$product || $variantName === '') return null;
        $variants = getProductRealVariants($product);
        foreach (($variants['capacities'] ?? []) as $capacity) {
            if ((string)($capacity['label'] ?? '') === (string)$variantName) {
                return (float)($capacity['diff'] ?? 0);
            }
        }
        return null;
    }
    
    public function index() {
        $this->requireAuth();
        
        $items = [];
        $subtotal = 0;
        
        if (isset($_SESSION['buy_now'])) {
            $buyNow = $_SESSION['buy_now'];
            $product = $this->productModel->findById($buyNow['product_id']);
            $variantRecord = $product && !empty($buyNow['variant_id']) ? $this->productModel->getVariantById($buyNow['variant_id'], $product['id']) : null;
            $productVariants = $product ? $this->productModel->getVariants($product['id']) : [];
            $available = (int)($variantRecord['stock_quantity'] ?? ($product['stock_quantity'] ?? 0));
            $syntheticVariant = !empty($buyNow['variant']) ? trim((string)$buyNow['variant']) : '';
            $variantAdjustment = $syntheticVariant !== '' ? $this->getSyntheticVariantAdjustment($product, $syntheticVariant) : 0;
            if ($product && $product['status'] === 'active'
                && (int)$buyNow['quantity'] > 0
                && (!empty($variantRecord) || ($syntheticVariant !== '' && $variantAdjustment !== null) || (empty($productVariants) && empty($buyNow['variant_id'])))
                && (int)$buyNow['quantity'] <= $available) {
                $basePrice = (float)($product['price_discount'] ?? $product['price_sale']);
                $price = max(0, $basePrice + (float)($variantRecord['price_adjustment'] ?? $variantAdjustment));
                $items[] = [
                    'id' => null,
                    'product_id' => $product['id'],
                    'product_name' => $product['name'],
                    'product_slug' => $product['slug'],
                    'main_image' => $product['main_image'],
                    'brand_name' => $product['brand_name'] ?? '',
                    'price_sale' => $product['price_sale'],
                    'price_discount' => $product['price_discount'],
                    'quantity' => $buyNow['quantity'],
                    'stock_quantity' => $available,
                    'variant_id' => $variantRecord['id'] ?? null,
                    'variant_name' => $variantRecord['variant_name'] ?? ($syntheticVariant !== '' ? $syntheticVariant : null),
                    'variant_sku' => $variantRecord['sku'] ?? null,
                    'variant' => $variantRecord['variant_name'] ?? ($syntheticVariant !== '' ? $syntheticVariant : null),
                    'price' => $price,
                    'subtotal' => $price * $buyNow['quantity']
                ];
                $subtotal = $price * $buyNow['quantity'];
            } else {
                unset($_SESSION['buy_now']);
                $this->redirect(url('cart'));
                return;
            }
        } else {
            $cart = $this->getCart();
            $items = $this->cartModel->getItems($cart['id']);
            
            if (empty($items)) {
                setFlash('warning', 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán.');
                $this->redirect(url('cart'));
                return;
            }
            
            foreach ($items as &$item) {
                if ($item['status'] !== 'active' || (int)$item['quantity'] > (int)$item['stock_quantity']) {
                    setFlash('error', 'Một sản phẩm trong giỏ không còn đủ hàng. Vui lòng cập nhật giỏ hàng.');
                    $this->redirect(url('cart'));
                    return;
                }
                $variantRecord = !empty($item['variant_id']) ? $this->productModel->getVariantById($item['variant_id'], $item['product_id']) : null;
                $syntheticVariant = trim((string)($item['variant'] ?? ''));
                $syntheticAdjustment = $syntheticVariant !== '' ? $this->getSyntheticVariantAdjustment($this->productModel->findById($item['product_id']), $syntheticVariant) : null;
                $item['variant'] = $variantRecord['variant_name'] ?? $syntheticVariant;
                $item['variant_sku'] = $variantRecord['sku'] ?? null;
                $price = max(0, (float)($item['price_discount'] ?? $item['price_sale']) + (float)($variantRecord['price_adjustment'] ?? $syntheticAdjustment ?? 0));
                $item['current_price'] = $price;
                $item['item_total'] = $price * $item['quantity'];
                $item['subtotal'] = $item['item_total'];
                $subtotal += $item['item_total'];
            }
        }
        
        $user = null;
        if ($this->isLoggedIn()) {
            $user = $this->userModel->findById($this->getUserId());
            if ($user) {
                $_SESSION['user'] = array_merge($_SESSION['user'] ?? [], $user);
            }
        }
        if (!$user) {
            $user = $this->getUser();
        }
        $shippingFee = $subtotal >= getShippingFeeFreeAmount() ? 0 : getShippingFeeStandard();
        
        // Tự động kiểm tra và tính toán lại giá trị mã giảm giá
        if (isset($_SESSION['coupon'])) {
            $coupon = $this->couponModel->findById($_SESSION['coupon']['id']);
            if ($coupon && $coupon['status'] === 'active' && strtotime($coupon['start_date']) <= time() && strtotime($coupon['end_date']) >= time()) {
                if (!empty($coupon['max_uses']) && (int)$coupon['used_count'] >= (int)$coupon['max_uses']) {
                    unset($_SESSION['coupon']);
                } else
                if ($this->isLoggedIn() && !$this->couponModel->isValidForUser($coupon['id'], $this->getUserId())) {
                    unset($_SESSION['coupon']);
                } else {
                    $discountAmt = $this->couponModel->calculateDiscount($coupon, $subtotal);
                    if ($discountAmt > 0) {
                        $_SESSION['coupon']['discount'] = $discountAmt;
                        $_SESSION['coupon']['code'] = $coupon['coupon_code'];
                    } else {
                        unset($_SESSION['coupon']);
                    }
                }
            } else {
                unset($_SESSION['coupon']);
            }
        }
        
        $discount = 0;
        $couponDiscount = 0;
        if (isset($_SESSION['coupon'])) {
            $couponDiscount = $_SESSION['coupon']['discount'];
            $discount = $couponDiscount;
        }
        $finalAmount = $subtotal + $shippingFee - $discount;
        
        $this->view('checkout.index', [
            'title' => 'Thanh toán - ' . APP_NAME,
            'cartItems' => $items,
            'user' => $user,
            'savedAddresses' => $this->addressModel->getForUser($this->getUserId()),
            'subtotal' => $subtotal,
            'shippingFee' => $shippingFee,
            'discount' => $discount,
            'couponDiscount' => $couponDiscount,
            'finalAmount' => $finalAmount,
            'total' => $finalAmount,
        ]);
    }

    public function buyNow() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid method'], 405);
            return;
        }
        $this->validateCsrf();
        
        $productId = filter_var($this->getInput('product_id', 0), FILTER_VALIDATE_INT);
        $quantity = filter_var($this->getInput('quantity', 1), FILTER_VALIDATE_INT);
        $variantIdRaw = $this->getInput('variant_id', '');
        $variantId = $variantIdRaw === '' ? null : filter_var($variantIdRaw, FILTER_VALIDATE_INT);
        $variant = trim((string)$this->getInput('variant', ''));
        $variantDiff = max(0, (float)$this->getInput('variant_diff', 0));
        $product = $productId !== false && $productId > 0 ? $this->productModel->findById($productId) : null;

        $variants = $product ? $this->productModel->getVariants($productId) : [];
        $variantRecord = ($variantId !== null && $variantId !== false) ? $this->productModel->getVariantById($variantId, $productId) : null;
        $stock = (int)($variantRecord['stock_quantity'] ?? ($product['stock_quantity'] ?? 0));
        $syntheticAdjustment = $variant !== '' ? $this->getSyntheticVariantAdjustment($product, $variant) : null;
        $syntheticVariantValid = $variant === '' || $syntheticAdjustment !== null;
        if ($product && $product['status'] === 'active' && $quantity !== false && $quantity > 0
            && !($variantId === false) && (($variantId === null && $syntheticVariantValid) || (bool)$variantRecord || $syntheticVariantValid)
            && $quantity <= $stock) {
            $_SESSION['buy_now'] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'variant_id' => $variantId,
                'variant' => $variant,
                'variant_diff' => $variantDiff
            ];
            $this->json(['success' => true]);
        } else {
            $this->json(['success' => false, 'message' => 'Sản phẩm hoặc số lượng không hợp lệ, hoặc sản phẩm không đủ hàng'], 400);
        }

    }
    
    public function applyCoupon() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->json(['success' => false, 'message' => 'Invalid method'], 405); return; }
        $this->validateCsrf();
        $couponCode = sanitize($this->getInput('coupon_code', ''));
        $orderAmount = 0;

        if (isset($_SESSION['buy_now'])) {
            $buyNow = $_SESSION['buy_now'];
            $product = $this->productModel->findById($buyNow['product_id']);
            if ($product && $product['status'] === 'active') {
                $variantId = !empty($buyNow['variant_id']) ? (int)$buyNow['variant_id'] : null;
                $variant = $variantId ? $this->productModel->getVariantById($variantId, $product['id']) : null;
                $price = max(0, (float)($product['price_discount'] ?? $product['price_sale']) + (float)($variant['price_adjustment'] ?? 0));
                $orderAmount = $price * max(1, (int)$buyNow['quantity']);
            }
        } else {
            $cart = $this->getCart();
            foreach ($this->cartModel->getItems($cart['id']) as $item) {
                $product = $this->productModel->findById($item['product_id']);
                if (!$product || $product['status'] !== 'active') continue;
                $variantId = !empty($item['variant_id']) ? (int)$item['variant_id'] : null;
                $variant = $variantId ? $this->productModel->getVariantById($variantId, $product['id']) : null;
                $price = max(0, (float)($product['price_discount'] ?? $product['price_sale']) + (float)($variant['price_adjustment'] ?? 0));
                $orderAmount += $price * max(1, (int)$item['quantity']);
            }
        }

        if ($orderAmount <= 0) {
            $this->json(['success' => false, 'message' => 'Giỏ hàng không hợp lệ hoặc đã hết hàng']);
            return;
        }
        
        $coupon = $this->couponModel->findByCode($couponCode);
        
        if (!$coupon) {
            $this->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn']);
            return;
        }
        
        if ($this->isLoggedIn() && !$this->couponModel->isValidForUser($coupon['id'], $this->getUserId())) {
            $this->json(['success' => false, 'message' => 'Bạn đã sử dụng mã giảm giá này rồi']);
            return;
        }
        
        $discount = $this->couponModel->calculateDiscount($coupon, $orderAmount);
        
        if ($discount <= 0) {
            $this->json(['success' => false, 'message' => 'Đơn hàng chưa đủ điều kiện áp dụng mã này. Tối thiểu: ' . formatPrice($coupon['min_order_amount'])]);
            return;
        }
        
        $_SESSION['coupon'] = [
            'id' => $coupon['id'],
            'code' => strtoupper(trim($couponCode)),
            'discount' => $discount,
        ];
        
        $this->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'discount' => formatPrice($discount),
            'discountRaw' => $discount,
            'couponCode' => e($_SESSION['coupon']['code']),
        ]);
    }
    
    public function removeCoupon() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->json(['success' => false, 'message' => 'Invalid method'], 405); return; }
        $this->validateCsrf();
        if (isset($_SESSION['coupon'])) {
            unset($_SESSION['coupon']);
        }
        $this->json(['success' => true, 'message' => 'Đã hủy áp dụng mã giảm giá thành công']);
    }
    
    public function process() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(url('checkout'));
            return;
        }
        
        $this->validateCsrf();

        if ($this->getInput('privacy_consent') !== '1') {
            setFlash('error', 'Vui lòng xác nhận điều khoản và việc xử lý dữ liệu cá nhân trước khi đặt hàng.');
            $this->redirect(url('checkout'));
            return;
        }
        
        $items = [];
        $isBuyNow = isset($_SESSION['buy_now']);
        $cart = $this->getCart();
        
        if ($isBuyNow) {
            $buyNow = $_SESSION['buy_now'];
            $product = $this->productModel->findById($buyNow['product_id']);
            if ($product) {
                $variantRecord = !empty($buyNow['variant_id']) ? $this->productModel->getVariantById($buyNow['variant_id'], $product['id']) : null;
                $basePrice = (float)($product['price_discount'] ?? $product['price_sale']);
                $syntheticVariant = trim((string)($buyNow['variant'] ?? ''));
                $variantAdjustment = $syntheticVariant !== '' ? $this->getSyntheticVariantAdjustment($product, $syntheticVariant) : 0;
                $price = max(0, $basePrice + (float)($variantRecord['price_adjustment'] ?? $variantAdjustment));
                $items[] = [
                    'product_id' => $product['id'],
                    'product_name' => $product['name'],
                    'quantity' => $buyNow['quantity'],
                    'price_sale' => $product['price_sale'],
                    'price_discount' => $product['price_discount'],
                    'price' => $price,
                    'variant' => $variantRecord['variant_name'] ?? $syntheticVariant,
                    'variant_id' => $variantRecord['id'] ?? null,
                    'variant_name' => $variantRecord['variant_name'] ?? $syntheticVariant,
                    'variant_sku' => $variantRecord['sku'] ?? null,
                    'stock_quantity' => $variantRecord['stock_quantity'] ?? $product['stock_quantity'],
                    'subtotal' => $price * $buyNow['quantity']
                ];
            } else {
                unset($_SESSION['buy_now']);
                setFlash('error', 'Sản phẩm không hợp lệ.');
                $this->redirect(url('checkout'));
                return;
            }
        } else {
            $items = $this->cartModel->getItems($cart['id']);
            if (empty($items)) {
                setFlash('error', 'Giỏ hàng trống');
                $this->redirect(url('checkout'));
                return;
            }
        }
        
        // Re-read product price and SKU data server-side. Cart/session values are snapshots only.
        foreach ($items as &$item) {
            $product = $this->productModel->findById($item['product_id']);
            $variantId = !empty($item['variant_id']) ? (int)$item['variant_id'] : null;
            $activeVariants = $product ? $this->productModel->getVariants($product['id']) : [];
            $variantRecord = $variantId !== null ? $this->productModel->getVariantById($variantId, $item['product_id']) : null;
            $syntheticVariant = trim((string)($item['variant'] ?? ''));
            $syntheticAdjustment = $syntheticVariant !== '' ? $this->getSyntheticVariantAdjustment($product, $syntheticVariant) : null;
            if (!$product || $product['status'] !== 'active' || (!empty($activeVariants) && !$variantRecord && $syntheticAdjustment === null) || (empty($activeVariants) && $variantId !== null)) {
                unset($items);
                setFlash('error', 'Sản phẩm hoặc biến thể trong giỏ không còn hợp lệ. Vui lòng chọn lại.');
                $this->redirect(url('cart'));
                return;
            }
            $item['product_name'] = $product['name'];
            $item['variant_id'] = $variantId;
            $item['variant_name'] = $variantRecord['variant_name'] ?? ($syntheticVariant !== '' ? $syntheticVariant : null);
            $item['variant_sku'] = $variantRecord['sku'] ?? null;
            $item['variant'] = $variantRecord['variant_name'] ?? $syntheticVariant;
            $item['stock_quantity'] = (int)($variantRecord['stock_quantity'] ?? $product['stock_quantity']);
            $item['price'] = max(0, (float)($product['price_discount'] ?? $product['price_sale']) + (float)($variantRecord['price_adjustment'] ?? $syntheticAdjustment ?? 0));
        }
        unset($item);

        // Validate thông tin giao hàng
        $shipName = sanitize($this->getInput('ship_name', ''));
        $shipPhone = sanitize($this->getInput('ship_phone', ''));
        $shipEmail = sanitize($this->getInput('ship_email', ''));
        $shipAddress = sanitize($this->getInput('ship_address', ''));
        $shipCity = sanitize($this->getInput('ship_city', ''));
        $shipDistrict = sanitize($this->getInput('ship_district', ''));
        $shipWard = sanitize($this->getInput('ship_ward', ''));
        $paymentMethod = sanitize($this->getInput('payment_method', 'cod'));
        $customerNote = sanitize($this->getInput('customer_note', ''));

        if (!in_array($paymentMethod, ['cod', 'bank_transfer', 'momo', 'vnpay'], true)) {
            setFlash('error', 'Phương thức thanh toán không hợp lệ.');
            $this->redirect(url('checkout'));
            return;
        }
        if ($paymentMethod === 'vnpay' && (VNPAY_TMN_CODE === '' || VNPAY_HASH_SECRET === '')) {
            setFlash('error', 'Thanh toán VNPAY chưa được cấu hình. Vui lòng chọn phương thức khác hoặc liên hệ cửa hàng.');
            $this->redirect(url('checkout'));
            return;
        }
        
        $errors = [];
        if (empty($shipName)) $errors[] = 'Vui lòng nhập họ tên người nhận';
        if (empty($shipPhone) || !validatePhone($shipPhone)) $errors[] = 'Số điện thoại không hợp lệ';
        if (empty($shipEmail) || !validateEmail($shipEmail)) $errors[] = 'Vui lòng nhập email hợp lệ';
        if (empty($shipCity)) $errors[] = 'Vui lòng chọn Tỉnh / Thành phố';
        if (empty($shipAddress)) $errors[] = 'Vui lòng nhập địa chỉ cụ thể (số nhà, tên đường...)';
        
        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->redirect(url('checkout'));
            return;
        }
        
        // Tính tổng tiền
        $subtotal = 0;
        foreach ($items as $item) {
            $price = (!empty($item['price']) && (float)$item['price'] > 0)
                ? (float)$item['price']
                : (float)($item['price_discount'] ?? $item['price_sale']);
            $subtotal += $price * $item['quantity'];
        }
        
        $shippingFee = $subtotal >= getShippingFeeFreeAmount() ? 0 : getShippingFeeStandard();
        $discountAmount = 0;
        $couponId = null;
        
        // Tự động kiểm tra và tính toán lại giá trị mã giảm giá tại thời điểm thanh toán
        if (isset($_SESSION['coupon'])) {
            $coupon = $this->couponModel->findById($_SESSION['coupon']['id']);
            if ($coupon && $coupon['status'] === 'active' && strtotime($coupon['start_date']) <= time() && strtotime($coupon['end_date']) >= time()) {
                if (!empty($coupon['max_uses']) && (int)$coupon['used_count'] >= (int)$coupon['max_uses']) {
                    unset($_SESSION['coupon']);
                } else
                if ($this->isLoggedIn() && !$this->couponModel->isValidForUser($coupon['id'], $this->getUserId())) {
                    unset($_SESSION['coupon']);
                } else {
                    $discountAmt = $this->couponModel->calculateDiscount($coupon, $subtotal);
                    if ($discountAmt > 0) {
                        $couponId = $coupon['id'];
                        $discountAmount = $discountAmt;
                        $_SESSION['coupon']['discount'] = $discountAmt;
                    } else {
                        unset($_SESSION['coupon']);
                    }
                }
            } else {
                unset($_SESSION['coupon']);
            }
        }
        
        $finalAmount = $subtotal + $shippingFee - $discountAmount;
        
        // Kiểm tra tồn kho
        foreach ($items as $item) {
            $product = $this->productModel->findById($item['product_id']);
            $available = (int)($item['stock_quantity'] ?? ($product['stock_quantity'] ?? 0));
            if (!$product || $product['status'] !== 'active'
                || (int)$item['quantity'] < 1
                || $available < (int)$item['quantity']) {
                setFlash('error', 'Sản phẩm "' . e($item['product_name']) . '" không đủ hàng. Tồn kho: ' . $available);
                $this->redirect(url('checkout'));
                return;
            }
        }
        
        // Tạo đơn hàng
        $orderData = [
            'user_id' => $this->getUserId(),
            'coupon_id' => $couponId,
            'total_amount' => $subtotal,
            'discount_amount' => $discountAmount,
            'shipping_fee' => $shippingFee,
            'final_amount' => $finalAmount,
            'ship_name' => $shipName,
            'ship_phone' => $shipPhone,
            'ship_email' => $shipEmail,
            'ship_address' => $shipAddress,
            'ship_city' => $shipCity,
            'ship_district' => $shipDistrict,
            'ship_ward' => $shipWard,
            'payment_method' => $paymentMethod,
            'customer_note' => $customerNote,
        ];
        
        $orderLines = [];
        foreach ($items as $item) {
            $orderLines[] = [
                'product_id' => $item['product_id'], 'product_name' => $item['product_name'],
                'quantity' => (int)$item['quantity'], 'price' => (float)$item['price'],
                'discount' => 0, 'subtotal' => (float)$item['price'] * (int)$item['quantity'],
                'variant' => $item['variant_name'] ?? '', 'variant_id' => $item['variant_id'] ?? null,
                'variant_name' => $item['variant_name'] ?? null, 'variant_sku' => $item['variant_sku'] ?? null,
            ];
        }
        $orderId = $this->orderModel->createWithDetails($orderData, $orderLines);
        
        if (!$orderId) {
            setFlash('error', 'Đặt hàng thất bại. Vui lòng thử lại.');
            $this->redirect(url('checkout'));
            return;
        }
        
        // Cập nhật địa chỉ đầy đủ vào hồ sơ cá nhân để tiện cho các lần mua sau
        if ($this->isLoggedIn()) {
            $uId = $this->getUserId();
            $currUser = $this->userModel->findById($uId);
            if ($currUser) {
                $this->userModel->updateProfile($uId, [
                    'full_name' => $currUser['full_name'],
                    'phone' => !empty($currUser['phone']) ? $currUser['phone'] : $shipPhone,
                    'address' => !empty($currUser['address']) && $currUser['address'] !== $shipCity ? $currUser['address'] : $shipAddress,
                    'city' => !empty($shipCity) ? $shipCity : ($currUser['city'] ?? null),
                    'district' => !empty($shipDistrict) ? $shipDistrict : ($currUser['district'] ?? null),
                    'ward' => !empty($shipWard) ? $shipWard : ($currUser['ward'] ?? null),
                ]);
            }
        }
        
        // Thêm chi tiết đơn hàng
        // Xử lý coupon
        if ($couponId && $discountAmount > 0) unset($_SESSION['coupon']);
        
        // Xóa giỏ hàng nếu không phải Buy Now
        if (!$isBuyNow) {
            $this->cartModel->clearCart($cart['id']);
            $_SESSION['cart_count'] = 0;
        } else {
            // Giữ Buy Now trong session khi chuyển sang VNPay để callback không làm mất dữ liệu đơn.
            if ($paymentMethod !== 'vnpay') {
                unset($_SESSION['buy_now']);
            }
        }
        
        // Cần lấy đối tượng đơn hàng vừa tạo để lấy order_number
        $order = $this->orderModel->findById($orderId);
        
        // Tạo thông báo hệ thống cho tất cả Admin và Nhân viên
        $dbNotif = new Database();
        $adminUsers = $dbNotif->query("SELECT id FROM users WHERE role_id IN (1, 2)")->resultSet();
        $notificationModel = new NotificationModel();
        foreach ($adminUsers as $adminUser) {
            $notificationModel->create(
                $adminUser['id'], 
                'order', 
                'Đơn hàng mới', 
                'Đơn hàng #' . $order['order_number'] . ' đang chờ xử lý.', 
                ['order_id' => $orderId]
            );
        }
        
        // Xử lý thanh toán VNPAY
        if ($paymentMethod === 'vnpay') {
            $_SESSION['last_order_email_sent'] = null;
            $vnp_Url = defined('VNPAY_URL') && !empty(VNPAY_URL) ? VNPAY_URL : "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = url('checkout/vnpayReturn');
            if (strpos($vnp_Returnurl, 'localhost') === false && strpos($vnp_Returnurl, '127.0.0.1') === false) {
                if (strpos($vnp_Returnurl, 'http://') === 0) {
                    $vnp_Returnurl = 'https://' . substr($vnp_Returnurl, 7);
                }
            }
            $vnp_TmnCode = VNPAY_TMN_CODE;
            $vnp_HashSecret = VNPAY_HASH_SECRET;
            
            $vnp_TxnRef = $order['order_number'] . '_' . date('His') . '_' . bin2hex(random_bytes(3));
            $vnp_OrderInfo = "Thanh toan don hang " . $order['order_number']; // Tiếng Việt không dấu, không ký tự đặc biệt như #
            $vnp_OrderType = "other"; // Mã danh mục hàng hóa (other cho bán lẻ/TMĐT)
            $vnp_Amount = (int)($finalAmount * 100);
            $vnp_Locale = "vn";
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            if ($vnp_IpAddr === '::1') {
                $vnp_IpAddr = '127.0.0.1';
            }
            
            $tz = new DateTimeZone('Asia/Ho_Chi_Minh');
            $createTime = new DateTime('now', $tz);
            $expireTime = (clone $createTime)->add(new DateInterval('PT15M')); // Hết hạn sau 15 phút
            if (!$this->orderModel->createVnpayAttempt($orderId, $vnp_TxnRef, $finalAmount, $createTime->format('YmdHis'))) {
                $this->orderModel->cancelOrder($orderId, 'Không tạo được bản ghi giao dịch VNPay');
                setFlash('error', 'Không thể khởi tạo giao dịch VNPay. Đơn hàng đã được hủy và tồn kho được trả lại.');
                $this->redirect(url('checkout'));
                return;
            }
            
            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_Command" => "pay",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_CreateDate" => $createTime->format('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_ExpireDate" => $expireTime->format('YmdHis')
            );
            
            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }
            
            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }
            
            // Chuyển hướng sang VNPAY
            $this->redirect($vnp_Url);
            return;
        }
        

        
        // Gửi email xác nhận đơn hàng; đơn hàng vẫn được giữ nếu máy chủ mail tạm lỗi.
        $_SESSION['last_order_email_sent'] = sendOrderInvoiceEmail($orderId);
        
        // Gửi thông báo đơn hàng mới đến Telegram Bot
        $this->notifyTelegramOrder($order, $items);

        // Lưu order ID cho trang success
        $_SESSION['last_order_id'] = $orderId;
        
        $this->redirect(url('checkout/success'));
    }
    
    public function success() {
        $orderId = $_SESSION['last_order_id'] ?? null;
        
        if (!$orderId) {
            $this->redirect(url());
            return;
        }
        
        $order = $this->orderModel->findById($orderId);
        if (!$order) {
            $this->redirect(url());
            return;
        }
        
        $orderDetails = $this->orderModel->getDetails($orderId);
        
        unset($_SESSION['last_order_id']);
        $emailSent = $_SESSION['last_order_email_sent'] ?? null;
        unset($_SESSION['last_order_email_sent']);
        
        $isPaymentFailed = !empty($_SESSION['payment_failed']) || (($order['payment_status'] ?? '') === 'failed');
        $paymentErrorMessage = $_SESSION['payment_error_message'] ?? null;
        unset($_SESSION['payment_failed']);
        unset($_SESSION['payment_error_message']);
        
        $pageTitle = $isPaymentFailed 
            ? 'Thanh toán chưa hoàn tất - ' . APP_NAME 
            : 'Đặt hàng thành công - ' . APP_NAME;

        $this->view('checkout.success', [
            'title' => $pageTitle,
            'order' => $order,
            'orderDetails' => $orderDetails,
            'emailSent' => $emailSent,
            'isPaymentFailed' => $isPaymentFailed,
            'paymentErrorMessage' => $paymentErrorMessage,
        ]);
    }
    
    public function vnpayReturn() {
        if (!defined('VNPAY_HASH_SECRET') || VNPAY_HASH_SECRET === '') {
            setFlash('error', 'VNPay chưa được cấu hình.');
            $this->redirect(url('checkout'));
            return;
        }
        $providedHash = (string)($_GET['vnp_SecureHash'] ?? '');
        $params = [];
        foreach ($_GET as $key => $value) if (strpos($key, 'vnp_') === 0 && $key !== 'vnp_SecureHash') $params[$key] = (string)$value;
        ksort($params);
        $pairs = [];
        foreach ($params as $key => $value) $pairs[] = urlencode($key) . '=' . urlencode($value);
        $expectedHash = hash_hmac('sha512', implode('&', $pairs), VNPAY_HASH_SECRET);
        if ($providedHash === '' || !hash_equals($expectedHash, $providedHash)) {
            setFlash('error', 'Chữ ký VNPay không hợp lệ.');
            $this->redirect(url('checkout'));
            return;
        }
        $txnRef = (string)($params['vnp_TxnRef'] ?? '');
        $orderNumber = explode('_', $txnRef)[0] ?? '';
        $order = $this->orderModel->findByOrderNumber($orderNumber);
        if (!$order) {
            setFlash('error', 'Không tìm thấy đơn hàng từ phản hồi VNPay.');
            $this->redirect(url('checkout'));
            return;
        }
        $_SESSION['last_order_id'] = $order['id'];
        unset($_SESSION['buy_now']);
        if (($params['vnp_ResponseCode'] ?? '') === '00' && ($params['vnp_TransactionStatus'] ?? '') === '00') {
            setFlash('info', 'VNPay đã chuyển hướng về cửa hàng. Đang chờ xác nhận IPN từ máy chủ VNPay.');
            unset($_SESSION['payment_failed'], $_SESSION['payment_error_message']);
        } else {
            $_SESSION['payment_failed'] = true;
            $_SESSION['payment_error_message'] = 'VNPay không xác nhận giao dịch thành công. Đơn hàng chưa được đánh dấu đã thanh toán.';
        }
        $this->redirect(url('checkout/success'));
    }
    public function vnpayIpn() {
        $vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        if (VNPAY_HASH_SECRET === '') {
            $this->json(['RspCode' => '99', 'Message' => 'Payment gateway is not configured']);
            return;
        }
        $vnp_HashSecret = VNPAY_HASH_SECRET;
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        $rawTxnRef = $_GET['vnp_TxnRef'] ?? '';
        $parts = explode('_', $rawTxnRef);
        $orderNumber = $parts[0];
        $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
        $vnp_TransactionStatus = $_GET['vnp_TransactionStatus'] ?? '';
        $vnp_Amount = $_GET['vnp_Amount'] ?? 0;
        
        if ($vnp_SecureHash === '' || !hash_equals($secureHash, $vnp_SecureHash)) {
            $this->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
            return;
        }
        
        $order = $this->orderModel->findByOrderNumber($orderNumber);
        if (!$order) {
            $this->json(['RspCode' => '01', 'Message' => 'Order not found']);
            return;
        }
        if (($order['status'] ?? '') === 'cancelled') {
            $this->json(['RspCode' => '04', 'Message' => 'Order is cancelled']);
            return;
        }

        if (($inputData['vnp_TmnCode'] ?? '') !== VNPAY_TMN_CODE || strpos($rawTxnRef, $orderNumber . '_') !== 0 || ($order['payment_method'] ?? '') !== 'vnpay') {
            $this->json(['RspCode' => '04', 'Message' => 'Invalid transaction reference']);
            return;
        }
        
        // Kiểm tra số tiền
        $expectedAmount = (int)round((float)$order['final_amount'] * 100);
        if (!ctype_digit((string)$vnp_Amount) || (int)$vnp_Amount !== $expectedAmount) {
            $this->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
            return;
        }
        
        // Kiểm tra trạng thái đơn hàng đã được cập nhật trước đó chưa
        if ($order['payment_status'] === 'paid') {
            $this->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
            return;
        }
        
        if ($vnp_ResponseCode == '00' && $vnp_TransactionStatus == '00') {
            if (!$this->orderModel->recordVnpayPayment($order, $inputData)) {
                $this->json(['RspCode' => '04', 'Message' => 'Payment attempt not found or mismatched']);
                return;
            }
            $firstConfirmation = $this->orderModel->updatePaymentStatus($order['id'], 'paid');
            if (!$firstConfirmation) {
                $this->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
                return;
            }
            $this->orderModel->updateStatus($order['id'], 'processing');
            

            
            // Gửi email hóa đơn cho khách và admin
            sendOrderInvoiceEmail($order['id']);
            $items = $this->orderModel->getDetails($order['id']);
            $this->notifyTelegramOrder($order, $items);
            
            $this->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        } else {
            $this->orderModel->failVnpayAttempt($order['id'], $rawTxnRef);
            $this->orderModel->updatePaymentStatus($order['id'], 'failed');
            $this->json(['RspCode' => '00', 'Message' => 'Confirm Success (Failed Transaction)']);
        }
    }

    /**
     * Cho phép khách hàng thanh toán lại đơn hàng qua VNPAY
     * @param int|string $orderId
     * @return void
     */
    public function retryPayment($orderId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $this->requireAuth();
        $order = $this->orderModel->findById($orderId);
        
        if (!$order || $order['user_id'] != $this->getUserId()) {
            setFlash('error', 'Đơn hàng không tồn tại hoặc bạn không có quyền truy cập');
            $this->redirect(url());
            return;
        }
        
        if ($order['payment_status'] === 'paid') {
            setFlash('info', 'Đơn hàng này đã được thanh toán thành công.');
            $_SESSION['last_order_id'] = $orderId;
            $this->redirect(url('checkout/success'));
            return;
        }
        
        if ($order['status'] === 'cancelled') {
            setFlash('error', 'Đơn hàng này đã bị hủy, không thể tiếp tục thanh toán.');
            $this->redirect(url('orders/' . $orderId));
            return;
        }
        
        $vnp_Url = defined('VNPAY_URL') && !empty(VNPAY_URL) ? VNPAY_URL : "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = url('checkout/vnpayReturn');
        if (strpos($vnp_Returnurl, 'localhost') === false && strpos($vnp_Returnurl, '127.0.0.1') === false) {
            if (strpos($vnp_Returnurl, 'http://') === 0) {
                $vnp_Returnurl = 'https://' . substr($vnp_Returnurl, 7);
            }
        }
        $vnp_TmnCode = VNPAY_TMN_CODE;
        $vnp_HashSecret = VNPAY_HASH_SECRET;
        
        // Dùng timestamp mới để tạo TxnRef duy nhất trên VNPAY, tránh lỗi "Giao dịch đã tồn tại"
        $vnp_TxnRef = $order['order_number'] . '_' . date('His') . '_' . bin2hex(random_bytes(3));
        $vnp_OrderInfo = "Thanh toan don hang " . $order['order_number'];
        $vnp_OrderType = "other";
        $vnp_Amount = (int)((float)$order['final_amount'] * 100);
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if ($vnp_IpAddr === '::1') {
            $vnp_IpAddr = '127.0.0.1';
        }
        
        $tz = new DateTimeZone('Asia/Ho_Chi_Minh');
        $createTime = new DateTime('now', $tz);
        $expireTime = (clone $createTime)->add(new DateInterval('PT15M'));
        if (!$this->orderModel->createVnpayAttempt($orderId, $vnp_TxnRef, $order['final_amount'], $createTime->format('YmdHis'))) {
            setFlash('error', 'Không tạo được bản ghi giao dịch VNPay; không chuyển hướng sang cổng thanh toán.');
            $this->redirect(url('orders/' . $orderId));
            return;
        }
        
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_Command" => "pay",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_CreateDate" => $createTime->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $expireTime->format('YmdHis')
        );
        
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        
        $this->redirect($vnp_Url);
    }

    /**
     * Cho phép khách hàng chuyển đơn sang Thanh toán khi nhận hàng (COD)
     * @param int|string $orderId
     * @return void
     */
    public function changeToCod($orderId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $this->requireAuth();
        $order = $this->orderModel->findById($orderId);
        
        if (!$order || $order['user_id'] != $this->getUserId()) {
            setFlash('error', 'Đơn hàng không tồn tại hoặc bạn không có quyền truy cập');
            $this->redirect(url());
            return;
        }
        
        if ($order['payment_status'] === 'paid') {
            setFlash('info', 'Đơn hàng này đã được thanh toán thành công.');
            $_SESSION['last_order_id'] = $orderId;
            $this->redirect(url('checkout/success'));
            return;
        }
        
        if ($order['status'] === 'cancelled') {
            setFlash('error', 'Đơn hàng này đã bị hủy.');
            $this->redirect(url('orders/' . $orderId));
            return;
        }

        $this->orderModel->updatePaymentMethod($orderId, 'cod');
        $this->orderModel->updatePaymentStatus($orderId, 'unpaid');
        $this->orderModel->updateStatus($orderId, 'pending');

        $_SESSION['last_order_id'] = $orderId;
        unset($_SESSION['payment_failed']);
        unset($_SESSION['payment_error_message']);

        // Gửi email xác nhận COD
        sendOrderInvoiceEmail($orderId);
        $items = $this->orderModel->getDetails($orderId);
        $this->notifyTelegramOrder($order, $items);

        setFlash('success', 'Đã chuyển phương thức thanh toán sang Thanh toán khi nhận hàng (COD) thành công!');
        $this->redirect(url('checkout/success'));
    }

    /**
     * Cho phép khách hủy đơn hàng chưa thanh toán
     * @param int|string $orderId
     * @return void
     */
    public function cancelUnpaidOrder($orderId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $this->requireAuth();
        $order = $this->orderModel->findById($orderId);
        
        if (!$order || $order['user_id'] != $this->getUserId()) {
            setFlash('error', 'Đơn hàng không tồn tại hoặc bạn không có quyền truy cập');
            $this->redirect(url());
            return;
        }
        
        if ($order['payment_status'] === 'paid') {
            setFlash('error', 'Đơn hàng đã thanh toán thành công, không thể hủy tự động.');
            $_SESSION['last_order_id'] = $orderId;
            $this->redirect(url('checkout/success'));
            return;
        }

        $this->orderModel->cancelOrder($orderId, 'Khách hàng hủy sau khi hủy thanh toán trực tuyến');
        unset($_SESSION['last_order_id']);
        unset($_SESSION['payment_failed']);
        unset($_SESSION['payment_error_message']);

        setFlash('info', 'Đã hủy đơn hàng #' . $order['order_number'] . ' thành công.');
        $this->redirect(url('products'));
    }

    /**
     * Gửi thông báo đơn hàng mới qua Telegram Bot
     * @param array $order
     * @param array $items
     * @return void
     */
    private function notifyTelegramOrder($order, $items) {
        try {
            if (getSetting('telegram_notify_order', '1') != '1') {
                return;
            }

            $pmTexts = [
                'cod' => 'Thanh toán khi nhận hàng (COD)',
                'bank_transfer' => 'Chuyển khoản ngân hàng',
                'momo' => 'Ví điện tử MoMo',
                'vnpay' => 'Cổng VNPAY'
            ];
            $pmName = $pmTexts[$order['payment_method'] ?? 'cod'] ?? ($order['payment_method'] ?? 'COD');

            $fullAddress = trim(($order['ship_address'] ?? '') 
                . (!empty($order['ship_ward']) ? ', ' . $order['ship_ward'] : '') 
                . (!empty($order['ship_district']) ? ', ' . $order['ship_district'] : '') 
                . (!empty($order['ship_city']) ? ', ' . $order['ship_city'] : ''));

            $msg = "🛒 <b>ĐƠN HÀNG MỚI TẠI VŨ HUÂN SHOP!</b>\n"
                 . "━━━━━━━━━━━━━━━━━━━━\n"
                 . "🆔 <b>Mã đơn:</b> <code>#{$order['order_number']}</code>\n"
                 . "👤 <b>Khách hàng:</b> " . e($order['ship_name'] ?? 'Khách mua hàng') . "\n"
                 . "📞 <b>Điện thoại:</b> <a href=\"tel:{$order['ship_phone']}\">{$order['ship_phone']}</a>\n"
                 . "📍 <b>Địa chỉ:</b> " . e($fullAddress) . "\n"
                 . "💰 <b>Tổng tiền:</b> <b>" . formatPrice($order['final_amount'] ?? 0) . "</b>\n"
                 . "💳 <b>Phương thức:</b> {$pmName} (" . (($order['payment_status'] ?? '') === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán') . ")\n"
                 . "━━━━━━━━━━━━━━━━━━━━\n"
                 . "📦 <b>Sản phẩm đã đặt:</b>\n";

            if (is_array($items)) {
                foreach ($items as $it) {
                    $vText = !empty($it['variant']) ? ' [' . $it['variant'] . ']' : '';
                    $subTotal = !empty($it['subtotal']) ? $it['subtotal'] : ((float)($it['price'] ?? 0) * (int)($it['quantity'] ?? 1));
                    $msg .= "• " . e($it['product_name'] ?? 'Sản phẩm') . "{$vText} x" . ($it['quantity'] ?? 1) . " (" . formatPrice($subTotal) . ")\n";
                }
            }

            if (!empty($order['customer_note'])) {
                $msg .= "📝 <b>Ghi chú:</b> " . e($order['customer_note']) . "\n";
            }

            $msg .= "━━━━━━━━━━━━━━━━━━━━\n"
                 . "⏰ <i>" . date('d/m/Y H:i:s') . "</i>\n"
                 . "👉 <a href=\"" . APP_URL . "/admin/orders/{$order['id']}\">Xem chi tiết đơn trong Admin</a>";

            sendTelegramNotification($msg);
        } catch (\Throwable $e) {
            // Không ngắt luồng đặt hàng nếu gửi Telegram gặp trục trặc
            error_log('Telegram Order Notification Error: ' . $e->getMessage());
        }
    }
}
?>
