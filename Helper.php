<?php
/**
 * Helper Functions - Các hàm tiện ích dùng chung
 */

/**
 * Escape HTML để chống XSS
 * @param string|null $str
 * @return string
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Lấy giá trị cài đặt từ CSDL (chạy 1 truy vấn và lưu tĩnh để tối ưu hiệu năng)
 * @param string $key
 * @param mixed $default
 * @param bool $refresh
 * @return mixed
 */
function getSetting($key, $default = '', $refresh = false) {
    static $settings = null;
    if ($settings === null || $refresh) {
        $settings = [];
        try {
            $db = new Database();
            $keyCol = (defined('DB_TYPE') && DB_TYPE === 'mysql') ? '`key`' : '[key]';
            $valueCol = (defined('DB_TYPE') && DB_TYPE === 'mysql') ? '`value`' : '[value]';
            $rows = $db->query("SELECT $keyCol as k, $valueCol as v FROM settings")->resultSet();
            if (is_array($rows)) {
                foreach ($rows as $r) {
                    $settings[strtolower($r['k'])] = $r['v'];
                }
            }
        } catch (Exception $e) {
            // Bỏ qua nếu lỗi
        }
    }
    return $settings[strtolower($key)] ?? $default;
}

/**
 * Lấy phí vận chuyển tiêu chuẩn
 * @return float
 */
function getShippingFeeStandard() {
    return (float) getSetting('shipping_fee_standard', SHIPPING_FEE_STANDARD);
}

/**
 * Lấy ngưỡng miễn phí vận chuyển
 * @return float
 */
function getShippingFeeFreeAmount() {
    return (float) getSetting('shipping_fee_free_amount', SHIPPING_FEE_FREE_AMOUNT);
}

/**
 * Format tiền VND
 * @param float|int|string|null $price
 * @return string
 */
function formatPrice($price) {
    return number_format((float)($price ?? 0), 0, ',', '.') . '₫';
}

/**
 * Tạo slug từ chuỗi tiếng Việt
 * @param string $str
 * @return string
 */
function createSlug($str) {
    $str = mb_strtolower(trim($str), 'UTF-8');
    
    $unicode = [
        'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
        'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
        'ì','í','ị','ỉ','ĩ',
        'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
        'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
        'ỳ','ý','ỵ','ỷ','ỹ',
        'đ',
        'À','Á','Ạ','Ả','Ã','Â','Ầ','Ấ','Ậ','Ẩ','Ẫ','Ă','Ằ','Ắ','Ặ','Ẳ','Ẵ',
        'È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ễ',
        'Ì','Í','Ị','Ỉ','Ĩ',
        'Ò','Ó','Ọ','Ỏ','Õ','Ô','Ồ','Ố','Ộ','Ổ','Ỗ','Ơ','Ờ','Ớ','Ợ','Ở','Ỡ',
        'Ù','Ú','Ụ','Ủ','Ũ','Ư','Ừ','Ứ','Ự','Ử','Ữ',
        'Ỳ','Ý','Ỵ','Ỷ','Ỹ',
        'Đ',
    ];
    
    $ascii = [
        'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
        'e','e','e','e','e','e','e','e','e','e','e',
        'i','i','i','i','i',
        'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
        'u','u','u','u','u','u','u','u','u','u','u',
        'y','y','y','y','y',
        'd',
        'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
        'e','e','e','e','e','e','e','e','e','e','e',
        'i','i','i','i','i',
        'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
        'u','u','u','u','u','u','u','u','u','u','u',
        'y','y','y','y','y',
        'd',
    ];
    
    $str = str_replace($unicode, $ascii, $str);
    $str = preg_replace('/[^a-z0-9\-]/', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-');
}

/**
 * Chuyển chuỗi không dấu để tìm kiếm
 * @param string $str
 * @return string
 */
function removeAccents($str) {
    $unicode = ['à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ','è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ','ì','í','ị','ỉ','ĩ','ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ','ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ','ỳ','ý','ỵ','ỷ','ỹ','đ','À','Á','Ạ','Ả','Ã','Â','Ầ','Ấ','Ậ','Ẩ','Ẫ','Ă','Ằ','Ắ','Ặ','Ẳ','Ẵ','È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ễ','Ì','Í','Ị','Ỉ','Ĩ','Ò','Ó','Ọ','Ỏ','Õ','Ô','Ồ','Ố','Ộ','Ổ','Ỗ','Ơ','Ờ','Ớ','Ợ','Ở','Ỡ','Ù','Ú','Ụ','Ủ','Ũ','Ư','Ừ','Ứ','Ự','Ử','Ữ','Ỳ','Ý','Ỵ','Ỷ','Ỹ','Đ'];
    $ascii = ['a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','e','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y','d','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','E','E','E','E','E','E','E','E','E','E','E','I','I','I','I','I','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','U','U','U','U','U','U','U','U','U','U','U','Y','Y','Y','Y','Y','D'];
    return str_replace($unicode, $ascii, $str);
}

/**
 * Format thời gian hiển thị
 * @param string|int|\DateTimeInterface|null $datetime
 * @return string
 */
function timeAgo($datetime) {
    if (empty($datetime)) return '';
    $now = new DateTime();
    $ago = ($datetime instanceof DateTimeInterface) ? $datetime : new DateTime((string)$datetime);
    $diff = $now->diff($ago);
    
    if ($diff->y > 0) return $diff->y . ' năm trước';
    if ($diff->m > 0) return $diff->m . ' tháng trước';
    if ($diff->d > 0) return $diff->d . ' ngày trước';
    if ($diff->h > 0) return $diff->h . ' giờ trước';
    if ($diff->i > 0) return $diff->i . ' phút trước';
    return 'Vừa xong';
}

/**
 * Format ngày tháng tiếng Việt
 * @param string|int|\DateTimeInterface|null $datetime
 * @param string $format
 * @return string
 */
function formatDate($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime)) return '';
    if ($datetime instanceof DateTimeInterface) {
        return $datetime->format($format);
    }
    return date($format, is_numeric($datetime) ? (int)$datetime : strtotime((string)$datetime));
}

/**
 * Render sao đánh giá
 * @param float|int $rating
 * @param int $max
 * @return string
 */
