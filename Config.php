<?php
/**
 * Cấu hình ứng dụng
 */

// Read optional deployment secrets from the project-root .env file.
$appEnvPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
if (is_readable($appEnvPath)) {
    foreach (file($appEnvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $envLine) {
        // FTP editors may save UTF-8 BOM or use `export NAME=value` syntax.
        $envLine = preg_replace('/^\xEF\xBB\xBF/', '', $envLine);
        $envLine = trim($envLine);
        if ($envLine === '' || $envLine[0] === '#' || strpos($envLine, '=') === false) continue;
        [$envName, $envValue] = explode('=', $envLine, 2);
        $envName = trim($envName);
        if (strpos($envName, 'export ') === 0) $envName = trim(substr($envName, 7));
        $envValue = trim($envValue);
        if ($envName === '') continue;
        $existingValue = getenv($envName);
        if ($existingValue !== false && $existingValue !== '') continue;
        if (strlen($envValue) >= 2 && (($envValue[0] === '"' && substr($envValue, -1) === '"') || ($envValue[0] === "'" && substr($envValue, -1) === "'"))) {
            $envValue = substr($envValue, 1, -1);
        }
        // Some shared hosts (including InfinityFree configurations) disable putenv().
        if (function_exists('putenv')) {
            @putenv($envName . '=' . $envValue);
        }
        $_ENV[$envName] = $envValue;
        $_SERVER[$envName] = $envValue;
    }
}

if (!function_exists('appEnv')) {
    function appEnv($name, $default = '') {
        $value = getenv($name);
        if ($value === false || $value === '') {
            $value = $_ENV[$name] ?? $_SERVER[$name] ?? $default;
        }
        return $value;
    }
}

// Tự động nhận diện môi trường (Nếu không phải localhost/127.0.0.1 thì là môi trường production)
define('APP_ENV', (PHP_SAPI !== 'cli' && strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') === false && strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') === false) ? 'production' : 'local');

if (APP_ENV === 'production') {
    // Cấu hình Database cho hosting InfinityFree MỚI (MySQL)
    define('DB_HOST', 'sql207.infinityfree.com');
    define('DB_USER', 'if0_39800571');
    define('DB_PASS', 'WteOfs2nEhHVDW');
    define('DB_NAME', 'if0_39800571_shopbandienthoai');
    define('DB_TYPE', 'mysql');
} else {
    // Cấu hình Database cho Local PC (SQL Server qua PowerShell)
    define('DB_HOST', 'LAPTOP-64QR9LTM\VUVANHUAN');
    define('DB_USER', ''); // Để trống để tự động sử dụng Windows Authentication
    define('DB_PASS', '');
    define('DB_NAME', 'webbandienthoaimoi');
    define('DB_TYPE', 'mssql_ps');
}

// Cấu hình ứng dụng
define('APP_NAME', 'Vũ Huân Shop');
define('GEMINI_API_KEY', (string)appEnv('GEMINI_API_KEY', ''));
define('GEMINI_MODEL', (string)appEnv('GEMINI_MODEL', 'gemini-3.5-flash-lite'));
// Tự động nhận diện domain và port để tránh lỗi load tài nguyên khi đổi cổng chạy (8085 -> 8000)
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
    ($_SERVER['SERVER_PORT'] ?? 80) == 443 || 
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
    (isset($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false) ||
    (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') ||
    (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == 443)
);
$protocol = $isHttps ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
define('APP_URL', $protocol . $host);
define('UPLOAD_DIR', 'public/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Cấu hình session
define('SESSION_TIMEOUT', 30 * 60); // 30 phút

// Cấu hình email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'vuhuan123890@gmail.com');
define('SMTP_PASS', 'tyrxrvhxblwwnnoj');
define('ADMIN_EMAIL', 'vuhuan123890@gmail.com');

// Cấu hình phân quyền
define('ROLE_ADMIN', 'admin');
define('ROLE_STAFF', 'staff');
define('ROLE_CUSTOMER', 'customer');
define('ROLE_GUEST', 'guest');

// Cấu hình sản phẩm
define('PRODUCTS_PER_PAGE', 15);
define('REVIEWS_PER_PAGE', 5);

// Cấu hình vận chuyển
define('SHIPPING_FEE_FREE_AMOUNT', 500000); // Miễn phí vận chuyển từ 500k
define('SHIPPING_FEE_STANDARD', 30000); // Phí vận chuyển tiêu chuẩn

// Cấu hình cổng thanh toán VNPAY (Môi trường Sandbox / Test)
// Các giá trị nhạy cảm đọc từ biến môi trường (.env), không hardcode trong repo
define('VNPAY_TMN_CODE', getenv('VNPAY_TMN_CODE') ?: '');
define('VNPAY_HASH_SECRET', getenv('VNPAY_HASH_SECRET') ?: '');
define('VNPAY_URL', getenv('VNPAY_URL') ?: 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');

// Cấu hình thông báo đơn hàng qua Telegram Bot
define('TELEGRAM_BOT_TOKEN', getenv('TELEGRAM_BOT_TOKEN') ?: '');
define('TELEGRAM_CHAT_ID', getenv('TELEGRAM_CHAT_ID') ?: '');
define('TELEGRAM_API_URL', getenv('TELEGRAM_API_URL') ?: '');

// Cấu hình đăng nhập bằng Google (Google OAuth 2.0)
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
define('GOOGLE_REDIRECT_URI', APP_URL . '/auth/google-callback');

// Cấu hình đăng nhập bằng Facebook (Facebook Graph API OAuth) - Đã tắt
define('FACEBOOK_APP_ID', '');
define('FACEBOOK_APP_SECRET', '');
define('FACEBOOK_REDIRECT_URI', APP_URL . '/auth/facebook-callback');




