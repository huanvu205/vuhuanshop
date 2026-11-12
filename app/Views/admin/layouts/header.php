<?php
$dbInstance = new Database();
$pendingReviews = $dbInstance->query("SELECT r.*, u.full_name, p.name as product_name FROM reviews r LEFT JOIN users u ON r.user_id = u.id LEFT JOIN products p ON r.product_id = p.id WHERE r.status = 'pending' ORDER BY r.created_at DESC LIMIT 5")->resultSet();
$pendingReviewsCount = count($pendingReviews);

$adminUserId = currentUser()['id'] ?? null;
$adminNotifications = [];
$unreadNotifsCount = 0;
if ($adminUserId) {
    $adminNotifications = $dbInstance->query("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 5")->bind(':uid', $adminUserId)->resultSet();
    $unreadNotifsCount = $dbInstance->query("SELECT COUNT(*) as total FROM notifications WHERE user_id = :uid AND is_read = 0")->bind(':uid', $adminUserId)->single()['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $this->generateCsrfToken() ?>">
    <title><?= e($title ?? 'Admin - Vũ Huân Shop') ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Admin CSS -->
    <link href="<?= APP_URL ?>/public/css/admin.css" rel="stylesheet">
</head>
<body>

<script>
    // Prevent flash of incorrect theme on load
    const storedTheme = localStorage.getItem('admin-theme') || 'light';
    document.documentElement.setAttribute('data-theme', storedTheme);
</script>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="admin-wrapper">
    <!-- Sidebar Left -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Logo -->
        <div class="sidebar-logo">
            <img src="<?= APP_URL ?>/public/images/logo.png" alt="Logo" style="height: 36px; width: 36px; object-fit: contain; border-radius: 8px; flex-shrink: 0;">
            <div>
                <div class="sidebar-logo-text">Vũ Huân <span>Shop</span></div>
                <div class="sidebar-logo-sub">Hệ thống Quản trị</div>
            </div>
        </div>
        
        <!-- User Info -->
        <div class="sidebar-user">
            <?php if (!empty(currentUser()['avatar'])): ?>
                <img src="<?= productImage(currentUser()['avatar']) ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1.5px solid rgba(255,255,255,0.2); margin-right: 12px; flex-shrink: 0;">
            <?php else: ?>
                <div class="sidebar-user-avatar">
                    <?= mb_substr(currentUser()['full_name'] ?? 'A', 0, 1) ?>
                </div>
            <?php endif; ?>
            <div>
                <div class="sidebar-user-name"><?= e(currentUser()['full_name'] ?? 'Quản trị viên') ?></div>
                <div class="sidebar-user-role"><?= e((currentUser()['role'] ?? 'admin') === 'admin' ? 'Super Admin' : 'Nhân viên') ?></div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Báo cáo tổng quan</div>
            <a href="<?= url('admin/dashboard') ?>" class="sidebar-nav-item <?= isActiveRoute('admin/dashboard') ? 'active' : '' ?>">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            
            <div class="sidebar-section-label">Quản lý hàng hóa</div>
            <a href="<?= url('admin/products') ?>" class="sidebar-nav-item <?= isActiveRoute('admin/products') ? 'active' : '' ?>">
                <i class="fas fa-box"></i> Quản lý sản phẩm
            </a>
            <a href="<?= url('admin/categories') ?>" class="sidebar-nav-item <?= isActiveRoute('admin/categories') ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i> Danh mục sản phẩm
            </a>
            <a href="<?= url('admin/inventory') ?>" class="sidebar-nav-item <?= isActiveRoute('admin/inventory') ? 'active' : '' ?>">
                <i class="fas fa-warehouse"></i> Quản lý kho hàng
            </a>
            
            <div class="sidebar-section-label">Giao dịch & Khách hàng</div>
            <a href="<?= url('admin/orders') ?>" class="sidebar-nav-item <?= isActiveRoute('admin/orders') ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i> Quản lý đơn hàng
            </a>
            <a href="<?= url('admin/users') ?>" class="sidebar-nav-item <?= isActiveRoute('admin/users') ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Quản lý khách hàng
            </a>
            <a href="<?= url('admin/reviews') ?>" class="sidebar-nav-item <?= isActiveRoute('admin/reviews') ? 'active' : '' ?>">
                <i class="fas fa-star"></i> Đánh giá sản phẩm
            </a>
            
            <div class="sidebar-section-label">Tiếp thị & Báo cáo</div>
            <a href="<?= url('admin/coupons') ?>" class="sidebar-nav-item <?= isActiveRoute('admin/coupons') ? 'active' : '' ?>">
                <i class="fas fa-ticket-alt"></i> Khuyến mãi & Voucher
            </a>
            <a href="<?= url('admin/reports') ?>" class="sidebar-nav-item <?= isActiveRoute('admin/reports') ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i> Doanh thu & Báo cáo
            </a>
            
            <div class="sidebar-section-label">Cấu hình hệ thống</div>
            <a href="<?= url('admin/banners') ?>" class="sidebar-nav-item <?= isActiveRoute('admin/banners') ? 'active' : '' ?>">
                <i class="fas fa-image"></i> Quản lý Banner
            </a>
            <a href="<?= url('admin/news') ?>" class="sidebar-nav-item <?= isActiveRoute('admin/news') ? 'active' : '' ?>">
                <i class="fas fa-newspaper"></i> Quản lý Tin tức
            </a>
            <a href="<?= url('admin/settings') ?>" class="sidebar-nav-item <?= isActiveRoute('admin/settings') ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> Cài đặt hệ thống
            </a>
        </nav>
    </aside>
    
    <!-- Main Content wrapper -->
    <main class="admin-main">
        <!-- Topbar Header -->
        <header class="admin-topbar">
            <!-- Left Side: Toggle menu button + Title -->
            <div class="topbar-left">
                <button class="btn p-0 me-2 d-lg-none" onclick="toggleSidebar()" style="border:none;background:none;color:var(--text-muted)">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <div class="d-none d-md-block">
                    <span class="topbar-title"><?= e($pageTitle ?? $title ?? 'Dashboard') ?></span>
                    <?php if (!empty($breadcrumb)): ?>
                    <div class="topbar-breadcrumb"><?= e($breadcrumb) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Search bar in Header -->
                <form class="topbar-search-form d-none d-sm-block" action="<?= url('admin/products') ?>" method="GET">
                    <i class="fas fa-search topbar-search-icon"></i>
                    <input type="text" name="q" class="topbar-search-input" placeholder="Tìm sản phẩm, mã hàng...">
                </form>
            </div>
            
            <!-- Right Side: Dark mode + notification + messages + profile dropdown -->
            <div class="topbar-actions">
                <!-- Chuyển đổi Dark/Light Mode -->
                <button class="topbar-icon-btn theme-switch-btn" id="themeToggle" title="Chuyển đổi sáng/tối">
                    <i class="fas fa-moon"></i>
                </button>
                               <!-- Tin nhắn / Bình luận -->
                <div class="dropdown">
                    <button class="topbar-icon-btn dropdown-toggle" id="commentsDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Tin nhắn & Đánh giá">
                        <i class="fas fa-comment-alt"></i>
                        <?php if ($pendingReviewsCount > 0): ?>
                            <span class="topbar-badge-count" id="pendingReviewsBadge"><?= $pendingReviewsCount ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-custom" aria-labelledby="commentsDropdown">
                        <div class="dropdown-header-custom d-flex justify-content-between align-items-center">
                            <span>Bình luận & Đánh giá mới</span>
                            <span class="badge bg-danger rounded-pill" id="pendingReviewsHeaderBadge"><?= $pendingReviewsCount ?> chờ duyệt</span>
                        </div>
                        <div style="max-height: 350px; overflow-y: auto;">
                            <?php if (empty($pendingReviews)): ?>
                                <div class="p-3 text-center text-muted" style="font-size:0.8125rem;">Không có đánh giá nào đang chờ duyệt.</div>
                            <?php else: ?>
                                <?php foreach ($pendingReviews as $rev): ?>
                                    <a class="dropdown-item-review" href="<?= url('admin/reviews') ?>">
                                        <div class="dropdown-item-review-avatar">
                                            <?= mb_substr($rev['full_name'] ?? 'K', 0, 1) ?>
                                        </div>
                                        <div class="dropdown-item-custom-content">
                                            <div class="dropdown-item-custom-title"><?= e($rev['full_name'] ?? 'Khách ẩn danh') ?></div>
                                            <div class="mb-1">
                                                <?= renderStars($rev['rating']) ?>
                                            </div>
                                            <div class="dropdown-item-custom-desc">
                                                <strong><?= e($rev['product_name'] ?? 'Sản phẩm') ?>:</strong> <?= e($rev['comment']) ?>
                                            </div>
                                            <div class="dropdown-item-custom-time"><?= timeAgo($rev['created_at']) ?></div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="dropdown-footer-custom">
                            <a href="<?= url('admin/reviews') ?>">Xem tất cả đánh giá</a>
                        </div>
                    </div>
                </div>
                
                <!-- Thông báo -->
                <div class="dropdown">
                    <button class="topbar-icon-btn dropdown-toggle" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo">
                        <i class="fas fa-bell"></i>
                        <?php if ($unreadNotifsCount > 0): ?>
                            <span class="topbar-badge-dot" id="unreadNotifsDot"></span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-custom" aria-labelledby="notificationsDropdown">
                        <div class="dropdown-header-custom d-flex justify-content-between align-items-center">
                            <span>Thông báo hệ thống</span>
                            <a href="javascript:void(0)" onclick="markAllNotificationsRead(event)" class="text-danger" style="font-size: 0.75rem; font-weight: 500;">Đọc tất cả</a>
                        </div>
                        <div style="max-height: 350px; overflow-y: auto;" id="notificationListContainer">
                            <?php if (empty($adminNotifications)): ?>
                                <div class="p-3 text-center text-muted" style="font-size:0.8125rem;">Không có thông báo nào.</div>
                            <?php else: ?>
                                <?php foreach ($adminNotifications as $notif): ?>
                                    <?php 
                                        $iconClass = 'fa-info-circle';
                                        $typeClass = 'info';
                                        if (($notif['type'] ?? '') === 'order') {
                                            $iconClass = 'fa-shopping-cart';
                                            $typeClass = 'order';
                                        } elseif (($notif['type'] ?? '') === 'review') {
                                            $iconClass = 'fa-star';
                                            $typeClass = 'review';
                                        } elseif (($notif['type'] ?? '') === 'stock') {
                                            $iconClass = 'fa-exclamation-triangle';
                                            $typeClass = 'stock';
                                        }
                                        $itemUnreadClass = $notif['is_read'] == 0 ? 'unread' : '';
                                    ?>
                                    <a class="dropdown-item-custom <?= $itemUnreadClass ?>" href="javascript:void(0)" onclick="readNotification(<?= $notif['id'] ?>, '<?= e($notif['type'] ?? '') ?>', event)" data-id="<?= $notif['id'] ?>">
                                        <div class="dropdown-item-custom-icon <?= $typeClass ?>">
                                            <i class="fas <?= $iconClass ?>"></i>
                                        </div>
                                        <div class="dropdown-item-custom-content">
                                            <div class="dropdown-item-custom-title"><?= e($notif['title']) ?></div>
                                            <div class="dropdown-item-custom-desc"><?= e($notif['message']) ?></div>
                                            <div class="dropdown-item-custom-time"><?= timeAgo($notif['created_at']) ?></div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="dropdown-footer-custom">
                            <span class="text-muted" style="font-size:0.75rem;">Thông báo mới cập nhật liên tục</span>
                        </div>
                    </div>
                </div>
                
                <!-- Hồ sơ Admin -->
                <div class="dropdown">
                    <?php if (!empty(currentUser()['avatar'])): ?>
                        <img src="<?= productImage(currentUser()['avatar']) ?>" alt="Avatar" class="topbar-avatar dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1.5px solid rgba(0,0,0,0.1); cursor: pointer;">
                    <?php else: ?>
                        <div class="topbar-avatar dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= mb_substr(currentUser()['full_name'] ?? 'A', 0, 1) ?>
                        </div>
                    <?php endif; ?>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <div class="dropdown-header d-flex align-items-center gap-2">
                                <?php if (!empty(currentUser()['avatar'])): ?>
                                    <img src="<?= productImage(currentUser()['avatar']) ?>" alt="Avatar" style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:1px solid #ddd;">
                                <?php else: ?>
                                    <div class="topbar-avatar" style="width:28px;height:28px;font-size:0.75rem"><?= mb_substr(currentUser()['full_name'] ?? 'A', 0, 1) ?></div>
                                <?php endif; ?>
                                <div>
                                    <div style="font-weight:600;color:var(--text-main)"><?= e(currentUser()['full_name'] ?? 'Admin') ?></div>
                                    <div style="font-size:10px;color:var(--text-muted)"><?= e(currentUser()['email'] ?? '') ?></div>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= url('user/profile') ?>"><i class="fas fa-user-cog me-2"></i>Hồ sơ của tôi</a></li>
                        <li><a class="dropdown-item" href="<?= url() ?>" target="_blank"><i class="fas fa-external-link-alt me-2"></i>Xem Website</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= url('auth/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Content Area -->
        <div class="admin-content">
            <?php showFlash(); ?>
