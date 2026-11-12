<?php
// Các biến do Controller::view() extract() truyền sang — khởi tạo mặc định phòng khi thiếu dữ liệu
$product         = $product ?? [];
$images          = $images ?? [];
$specs           = $specs ?? [];
$reviews         = $reviews ?? [];
$relatedProducts = $relatedProducts ?? [];
$ratingStats     = $ratingStats ?? [];
$reviewStats     = $reviewStats ?? [];
$reviewCount     = $reviewCount ?? 0;
$hasReviewed     = $hasReviewed ?? false;
$isWishlisted    = $isWishlisted ?? (isset($product['id']) ? isProductWishlisted($product['id']) : false);
$mainProduct     = $product;

require_once __DIR__ . '/../layouts/header.php';

if (empty($product)) {
    http_response_code(404);
    echo '<div class="container py-5 text-center"><h1>Sản phẩm không tồn tại</h1><a href="' . url('products') . '" class="btn btn-danger mt-2">Về danh sách sản phẩm</a></div>';
    require_once __DIR__ . '/../layouts/footer.php';
    return;
}
?>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= url('products') ?>">Sản phẩm</a></li>
                <li class="breadcrumb-item active"><?= e($product['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<?php
$price = $product['price_discount'] ?? $product['price_sale'] ?? 0;
$currentPrice = $price;
$originalPrice = $product['price_sale'] ?? 0;
$hasDiscount = !empty($product['price_discount']) && $product['price_discount'] < $product['price_sale'];
$discountPct = $hasDiscount ? discountPercent($originalPrice, $price) : ($product['discount_percent'] ?? 0);
$skuVariants = $skuVariants ?? [];
$hasSkuVariants = !empty($skuVariants);
$variantStockTotal = 0;
foreach ($skuVariants as $skuVariant) $variantStockTotal += max(0, (int)$skuVariant['stock_quantity']);
$inStock = $hasSkuVariants ? $variantStockTotal > 0 : ((int)($product['stock_quantity'] ?? 0)) > 0;
$variantsData = getProductRealVariants($product);
$capacities = $variantsData['capacities'] ?? [];
$colors = $variantsData['colors'] ?? [];
$hasVariants = !empty($capacities) || !empty($colors);
$galleryMainImage = $product['main_image'] ?? '';
?>

<div class="container mt-4 mb-5">
    <div class="row g-4">
        <!-- Product Gallery -->
        <div class="col-lg-5">
            <div class="product-gallery">
                <div class="gallery-main position-relative">
                    <img class="product-detail-main-image" src="<?= productImage($galleryMainImage) ?>" 
                         alt="<?= e($product['name']) ?>" 
                         id="gallery-main"
                         data-original-src="<?= productImage($galleryMainImage) ?>"
                         onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/images/no-image.png'">
                </div>
                
                <?php if (!empty($images)): ?>
                <div class="gallery-thumbs">
                    <div class="gallery-thumb active" data-full="<?= productImage($galleryMainImage) ?>">
                        <img src="<?= productImage($galleryMainImage) ?>" alt="Ảnh chính" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/images/no-image.png'">
                    </div>
                    <?php foreach ($images as $img): ?>
                    <?php if ($img['image_path'] === $product['main_image']) continue; ?>
                    <div class="gallery-thumb" data-full="<?= productImage($img['image_path']) ?>">
                        <img src="<?= productImage($img['image_path']) ?>" alt="Ảnh sản phẩm" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/images/no-image.png'">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-lg-4">
            <div class="product-info-card">
                <!-- Brand & SKU -->
                <div class="product-detail-meta">
                    <span class="product-detail-brand">
                        Thương hiệu: <a href="<?= url('products') ?>?brand=<?= $product['brand_id'] ?>"><?= e($product['brand_name']) ?></a>
                    </span>
                    <span class="product-sku">SKU: <?= e($product['product_code']) ?></span>
                </div>
                
                <!-- Name -->
                <h1 class="product-detail-name"><?= e($product['name']) ?></h1>
                
                <!-- Rating -->
                <?php if ($reviewCount > 0): ?>
                <div class="product-detail-rating mb-3">
                    <?= renderStars($product['rating_avg'] ?? 5) ?>
                    <span style="font-size:13px;color:var(--text-muted);margin-left:6px"><?= number_format($product['rating_avg'] ?? 5, 1) ?> (<?= $reviewCount ?> đánh giá)</span>
                    <span style="font-size:13px;color:var(--text-muted)">· <?= number_format($product['view_count']) ?> lượt xem</span>
                </div>
                <?php endif; ?>
                
                <!-- Price -->
                <div class="product-detail-price">
                    <span class="detail-price-current"><?= formatPrice($price) ?></span>
                    <?php if ($hasDiscount): ?>
                    <span class="detail-price-original"><?= formatPrice($originalPrice) ?></span>
                    <span class="detail-discount-badge">-<?= $discountPct ?>%</span>
                    <?php endif; ?>
                </div>
                
                <!-- Short Description -->
                <?php if (!empty($product['short_description'])): ?>
                <p style="font-size:14px;color:var(--text-medium);margin-bottom:16px;line-height:1.7">
                    <?= e($product['short_description']) ?>
                </p>
                <?php endif; ?>
                
                <?php if ($hasSkuVariants): ?>
                <div class="product-variants-box mb-4">
                    <label class="form-label fw-bold" for="sku-variant-select">Chọn phiên bản / SKU</label>
                    <select id="sku-variant-select" class="form-select">
                        <option value="">-- Chọn biến thể --</option>
                        <?php foreach ($skuVariants as $skuVariant): ?>
                            <?php $variantPrice = max(0, (float)$currentPrice + (float)$skuVariant['price_adjustment']); ?>
                            <option value="<?= (int)$skuVariant['id'] ?>" data-price="<?= $variantPrice ?>" data-stock="<?= (int)$skuVariant['stock_quantity'] ?>" <?= (int)$skuVariant['stock_quantity'] < 1 ? 'disabled' : '' ?>><?= e($skuVariant['variant_name']) ?> · SKU <?= e($skuVariant['sku']) ?> · <?= formatPrice($variantPrice) ?> · tồn <?= (int)$skuVariant['stock_quantity'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="small text-muted mt-2">Giá và số lượng được xác nhận lại từ hệ thống khi thêm vào giỏ.</div>
                </div>
                <?php endif; ?>

                <?php if (false && $hasVariants): ?>
                <!-- Legacy display-only product option data; checkout uses database SKU records. -->
                <div class="product-variants-box mb-4">
                    <!-- Capacity / Version Variants -->
                    <?php if (!empty($capacities)): ?>
                    <div class="variant-group mb-3">
                        <div class="variant-group-header d-flex align-items-center justify-content-between mb-2">
                            <span class="d-flex align-items-center gap-2">
                                <i class="fas fa-microchip text-danger"></i>
                                <span class="fw-bold text-dark" style="font-size:13.5px;">Chọn phiên bản:</span>
                            </span>
                            <span class="selected-cap-display badge bg-light text-dark border">
                                <?= e($capacities[0]['label'] ?? $capacities[0]['name'] ?? '') ?>
                            </span>
                        </div>
                        <div class="variant-cap-grid">
                            <?php foreach ($capacities as $cIdx => $cap): ?>
                                <?php 
                                $diff = (float)($cap['diff'] ?? 0);
                                $isCapActive = ($cIdx === 0);
                                $capTotalPrice = $currentPrice + $diff;
                                ?>
                                <button type="button" 
                                        class="btn-cap-card <?= $isCapActive ? 'active' : '' ?>" 
                                        data-diff="<?= $diff ?>" 
                                        data-label="<?= e($cap['label'] ?? $cap['name'] ?? '') ?>"
                                        onclick="selectCapacity(this, <?= $diff ?>, this.dataset.label)">
                                    <span class="cap-card-label"><?= e($cap['label'] ?? $cap['name'] ?? '') ?></span>
                                    <span class="cap-card-price"><?= formatPrice($capTotalPrice) ?></span>
                                    <span class="cap-check-tick"><i class="fas fa-check"></i></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($colors)): ?>
                    <div class="variant-group product-color-group">
                        <div class="variant-group-header d-flex align-items-center justify-content-between mb-2">
                            <span class="d-flex align-items-center gap-2"><i class="fas fa-palette text-danger"></i><span class="fw-bold text-dark" style="font-size:13.5px;">Chọn màu sắc:</span></span>
                            <span class="selected-color-display badge bg-light text-dark border">Chưa chọn</span>
                        </div>
                        <div class="variant-color-grid">
                            <?php foreach ($colors as $colorIndex => $color): ?>
                                <?php
                                $colorHex = $color['hex'] ?? '#777777';
                                if (!preg_match('/^#[0-9a-fA-F]{6}$/', $colorHex)) $colorHex = '#777777';
                                ?>
                                <button type="button" class="btn-color-card" data-name="<?= e($color['name'] ?? '') ?>" data-image="<?= e(productImage($color['image'] ?? '')) ?>" onclick="selectProductColor(this)">
                                    <span class="color-swatch" style="background-color:<?= e($colorHex) ?>"></span>
                                    <span><?= e($color['name'] ?? '') ?></span>
                                    <span class="cap-check-tick"><i class="fas fa-check"></i></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <style>
                .product-variants-box {
                    background: #ffffff;
                    border: 1px solid #e2e8f0;
                    border-radius: 14px;
                    padding: 16px;
                    box-shadow: 0 1px 4px rgba(0,0,0,0.03);
                }
                .variant-cap-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(118px, 1fr));
                    gap: 8px;
                }
                .btn-cap-card {
                    position: relative;
                    border: 1.5px solid #e2e8f0;
                    background: #ffffff;
                    border-radius: 10px;
                    padding: 8px 10px;
                    text-align: center;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    min-height: 54px;
                    overflow: hidden;
                }
                .btn-cap-card:hover {
                    border-color: #cbd5e1;
                    background: #f8fafc;
                }
                .btn-cap-card.active {
                    border-color: #e53935 !important;
                    background: #fff5f5 !important;
                    box-shadow: 0 0 0 1px #e53935;
                }
                .variant-color-grid { display:flex; flex-wrap:wrap; gap:8px; }
                .btn-color-card {
                    position:relative; display:inline-flex; align-items:center; gap:8px; min-height:42px;
                    padding:7px 12px; border:1.5px solid #e2e8f0; border-radius:10px; background:#fff;
                    color:#1e293b; font-size:13px; cursor:pointer; overflow:hidden;
                }
                .btn-color-card.active { border-color:#e53935; background:#fff5f5; box-shadow:0 0 0 1px #e53935; }
                .color-swatch { width:22px; height:22px; flex:none; border:1px solid rgba(15,23,42,.2); border-radius:50%; box-shadow:0 1px 3px rgba(0,0,0,.18); }
                .btn-color-card .cap-check-tick { width:14px; height:14px; }
                .btn-color-card.active .cap-check-tick { display:flex; }
                .cap-card-label {
                    font-weight: 700;
                    font-size: 13px;
                    color: #1e293b;
                    line-height: 1.2;
                }
                .cap-card-price {
                    font-size: 11.5px;
                    font-weight: 600;
                    color: #e53935;
                    margin-top: 3px;
                    line-height: 1.2;
                }
                .cap-check-tick {
                    display: none;
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    width: 14px;
                    height: 14px;
                    background: #e53935;
                    color: #ffffff;
                    font-size: 8px;
                    border-top-left-radius: 6px;
                    border-bottom-right-radius: 8px;
                    align-items: center;
                    justify-content: center;
                }
                .btn-cap-card.active .cap-check-tick {
                    display: flex;
                }

                @media (max-width: 576px) {
                    .variant-cap-grid {
                        grid-template-columns: repeat(2, 1fr);
                    }}
                </style>
                <?php endif; ?>

                <!-- Stock Status -->
                <div class="product-stock-status">
                    <?php if (!$inStock): ?>
                    <span class="stock-none"><i class="fas fa-times-circle me-1"></i>Tạm hết hàng</span>
                    <?php elseif ($product['stock_quantity'] <= 5): ?>
                    <span class="stock-limited"><i class="fas fa-exclamation-triangle me-1"></i>Còn <?= $product['stock_quantity'] ?> sản phẩm</span>
                    <?php else: ?>
                    <span class="stock-available"><i class="fas fa-check-circle me-1"></i>Còn hàng</span>
                    <?php endif; ?>
                </div>
                
                <!-- Quantity -->
                <?php if ($inStock): ?>
                <div class="product-quantity-selector">
                    <span style="font-size:14px;font-weight:500">Số lượng:</span>
                    <button class="qty-btn" data-action="decrease">−</button>
                    <input type="number" class="qty-input" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
                    <button class="qty-btn" data-action="increase">+</button>
                </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="d-flex gap-3 mb-4 flex-wrap">
                    <?php if ($inStock): ?>
                    <button class="btn-cart-primary btn-add-cart" data-product="<?= $product['id'] ?>">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <button class="btn-buy-now" data-product="<?= $product['id'] ?>">
                        <i class="fas fa-bolt"></i> Mua ngay
                    </button>
                    <?php else: ?>
                    <button class="btn-cart-primary out-of-stock" disabled style="flex:1">
                        Tạm hết hàng
                    </button>
                    <?php endif; ?>
                </div>
                
                <!-- Wishlist & Share -->
                <div class="d-flex gap-3 mb-3">
                    <button class="btn btn-outline-danger btn-sm btn-wishlist <?= $isWishlisted ? 'active' : '' ?>" 
                            data-product="<?= $product['id'] ?>">
                        <i class="<?= $isWishlisted ? 'fas' : 'far' ?> fa-heart me-1"></i>
                        <?= $isWishlisted ? 'Đã yêu thích' : 'Yêu thích' ?>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="shareProduct()">
                        <i class="fas fa-share-alt me-1"></i>Chia sẻ
                    </button>
                </div>
                
                <!-- Promotions -->
                <div style="background:var(--bg-light);border-radius:8px;padding:14px">
                    <div style="font-size:13px;font-weight:700;margin-bottom:8px;color:var(--primary)">
                        <i class="fas fa-gift me-2"></i>Khuyến mãi đặc biệt
                    </div>
                    <ul style="list-style:none;padding:0;margin:0;font-size:13px;color:var(--text-medium)">
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Miễn phí giao hàng đơn từ 500K</li>
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Bảo hành 12 tháng chính hãng</li>
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Đổi trả theo điều kiện sản phẩm</li>
                        <li><i class="fas fa-check text-success me-2"></i>Trả góp 0% lãi suất 12 tháng</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Right Panel -->
        <div class="col-lg-3">
            <!-- Delivery Card -->
            <div style="background:white;border-radius:10px;padding:18px;box-shadow:0 1px 4px rgba(0,0,0,0.06);margin-bottom:16px">
                <div style="font-size:14px;font-weight:700;margin-bottom:12px">
                    <i class="fas fa-truck text-primary me-2"></i>Giao hàng
                </div>
                <div style="font-size:13px;color:var(--text-medium)">
                    <div class="d-flex gap-2 mb-2">
                        <i class="fas fa-check text-success" style="margin-top:2px"></i>
                        <div><strong>Giao hàng nhanh</strong><br>Nội thành TP.HCM, Hà Nội</div>
                    </div>
                    <div class="d-flex gap-2">
                        <i class="fas fa-check text-success" style="margin-top:2px"></i>
                        <div><strong>Giao 1-3 ngày</strong><br>Toàn quốc, miễn phí từ 500K</div>
                    </div>
                </div>
            </div>
            
            <!-- Installment Card -->
            <div style="background:white;border-radius:10px;padding:18px;box-shadow:0 1px 4px rgba(0,0,0,0.06);margin-bottom:16px">
                <div style="font-size:14px;font-weight:700;margin-bottom:12px">
                    <i class="fas fa-credit-card text-warning me-2"></i>Trả góp 0%
                </div>
                <div style="font-size:13px;color:var(--text-medium)">
                    <div>Duyệt nhanh qua các ngân hàng:<br>
                    Vietcombank, Techcombank, VPBank, TPBank...</div>
                    <div class="mt-2" style="font-weight:600;color:var(--primary)">
                        Từ <?= formatPrice(ceil($price / 12 / 1000) * 1000) ?>/tháng
                    </div>
                </div>
            </div>
            
            <!-- Compare / Recently Viewed -->
            <div style="background:white;border-radius:10px;padding:18px;box-shadow:0 1px 4px rgba(0,0,0,0.06)">
                <div style="font-size:14px;font-weight:700;margin-bottom:12px">
                    <i class="fas fa-shield-alt text-success me-2"></i>Cam kết
                </div>
                <div style="font-size:13px;color:var(--text-medium)">
                    <div class="d-flex gap-2 mb-2"><i class="fas fa-check text-success"></i><span>Hàng chính hãng 100%</span></div>
                    <div class="d-flex gap-2 mb-2"><i class="fas fa-check text-success"></i><span>Giá cạnh tranh</span></div>
                    <div class="d-flex gap-2"><i class="fas fa-check text-success"></i><span>Hỗ trợ 24/7</span></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Tabs -->
    <div class="row mt-4">
        <div class="col-lg-9">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-description">Mô tả sản phẩm</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-specs">Thông số kỹ thuật</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-reviews">
                    Đánh giá (<?= $reviewCount ?>)
                </button></li>
            </ul>
            
            <div class="tab-content" style="background:white;padding:24px;border:1px solid #dee2e6;border-top:none;border-radius:0 0 8px 8px">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="tab-description">
                    <?php if (!empty($product['description'])): ?>
                    <div class="product-description" style="font-size:14px;line-height:1.8;color:var(--text-medium)">
                        <?= $product['description'] ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">Chưa có mô tả sản phẩm.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Specs Tab -->
                <div class="tab-pane fade" id="tab-specs">
                    <?php if (!empty($specs)): ?>
                    <div class="specs-table">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <?php foreach ($specs as $spec): ?>
                                <tr>
                                    <td style="font-weight:500;color:var(--text-medium);width:40%;background:var(--bg-light)"><?= e($spec['spec_name']) ?></td>
                                    <td><?= e($spec['spec_value']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">Chưa có thông số kỹ thuật.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="tab-reviews">
                    <?php if ($reviewCount > 0): ?>
                    <!-- Rating Summary -->
                    <div class="row mb-4 g-3">
                        <div class="col-md-3 text-center">
                            <div style="font-size:64px;font-weight:800;color:var(--primary);line-height:1">
                                <?= number_format($product['rating_avg'] ?? 5, 1) ?>
                            </div>
                            <div><?= renderStars($product['rating_avg'] ?? 5) ?></div>
                            <div style="font-size:13px;color:var(--text-muted);margin-top:4px"><?= $reviewCount ?> đánh giá</div>
                        </div>
                        <div class="col-md-9">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                            <?php $count = $ratingStats[$i] ?? 0; $pct = $reviewCount > 0 ? ($count / $reviewCount * 100) : 0; ?>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span style="font-size:12px;width:40px;text-align:right"><?= $i ?> <i class="fas fa-star text-warning" style="font-size:11px"></i></span>
                                <div style="flex:1;height:8px;background:#F0F0F0;border-radius:4px;overflow:hidden">
                                    <div style="height:100%;background:var(--primary);width:<?= $pct ?>%;border-radius:4px;transition:width 0.5s"></div>
                                </div>
                                <span style="font-size:12px;color:var(--text-muted);width:30px"><?= $count ?></span>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <!-- Reviews List -->
                    <?php foreach ($reviews as $review): ?>
                    <div style="border-bottom:1px solid var(--border-light);padding:16px 0">
                        <div class="d-flex gap-3">
                            <div style="width:40px;height:40px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;flex-shrink:0">
                                <?= mb_substr($review['reviewer_name'] ?? 'U', 0, 1) ?>
                            </div>
                            <div style="flex:1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div style="font-weight:600;font-size:14px"><?= e($review['reviewer_name'] ?? 'Ẩn danh') ?></div>
                                        <div><?= renderStars($review['rating']) ?></div>
                                    </div>
                                    <div style="font-size:12px;color:var(--text-muted)"><?= timeAgo($review['created_at']) ?></div>
                                </div>
                                <?php if (!empty($review['title'])): ?>
                                <div style="font-weight:600;font-size:14px;margin-top:8px"><?= e($review['title']) ?></div>
                                <?php endif; ?>
                                <p style="font-size:13px;color:var(--text-medium);margin:8px 0 0;line-height:1.6"><?= e($review['comment']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p class="text-muted text-center py-3">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá!</p>
                    <?php endif; ?>
                    
                    <!-- Write Review Form -->
                    <?php if (isLoggedIn() && !$hasReviewed): ?>
                    <div style="background:var(--bg-light);border-radius:8px;padding:20px;margin-top:20px">
                        <h5 style="font-size:15px;font-weight:700;margin-bottom:16px">Viết đánh giá của bạn</h5>
                        <form action="<?= url('reviews/create') ?>" method="POST" id="review-form">
                            <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="rating" id="review-rating" value="5">
                            
                            <!-- Star Selector -->
                            <div class="mb-3">
                                <label class="form-label">Đánh giá <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star review-star active" data-rating="<?= $i ?>" 
                                       style="font-size:28px;color:#FFD600;cursor:pointer;transition:transform 0.1s"
                                       onmouseover="this.style.transform='scale(1.2)'"
                                       onmouseleave="this.style.transform='scale(1)'"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label" for="review-title">Tiêu đề đánh giá</label>
                                <input type="text" class="form-control" id="review-title" name="title" placeholder="Tóm tắt đánh giá của bạn">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label" for="review-comment">Nội dung <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="review-comment" name="comment" rows="4" placeholder="Chia sẻ trải nghiệm sử dụng sản phẩm..." required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-paper-plane me-1"></i>Gửi đánh giá
                            </button>
                        </form>
                    </div>
                    <?php elseif (!isLoggedIn()): ?>
                    <div class="text-center py-3">
                        <a href="<?= url('auth/login') ?>" class="btn btn-outline-danger">
                            <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập để đánh giá
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
    <div class="mt-5">
        <div class="section-header">
            <h2 class="section-title"><i class="fas fa-link"></i> Sản phẩm <span>liên quan</span></h2>
        </div>
        <div class="products-grid">
            <?php foreach ($relatedProducts as $product): ?>
            <?php include __DIR__ . '/../partials/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Base price for real-time calculations
window.baseProductPrice = <?= (int)$currentPrice ?>;

// Global Capacity Selection Function (Mobile & Desktop)
window.selectCapacity = function(btn, diff, label) {
    document.querySelectorAll('.btn-cap-card, .btn-variant-opt').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    const capDisplay = document.querySelector('.selected-cap-display');
    if (capDisplay && label) {
        capDisplay.textContent = label;
    }

    const numericDiff = Number(diff) || 0;
    const finalPrice = window.baseProductPrice + numericDiff;
    const formatted = new Intl.NumberFormat('vi-VN').format(finalPrice) + '₫';
    
    // Update all price tags (main product price + mobile sticky bar)
    document.querySelectorAll('.detail-price-current').forEach(el => {
        el.textContent = formatted;
    });
};

// Variant selection interactive handlers for Cart/Checkout
window.getSelectedVariantInfo = function() {
    const skuSelect = document.getElementById('sku-variant-select');
    if (skuSelect) return { variantId: skuSelect.value || null };
    const capBtn = document.querySelector('.btn-cap-card.active, .btn-variant-opt.active');
    const capLabel = capBtn ? (capBtn.dataset.label || '').trim() : '';
    const capDiff = capBtn ? parseFloat(capBtn.dataset.diff || '0') : 0;
    const colorBtn = document.querySelector('.btn-color-card.active');
    const colorLabel = colorBtn ? (colorBtn.dataset.name || '').trim() : '';
    return { variant: [capLabel, colorLabel].filter(Boolean).join(' / '), variantDiff: capDiff, color: colorLabel };
};

document.getElementById('sku-variant-select')?.addEventListener('change', function() {
    const option = this.selectedOptions[0];
    const stock = parseInt(option?.dataset.stock || '0', 10);
    const price = parseFloat(option?.dataset.price || String(window.baseProductPrice));
    document.querySelectorAll('.detail-price-current').forEach(el => el.textContent = new Intl.NumberFormat('vi-VN').format(price) + ' ₫');
    const quantity = document.querySelector('.qty-input');
    if (quantity) { quantity.max = String(stock); if (parseInt(quantity.value || '1', 10) > stock) quantity.value = String(Math.max(1, stock)); }
});

window.selectProductColor = function(btn) {
    document.querySelectorAll('.btn-color-card').forEach(color => color.classList.remove('active'));
    btn.classList.add('active');
    const image = btn.dataset.image || '';
    const mainImg = document.getElementById('gallery-main');
    if (mainImg && image) {
        mainImg.src = image;
        mainImg.dataset.originalSrc = image;
        mainImg.classList.remove('is-apple-scaled');
    }
    const stickyImage = document.querySelector('#sticky-buy-bar img');
    if (stickyImage && image) stickyImage.src = image;
    const display = document.querySelector('.selected-color-display');
    if (display) display.textContent = btn.dataset.name || '';
};

(function initProductVariants() {
    // Tự động kiểm tra phóng to ảnh nếu là ảnh thiết bị có viền padding Apple CDN
    const mainImg = document.getElementById('gallery-main');
    if (mainImg && (mainImg.src.includes('finish-select') || mainImg.src.includes('cdn-apple.com'))) {
        mainImg.classList.add('is-apple-scaled');
    }

    // Mobile Sticky Buy Bar Scroll Trigger
    const mainBuyBtn = document.querySelector('.btn-buy-now');
    const stickyBar = document.getElementById('sticky-buy-bar');
    if (mainBuyBtn && stickyBar) {
        window.addEventListener('scroll', () => {
            const rect = mainBuyBtn.getBoundingClientRect();
            if (rect.bottom < 0) {
                stickyBar.classList.add('visible');
            } else {
                stickyBar.classList.remove('visible');
            }
        }, { passive: true });
    }
})();

function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '<?= e($product['name']) ?>',
            url: window.location.href
        }).catch(() => {});
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            Toast.success('Đã sao chép link sản phẩm');
        });
    }
}
</script>

