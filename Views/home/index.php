<?php
    $categories = $categories ?? [];
    $flashSaleEnd = $flashSaleEnd ?? null;
    $totalProducts = $totalProducts ?? 0;
?><?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
// Giá trị mặc định phòng khi biến chưa được truyền từ controller (tránh lỗi undefined)
$banners          = $banners ?? [];
$sideBanners      = $sideBanners ?? [];
$categories       = $categories ?? [];
$featuredProducts = $featuredProducts ?? [];
$newProducts      = $newProducts ?? [];
$bestSelling      = $bestSelling ?? [];
$hotDeals         = $hotDeals ?? [];
$recentNews       = $recentNews ?? [];
$brands           = $brands ?? [];
$flashSaleEnd     = $flashSaleEnd ?? date('Y-m-d 23:59:59');
$totalProducts    = $totalProducts ?? 0;
?>

<style>
.hero-banner-img {
    height: 400px;
    object-fit: cover;
    border-radius: 12px;
}
.hero-banner-default {
    min-height: 400px;
}
@media (max-width: 768px) {
    .hero-banner-img {
        height: 180px !important;
        border-radius: 10px;
    }
    .hero-banner-default {
        min-height: 180px;
        padding: 20px !important;
    }
    .hero-banner-default h2 {
        font-size: 20px !important;
    }
    .hero-banner-default p {
        font-size: 13px !important;
        margin-bottom: 12px !important;
    }
    .hero-slider .carousel-indicators [data-bs-target] {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
}
@media (max-width: 480px) {
    .hero-banner-img {
        height: 160px !important;
    }
}
</style>

<!-- Hero Section -->
<div class="container-fluid px-3 mt-3">
    <div class="row g-3">
        <!-- Main Slider -->
        <div class="col-lg-8">
            <?php if (!empty($banners)): ?>
            <div id="heroCarousel" class="carousel slide hero-slider" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($banners as $i => $banner): ?>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active"' : '' ?>></button>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-inner">
                    <?php foreach ($banners as $i => $banner): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <a href="<?= e($banner['link'] ?? '#') ?>">
                            <img src="<?= productImage($banner['image']) ?>" class="d-block w-100 hero-banner-img" alt="<?= e($banner['title']) ?>" onerror="this.onerror=null;this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiB2aWV3Qm94PSIwIDAgNDAwIDQwMCI+PHJlY3Qgd2lkdGg9IjQwMCIgaGVpZ2h0PSI0MDAiIGZpbGw9IiNmNWY1ZjUiLz48Y2lyY2xlIGN4PSIyMDAiIGN5PSIxNjAiIHI9IjUwIiBmaWxsPSIjZTBlMGUwIi8+PGNpcmNsZSBjeD0iMjAwIiBjeT0iMTYwIiByPSIzMCIgZmlsbD0iIzk5OTk5OSIvPjxwYXRoIGQ9Ik0xNDAgMjIwSDI2MlYyODBoLTEyMlYyMjB6IiBmaWxsPSIjOTk5OTk5Ii8+PC9zdmc+'">
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
            <?php else: ?>
            <!-- Default Banner if no banners -->
            <div class="hero-banner-default" style="background:linear-gradient(135deg,#B71C1C,#E53935);border-radius:12px;display:flex;align-items:center;padding:40px">
                <div>
                    <h2 style="color:white;font-size:36px;font-weight:800;margin-bottom:12px">Công nghệ đỉnh cao<br>Giá cạnh tranh hấp dẫn</h2>
                    <p style="color:rgba(255,255,255,0.85);font-size:16px;margin-bottom:20px">iPhone, Samsung, Xiaomi và hơn <?= number_format($totalProducts) ?>+ sản phẩm chính hãng</p>
                    <a href="<?= url('products') ?>" class="btn btn-warning btn-lg fw-bold rounded-pill px-5">Mua ngay <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Side Banners -->
        <div class="col-lg-4 d-none d-lg-block">
            <div class="d-flex flex-column gap-3 h-100 justify-content-between">
                <?php
                $sideBannerDefaults = [
                    ['title' => 'iPhone 16 Pro Max - Giảm đến 30%', 'link' => url('products') . '?brand=1', 'eyebrow' => 'HOT DEAL HÔM NAY', 'class' => 'promo-banner-blue'],
                    ['title' => 'MSI & Asus ROG - Trả góp 0%', 'link' => url('products') . '?category=2', 'eyebrow' => 'LAPTOP GAMING', 'class' => 'promo-banner-green'],
                ];
                for ($i = 0; $i < 2; $i++):
                    $sideBanner = $sideBanners[$i] ?? null;
                    $promo = $sideBannerDefaults[$i];
                    $promoTitle = !empty($sideBanner['title']) ? $sideBanner['title'] : $promo['title'];
                    $promoLink = !empty($sideBanner['link']) ? $sideBanner['link'] : ($sideBanner ? url('products') : $promo['link']);
                    $promoImage = !empty($sideBanner['image']) ? productImage($sideBanner['image']) : '';
                ?>
                <?php if ($promoImage): ?>
                    <a href="<?= e($promoLink) ?>" class="d-block shadow-sm side-banner-card" style="height:192px; border-radius:12px; overflow:hidden; position:relative; transition:all 0.3s ease;">
                        <img src="<?= e($promoImage) ?>" alt="<?= e($promoTitle) ?>" style="width:100%; height:100%; object-fit:cover; display:block; transition:transform 0.3s ease;" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/images/no-image.png'" onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                    </a>
                <?php else: ?>
                    <a class="promo-banner <?= e($promo['class']) ?>"
                       href="<?= e($promoLink) ?>" style="height:192px; margin:0; border-radius:12px;">
                        <span class="promo-banner-eyebrow"><?= e($promo['eyebrow']) ?></span>
                        <span class="promo-banner-title"><?= e($promoTitle) ?></span>
                        <span class="promo-banner-link">Xem ngay <i class="fas fa-arrow-right ms-1"></i></span>
                    </a>
                <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<!-- Trust Badges -->
<div class="container mt-3">
    <div class="row g-2">
        <?php $trustItems = [['fas fa-shield-alt','#27AE60','Hàng chính hãng','Cam kết chính hãng'],['fas fa-truck','#2980B9','Giao hàng nhanh','Trong ngày tại nội thành'],['fas fa-undo','#E67E22','Đổi trả dễ dàng','Theo điều kiện sản phẩm'],['fas fa-credit-card','#8E44AD','Trả góp 0%','12-24 tháng'],['fas fa-headset','#E53935','Hỗ trợ 24/7','Luôn sẵn sàng']]; ?>
        <?php foreach ($trustItems as $item): ?>
        <div class="col-6 col-md-4 col-lg-2half">
            <div style="background:white;border-radius:10px;padding:14px 12px;display:flex;align-items:center;gap:10px;box-shadow:0 1px 4px rgba(0,0,0,0.06)">
                <div style="width:36px;height:36px;border-radius:50%;background:<?= $item[1] ?>20;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="<?= $item[0] ?>" style="color:<?= $item[1] ?>;font-size:15px"></i>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600"><?= $item[2] ?></div>
                    <div style="font-size:11px;color:#9E9E9E"><?= $item[3] ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Categories -->
<div class="container mt-4">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-th-large"></i> Danh mục <span>nổi bật</span></h2>
        <a href="<?= url('products') ?>" class="section-link">Xem tất cả <i class="fas fa-chevron-right"></i></a>
    </div>
    <div class="category-grid">
        <?php foreach ($categories as $cat): ?>
        <a href="<?= url('products') ?>?category=<?= $cat['id'] ?>" class="category-card" style="text-decoration:none;color:inherit">
            <div class="category-card-icon">
                <?php 
                $icons = ['Điện Thoại' => '📱', 'Laptop' => '💻', 'Máy Tính Bảng' => '📟', 'Đồng Hồ Thông Minh' => '⌚', 'Phụ Kiện' => '🎧'];
                $icon = $icons[$cat['name']] ?? '🛍️';
                echo $icon;
                ?>
            </div>
            <div class="category-card-name"><?= e($cat['name']) ?></div>
            <div class="category-card-count"><?= number_format($cat['product_count']) ?> sản phẩm</div>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Hot Deals -->
<?php if (!empty($hotDeals)): ?>
<div class="container mt-4">
    <div class="hot-deal-section">
        <div class="hot-deal-header">
            <div class="hot-deal-left">
                <h2 class="hot-deal-title">
                    <span class="hot-deal-icon">🔥</span> FLASH SALE <span class="hot-deal-sub d-none d-sm-inline">Hôm nay</span>
                </h2>
                <div class="hot-deal-timer-wrap">
                    <span class="timer-label d-none d-md-inline">Kết thúc trong</span>
                    <div class="hot-deal-timer" id="countdown-timer" data-end="<?= e($flashSaleEnd) ?>">
                        <div class="timer-unit"><span class="timer-h">00</span></div>
                        <span class="timer-colon">:</span>
                        <div class="timer-unit"><span class="timer-m">00</span></div>
                        <span class="timer-colon">:</span>
                        <div class="timer-unit"><span class="timer-s">00</span></div>
                    </div>
                </div>
            </div>
            <a href="<?= url('products') ?>" class="hot-deal-more">
                <span>Xem tất cả</span>
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
        <div class="products-scroll">
            <?php foreach ($hotDeals as $product): ?>
            <?php include __DIR__ . '/../partials/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Featured Products -->
<?php if (!empty($featuredProducts)): ?>
<div class="container mt-4">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-star"></i> Sản phẩm <span>nổi bật</span></h2>
        <a href="<?= url('products') ?>" class="section-link">Xem tất cả <i class="fas fa-chevron-right"></i></a>
    </div>
    <div class="products-grid">
        <?php foreach ($featuredProducts as $product): ?>
        <?php include __DIR__ . '/../partials/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- New Products -->
<?php if (!empty($newProducts)): ?>
<div class="container mt-4">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-bolt"></i> Sản phẩm <span>mới nhất</span></h2>
        <a href="<?= url('products') ?>?sort=newest" class="section-link">Xem tất cả <i class="fas fa-chevron-right"></i></a>
    </div>
    <div class="products-scroll">
        <?php foreach ($newProducts as $product): ?>
        <?php include __DIR__ . '/../partials/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Best Selling -->
<?php if (!empty($bestSelling)): ?>
<div class="container mt-4">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-fire"></i> Bán chạy <span>nhất</span></h2>
        <a href="<?= url('products') ?>?sort=best_selling" class="section-link">Xem tất cả <i class="fas fa-chevron-right"></i></a>
    </div>
    <div class="products-grid">
        <?php foreach ($bestSelling as $product): ?>
        <?php include __DIR__ . '/../partials/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Brands -->
<?php if (!empty($brands)): ?>
<div class="container mt-4">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-tags"></i> Thương hiệu <span>nổi tiếng</span></h2>
    </div>
    <div class="brands-strip d-flex align-items-center gap-1 flex-wrap px-2" style="background:white;border-radius:12px;padding:16px">
        <?php foreach ($brands as $brand): ?>
        <a href="<?= url('products') ?>?brand=<?= $brand['id'] ?>" class="brand-item flex-column text-center" style="min-width:80px">
            <?php if ($brand['logo']): ?>
            <img src="<?= productImage($brand['logo']) ?>" alt="<?= e($brand['name']) ?>" style="height:32px;object-fit:contain" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/images/no-image.png'">
            <?php else: ?>
            <div style="font-size:13px;font-weight:700;color:#424242"><?= e($brand['name']) ?></div>
            <?php endif; ?>
            <div style="font-size:10px;color:#9E9E9E;margin-top:4px"><?= number_format($brand['product_count']) ?> SP</div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- News -->
<?php if (!empty($recentNews)): ?>
<div class="container mt-4">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-newspaper"></i> Tin tức <span>công nghệ</span></h2>
        <a href="<?= url('news') ?>" class="section-link">Xem tất cả <i class="fas fa-chevron-right"></i></a>
    </div>
    <div class="row g-3">
        <?php foreach ($recentNews as $news): ?>
        <div class="col-6 col-md-3">
            <div class="news-card">
                <div class="news-card-img">
                    <a href="<?= url('news/' . $news['slug']) ?>">
                        <img src="<?= productImage($news['featured_image']) ?>" alt="<?= e($news['title']) ?>" onerror="this.src='<?= APP_URL ?>/public/images/no-image.png'">
                    </a>
                </div>
                <div class="news-card-body">
                    <div class="news-card-category"><?= e($news['category'] ?? 'Công nghệ') ?></div>
                    <h3 class="news-card-title">
                        <a href="<?= url('news/' . $news['slug']) ?>"><?= e($news['title']) ?></a>
                    </h3>
                    <div class="news-card-meta">
                        <span><i class="fas fa-clock me-1"></i><?= timeAgo($news['published_at'] ?? $news['created_at']) ?></span>
                        <span><i class="fas fa-eye me-1"></i><?= number_format($news['view_count']) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div style="height:40px"></div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
// Initialize countdown for flash sale
document.getElementById('countdown-timer') && (function() {
    Timer.init();
})();
</script>
