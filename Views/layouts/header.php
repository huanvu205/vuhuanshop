<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDesc ?? (APP_NAME . ' - Chuỗi bán lẻ điện thoại, laptop, tablet, phụ kiện công nghệ chính hãng giá tốt nhất Việt Nam')) ?>">
    <meta name="keywords" content="<?= e($metaKeywords ?? 'điện thoại chính hãng, iphone, samsung, xiaomi, laptop, phụ kiện giá rẻ, vũ huân shop') ?>">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Vũ Huân Shop">
    <meta name="app-url" content="<?= APP_URL ?>">
    <meta name="csrf-token" content="<?= $this->generateCsrfToken() ?>">
    <title><?= e($title ?? APP_NAME) ?></title>
    <meta name="google-site-verification" content="google747c1c911645ecc3" />

    <!-- Canonical URL -->
    <link rel="canonical" href="<?= APP_URL . ($_SERVER['REQUEST_URI'] ?? '') ?>">

    <!-- Open Graph / Facebook / Zalo -->
    <meta property="og:type" content="<?= isset($product) ? 'product' : 'website' ?>">
    <meta property="og:url" content="<?= APP_URL . ($_SERVER['REQUEST_URI'] ?? '') ?>">
    <meta property="og:title" content="<?= e($title ?? APP_NAME) ?>">
    <meta property="og:description" content="<?= e($metaDesc ?? (APP_NAME . ' - Điện thoại, Laptop chính hãng, bảo hành 12 tháng, 1 đổi 1 30 ngày.')) ?>">
    <meta property="og:image" content="<?= isset($product['main_image']) ? productImage($product['main_image']) : (APP_URL . '/public/images/logo.png') ?>">
    <meta property="og:site_name" content="<?= APP_NAME ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($title ?? APP_NAME) ?>">
    <meta name="twitter:description" content="<?= e($metaDesc ?? (APP_NAME . ' - Điện thoại, Laptop chính hãng.')) ?>">
    <meta name="twitter:image" content="<?= isset($product['main_image']) ? productImage($product['main_image']) : (APP_URL . '/public/images/logo.png') ?>">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= APP_URL ?>/public/css/style.css?v=<?= time() ?>" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/public/images/favicon.png">
</head>
<body>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loading-overlay">
    <div class="spinner"></div>
</div>

<!-- Main Navbar -->