<?php if ($inStock): ?>
<!-- Mobile Sticky Buy Bar -->
<div class="mobile-sticky-buy-bar d-lg-none" id="sticky-buy-bar">
    <div class="d-flex align-items-center gap-2 mb-0">
        <img src="<?= productImage($product['main_image']) ?>" alt="Thumb" style="width:40px; height:40px; border-radius:8px; object-fit:cover; border:1px solid #eee;">
        <div class="flex-grow-1 text-truncate">
            <div class="text-truncate fw-bold text-dark" style="font-size:12px;"><?= e($product['name']) ?></div>
            <div class="text-danger fw-bold detail-price-current" style="font-size:13px;"><?= formatPrice($price) ?></div>
        </div>
        <button class="btn btn-outline-danger btn-sm px-2 py-1 btn-add-cart" data-product="<?= $product['id'] ?>" title="Thêm vào giỏ" style="border-radius:8px;">
            <i class="fas fa-cart-plus"></i>
        </button>
        <button class="btn btn-danger btn-sm px-3 py-1 fw-bold btn-buy-now" data-product="<?= $product['id'] ?>" style="border-radius:8px; background:#E53935; white-space:nowrap;">
            <i class="fas fa-bolt me-1"></i>Mua ngay
        </button>
    </div>
</div>