function renderStars($rating, $max = 5) {
    $html = '<div class="stars">';
    for ($i = 1; $i <= $max; $i++) {
        if ($i <= $rating) {
            $html .= '<i class="fas fa-star text-warning"></i>';
        } elseif ($i - 0.5 <= $rating) {
            $html .= '<i class="fas fa-star-half-alt text-warning"></i>';
        } else {
            $html .= '<i class="far fa-star text-warning"></i>';
        }
    }
    $html .= '</div>';
    return $html;
}

/**
 * Tạo CSRF token
 */
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        // Thử khôi phục từ cookie nếu session mất (InfinityFree hosting)
        if (!empty($_COOKIE['csrf_token'])) {
            $_SESSION['csrf_token'] = $_COOKIE['csrf_token'];
        } else {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
    // Đồng bộ cookie
    if (!headers_sent()) {
        setcookie('csrf_token', $_SESSION['csrf_token'], [
            'expires' => time() + 7200,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    return '<input type="hidden" name="csrf_token" value="' . e($_SESSION['csrf_token']) . '">';
}

/**
 * Alias cho csrfToken() dùng trong các View cũ
 */
function csrf_field() {
    return csrfToken();
}

/**
 * Kiểm tra CSRF token
 */
function verifyCsrf() {
    $postToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $cookieToken = $_COOKIE['csrf_token'] ?? '';
    $validToken = !empty($sessionToken) ? $sessionToken : $cookieToken;
    
    if (empty($postToken) || empty($validToken) || !hash_equals($validToken, $postToken)) {
        // Tạo token mới
        $newToken = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $newToken;
        if (!headers_sent()) {
            setcookie('csrf_token', $newToken, ['expires' => time() + 7200, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
        }
        setFlash('error', 'Phiên làm việc đã hết hạn. Vui lòng thử lại.');
        $referer = $_SERVER['HTTP_REFERER'] ?? (defined('APP_URL') ? APP_URL : '/');
        header('Location: ' . $referer);
        exit;
    }
}

/**
 * Upload hình ảnh
 * @param array $file
 * @param string $folder
 * @return array
 */
function uploadImage($file, $folder = 'products') {
    if (!preg_match('/\A[a-zA-Z0-9_-]+\z/', $folder)
        || !isset($file['error'], $file['size'], $file['tmp_name'])
        || $file['error'] !== UPLOAD_ERR_OK
        || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'Tệp tải lên không hợp lệ'];
    }

    $uploadDir = __DIR__ . '/../../public/uploads/' . $folder . '/';
    
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            return ['success' => false, 'error' => 'Không thể tạo thư mục tải ảnh lên'];
        }
    }
    
    if ((int)$file['size'] < 1 || (int)$file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'File quá lớn (tối đa 5MB) hoặc rỗng'];
    }

    $imageInfo = @getimagesize($file['tmp_name']);
    $mimeType = $imageInfo['mime'] ?? '';
    $extensionsByMime = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    if (!$imageInfo || !isset($extensionsByMime[$mimeType])) {
        return ['success' => false, 'error' => 'Chỉ hỗ trợ JPEG, PNG, GIF, WebP'];
    }
    
    $filename = bin2hex(random_bytes(16)) . '.' . $extensionsByMime[$mimeType];
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => 'uploads/' . $folder . '/' . $filename];
    }
    
    return ['success' => false, 'error' => 'Lỗi upload file'];
}

/**
 * Lấy ảnh sản phẩm với fallback
 * @param string|null $image
 * @param string $size
 * @return string
 */
function productImage($image, $size = '') {
    if (empty($image)) {
        return url('public/images/no-image.png');
    }
    
    if (strpos($image, 'http') === 0) {
        return $image;
    }
    
    // Auto-prepend uploads/products/ for filenames that do not contain a path separator
    if (strpos($image, '/') === false && strpos($image, '\\') === false) {
        $image = 'uploads/products/' . $image;
    }
    
    return url('public/' . ltrim($image, '/'));
}

/**
 * Tính phần trăm giảm giá
 * @param float|int $originalPrice
 * @param float|int $salePrice
 * @return int
 */
function discountPercent($originalPrice, $salePrice) {
    if ($originalPrice <= 0) return 0;
    return (int) round(($originalPrice - $salePrice) / $originalPrice * 100);
}

/**
 * Kiểm tra đăng nhập
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Lấy user hiện tại
 * @return array|null
 */
function currentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Kiểm tra role
 * @param string $role
 * @return bool
 */
function hasRole($role) {
    return ($_SESSION['role'] ?? 'guest') === $role;
}

/**
 * Kiểm tra admin
 * @return bool
 */
function isAdmin() {
    return hasRole('admin');
}

/**
 * Kiểm tra staff
 * @return bool
 */
function isStaff() {
    return in_array($_SESSION['role'] ?? 'guest', ['admin', 'staff'], true);
}

/**
 * Lấy danh sách ID sản phẩm yêu thích của user hiện tại (cache tĩnh theo request)
 * @return array
 */
function getUserWishlistProductIds() {
    static $wishlistIds = null;
    if ($wishlistIds === null) {
        $wishlistIds = [];
        if (isLoggedIn()) {
            $userId = $_SESSION['user_id'] ?? null;
            if ($userId) {
                try {
                    $db = new Database();
                    $rows = $db->query("SELECT product_id FROM wishlists WHERE user_id = :uid")
                        ->bind(':uid', $userId)->resultSet();
                    if (is_array($rows)) {
                        $wishlistIds = array_map('intval', array_column($rows, 'product_id'));
                    }
                } catch (Exception $e) {
                    $wishlistIds = [];
                }
            }
        }
    }
    return $wishlistIds;
}

/**
 * Kiểm tra xem một sản phẩm có nằm trong danh sách yêu thích không
 * @param int|string $productId
 * @return bool
 */
function isProductWishlisted($productId) {
    if (!isLoggedIn() || empty($productId)) return false;
    $ids = getUserWishlistProductIds();
    return in_array((int)$productId, $ids, true);
}

/**
 * Redirect với flash message
 * @param string $url
 * @return void
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Lưu flash message
 * @param string $type
 * @param string $message
 * @return void
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Lấy và xóa flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Hiển thị flash message
 */
/**
 * Hiển thị flash message theo phong cách Toast hiện đại, tự động biến mất sau 4 giây
 */
