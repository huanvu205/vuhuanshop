<?php
/**
 * Base Controller - Lớp cơ sở cho tất cả Controllers
 */

class Controller {
    protected $db;
    protected $data = [];
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Render view
     */
    public function view($view, $data = []) {
        // Ghi log truy cập
        $this->logPageView($_SERVER['REQUEST_URI']);
        
        extract($data);
        
        // Kiểm tra view file
        $relativeView = str_replace('.', '/', $view) . '.php';
        $viewPath = __DIR__ . '/../app/Views/' . $relativeView;
        
        if (!file_exists($viewPath)) {
            if (file_exists(__DIR__ . '/../app/views/' . $relativeView)) {
                $viewPath = __DIR__ . '/../app/views/' . $relativeView;
            } elseif (file_exists(__DIR__ . '/../Views/' . $relativeView)) {
                $viewPath = __DIR__ . '/../Views/' . $relativeView;
            } elseif (file_exists(__DIR__ . '/../views/' . $relativeView)) {
                $viewPath = __DIR__ . '/../views/' . $relativeView;
            } else {
                die("View file not found: $viewPath");
            }
        }
        
        require_once $viewPath;
    }
    
    /**
     * Kiểm tra quyền truy cập
     */
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }
    }
    
    /**
     * Kiểm tra quyền admin
     */
    public function requireAdmin() {
        $this->requireAuth();
        
        if ($this->getRole() !== ROLE_ADMIN) {
            http_response_code(403);
            die('Forbidden');
        }
    }
    
    /**
     * Kiểm tra quyền staff
     */
    public function requireStaff() {
        $this->requireAuth();
        
        if (!in_array($this->getRole(), [ROLE_ADMIN, ROLE_STAFF])) {
            http_response_code(403);
            die('Forbidden');
        }
    }
    
    /**
     * Kiểm tra đăng nhập
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Lấy ID người dùng
     */
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Lấy role người dùng
     */
    public function getRole() {
        return $_SESSION['role'] ?? ROLE_GUEST;
    }
    
    /**
     * Lấy thông tin người dùng
     */
    public function getUser() {
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Ghi log trang view
     */
    public function logPageView($page) {
        // Implement page view logging
    }
    
    /**
     * Chuyển hướng
     */
    public function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Trả về JSON response
     */
    public function json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Lấy POST/GET/JSON data an toàn
     * Hỗ trợ cả application/json (AJAX fetch) và form POST thông thường
     */
    public function getInput($key = null, $default = null) {
        // Đọc JSON body nếu Content-Type là application/json
        static $jsonBody = null;
        if ($jsonBody === null) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $raw = file_get_contents('php://input');
                $decoded = json_decode($raw, true);
                $jsonBody = is_array($decoded) ? $decoded : [];
            } else {
                $jsonBody = [];
            }
        }

        if ($key === null) {
            return array_merge($_GET, $_POST, $jsonBody);
        }
        return $_POST[$key] ?? $_GET[$key] ?? $jsonBody[$key] ?? $default;
    }
    
    /**
     * Kiểm tra CSRF token
     */
    public function validateCsrf() {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
    }
    
    /**
     * Tạo CSRF token
     */
    public function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

?>
