<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDesc ?? APP_NAME . ' - Mua sắm điện thoại, laptop, tablet chính hãng với giá tốt nhất') ?>">
    <meta name="app-url" content="<?= APP_URL ?>">
    <meta name="csrf-token" content="<?= $this->generateCsrfToken() ?>">
    <title><?= e($title ?? APP_NAME) ?></title>
    
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

<!-- Header Top Bar -->
<div class="header-top d-none d-md-block">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex gap-4">
                <span><i class="fas fa-phone me-1"></i> Hotline: <a href="tel:0336120405"><strong>0336.120.405</strong></a></span>
                <span><i class="fas fa-clock me-1"></i> Thứ 2 - CN: 8:00 - 21:00</span>
                <span><i class="fas fa-map-marker-alt me-1"></i> 100+ Cửa hàng toàn quốc</span>
            </div>
            <div class="d-flex gap-3">
                <a href="<?= url('orders/track') ?>"><i class="fas fa-truck me-1"></i>Theo dõi đơn hàng</a>
                <a href="<?= url('news') ?>"><i class="fas fa-newspaper me-1"></i>Tin tức</a>
                <?php if (isLoggedIn()): ?>
                    <a href="<?= url('user/profile') ?>"><i class="fas fa-user me-1"></i><?= e(currentUser()['full_name'] ?? 'Tài khoản') ?></a>
                <?php else: ?>
                    <a href="<?= url('auth/login') ?>"><i class="fas fa-sign-in-alt me-1"></i>Đăng nhập</a>
                    <a href="<?= url('auth/register') ?>"><i class="fas fa-user-plus me-1"></i>Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Navbar -->
<nav class="navbar navbar-main navbar-expand-lg">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url() ?>">
            <img src="<?= APP_URL ?>/public/images/logo.png" alt="Logo" style="height: 36px; width: 36px; object-fit: contain; border-radius: 8px;">
            <span>Vũ Huân <span>Shop</span></span>
        </a>
        
        <!-- Search (Desktop) -->
        <div class="search-wrapper d-none d-lg-flex align-items-center">
            <input type="text" class="search-input" id="search-input" placeholder="Tìm sản phẩm, thương hiệu..." autocomplete="off">
            <button class="search-btn" onclick="window.location.href='<?= url('search') ?>?q='+document.getElementById('search-input').value">
                <i class="fas fa-search"></i>
            </button>
            <div class="search-suggestions" id="search-suggestions"></div>
        </div>
        
        <!-- Right Nav -->
        <div class="d-flex align-items-center gap-2 ms-auto">
            <!-- Mobile Search -->
            <button class="btn text-white d-lg-none" data-bs-toggle="collapse" data-bs-target="#mobileSearch">
                <i class="fas fa-search"></i>
            </button>
            
            <!-- Wishlist -->
            <?php if (isLoggedIn()): ?>
            <a href="<?= url('wishlist') ?>" class="btn text-white position-relative" title="Yêu thích">
                <i class="far fa-heart"></i>
            </a>
            <?php endif; ?>
            
            <!-- Cart -->
            <a href="<?= url('cart') ?>" class="btn text-white cart-icon position-relative" title="Giỏ hàng">
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
            <a href="<?= url('auth/login') ?>" class="btn text-white d-none d-sm-flex align-items-center gap-1">
                <i class="fas fa-sign-in-alt"></i> <span>Đăng nhập</span>
            </a>
            <?php endif; ?>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler text-white border-0 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <!-- Nav Links (Desktop) -->
        <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarMain">
        </div>
    </div>
    
    <!-- Mobile Search -->
    <div class="collapse" id="mobileSearch">
        <div class="container py-2">
            <div class="search-wrapper" style="max-width:100%;margin:0">
                <input type="text" class="search-input w-100" placeholder="Tìm sản phẩm..." id="search-input-mobile">
                <button class="search-btn" onclick="window.location.href='<?= url('search') ?>?q='+document.getElementById('search-input-mobile').value">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
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