function showFlash() {
    $flash = getFlash();
    if ($flash) {
        $type = $flash['type'] ?? 'info';
        $message = htmlspecialchars($flash['message'] ?? '', ENT_QUOTES, 'UTF-8');
        
        $configs = [
            'success' => [
                'bg' => '#f0fdf4',
                'border' => '#86efac',
                'color' => '#166534',
                'icon_bg' => '#22c55e',
                'icon' => 'fas fa-check',
                'bar_color' => '#22c55e',
            ],
            'error' => [
                'bg' => '#fef2f2',
                'border' => '#fca5a5',
                'color' => '#991b1b',
                'icon_bg' => '#ef4444',
                'icon' => 'fas fa-exclamation',
                'bar_color' => '#ef4444',
            ],
            'warning' => [
                'bg' => '#fffbeb',
                'border' => '#fde68a',
                'color' => '#92400e',
                'icon_bg' => '#f59e0b',
                'icon' => 'fas fa-exclamation-triangle',
                'bar_color' => '#f59e0b',
            ],
            'info' => [
                'bg' => '#f0f9ff',
                'border' => '#bae6fd',
                'color' => '#075985',
                'icon_bg' => '#0ea5e9',
                'icon' => 'fas fa-info',
                'bar_color' => '#0ea5e9',
            ],
        ];
        
        $cfg = $configs[$type] ?? $configs['info'];
        $toastId = 'flash-toast-' . mt_rand(1000, 9999);
        
        echo <<<HTML
<div id="{$toastId}" class="custom-flash-toast" style="
    position: relative;
    background: {$cfg['bg']};
    border: 1.5px solid {$cfg['border']};
    border-radius: 14px;
    padding: 14px 18px;
    margin-bottom: 20px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06), 0 4px 10px -2px rgba(0,0,0,0.03);
    display: flex;
    align-items: center;
    gap: 14px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    animation: flashSlideDown 0.35s ease-out;
">
    <div style="
        width: 34px; height: 34px;
        border-radius: 50%;
        background: {$cfg['icon_bg']};
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
        box-shadow: 0 4px 10px {$cfg['border']};
    ">
        <i class="{$cfg['icon']}"></i>
    </div>
    
    <div style="flex: 1; min-width: 0;">
        <div style="font-size: 0.92rem; font-weight: 600; color: {$cfg['color']}; line-height: 1.45;">
            {$message}
        </div>
    </div>
    
    <button type="button" onclick="dismissFlashToast('{$toastId}')" style="
        background: transparent;
        border: none;
        color: {$cfg['color']};
        opacity: 0.6;
        cursor: pointer;
        padding: 6px;
        border-radius: 8px;
        line-height: 1;
        transition: opacity 0.2s, background 0.2s;
        font-size: 1.1rem;
    " onmouseover="this.style.opacity='1'; this.style.background='rgba(0,0,0,0.05)';" onmouseout="this.style.opacity='0.6'; this.style.background='transparent';" title="Đóng thông báo">
        <i class="fas fa-times"></i>
    </button>
    
    <!-- Progress Bar Countdown -->
    <div id="{$toastId}-progress" style="
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3.5px;
        width: 100%;
        background: {$cfg['bar_color']};
        opacity: 0.8;
        transform-origin: left;
        animation: flashCountdown 4.5s linear forwards;
    "></div>
</div>

<style>
@keyframes flashSlideDown {
    from { opacity: 0; transform: translateY(-12px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes flashCountdown {
    from { width: 100%; }
    to { width: 0%; }
}
</style>

<script>
(function() {
    const el = document.getElementById('{$toastId}');
    if (!el) return;
    
    let timer = setTimeout(() => {
        dismissFlashToast('{$toastId}');
    }, 4500);
    
    el.addEventListener('mouseenter', () => {
        clearTimeout(timer);
        const pb = document.getElementById('{$toastId}-progress');
        if (pb) pb.style.animationPlayState = 'paused';
    });
    
    el.addEventListener('mouseleave', () => {
        const pb = document.getElementById('{$toastId}-progress');
        if (pb) pb.style.animationPlayState = 'running';
        timer = setTimeout(() => {
            dismissFlashToast('{$toastId}');
        }, 2000);
    });
})();

if (typeof window.dismissFlashToast === 'undefined') {
    window.dismissFlashToast = function(id) {
        const toast = document.getElementById(id);
        if (!toast) return;
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-14px) scale(0.98)';
        setTimeout(() => {
            toast.style.maxHeight = '0';
            toast.style.padding = '0';
            toast.style.margin = '0';
            toast.style.overflow = 'hidden';
            setTimeout(() => toast.remove(), 300);
        }, 200);
    };
}
</script>
HTML;
    }
}

/**
 * Tạo pagination HTML
 * @param int $currentPage
 * @param int $totalPages
 * @param string $baseUrl
 * @return string
 */
function pagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) return '';
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage - 1) . '"><i class="fas fa-chevron-left"></i></a></li>';
    }
    
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=1">1</a></li>';
        if ($start > 2) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $active = ($i === $currentPage) ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
    }
    
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage + 1) . '"><i class="fas fa-chevron-right"></i></a></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}

/**
 * Truncate text
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Lấy số lượng giỏ hàng từ session
 * @return int
 */
function getCartCount() {
    return (int) ($_SESSION['cart_count'] ?? 0);
}

/**
 * Làm sạch input
 * @param mixed $input
 * @return string
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim((string)$input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 * @param string $email
 * @return mixed
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone
 * @param string $phone
 * @return int|false
 */
function validatePhone($phone) {
    return preg_match('/^(0|\+84)[3-9][0-9]{8}$/', $phone);
}

/**
 * Tạo mã đơn hàng
 * @return string
 */
function generateOrderNumber() {
    return 'ORD' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));
}

/**
 * URL hiện tại
 * @return string
 */
function currentUrl() {
    return APP_URL . ($_SERVER['REQUEST_URI'] ?? '/');
}

/**
 * Asset URL
 * @param string $path
 * @return string
 */
function asset($path) {
    return APP_URL . '/public/' . ltrim($path, '/');
}

/**
 * App URL
 * @param string $path
 * @return string
 */