<nav class="navbar navbar-main navbar-expand-lg">
    <div class="container-fluid px-3 px-md-4 px-xl-5">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url() ?>">
            <img src="<?= APP_URL ?>/public/images/logo.png" alt="Logo" style="height: 36px; width: 36px; object-fit: contain; border-radius: 8px;">
            <span>Vũ Huân <span>Shop</span></span>
        </a>
        
        <!-- Search (Desktop) -->
        <form action="<?= url('search') ?>" method="GET" class="search-wrapper desktop-search-wrapper d-none d-lg-flex align-items-center mb-0">
            <input type="search" name="q" class="search-input" id="search-input" placeholder="Tìm sản phẩm, thương hiệu..." value="<?= e($_GET['q'] ?? '') ?>" autocomplete="off">
            <button type="submit" class="search-btn" aria-label="Tìm kiếm">
                <i class="fas fa-search"></i>
            </button>
            <div class="search-suggestions" id="search-suggestions"></div>
        </form>
        
        <!-- Right Nav -->
        <div class="d-flex align-items-center gap-3 ms-auto justify-content-end">
            <!-- Wishlist -->
            <?php if (isLoggedIn()): ?>
            <a href="<?= url('wishlist') ?>" class="btn text-white position-relative" title="Yêu thích">
                <i class="far fa-heart"></i>
            </a>
            <?php endif; ?>
            
            <!-- Cart (Ẩn trên mobile vì đã có Bottom App Bar) -->
            <a href="<?= url('cart') ?>" class="btn text-white cart-icon position-relative d-none d-lg-inline-flex align-items-center justify-content-center" title="Giỏ hàng">
                <i class="fas fa-shopping-cart fs-5"></i>
                <span class="cart-count" style="<?= getCartCount() > 0 ? '' : 'display:none' ?>"><?= getCartCount() ?></span>
            </a>
            
            <!-- User Menu -->
            <?php if (isLoggedIn()): ?>
            <div class="dropdown">
                <button class="btn text-white dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="border:none; background:none; padding: 4px 8px;">
                    <?php if (!empty(currentUser()['avatar'])): ?>
                        <img src="<?= productImage(currentUser()['avatar']) ?>" alt="Avatar" style="width: 26px; height: 26px; border-radius: 50%; object-fit: cover; border: 1.5px solid rgba(255,255,255,0.8);">
                    <?php else: ?>
                        <i class="fas fa-user-circle fs-5"></i>
                    <?php endif; ?>
                    <span class="d-none d-md-inline"><?= e(currentUser()['full_name'] ?? '') ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= url('user/profile') ?>"><i class="fas fa-user me-2 text-primary"></i>Tài khoản</a></li>
                    <li><a class="dropdown-item" href="<?= url('orders') ?>"><i class="fas fa-box me-2 text-primary"></i>Đơn hàng</a></li>
                    <li><a class="dropdown-item" href="<?= url('wishlist') ?>"><i class="fas fa-heart me-2 text-primary"></i>Yêu thích</a></li>
                    <?php if (isStaff()): ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= url('admin/dashboard') ?>"><i class="fas fa-tachometer-alt me-2 text-warning"></i>Quản trị</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= url('auth/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                </ul>
            </div>
            <?php else: ?>
            <a href="<?= url('auth/login') ?>" class="btn text-white d-flex align-items-center gap-1" title="Đăng nhập" style="padding: 4px 8px;">
                <i class="fas fa-user-circle fs-5"></i> <span class="d-none d-sm-inline">Đăng nhập</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mobile Search Bar (Luôn hiển thị trực tiếp 100% trên điện thoại như Shopee/CellphoneS) -->
    <div class="mobile-search-bar-row d-lg-none w-100 px-3 pb-2 pt-1">
        <form action="<?= url('search') ?>" method="GET" class="mobile-search-form position-relative w-100 m-0">
            <div class="position-relative w-100">
                <input type="search" name="q" class="mobile-search-input" id="search-input-mobile" placeholder="Tìm điện thoại, laptop, phụ kiện..." value="<?= e($_GET['q'] ?? '') ?>" autocomplete="off">
                <button type="submit" class="mobile-search-submit" aria-label="Tìm kiếm">
                    <i class="fas fa-search"></i>
                </button>
                <div class="search-suggestions" id="search-suggestions-mobile"></div>
            </div>
        </form>
    </div>
</nav>

<!-- Category Navigation -->
<nav class="nav-categories d-none d-lg-block">
    <div class="container">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="<?= url('products') ?>?category=1">
                    <i class="fas fa-mobile-alt"></i> Điện thoại
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= url('products') ?>?category=2">
                    <i class="fas fa-laptop"></i> Laptop
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= url('products') ?>?category=3">
                    <i class="fas fa-tablet-alt"></i> Máy tính bảng
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= url('products') ?>?category=4">
                    <i class="fas fa-clock"></i> Đồng hồ thông minh
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= url('products') ?>?category=5">
                    <i class="fas fa-headphones"></i> Phụ kiện
                </a>
            </li>
            <li class="nav-item ms-auto">
                <a class="nav-link" href="<?= url('news') ?>">
                    <i class="fas fa-newspaper"></i> Tin tức
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= url('warranty') ?>">
                    <i class="fas fa-shield-alt"></i> Tra cứu bảo hành
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= url('orders/track') ?>">
                    <i class="fas fa-truck"></i> Theo dõi đơn hàng
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Flash Messages -->
<div class="container mt-3">
    <?php showFlash(); ?>
</div>
