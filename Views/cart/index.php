<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4 mb-5">
    <h1 style="font-size:22px;font-weight:700;margin-bottom:4px">
        <?php if (!empty($items)): ?>
        Giỏ hàng của bạn <span class="badge bg-danger ms-2" style="font-size:15px"><?= count($items) ?></span>
        <?php else: ?>
        Giỏ hàng trống
        <?php endif; ?>
    </h1>
    
    <?php if (empty($items)): ?>
    <div class="text-center py-5">
        <div style="font-size:80px;margin-bottom:20px">🛒</div>
        <h3 style="font-size:20px;color:var(--text-medium);margin-bottom:12px">Giỏ hàng của bạn đang trống</h3>
        <p style="color:var(--text-muted);font-size:14px;margin-bottom:24px">Hãy thêm sản phẩm vào giỏ hàng để tiến hành thanh toán</p>
        <a href="<?= url('products') ?>" class="btn btn-danger btn-lg rounded-pill px-5">
            <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
        </a>
    </div>
    <?php else: ?>
    <div class="row g-4 mt-1">
        <!-- Cart Items -->
        <div class="col-lg-8">
            <div id="cart-items-container">
                <?php foreach ($items as $item): ?>
                <?php
                $itemPrice = (!empty($item['price']) && (float)$item['price'] > 0) 
                    ? (float)$item['price'] 
                    : ($item['price_discount'] ?? $item['price_sale']);
                $itemTotal = $itemPrice * $item['quantity'];
                ?>
                <div class="cart-item" data-item="<?= $item['id'] ?>">
                    <img class="cart-item-img" 
                         src="<?= productImage($item['main_image']) ?>" 
                         alt="<?= e($item['product_name']) ?>"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiB2aWV3Qm94PSIwIDAgNDAwIDQwMCI+PHJlY3Qgd2lkdGg9IjQwMCIgaGVpZ2h0PSI0MDAiIGZpbGw9IiNmNWY1ZjUiLz48Y2lyY2xlIGN4PSIyMDAiIGN5PSIxNjAiIHI9IjUwIiBmaWxsPSIjZTBlMGUwIi8+PGNpcmNsZSBjeD0iMjAwIiBjeT0iMTYwIiByPSIzMCIgZmlsbD0iIzk5OTk5OSIvPjxwYXRoIGQ9Ik0xNDAgMjIwSDI2MlYyODBoLTEyMlYyMjB6IiBmaWxsPSIjOTk5OTk5Ii8+PC9zdmc+'">
                    
                    <div class="cart-item-info flex-1">
                        <div class="cart-item-name">
                            <a href="<?= url('products/' . $item['product_slug']) ?>"><?= e($item['product_name']) ?></a>
                        </div>
                        <?php if (!empty($item['variant'])): ?>
                            <div class="cart-item-variant mt-1 mb-1">
                                <span class="badge bg-light text-dark border" style="font-size:12px; font-weight:500;">
                                    <i class="fas fa-tag me-1 text-danger"></i><?= e($item['variant']) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <div class="cart-item-brand"><?= e($item['brand_name'] ?? '') ?></div>
                        <div class="d-flex align-items-center gap-3 mt-2 flex-wrap">
                            <span class="cart-item-price"><?= formatPrice($itemPrice) ?></span>
                            <div class="d-flex align-items-center gap-2">
                                <button class="qty-btn" onclick="changeQty(<?= $item['id'] ?>, -1, this)" style="width:30px;height:30px;font-size:16px">−</button>
                                <input type="number" class="qty-input cart-qty-input" 
                                       value="<?= $item['quantity'] ?>" 
                                       min="1" max="<?= $item['stock_quantity'] ?>"
                                       data-item="<?= $item['id'] ?>"
                                       style="width:52px;height:30px;font-size:14px">
                                <button class="qty-btn" onclick="changeQty(<?= $item['id'] ?>, 1, this)" style="width:30px;height:30px;font-size:16px">+</button>
                            </div>
                            <span class="item-total" data-item="<?= $item['id'] ?>" style="font-size:15px;font-weight:700;color:var(--primary)">
                                = <?= formatPrice($itemTotal) ?>
                            </span>
                        </div>
                    </div>
                    
                    <button class="cart-remove-btn btn-action" data-item="<?= $item['id'] ?>" title="Xóa sản phẩm">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <a href="<?= url('products') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Tiếp tục mua sắm
                </a>
            </div>
        </div>
        
        <!-- Cart Summary -->
        <div class="col-lg-4">
            <div class="cart-summary">
                <h5 style="font-weight:700;margin-bottom:16px">Tóm tắt đơn hàng</h5>
                
                <!-- Coupon -->
                <div class="mb-3">
                    <label class="form-label" style="font-size:13px;font-weight:500">Mã giảm giá</label>
                    <div class="coupon-input">
                        <input type="text" id="coupon-code" class="form-control" placeholder="Nhập mã giảm giá..." 
                               value="<?= e($_SESSION['coupon']['code'] ?? '') ?>"
                               <?= isset($_SESSION['coupon']) ? 'readonly style="background:#f5f5f5;"' : '' ?>
                               style="text-transform:uppercase">
                        <?php if (isset($_SESSION['coupon'])): ?>
                            <button id="remove-coupon-btn" class="btn btn-outline-secondary btn-sm" style="white-space:nowrap">
                                Hủy
                            </button>
                        <?php else: ?>
                            <button id="apply-coupon-btn" class="btn btn-outline-danger btn-sm" style="white-space:nowrap">
                                Áp dụng
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <hr>
                
                <!-- Price Summary -->
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <span id="subtotal"><?= formatPrice($subtotal) ?></span>
                </div>
                <div class="summary-row <?= empty($_SESSION['coupon']) ? 'd-none' : '' ?>" id="discount-row">
                    <span>Giảm giá (<span id="applied-coupon"><?= e($_SESSION['coupon']['code'] ?? '') ?></span>)</span>
                    <span class="text-success">-<span id="discount-amount"><?= formatPrice($_SESSION['coupon']['discount'] ?? 0) ?></span></span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span id="shipping-fee">
                        <?php if ($shippingFee > 0): ?>
                        <?= formatPrice($shippingFee) ?>
                        <?php else: ?>
                        <span class="text-success">Miễn phí</span>
                        <?php endif; ?>
                    </span>
                </div>
                
                <?php if ($subtotal < getShippingFeeFreeAmount() && $shippingFee > 0): ?>
                <div style="background:#FFF3E0;border-radius:6px;padding:10px;font-size:12px;color:#E65100;margin:8px 0">
                    <i class="fas fa-truck me-1"></i>
                    Mua thêm <?= formatPrice(getShippingFeeFreeAmount() - $subtotal) ?> để được miễn phí vận chuyển
                </div>
                <?php endif; ?>
                
                <div class="summary-row total">
                    <span>Tổng cộng</span>
                    <span id="total-amount"><?= formatPrice($total) ?></span>
                </div>
                
                <!-- Hidden inputs for JS calculation -->
                <input type="hidden" id="subtotal-raw" value="<?= $subtotal ?>">
                <input type="hidden" id="shipping-raw" value="<?= $shippingFee ?>">
                
                <a href="<?= url('checkout') ?>" class="btn btn-danger w-100 mt-3 btn-lg" style="border-radius:8px">
                    <i class="fas fa-lock me-2"></i>Thanh toán ngay
                </a>
                
                <!-- Payment Methods -->
                <div class="mt-3 text-center">
                    <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px">Chấp nhận thanh toán</div>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <span style="background:#F5F5F5;border-radius:4px;padding:4px 8px;font-size:11px;font-weight:600">COD</span>
                        <span style="background:#F5F5F5;border-radius:4px;padding:4px 8px;font-size:11px;font-weight:600">MOMO</span>
                        <span style="background:#F5F5F5;border-radius:4px;padding:4px 8px;font-size:11px;font-weight:600">VNPAY</span>
                        <span style="background:#F5F5F5;border-radius:4px;padding:4px 8px;font-size:11px;font-weight:600">ATM/Visa</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Quick qty change
function changeQty(itemId, delta, btn) {
    const input = document.querySelector(`input[data-item="${itemId}"]`);
    if (!input) return;
    const newVal = Math.max(1, Math.min(parseInt(input.max) || 99, parseInt(input.value) + delta));
    input.value = newVal;
    Cart.updateQuantity(itemId, newVal);
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