function url($path = '') {
    $base = APP_URL;
    $isReqHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        ($_SERVER['SERVER_PORT'] ?? 80) == 443 ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
        (isset($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false) ||
        (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
    );
    if ($isReqHttps && strpos($base, 'http://') === 0) {
        $base = 'https://' . substr($base, 7);
    }
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

/**
 * Lấy Google Redirect URI chuẩn xác (tự động chuẩn hóa HTTPS cho domain production)
 * @return string
 */
function getGoogleRedirectUri() {
    $uri = url('auth/google-callback');
    // Google OAuth không cho phép HTTP đối với domain thật (chỉ localhost mới được dùng http)
    if (strpos($uri, 'localhost') === false && strpos($uri, '127.0.0.1') === false) {
        if (strpos($uri, 'http://') === 0) {
            $uri = 'https://' . substr($uri, 7);
        }
    }
    return $uri;
}

/**
 * Lấy Facebook Redirect URI chuẩn xác
 * @return string
 */
function getFacebookRedirectUri() {
    $uri = url('auth/facebook-callback');
    if (strpos($uri, 'localhost') === false && strpos($uri, '127.0.0.1') === false) {
        if (strpos($uri, 'http://') === 0) {
            $uri = 'https://' . substr($uri, 7);
        }
    }
    return $uri;
}

/**
 * Ghép nối đầy đủ thông tin địa chỉ đơn hàng: Số nhà/đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố
 * @param array|object|string $order
 * @return string
 */
function formatOrderAddress($order) {
    if (empty($order)) return 'Tại cửa hàng';
    if (is_string($order)) return $order;
    if (is_object($order)) $order = (array)$order;

    $address = trim($order['ship_address'] ?? '');
    $ward = trim($order['ship_ward'] ?? '');
    $district = trim($order['ship_district'] ?? '');
    $city = trim($order['ship_city'] ?? '');

    $parts = [];
    if (!empty($address)) $parts[] = $address;
    if (!empty($ward) && stripos($address, $ward) === false) $parts[] = $ward;
    if (!empty($district) && stripos($address, $district) === false) $parts[] = $district;
    if (!empty($city) && stripos($address, $city) === false) $parts[] = $city;

    return !empty($parts) ? implode(', ', $parts) : 'Tại cửa hàng';
}

/**
 * Kiem tra route hien tai de highlight menu.
 * @param string $path
 * @return bool
 */
function isActiveRoute($path) {
    $current = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
    $target = trim(parse_url(url($path), PHP_URL_PATH), '/');

    return $current === $target || strpos($current, $target . '/') === 0;
}

/**
 * Chuyen doi trang thai don hang sang tieng Viet
 * @param string $status
 * @return string
 */
function orderStatusLabel($status) {
    $labels = [
        'pending' => 'Chờ xử lý',
        'confirmed' => 'Đã xác nhận',
        'processing' => 'Đang xử lý',
        'shipping' => 'Đang giao hàng',
        'delivered' => 'Đã giao hàng',
        'cancelled' => 'Đã hủy',
    ];
    return $labels[$status] ?? 'Không xác định';
}

/**
 * Gửi email HTML qua máy chủ SMTP đã cấu hình.
 * @param string $to
 * @param string $subject
 * @param string $html
 * @return bool
 */
function sendAppEmail($to, $subject, $html) {
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        error_log('Email delivery skipped because the recipient address is invalid.');
        return false;
    }

    if (SMTP_USER === '' || SMTP_PASS === '') {
        error_log('Email delivery failed: configure SMTP_USER and SMTP_PASS environment variables.');
        return false;
    }

    require_once __DIR__ . '/../../PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../../PHPMailer/src/SMTP.php';
    require_once __DIR__ . '/../../PHPMailer/src/Exception.php';

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port = SMTP_PORT;
        $mail->Timeout = 15;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(SMTP_USER, APP_NAME);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;

        return $mail->send();
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('Email delivery failed: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Gửi liên kết đặt lại mật khẩu có thời hạn cho người dùng.
 * @param array $user
 * @param string $token
 * @return bool
 */
function sendPasswordResetEmail($user, $token) {
    $resetUrl = url('auth/reset-password') . '?token=' . rawurlencode($token);
    $name = e($user['full_name'] ?? '');
    $link = e($resetUrl);
    $html = '<!doctype html><html lang="vi"><body style="margin:0;background:#f5f6f8;font-family:Arial,sans-serif;color:#263238">'
        . '<div style="max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden">'
        . '<div style="padding:28px;background:#c62828;color:#fff;text-align:center"><h1 style="margin:0;font-size:24px">' . e(APP_NAME) . '</h1></div>'
        . '<div style="padding:32px"><h2 style="margin-top:0">Đặt lại mật khẩu</h2>'
        . '<p>Xin chào ' . $name . ', chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>'
        . '<p style="text-align:center;margin:28px 0"><a href="' . $link . '" style="display:inline-block;padding:14px 24px;background:#c62828;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold">Đặt lại mật khẩu</a></p>'
        . '<p>Liên kết có hiệu lực trong <strong>15 phút</strong> và chỉ sử dụng được một lần. Nếu bạn không yêu cầu thay đổi này, hãy bỏ qua email.</p>'
        . '<p style="font-size:13px;color:#687078">Nếu nút không hoạt động, hãy sao chép liên kết sau vào trình duyệt:<br><a href="' . $link . '">' . $link . '</a></p>'
        . '</div><div style="padding:18px 32px;background:#f5f6f8;font-size:12px;color:#687078">Email tự động từ ' . e(APP_NAME) . '. Vui lòng không trả lời email này.</div>'
        . '</div></body></html>';

    return sendAppEmail($user['email'], 'Đặt lại mật khẩu - ' . APP_NAME, $html);
}

/**
 * Gửi email hóa đơn xác nhận đơn hàng cho khách hàng và admin.
 * @param int|string $orderId
 * @return bool
 */
function sendOrderInvoiceEmail($orderId) {
    $orderModel = new OrderModel();
    $order = $orderModel->findById($orderId);
    if (!$order) {
        error_log('Order email delivery failed: order not found.');
        return false;
    }
    
    $orderDetails = $orderModel->getDetails($orderId);

    // Render HTML template using output buffering
    ob_start();
    require __DIR__ . '/../Views/emails/invoice.php';
    $invoiceHTML = ob_get_clean();

    $customerEmail = trim((string) ($order['ship_email'] ?? ''));
    if ($customerEmail === '') {
        $customerEmail = $order['customer_email'] ?? '';
    }
    $customerSent = sendAppEmail(
        $customerEmail,
        'Xác nhận đơn hàng #' . $order['order_number'] . ' - ' . APP_NAME,
        $invoiceHTML
    );

    if (ADMIN_EMAIL !== '' && ADMIN_EMAIL !== $customerEmail) {
        sendAppEmail(
            ADMIN_EMAIL,
            'Đơn hàng mới #' . $order['order_number'] . ' - ' . APP_NAME,
            $invoiceHTML
        );
    }

    return $customerSent;
}

/**
 * Gửi thông báo đến Telegram Bot
 *
 * @param string $message Nội dung tin nhắn (hỗ trợ HTML)
 * @param string|null $customToken Token bot
 * @param string|null $customChatId Chat ID hoặc Group ID
 * @param string|null $customApiUrl URL API / Proxy Telegram
 * @return array ['success' => bool, 'message' => string]
 */
function sendTelegramNotification($message, $customToken = null, $customChatId = null, $customApiUrl = null) {
    $token = $customToken ?: trim((string)getSetting('telegram_bot_token', defined('TELEGRAM_BOT_TOKEN') ? TELEGRAM_BOT_TOKEN : ''));
    $chatId = $customChatId ?: trim((string)getSetting('telegram_chat_id', defined('TELEGRAM_CHAT_ID') ? TELEGRAM_CHAT_ID : ''));
    
    $apiUrl = $customApiUrl ?: trim((string)getSetting('telegram_api_url', defined('TELEGRAM_API_URL') ? TELEGRAM_API_URL : 'https://telegram-proxy.vuhuan123890.workers.dev'));
    if (empty($apiUrl)) {
        $apiUrl = 'https://telegram-proxy.vuhuan123890.workers.dev';
    }
    $apiUrl = rtrim($apiUrl, '/');
    // Loại bỏ đuôi /bot nếu người dùng lỡ nhập thừa
    $apiUrl = preg_replace('#/bot/?$#i', '', $apiUrl);

    if (empty($token) || empty($chatId)) {
        return ['success' => false, 'message' => 'Chưa cấu hình Telegram Bot Token hoặc Chat ID'];
    }

    $url = "{$apiUrl}/bot{$token}/sendMessage";
    $payload = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true,
    ];

    try {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) VuHuanShop/1.0');
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            // Bỏ qua lỗi DNS "Could not resolve host" trên hosting InfinityFree bằng cách gán sẵn IP Cloudflare
            $host = parse_url($url, PHP_URL_HOST);
            if (!empty($host) && strpos($host, 'workers.dev') !== false) {
                curl_setopt($ch, CURLOPT_RESOLVE, [
                    "{$host}:443:104.21.83.233",
                    "{$host}:443:172.67.182.224",
                    "{$host}:80:104.21.83.233",
                    "{$host}:80:172.67.182.224"
                ]);
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            if (PHP_VERSION_ID < 80000 && is_resource($ch)) {
                @call_user_func('curl_close', $ch);
            }
            unset($ch);

            if ($err) {
                if (stripos($err, 'Could not resolve host') !== false && stripos($apiUrl, 'api.telegram.org') !== false) {
                    return [
                        'success' => false,
                        'message' => 'Máy chủ hosting InfinityFree (gói miễn phí) chặn phân giải DNS/kết nối ra ngoài tới Telegram (api.telegram.org). Hãy điền link Proxy Cloudflare Worker để gửi thông báo!'
                    ];
                }
                return ['success' => false, 'message' => "Lỗi kết nối tới {$apiUrl}: " . $err];
            }

            $resData = json_decode($response, true);
            if ($httpCode === 200 && !empty($resData['ok'])) {
                return ['success' => true, 'message' => 'Gửi tin nhắn Telegram thành công!'];
            }
            return ['success' => false, 'message' => 'Telegram báo lỗi: ' . ($resData['description'] ?? 'Mã phản hồi HTTP ' . $httpCode)];
        } else {
            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\nUser-Agent: VuHuanShop/1.0\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($payload),
                    'timeout' => 10
                ]
            ];
            $context  = stream_context_create($options);
            $result = @file_get_contents($url, false, $context);
            if ($result !== false) {
                return ['success' => true, 'message' => 'Gửi tin nhắn Telegram thành công!'];
            }
            return ['success' => false, 'message' => "Không thể kết nối tới {$apiUrl} (file_get_contents failed)"];
        }
    } catch (\Throwable $e) {
        return ['success' => false, 'message' => 'Lỗi ngoại lệ: ' . $e->getMessage()];
    }
}