<style>
.gallery-main {
    position: relative;
    border-radius: var(--radius);
    overflow: hidden !important;
    background: #ffffff;
    border: 1px solid var(--border-light);
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px;
    margin-bottom: 12px;
}
.gallery-main img {
    max-height: 420px;
    max-width: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    transform-origin: center center;
    transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.22s ease, filter 0.25s ease;
}
/* Tự động phóng to ảnh có viền padding thừa Apple CDN để máy to rõ, cân đối chiếm trọn khung hình */
.gallery-main img.is-apple-scaled,
.gallery-main img[src*="finish-select"] {
    transform: scale(2.05) !important;
}
.mobile-sticky-buy-bar {
    position: fixed;
    bottom: 58px;
    left: 0;
    right: 0;
    background: #ffffff;
    padding: 8px 12px;
    box-shadow: 0 -4px 16px rgba(0,0,0,0.1);
    z-index: 1039;
    border-top: 1px solid #f1f5f9;
    transform: translateY(120%);
    transition: transform 0.3s ease;
}
.mobile-sticky-buy-bar.visible {
    transform: translateY(0);
}
</style>
<?php endif; ?>

<!-- Schema.org JSON-LD Structured Data for Google Rich Snippets -->
<?php
$seoPrice = $mainProduct['price_discount'] ?? $mainProduct['price_sale'] ?? 0;
$seoInStock = ((int)($mainProduct['stock_quantity'] ?? 0)) > 0;
$seoAvgRating = !empty($ratingStats['avg_rating']) ? round((float)$ratingStats['avg_rating'], 1) : 5.0;
$seoTotalReviews = max(1, (int)($reviewCount ?? 1));
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": <?= json_encode($mainProduct['name'] ?? '', JSON_UNESCAPED_UNICODE) ?>,
  "image": [
    <?= json_encode(productImage($mainProduct['main_image'] ?? ''), JSON_UNESCAPED_SLASHES) ?>
  ],
  "description": <?= json_encode(strip_tags(mb_substr($mainProduct['description'] ?? $mainProduct['name'] ?? '', 0, 250)), JSON_UNESCAPED_UNICODE) ?>,
  "sku": <?= json_encode('VH-' . ($mainProduct['id'] ?? 0)) ?>,
  "brand": {
    "@type": "Brand",
    "name": <?= json_encode($mainProduct['brand_name'] ?? 'Apple', JSON_UNESCAPED_UNICODE) ?>
  },
  "offers": {
    "@type": "Offer",
    "url": <?= json_encode(APP_URL . '/products/' . ($mainProduct['slug'] ?? ''), JSON_UNESCAPED_SLASHES) ?>,
    "priceCurrency": "VND",
    "price": <?= json_encode((string)$seoPrice) ?>,
    "itemCondition": "https://schema.org/NewCondition",
    "availability": "<?= $seoInStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' ?>",
    "seller": {
      "@type": "Organization",
      "name": "Vũ Huân Shop"
    }
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "<?= $seoAvgRating ?>",
    "reviewCount": "<?= $seoTotalReviews ?>",
    "bestRating": "5",
    "worstRating": "1"
  }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

