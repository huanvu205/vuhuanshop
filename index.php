<?php
ob_start();
// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Do not expose stack traces or configuration details to website visitors.
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

/**
 * Entry Point - Router chính của ứng dụng
 * Website bán điện thoại, laptop, tablet
 */

// Khởi động session với cấu hình tối ưu cho hosting
if (session_status() === PHP_SESSION_NONE) {
    // Tăng thời gian sống session (2 giờ)
    ini_set('session.gc_maxlifetime', 7200);
    ini_set('session.cookie_lifetime', 7200);
    
    // Đảm bảo session cookie hoạt động đúng
    session_set_cookie_params([
        'lifetime' => 7200,
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    @session_start();
}

// Load cấu hình
require_once __DIR__ . '/core/Config.php';
if (defined('APP_ENV') && APP_ENV === 'local') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/app/Helpers/Helper.php';

// Load tất cả Models
require_once __DIR__ . '/app/Models/UserModel.php';
require_once __DIR__ . '/app/Models/CategoryModel.php';
require_once __DIR__ . '/app/Models/BrandModel.php';
require_once __DIR__ . '/app/Models/ProductModel.php';
require_once __DIR__ . '/app/Models/CartModel.php';
require_once __DIR__ . '/app/Models/OrderModel.php';
require_once __DIR__ . '/app/Models/ReviewModel.php';
require_once __DIR__ . '/app/Models/WishlistModel.php';
require_once __DIR__ . '/app/Models/AddressModel.php';
require_once __DIR__ . '/app/Models/BannerModel.php';
require_once __DIR__ . '/app/Models/NewsModel.php';
require_once __DIR__ . '/app/Models/CouponModel.php';
require_once __DIR__ . '/app/Models/InventoryModel.php';
require_once __DIR__ . '/app/Models/NotificationModel.php';
require_once __DIR__ . '/app/Models/SettingModel.php';
require_once __DIR__ . '/app/Services/VnpayService.php';

// Load Controllers
require_once __DIR__ . '/app/Controllers/HomeController.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/ProductController.php';
require_once __DIR__ . '/app/Controllers/CompareController.php';
require_once __DIR__ . '/app/Controllers/CartController.php';
require_once __DIR__ . '/app/Controllers/CheckoutController.php';
require_once __DIR__ . '/app/Controllers/OrderController.php';
require_once __DIR__ . '/app/Controllers/ReviewController.php';
require_once __DIR__ . '/app/Controllers/WishlistController.php';
require_once __DIR__ . '/app/Controllers/UserController.php';
require_once __DIR__ . '/app/Controllers/NewsController.php';
require_once __DIR__ . '/app/Controllers/PolicyController.php';
require_once __DIR__ . '/app/Controllers/WarrantyController.php';
require_once __DIR__ . '/app/Controllers/Admin/DashboardController.php';
require_once __DIR__ . '/app/Controllers/Admin/ProductAdminController.php';
require_once __DIR__ . '/app/Controllers/Admin/CategoryController.php';
require_once __DIR__ . '/app/Controllers/Admin/BrandController.php';
require_once __DIR__ . '/app/Controllers/Admin/OrderAdminController.php';
require_once __DIR__ . '/app/Controllers/Admin/UserAdminController.php';
require_once __DIR__ . '/app/Controllers/Admin/CouponController.php';
require_once __DIR__ . '/app/Controllers/Admin/InventoryController.php';
require_once __DIR__ . '/app/Controllers/Admin/BannerController.php';
require_once __DIR__ . '/app/Controllers/Admin/NewsAdminController.php';
require_once __DIR__ . '/app/Controllers/Admin/ReviewAdminController.php';
require_once __DIR__ . '/app/Controllers/Admin/ReportController.php';
require_once __DIR__ . '/app/Controllers/Admin/SettingController.php';

// Lấy URL hiện tại
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = parse_url(APP_URL, PHP_URL_PATH) ?? '';
$path = str_replace($basePath, '', $requestUri);
$path = strtok($path, '?');
$path = rtrim($path, '/');
if (empty($path)) $path = '/';

// Google Search Console Verification
if (strpos($path, 'google747c1c911645ecc3.html') !== false) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: text/html; charset=utf-8');
    echo 'google-site-verification: google747c1c911645ecc3.html';
    exit;
}

// Dynamic XML Sitemap for Google Search Console
if ($path === '/sitemap.xml' || $path === 'sitemap.xml' || (strpos($path, 'sitemap.xml') !== false)) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    $staticUrls = [
        ['loc' => APP_URL . '/', 'priority' => '1.0', 'changefreq' => 'daily'],
        ['loc' => APP_URL . '/products', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['loc' => APP_URL . '/news', 'priority' => '0.7', 'changefreq' => 'weekly'],
        ['loc' => APP_URL . '/policies/warranty', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['loc' => APP_URL . '/policies/returns', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['loc' => APP_URL . '/policies/payment', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['loc' => APP_URL . '/policies/terms', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['loc' => APP_URL . '/policies/privacy', 'priority' => '0.5', 'changefreq' => 'monthly'],
    ];
    
    foreach ($staticUrls as $u) {
        echo '  <url><loc>' . htmlspecialchars($u['loc']) . '</loc><changefreq>' . $u['changefreq'] . '</changefreq><priority>' . $u['priority'] . '</priority></url>' . "\n";
    }
    
    try {
        $pModel = new ProductModel();
        $prods = $pModel->getActive(100);
        foreach ($prods as $p) {
            echo "  <url>\n";
            echo '    <loc>' . htmlspecialchars(APP_URL . '/products/' . $p['slug']) . "</loc>\n";
            echo '    <lastmod>' . date('Y-m-d', strtotime($p['updated_at'] ?? 'now')) . "</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.8</priority>\n";
            echo "  </url>\n";
        }
    } catch (\Throwable $e) {}
    
    echo '</urlset>';
    exit;
}

// Routing
$segments = explode('/', ltrim($path, '/'));
$controller = $segments[0] ?? '';
$action = $segments[1] ?? 'index';
$param = $segments[2] ?? null;
$param2 = $segments[3] ?? null;

// Routes định nghĩa
$routes = [
    '' => ['HomeController', 'index'],
    'home' => ['HomeController', 'index'],
    
    // Auth
    'auth' => 'AuthController',
    
    // Products
    'products' => 'ProductController',
    'compare' => 'CompareController',
    'search' => ['ProductController', 'search'],
    
    // Cart
    'cart' => 'CartController',
    
    // Checkout
    'checkout' => 'CheckoutController',
    
    // Orders
    'orders' => 'OrderController',
    
    // Reviews
    'reviews' => 'ReviewController',
    
    // Wishlist
    'wishlist' => 'WishlistController',
    
    // User
    'user' => 'UserController',
    
    // News
    'news' => 'NewsController',
    
    // Admin
    'admin' => 'AdminRouter',
];

/**
 * Điều phối router khu vực quản trị.
 *
 * @param array $segments Các đoạn URL sau "/admin".
 */
function dispatchAdmin($segments) {
    $section = $segments[1] ?? 'dashboard';
    $action = $segments[2] ?? 'index';
    $param = $segments[3] ?? null;
    
    switch ($section) {
        case 'dashboard':
        case '':
            $ctrl = new DashboardController();
            $ctrl->index();
            break;
        case 'notifications':
            $ctrl = new DashboardController();
            dispatchAction($ctrl, $action, $param);
            break;
        case 'products':
            $ctrl = new ProductAdminController();
            dispatchAction($ctrl, $action, $param);
            break;
        case 'categories':
            $ctrl = new CategoryController();
            dispatchAction($ctrl, $action, $param);
            break;
        case 'brands':
            $ctrl = new BrandController();
            dispatchAction($ctrl, $action, $param);
            break;
        case 'orders':
            $ctrl = new OrderAdminController();
            if (is_numeric($action)) {
                $ctrl->detail($action);
                break;
            }
            dispatchAction($ctrl, $action, $param);
            break;
        case 'users':
            $ctrl = new UserAdminController();
            if (is_numeric($action)) {
                $ctrl->detail($action);
                break;
            }
            dispatchAction($ctrl, $action, $param);
            break;
        case 'coupons':
            $ctrl = new CouponController();
            dispatchAction($ctrl, $action, $param);
            break;
        case 'inventory':
            $ctrl = new InventoryController();
            dispatchAction($ctrl, $action, $param);
            break;
        case 'banners':
            $ctrl = new BannerController();
            dispatchAction($ctrl, $action, $param);
            break;
        case 'news':
            $ctrl = new NewsAdminController();
            dispatchAction($ctrl, $action, $param);
            break;
        case 'reviews':
            $ctrl = new ReviewAdminController();
            dispatchAction($ctrl, $action, $param);
            break;
        case 'reports':
            $ctrl = new ReportController();
            dispatchAction($ctrl, $action, $param);
            break;
        case 'settings':
            $ctrl = new SettingController();
            dispatchAction($ctrl, $action, $param);
            break;
        default:
            http_response_code(404);
            echo "404 Not Found";
    }
}

/**
 * Gọi action trên controller theo phương thức HTTP, có fallback về index/404.
 *
 * @param object $ctrl  Instance controller cần gọi.
 * @param string $action Tên action (hỗ trợ kebab-case).
 * @param mixed  $param  Tham số bổ sung cho action (ví dụ id/slug).
 */
function dispatchAction($ctrl, $action, $param = null) {
    // Convert kebab-case (e.g. apply-coupon) to camelCase (e.g. applyCoupon)
    if (strpos($action, '-') !== false) {
        $action = str_replace(' ', '', lcfirst(ucwords(str_replace('-', ' ', $action))));
    }
    
    if ($param !== null && method_exists($ctrl, $action)) {
        $ctrl->$action($param);
    } elseif (method_exists($ctrl, $action)) {
        $ctrl->$action();
    } elseif (method_exists($ctrl, 'index')) {
        $ctrl->index();
    } else {
        http_response_code(404);
        echo "404 Not Found";
    }
}

// Dispatch request
try {
    if ($controller === 'admin') {
        dispatchAdmin($segments);
    } elseif ($controller === '' || $controller === 'home') {
        $ctrl = new HomeController();
        dispatchAction($ctrl, $action, $param);
    } elseif ($controller === 'auth') {
        $ctrl = new AuthController();
        dispatchAction($ctrl, $action, $param);
    } elseif ($controller === 'products') {
        $ctrl = new ProductController();
        if ($action === 'index' || $action === '') {
            $ctrl->index();
        } elseif ($action === 'search') {
            $ctrl->search();
        } else {
            // action = slug của sản phẩm
            $ctrl->detail($action);
        }
    } elseif ($controller === 'search') {
        $ctrl = new ProductController();
        $ctrl->search();
    } elseif ($controller === 'compare') {
        $ctrl = new CompareController();
        dispatchAction($ctrl, $action, $param);
    } elseif ($controller === 'cart') {
        $ctrl = new CartController();
        dispatchAction($ctrl, $action, $param);
    } elseif ($controller === 'checkout') {
        $ctrl = new CheckoutController();
        dispatchAction($ctrl, $action, $param);
    } elseif ($controller === 'orders') {
        $ctrl = new OrderController();
        if (is_numeric($action)) {
            $ctrl->detail($action);
        } else {
            dispatchAction($ctrl, $action, $param);
        }
    } elseif ($controller === 'reviews') {
        $ctrl = new ReviewController();
        dispatchAction($ctrl, $action, $param);
    } elseif ($controller === 'wishlist') {
        $ctrl = new WishlistController();
        dispatchAction($ctrl, $action, $param);
    } elseif ($controller === 'user') {
        $ctrl = new UserController();
        // Handle nested routes: user/profile/update, user/profile/password
        if ($action === 'profile' && $param === 'update') {
            $ctrl->updateProfile();
        } elseif ($action === 'profile' && $param === 'password') {
            $ctrl->changePassword();
        } else {
            dispatchAction($ctrl, $action, $param);
        }
    } elseif ($controller === 'news') {
        $ctrl = new NewsController();
        if ($action === 'index' || $action === '') {
            $ctrl->index();
        } else {
            $ctrl->detail($action);
        }
    } elseif ($controller === 'policies') {
        $ctrl = new PolicyController();
        dispatchAction($ctrl, $action, $param);
    } elseif ($controller === 'warranty' || $controller === 'tra-cuu-bao-hanh') {
        $ctrl = new WarrantyController();
        dispatchAction($ctrl, $action, $param);
    } else {
        http_response_code(404);
        include __DIR__ . '/app/Views/errors/404.php';
    }
} catch (Throwable $e) {
    error_log('Application Error: ' . $e->getMessage());
    http_response_code(500);
    if (defined('APP_ENV') && APP_ENV === 'local') {
        echo '<pre>' . htmlspecialchars($e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
    } else {
        echo 'Dịch vụ tạm thời không khả dụng. Vui lòng thử lại sau.';
    }
}
?>