/**
 * Lấy danh sách màu sắc & dung lượng chuẩn xác theo từng dòng sản phẩm
 * Tham khảo thực tế từ FPT Shop, Thế Giới Di Động (TGDD), Apple, Samsung, Xiaomi...
 *
 * @param array $product
 * @return array ['capacities' => [...], 'colors' => [...]]
 */
function getProductRealVariants($product) {
    // 1. Nếu sản phẩm đã được quản trị viên cài đặt variants_json riêng trong DB, ưu tiên dùng
    if (!empty($product['variants_json'])) {
        $data = json_decode($product['variants_json'], true);
        if (is_array($data) && (!empty($data['capacities']) || !empty($data['colors']))) {
            return $data;
        }
    }

    $name = mb_strtolower($product['name'] ?? '', 'UTF-8');
    $brandSlug = mb_strtolower($product['brand_slug'] ?? $product['brand_name'] ?? '', 'UTF-8');
    $catSlug = mb_strtolower($product['category_slug'] ?? $product['category_name'] ?? '', 'UTF-8');

    $capacities = [];
    $colors = [];

    // --- IPHONE 18 / 17 / DUO SERIES (CÁC DÒNG MỚI CONCEPT/ĐẶC BIỆT) ---
    if (strpos($name, '18 pro') !== false || strpos($name, '17 pro') !== false || strpos($name, '18') !== false || strpos($name, '17') !== false || strpos($name, 'duo') !== false) {
        $capacities = [
            ['label' => '128GB', 'diff' => 0],
            ['label' => '256GB', 'diff' => 2000000],
            ['label' => '512GB', 'diff' => 6000000],
            ['label' => '1TB', 'diff' => 11000000],
        ];
        $colors = [
            [
                'name' => 'Đen (Black)',
                'hex' => '#212326',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-blacktitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Bạc (Silver)',
                'hex' => '#e2e4e8',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-whitetitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Sa Mạc',
                'hex' => '#c7b097',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-deserttitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Xanh (Blue)',
                'hex' => '#2c353f',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-bluetitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
        ];
    }
    // --- IPHONE 16 SERIES ---
    elseif (strpos($name, '16 pro max') !== false) {
        $capacities = [
            ['label' => '256GB', 'diff' => 0],
            ['label' => '512GB', 'diff' => 5000000],
            ['label' => '1TB', 'diff' => 11000000],
        ];
        $colors = [
            [
                'name' => 'Titan Sa Mạc',
                'hex' => '#c7b097',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-deserttitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Tự Nhiên',
                'hex' => '#9e9a93',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-naturaltitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Trắng',
                'hex' => '#f2f2f0',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-whitetitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Đen',
                'hex' => '#2a2b2d',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-blacktitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
        ];
    } elseif (strpos($name, '16 pro') !== false) {
        $capacities = [
            ['label' => '128GB', 'diff' => 0],
            ['label' => '256GB', 'diff' => 3000000],
            ['label' => '512GB', 'diff' => 8000000],
            ['label' => '1TB', 'diff' => 13000000],
        ];
        $colors = [
            [
                'name' => 'Titan Sa Mạc',
                'hex' => '#c7b097',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-deserttitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Tự Nhiên',
                'hex' => '#9e9a93',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-naturaltitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Trắng',
                'hex' => '#f2f2f0',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-whitetitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Đen',
                'hex' => '#2a2b2d',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-blacktitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
        ];
    } elseif (strpos($name, 'iphone 16') !== false || strpos($name, '16 plus') !== false) {
        $capacities = [
            ['label' => '128GB', 'diff' => 0],
            ['label' => '256GB', 'diff' => 3000000],
            ['label' => '512GB', 'diff' => 8500000],
        ];
        $colors = [
            [
                'name' => 'Đen',
                'hex' => '#2d2f31',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-black?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Trắng',
                'hex' => '#f4f4f4',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-white?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Hồng',
                'hex' => '#f6d2d7',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-pink?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Xanh Mòng Két',
                'hex' => '#a0c8c3',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-teal?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Xanh Lưu Ly',
                'hex' => '#8294bd',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-ultramarine?wid=940&hei=1112&fmt=png-alpha'
            ],
        ];
    }
    // --- IPHONE 15 SERIES ---
    elseif (strpos($name, '15 pro max') !== false) {
        $capacities = [
            ['label' => '256GB', 'diff' => 0],
            ['label' => '512GB', 'diff' => 5000000],
            ['label' => '1TB', 'diff' => 10000000],
        ];
        $colors = [
            [
                'name' => 'Titan Tự Nhiên',
                'hex' => '#9e9a93',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-naturaltitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Xanh',
                'hex' => '#2c353f',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-bluetitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Trắng',
                'hex' => '#f2f2f0',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-whitetitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Đen',
                'hex' => '#2a2b2d',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-blacktitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
        ];
    } elseif (strpos($name, '15 pro') !== false) {
        $capacities = [
            ['label' => '128GB', 'diff' => 0],
            ['label' => '256GB', 'diff' => 3000000],
            ['label' => '512GB', 'diff' => 8000000],
            ['label' => '1TB', 'diff' => 13000000],
        ];
        $colors = [
            [
                'name' => 'Titan Tự Nhiên',
                'hex' => '#9e9a93',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-naturaltitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Xanh',
                'hex' => '#2c353f',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-bluetitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Trắng',
                'hex' => '#f2f2f0',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-whitetitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Titan Đen',
                'hex' => '#2a2b2d',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-blacktitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
        ];
    } elseif (strpos($name, 'iphone 15') !== false || strpos($name, '15 plus') !== false) {
        $capacities = [
            ['label' => '128GB', 'diff' => 0],
            ['label' => '256GB', 'diff' => 3000000],
            ['label' => '512GB', 'diff' => 8000000],
        ];
        $colors = [
            [
                'name' => 'Hồng (Pink)',
                'hex' => '#fae1dc',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-pink?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Xanh Dương',
                'hex' => '#d1e1e9',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-ultramarine?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Xanh Lá',
                'hex' => '#d6e6d5',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-teal?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Vàng (Yellow)',
                'hex' => '#fdf0be',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-deserttitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Đen (Black)',
                'hex' => '#33373b',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-black?wid=940&hei=1112&fmt=png-alpha'
            ],
        ];
    }
    // --- IPHONE 14, 13, 12, 11 ---
    elseif (strpos($name, 'iphone 14') !== false || strpos($name, 'iphone 13') !== false || strpos($name, 'iphone 11') !== false || strpos($name, 'iphone 12') !== false) {
        $capacities = [
            ['label' => '128GB', 'diff' => 0],
            ['label' => '256GB', 'diff' => 2500000],
            ['label' => '512GB', 'diff' => 6000000],
        ];
        $colors = [
            ['name' => 'Đen Midnight', 'hex' => '#1f242d'],
            ['name' => 'Trắng Starlight', 'hex' => '#f7f6f2'],
            ['name' => 'Xanh Blue', 'hex' => '#8ba3b8'],
            ['name' => 'Tím Purple', 'hex' => '#d7d0de'],
            ['name' => 'Đỏ (PRODUCT)RED', 'hex' => '#bf2130'],
        ];
    }
    // --- SAMSUNG GALAXY S24 ULTRA ---
    elseif (strpos($name, 's24 ultra') !== false) {
        $capacities = [
            ['label' => '256GB', 'diff' => 0],
            ['label' => '512GB', 'diff' => 4500000],
            ['label' => '1TB', 'diff' => 10000000],
        ];
        $colors = [
            ['name' => 'Xám Titan', 'hex' => '#7c7e80'],
            ['name' => 'Đen Titan', 'hex' => '#2a2b2d'],
            ['name' => 'Tím Titan', 'hex' => '#4a4453'],
            ['name' => 'Vàng Titan', 'hex' => '#e8dec9'],
        ];
    }
    // --- SAMSUNG GALAXY S24 / S24+ / S23 ---
    elseif (strpos($name, 's24') !== false || strpos($name, 's23') !== false) {
        $capacities = [
            ['label' => '256GB', 'diff' => 0],
            ['label' => '512GB', 'diff' => 3500000],
        ];
        $colors = [
            ['name' => 'Đen Onyx', 'hex' => '#232528'],
            ['name' => 'Xám Marble', 'hex' => '#8e9094'],
            ['name' => 'Tím Cobalt', 'hex' => '#4a4857'],
            ['name' => 'Vàng Amber', 'hex' => '#e5dcbe'],
        ];
    }
    // --- SAMSUNG GALAXY Z FOLD ---
    elseif (strpos($name, 'fold') !== false) {
        $capacities = [
            ['label' => '256GB', 'diff' => 0],
            ['label' => '512GB', 'diff' => 4000000],
            ['label' => '1TB', 'diff' => 9000000],
        ];
        $colors = [
            ['name' => 'Xám Bạc', 'hex' => '#c2c5c9'],
            ['name' => 'Xanh Navy', 'hex' => '#1b2a47'],
            ['name' => 'Hồng Rose', 'hex' => '#e8c9c6'],
        ];
    }
    // --- SAMSUNG GALAXY Z FLIP ---
    elseif (strpos($name, 'flip') !== false) {
        $capacities = [
            ['label' => '256GB', 'diff' => 0],
            ['label' => '512GB', 'diff' => 3500000],
        ];
        $colors = [
            ['name' => 'Xanh Mint', 'hex' => '#bfe0cf'],
            ['name' => 'Xám Metal', 'hex' => '#7b7d82'],
            ['name' => 'Vàng Solar', 'hex' => '#fae5a2'],
            ['name' => 'Xanh Maya', 'hex' => '#96bfde'],
        ];
    }
    // --- SAMSUNG GALAXY A SERIES ---
    elseif (strpos($brandSlug, 'samsung') !== false && (strpos($name, 'galaxy a') !== false || preg_match('/\ba\d{2}\b/i', $name))) {
        $capacities = [
            ['label' => '128GB', 'diff' => 0],
            ['label' => '256GB', 'diff' => 1000000],
        ];
        $colors = [
            ['name' => 'Xanh Iceblue', 'hex' => '#b0d3e8'],
            ['name' => 'Xanh Navy', 'hex' => '#1e293b'],
            ['name' => 'Vàng Lemon', 'hex' => '#f6e99d'],
            ['name' => 'Tím Lilac', 'hex' => '#d3c5e5'],
        ];
    }
    // --- XIAOMI 14 / XIAOMI 13 ---
    elseif (strpos($name, 'xiaomi 14') !== false || strpos($name, 'xiaomi 13') !== false) {
        $capacities = [
            ['label' => '256GB', 'diff' => 0],
            ['label' => '512GB', 'diff' => 3000000],
        ];
        $colors = [
            ['name' => 'Đen Black', 'hex' => '#1f2022'],
            ['name' => 'Trắng White', 'hex' => '#f5f5f5'],
            ['name' => 'Xanh Ngọc Jade', 'hex' => '#7d9987'],
        ];
    }
    // --- XIAOMI REDMI / POCO ---
    elseif (strpos($name, 'poco') !== false || strpos($name, 'redmi') !== false) {
        $capacities = [
            ['label' => '128GB', 'diff' => 0],
            ['label' => '256GB', 'diff' => 800000],
        ];
        $colors = [
            ['name' => 'Đen Dạ Quang', 'hex' => '#1f2022'],
            ['name' => 'Xanh Băng Tuyết', 'hex' => '#98c5d9'],
            ['name' => 'Bạc Ánh Sao', 'hex' => '#e2e4e8'],
        ];
    }
    // --- OPPO ---
    elseif (strpos($name, 'reno') !== false || strpos($brandSlug, 'oppo') !== false) {
        $capacities = [
            ['label' => '256GB', 'diff' => 0],
            ['label' => '512GB', 'diff' => 2000000],
        ];
        $colors = [
            ['name' => 'Cam Hoàng Hôn', 'hex' => '#e67e50'],
            ['name' => 'Xanh Ô Liu', 'hex' => '#6b7a5e'],
            ['name' => 'Bạc Vũ Trụ', 'hex' => '#d9dce1'],
        ];
    }
    // --- HONOR ---
    elseif (strpos($brandSlug, 'honor') !== false || strpos($name, 'honor') !== false) {
        $capacities = [
            ['label' => '256GB', 'diff' => 0],
            ['label' => '512GB', 'diff' => 2000000],
        ];
        $colors = [
            ['name' => 'Đen Midnight', 'hex' => '#1a1c20'],
            ['name' => 'Bạc Ánh Kim', 'hex' => '#dedfe3'],
            ['name' => 'Xanh Emerald', 'hex' => '#326b56'],
        ];
    }
    // --- MACBOOK PRO ---
    elseif (strpos($name, 'macbook pro') !== false) {
        $capacities = [
            ['label' => '8GB - 512GB', 'diff' => 0],
            ['label' => '16GB - 512GB', 'diff' => 5000000],
            ['label' => '16GB - 1TB', 'diff' => 10000000],
        ];
        $colors = [
            ['name' => 'Đen Space Black', 'hex' => '#27282b'],
            ['name' => 'Xám Space Gray', 'hex' => '#4e4f54'],
            ['name' => 'Bạc Silver', 'hex' => '#e3e4e6'],
        ];
    }
    // --- MACBOOK AIR ---
    elseif (strpos($name, 'macbook air') !== false || strpos($name, 'macbook') !== false) {
        $capacities = [
            ['label' => '8GB - 256GB', 'diff' => 0],
            ['label' => '16GB - 256GB', 'diff' => 4500000],
            ['label' => '16GB - 512GB', 'diff' => 9000000],
        ];
        $colors = [
            ['name' => 'Midnight (Đêm Xanh)', 'hex' => '#1c2430'],
            ['name' => 'Starlight (Ánh Sao)', 'hex' => '#f0e9dc'],
            ['name' => 'Space Gray (Xám)', 'hex' => '#4e4f54'],
            ['name' => 'Silver (Bạc)', 'hex' => '#e3e4e6'],
        ];
    }
    // --- DELL ---
    elseif (strpos($brandSlug, 'dell') !== false || strpos($name, 'dell') !== false) {
        $capacities = [
            ['label' => '16GB - 512GB', 'diff' => 0],
            ['label' => '32GB - 1TB', 'diff' => 6000000],
        ];
        $colors = [
            ['name' => 'Bạch Kim (Platinum)', 'hex' => '#e6e7e9'],
            ['name' => 'Than Chì (Graphite)', 'hex' => '#3b3e43'],
        ];
    }
    // --- ASUS ---
    elseif (strpos($brandSlug, 'asus') !== false || strpos($name, 'asus') !== false) {
        $capacities = [
            ['label' => '16GB - 512GB', 'diff' => 0],
            ['label' => '16GB - 1TB', 'diff' => 2500000],
        ];
        $colors = [
            ['name' => 'Xám Mecha', 'hex' => '#3a3d42'],
            ['name' => 'Đen Jaeger', 'hex' => '#212326'],
        ];
    }
    // --- HP ---
    elseif (strpos($brandSlug, 'hp') !== false || strpos($name, 'hp') !== false) {
        $capacities = [
            ['label' => '8GB - 512GB', 'diff' => 0],
            ['label' => '16GB - 512GB', 'diff' => 1800000],
        ];
        $colors = [
            ['name' => 'Bạc Tự Nhiên', 'hex' => '#dcdde0'],
            ['name' => 'Đen Nhám', 'hex' => '#22252a'],
        ];
    }
    // --- LENOVO ---
    elseif (strpos($brandSlug, 'lenovo') !== false || strpos($name, 'lenovo') !== false) {
        $capacities = [
            ['label' => '16GB - 512GB', 'diff' => 0],
            ['label' => '16GB - 1TB', 'diff' => 2200000],
        ];
        $colors = [
            ['name' => 'Xám Bắc Cực', 'hex' => '#8e9399'],
            ['name' => 'Xanh Đậm', 'hex' => '#222938'],
        ];
    }
    // --- IPAD ---
    elseif (strpos($name, 'ipad') !== false) {
        $capacities = [
            ['label' => '128GB', 'diff' => 0],
            ['label' => '256GB', 'diff' => 3000000],
            ['label' => '512GB', 'diff' => 8500000],
        ];
        $colors = [
            [
                'name' => 'Xám Không Gian',
                'hex' => '#525357',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/ipad-air-finish-select-gallery-202405-11inch-space-gray?wid=1280&hei=720&fmt=p-jpg'
            ],
            [
                'name' => 'Xanh Da Trời',
                'hex' => '#c8d7e6',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/ipad-air-finish-select-gallery-202405-11inch-blue?wid=1280&hei=720&fmt=p-jpg'
            ],
            [
                'name' => 'Tím Pastel',
                'hex' => '#d8d2e2',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/ipad-air-finish-select-gallery-202405-11inch-purple?wid=1280&hei=720&fmt=p-jpg'
            ],
            [
                'name' => 'Ánh Sao Starlight',
                'hex' => '#eee8dc',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/ipad-air-finish-select-gallery-202405-11inch-starlight?wid=1280&hei=720&fmt=p-jpg'
            ],
        ];
    }
    // --- SAMSUNG GALAXY TAB ---
    elseif (strpos($name, 'tab') !== false) {
        $capacities = [
            ['label' => '128GB', 'diff' => 0],
            ['label' => '256GB', 'diff' => 2000000],
        ];
        $colors = [
            ['name' => 'Xám Tinh Vân', 'hex' => '#4a4d52'],
            ['name' => 'Xanh Nhiệt Đới', 'hex' => '#a8c5b8'],
            ['name' => 'Bạc Ánh Sao', 'hex' => '#dfdfe1'],
        ];
    }
    // --- APPLE WATCH ---
    elseif (strpos($name, 'apple watch') !== false) {
        $capacities = [
            ['label' => '41mm GPS', 'diff' => 0],
            ['label' => '45mm GPS', 'diff' => 800000],
            ['label' => '45mm LTE', 'diff' => 3000000],
        ];
        $colors = [
            ['name' => 'Midnight (Đen)', 'hex' => '#1b222d'],
            ['name' => 'Starlight (Ánh Sao)', 'hex' => '#ede7db'],
            ['name' => 'Silver (Bạc)', 'hex' => '#e2e3e5'],
            ['name' => 'Đỏ (PRODUCT)RED', 'hex' => '#d32f2f'],
        ];
    }
    // --- SAMSUNG GALAXY WATCH ---
    elseif (strpos($name, 'galaxy watch') !== false || strpos($name, 'watch') !== false) {
        $capacities = [
            ['label' => '40mm Bluetooth', 'diff' => 0],
            ['label' => '44mm Bluetooth', 'diff' => 1000000],
        ];
        $colors = [
            ['name' => 'Đen Graphite', 'hex' => '#232528'],
            ['name' => 'Bạc Silver', 'hex' => '#dedfe1'],
            ['name' => 'Vàng Gold', 'hex' => '#e6dfcf'],
        ];
    }
    // --- TAI NGHE / LOA / PHỤ KIỆN ---
    elseif (strpos($name, 'airpods') !== false || strpos($name, 'tai nghe') !== false || strpos($catSlug, 'phu-kien') !== false || strpos($name, 'sạc') !== false || strpos($name, 'sac') !== false) {
        $capacities = [];
        $colors = [
            ['name' => 'Trắng (White)', 'hex' => '#ffffff'],
            ['name' => 'Đen (Black)', 'hex' => '#1e1e1e'],
        ];
    }
    // --- FALLBACK ĐIỆN THOẠI KHÁC ---
    elseif (strpos($catSlug, 'dien-thoai') !== false || strpos($catSlug, 'phone') !== false || (int)($product['category_id'] ?? 0) === 1) {
        $capacities = [
            ['label' => '128GB', 'diff' => 0],
            ['label' => '256GB', 'diff' => 2000000],
        ];
        $colors = [
            [
                'name' => 'Đen (Black)', 
                'hex' => '#212326',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-blacktitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Bạc (Silver)', 
                'hex' => '#e2e4e8',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-whitetitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
            [
                'name' => 'Xanh (Blue)', 
                'hex' => '#2c353f',
                'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-bluetitanium?wid=940&hei=1112&fmt=png-alpha'
            ],
        ];
    }
    // --- FALLBACK LAPTOP KHÁC ---
    elseif (strpos($catSlug, 'laptop') !== false || (int)($product['category_id'] ?? 0) === 2) {
        $capacities = [
            ['label' => '16GB - 512GB', 'diff' => 0],
            ['label' => '16GB - 1TB', 'diff' => 2500000],
        ];
        $colors = [
            ['name' => 'Xám (Gray)', 'hex' => '#4b5563'],
            ['name' => 'Bạc (Silver)', 'hex' => '#e5e7eb'],
        ];
    }

    return [
        'capacities' => $capacities,
        'colors' => $colors,
    ];
}

