<?php
// Shared product card partial
$product = $product ?? [];
$price = $product['price_discount'] ?? $product['price_sale'] ?? 0;
$originalPrice = $product['price_sale'];
$hasDiscount = $product['price_discount'] && $product['price_discount'] < $product['price_sale'];
$discountPct = $hasDiscount ? discountPercent($originalPrice, $price) : $product['discount_percent'];
$inStock = $product['stock_quantity'] > 0;
$isWishlisted = isset($product['is_wishlisted']) ? (bool)$product['is_wishlisted'] : isProductWishlisted($product['id'] ?? 0);
// Tải ngay một vài ảnh đầu trang (above the fold), phần còn lại lazy để tối ưu tốc độ.
if (!isset($GLOBALS['__product_card_index'])) { $GLOBALS['__product_card_index'] = 0; }
$__cardIndex = $GLOBALS['__product_card_index']++;
$__imgLoading = $__cardIndex < 4 ? 'eager' : 'lazy';
?>
<div class="product-card">
    <a href="<?= url('products/' . $product['slug']) ?>" class="product-card-image d-block">
        <?php if ($discountPct > 0): ?>
        <div class="product-badge">
            <span class="badge-discount">-<?= $discountPct ?>%</span>
        </div>
        <?php elseif (!empty($product['is_featured'])): ?>
        <div class="product-badge">
            <span class="badge-new">Nổi bật</span>
        </div>
        <?php endif; ?>
        
        <img src="<?= productImage($product['main_image']) ?>" 
             alt="<?= e($product['name']) ?>"
             loading="<?= $__imgLoading ?>"
             decoding="async"
             style="background:#F5F5F5"
             onerror="this.onerror=null;this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiB2aWV3Qm94PSIwIDAgNDAwIDQwMCI+PHJlY3Qgd2lkdGg9IjQwMCIgaGVpZ2h0PSI0MDAiIGZpbGw9IiNmNWY1ZjUiLz48Y2lyY2xlIGN4PSIyMDAiIGN5PSIxNjAiIHI9IjUwIiBmaWxsPSIjZTBlMGUwIi8+PGNpcmNsZSBjeD0iMjAwIiBjeT0iMTYwIiByPSIzMCIgZmlsbD0iIzk5OTk5OSIvPjxwYXRoIGQ9Ik0xNDAgMjIwSDI2MlYyODBoLTEyMlYyMjB6IiBmaWxsPSIjOTk5OTk5Ii8+PC9zdmc+'">
    </a>

    <div class="product-card-actions <?= $isWishlisted ? 'has-active' : '' ?>">
        <button class="btn-action btn-wishlist <?= $isWishlisted ? 'active' : '' ?>" 
                data-product="<?= $product['id'] ?>" 
                title="<?= $isWishlisted ? 'Bỏ yêu thích' : 'Yêu thích' ?>">
            <i class="<?= $isWishlisted ? 'fas' : 'far' ?> fa-heart"></i>
        </button>
        <a href="<?= url('products/' . $product['slug']) ?>" class="btn-action" title="Xem chi tiết">
            <i class="fas fa-eye"></i>
        </a>
    </div>
    
    <div class="product-card-body">
        <div class="product-brand"><?= e($product['brand_name'] ?? '') ?></div>
        
        <h3 class="product-name">
            <a href="<?= url('products/' . $product['slug']) ?>"><?= e($product['name']) ?></a>
        </h3>
        
        <div class="product-rating">
            <?php if ($product['rating_count'] > 0): ?>
                <?= renderStars($product['rating_avg']) ?>
                <span class="rating-count">(<?= $product['rating_count'] ?>)</span>
            <?php else: ?>
                <span class="rating-placeholder" style="visibility: hidden;">&nbsp;</span>
            <?php endif; ?>
        </div>
        
        <div class="product-price">
            <span class="price-current"><?= formatPrice($price) ?></span>
            <?php if ($hasDiscount): ?>
            <span class="price-original"><?= formatPrice($originalPrice) ?></span>
            <?php endif; ?>
        </div>
        
        <div class="product-card-footer mt-auto">
            <?php if (!$inStock): ?>
            <div class="stock-out mb-2"><i class="fas fa-times-circle me-1"></i>Tạm hết hàng</div>
            <button class="btn-add-cart out-of-stock" disabled>Hết hàng</button>
            <?php elseif ($product['stock_quantity'] <= 5): ?>
            <div class="stock-low mb-2"><i class="fas fa-exclamation-triangle me-1"></i>Còn <?= $product['stock_quantity'] ?> sản phẩm</div>
            <button class="btn-add-cart" data-product="<?= $product['id'] ?>">
                <i class="fas fa-cart-plus me-1"></i>Thêm vào giỏ
            </button>
            <?php else: ?>
            <button class="btn-add-cart" data-product="<?= $product['id'] ?>">
                <i class="fas fa-cart-plus me-1"></i>Thêm vào giỏ
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>
