<?php
// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Tự động nhận diện môi trường để cấu hình hiển thị lỗi
$isLocal = (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false || strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false);
if ($isLocal) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    // Trên Hosting - Tắt hiển thị lỗi trực tiếp lên HTML để tránh lỗi "headers already sent"
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
}

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
require_once __DIR__ . '/app/Models/BannerModel.php';
require_once __DIR__ . '/app/Models/NewsModel.php';
require_once __DIR__ . '/app/Models/CouponModel.php';
require_once __DIR__ . '/app/Models/InventoryModel.php';
require_once __DIR__ . '/app/Models/NotificationModel.php';
require_once __DIR__ . '/app/Models/SettingModel.php';

// Load Controllers
require_once __DIR__ . '/app/Controllers/HomeController.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/ProductController.php';
require_once __DIR__ . '/app/Controllers/CartController.php';
require_once __DIR__ . '/app/Controllers/CheckoutController.php';
require_once __DIR__ . '/app/Controllers/OrderController.php';
require_once __DIR__ . '/app/Controllers/ReviewController.php';
require_once __DIR__ . '/app/Controllers/WishlistController.php';
require_once __DIR__ . '/app/Controllers/UserController.php';
require_once __DIR__ . '/app/Controllers/NewsController.php';
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
    } else {
        http_response_code(404);
        include __DIR__ . '/app/Views/errors/404.php';
    }
} catch (Exception $e) {
    error_log('Application Error: ' . $e->getMessage());
    http_response_code(500);
    echo '<h1>Đã xảy ra lỗi hệ thống</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
