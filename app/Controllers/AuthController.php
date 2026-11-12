<?php
require_once __DIR__ . '/../Helpers/Helper.php';
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Models/CartModel.php';

/**
 * AuthController - Xác thực người dùng
 */
class AuthController extends Controller {
    /**
     * @var UserModel
     */
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }
    
    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect(url());
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $login = sanitize($this->getInput('login', ''));
            $password = $this->getInput('password', '');
            $remember = $this->getInput('remember', false);
            $attemptKey = 'login_attempt_' . hash('sha256', strtolower($login) . '|' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            $attempt = $_SESSION[$attemptKey] ?? ['count' => 0, 'until' => 0];
            if (($attempt['until'] ?? 0) > time()) {
                setFlash('error', 'Bạn đã đăng nhập sai quá nhiều lần. Vui lòng thử lại sau 15 phút.');
                $this->view('auth.login', ['title' => 'Đăng nhập', 'login' => e($login)]);
                return;
            }
            if (($attempt['until'] ?? 0) <= time() && ($attempt['count'] ?? 0) >= 5) $attempt = ['count' => 0, 'until' => 0];
            
            if (empty($login) || empty($password)) {
                setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
                $this->view('auth.login', ['title' => 'Đăng nhập']);
                return;
            }
            
            $user = $this->userModel->findByEmailOrUsername($login);
            
            if (!$user || !$this->userModel->verifyPassword($user, $password)) {
                $attempt['count'] = ($attempt['count'] ?? 0) + 1;
                if ($attempt['count'] >= 5) $attempt['until'] = time() + 900;
                $_SESSION[$attemptKey] = $attempt;
                setFlash('error', 'Email/tên đăng nhập hoặc mật khẩu không đúng');
                $this->view('auth.login', ['title' => 'Đăng nhập', 'login' => e($login)]);
                return;
            }
            
            if ($user['status'] !== 'active') {
                setFlash('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.');
                $this->view('auth.login', ['title' => 'Đăng nhập']);
                return;
            }
            
            // Lưu session
            unset($_SESSION[$attemptKey]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'phone' => $user['phone'] ?? '',
                'address' => $user['address'] ?? '',
                'city' => $user['city'] ?? '',
                'district' => $user['district'] ?? '',
                'ward' => $user['ward'] ?? '',
                'avatar' => $user['avatar'],
                'role' => $user['role_name'],
            ];
            
            // Merge giỏ hàng khách với giỏ của user
            if (isset($_SESSION['cart_session_id'])) {
                $cartModel = new CartModel();
                $cartModel->mergeGuestCart($user['id'], $_SESSION['cart_session_id']);
            }
            
            // Cập nhật số lượng giỏ hàng
            $this->updateCartCount();
            
            // Cập nhật thời gian đăng nhập
            $this->userModel->updateLastLogin($user['id']);
            
            setFlash('success', 'Đăng nhập thành công! Chào mừng ' . $user['full_name']);
            
            // Redirect theo role
            $redirect = $_SESSION['redirect_after_login'] ?? null;
            unset($_SESSION['redirect_after_login']);
            
            if ($user['role_name'] === 'admin' || $user['role_name'] === 'staff') {
                $this->redirect($redirect ?? url('admin/dashboard'));
            } else {
                $this->redirect($redirect ?? url());
            }
        }
        
        $this->view('auth.login', ['title' => 'Đăng nhập - ' . APP_NAME]);
    }
    
    public function register() {
        if ($this->isLoggedIn()) {
            $this->redirect(url());
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = [
                'username' => sanitize($this->getInput('username', '')),
                'email' => sanitize($this->getInput('email', '')),
                'full_name' => sanitize($this->getInput('full_name', '')),
                'phone' => sanitize($this->getInput('phone', '')),
                'password' => $this->getInput('password', ''),
                'confirm_password' => $this->getInput('password_confirm', ''),
            ];
            
            $errors = [];
            
            if (empty($data['username'])) $errors['username'] = 'Tên đăng nhập không được để trống';
            elseif (strlen($data['username']) < 4) $errors['username'] = 'Tên đăng nhập ít nhất 4 ký tự';
            elseif ($this->userModel->usernameExists($data['username'])) $errors['username'] = 'Tên đăng nhập đã tồn tại';
            
            if (empty($data['email'])) $errors['email'] = 'Email không được để trống';
            elseif (!validateEmail($data['email'])) $errors['email'] = 'Email không hợp lệ';
            elseif ($this->userModel->emailExists($data['email'])) $errors['email'] = 'Email đã được đăng ký';
            
            if (empty($data['full_name'])) $errors['full_name'] = 'Họ tên không được để trống';
            if (!empty($data['phone']) && !validatePhone($data['phone'])) $errors['phone'] = 'Số điện thoại không hợp lệ';
            
            if (empty($data['password'])) $errors['password'] = 'Mật khẩu không được để trống';
            elseif (strlen($data['password']) < 6) $errors['password'] = 'Mật khẩu ít nhất 6 ký tự';
            elseif ($data['password'] !== $data['confirm_password']) $errors['password_confirm'] = 'Xác nhận mật khẩu không khớp';
            
            if (!empty($errors)) {
                $this->view('auth.register', [
                    'title' => 'Đăng ký - ' . APP_NAME,
                    'errors' => $errors,
                    'old' => $data
                ]);
                return;
            }
            
            $userId = $this->userModel->create($data);
            
            if ($userId) {
                setFlash('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
                $this->redirect(url('auth/login'));
            } else {
                setFlash('error', 'Đăng ký thất bại. Vui lòng thử lại.');
                $this->view('auth.register', ['title' => 'Đăng ký - ' . APP_NAME, 'old' => $data]);
                return;
            }
        }
        
        $this->view('auth.register', ['title' => 'Đăng ký - ' . APP_NAME]);
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        // Xóa thông tin tài khoản và giỏ hàng khỏi session
        unset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['user'], $_SESSION['cart_count']);

        // Đổi session ID an toàn
        session_regenerate_id(true);

        if (function_exists('setFlash')) {
            setFlash('success', 'Đã đăng xuất thành công');
        } else {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Đã đăng xuất thành công'];
        }

        $redirectUrl = function_exists('url') ? url('auth/login') : (defined('APP_URL') ? APP_URL . '/auth/login' : '/auth/login');
        $this->redirect($redirectUrl);
    }
    
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $email = trim($this->getInput('email', ''));
            
            if (!validateEmail($email)) {
                $this->view('auth.forgot_password', [
                    'title' => 'Quên mật khẩu - ' . APP_NAME,
                    'errors' => ['email' => 'Email không hợp lệ'],
                ]);
                return;
            }
            
            $user = $this->userModel->findByEmail($email);
            $viewData = [
                'title' => 'Quên mật khẩu - ' . APP_NAME,
                'emailSent' => true,
                'sentToEmail' => $email,
            ];
            
            if ($user) {
                $token = bin2hex(random_bytes(32));
                if (!$this->userModel->saveResetToken($user['id'], $token)) {
                    throw new RuntimeException('Unable to save password reset token.');
                }

                if (!sendPasswordResetEmail($user, $token)) {
                    if (!$this->userModel->deleteResetTokensForUser($user['id'])) {
                        error_log('Unable to remove undelivered password reset token.');
                    }
                    $viewData['emailSent'] = false;
                    $viewData['error'] = (SMTP_USER === '' || SMTP_PASS === '')
                        ? 'Website chưa được cấu hình SMTP_USER và SMTP_PASS nên chưa thể gửi email. Vui lòng liên hệ quản trị viên.'
                        : 'Máy chủ email hiện không phản hồi. Vui lòng thử lại sau hoặc liên hệ bộ phận hỗ trợ.';
                }
            }
            
            $this->view('auth.forgot_password', $viewData);
            return;
        }
        
        $this->view('auth.forgot_password', ['title' => 'Quên mật khẩu - ' . APP_NAME]);
    }
    
    public function resetPassword() {
        $token = (string) ($_GET['token'] ?? '');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $token = (string) $this->getInput('token', '');
            $password = $this->getInput('password', '');
            $confirm = $this->getInput('confirm_password', '');

            $userId = $this->userModel->verifyResetToken($token);
            if (!$userId) {
                $this->view('auth.forgot_password', [
                    'title' => 'Quên mật khẩu - ' . APP_NAME,
                    'error' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng yêu cầu liên kết mới.',
                ]);
                return;
            }
            
            if (strlen($password) < 6) {
                setFlash('error', 'Mật khẩu ít nhất 6 ký tự');
                $this->view('auth.reset_password', ['title' => 'Đặt lại mật khẩu', 'token' => e($token)]);
                return;
            }
            
            if ($password !== $confirm) {
                setFlash('error', 'Mật khẩu xác nhận không khớp');
                $this->view('auth.reset_password', ['title' => 'Đặt lại mật khẩu', 'token' => e($token)]);
                return;
            }
            
            if (!$this->userModel->updatePassword($userId, $password)) {
                setFlash('error', 'Không thể cập nhật mật khẩu. Vui lòng thử lại.');
                $this->view('auth.reset_password', ['title' => 'Đặt lại mật khẩu - ' . APP_NAME, 'token' => $token]);
                return;
            }
            
            if (!$this->userModel->deleteResetTokensForUser($userId)) {
                error_log('Unable to invalidate password reset tokens after successful password reset.');
            }
            setFlash('success', 'Mật khẩu đã được đặt lại thành công');
            $this->redirect(url('auth/login'));
            return;
        }

        if (!$this->userModel->verifyResetToken($token)) {
            $this->view('auth.forgot_password', [
                'title' => 'Quên mật khẩu - ' . APP_NAME,
                'error' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng yêu cầu liên kết mới.',
            ]);
            return;
        }
        
        $this->view('auth.reset_password', ['title' => 'Đặt lại mật khẩu - ' . APP_NAME, 'token' => $token]);
    }
    
    /**
     * Chuyển hướng người dùng đến Google OAuth 2.0
     */
    public function google() {
        if ($this->isLoggedIn()) {
            $this->redirect(url());
            return;
        }

        if (!defined('GOOGLE_CLIENT_ID') || empty(GOOGLE_CLIENT_ID)) {
            setFlash('error', 'Chức năng đăng nhập Google chưa được cấu hình Client ID.');
            $this->redirect(url('auth/login'));
            return;
        }

        // Tạo CSRF state để bảo vệ chống tấn công CSRF
        $state = bin2hex(random_bytes(16));
        $_SESSION['google_oauth_state'] = $state;

        $redirectUri = function_exists('getGoogleRedirectUri') ? getGoogleRedirectUri() : GOOGLE_REDIRECT_URI;

        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ];

        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        header('Location: ' . $authUrl);
        exit;
    }

    /**
     * Xử lý callback sau khi người dùng xác thực từ Google
     */
    public function googleCallback() {
        if ($this->isLoggedIn()) {
            $this->redirect(url());
            return;
        }

        // Kiểm tra lỗi từ Google (người dùng bấm hủy)
        if (!empty($_GET['error'])) {
            setFlash('error', 'Bạn đã hủy đăng nhập bằng Google.');
            $this->redirect(url('auth/login'));
            return;
        }

        $code = $_GET['code'] ?? '';
        $state = $_GET['state'] ?? '';
        $savedState = $_SESSION['google_oauth_state'] ?? '';
        unset($_SESSION['google_oauth_state']);

        // Kiểm tra tính hợp lệ của state (CSRF Protection)
        if (empty($code) || empty($state) || $state !== $savedState) {
            setFlash('error', 'Yêu cầu đăng nhập không hợp lệ hoặc phiên đã hết hạn. Vui lòng thử lại.');
            $this->redirect(url('auth/login'));
            return;
        }

        // 1. Đổi authorization code lấy access token
        $tokenData = $this->exchangeGoogleCode($code);
        if (!$tokenData || empty($tokenData['access_token'])) {
            setFlash('error', 'Không thể kết nối xác thực với Google. Vui lòng thử lại.');
            $this->redirect(url('auth/login'));
            return;
        }

        // 2. Lấy thông tin tài khoản người dùng từ Google
        $googleUser = $this->getGoogleUserInfo($tokenData['access_token']);
        if (!$googleUser || empty($googleUser['email'])) {
            setFlash('error', 'Không thể lấy thông tin email từ tài khoản Google.');
            $this->redirect(url('auth/login'));
            return;
        }

        $googleId = $googleUser['sub'] ?? ($googleUser['id'] ?? '');
        $email = strtolower(trim($googleUser['email']));
        $name = trim($googleUser['name'] ?? ($googleUser['given_name'] ?? 'Khách hàng Google'));
        $picture = $googleUser['picture'] ?? null;

        // 3. Tìm tài khoản trong database
        // 3a. Tìm theo google_id
        $user = !empty($googleId) ? $this->userModel->findByGoogleId($googleId) : null;

        if (!$user) {
            // 3b. Tìm theo email
            $user = $this->userModel->findByEmail($email);
            if ($user) {
                // Đã có tài khoản bằng email này, liên kết google_id
                $this->userModel->updateGoogleId($user['id'], $googleId, $picture);
                $user = $this->userModel->findById($user['id']);
            } else {
                // 3c. Tạo mới tài khoản
                // Tạo username duy nhất từ email
                $baseUsername = explode('@', $email)[0];
                $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '', $baseUsername);
                if (strlen($baseUsername) < 4) {
                    $baseUsername = 'user_' . substr(md5(uniqid()), 0, 6);
                }

                $username = $baseUsername;
                $counter = 1;
                while ($this->userModel->usernameExists($username)) {
                    $username = $baseUsername . '_' . $counter;
                    $counter++;
                }

                $newUserId = $this->userModel->createFromGoogle([
                    'username' => $username,
                    'email' => $email,
                    'full_name' => $name,
                    'google_id' => $googleId,
                    'avatar' => $picture,
                ]);

                if (!$newUserId) {
                    setFlash('error', 'Không thể tạo tài khoản từ Google. Vui lòng thử lại.');
                    $this->redirect(url('auth/login'));
                    return;
                }

                $user = $this->userModel->findById($newUserId);
            }
        }

        if (!$user) {
            setFlash('error', 'Đăng nhập Google thất bại. Vui lòng thử lại.');
            $this->redirect(url('auth/login'));
            return;
        }

        $this->doLoginUser($user, 'Đăng nhập Google thành công! Chào mừng ' . $user['full_name']);
    }

    /**
     * Fallback alias cho callback
     */
    public function google_callback() {
        $this->googleCallback();
    }

    /**
     * Chuyển hướng người dùng đến Facebook OAuth
     */
    public function facebook() {
        if ($this->isLoggedIn()) {
            $this->redirect(url());
            return;
        }

        if (!defined('FACEBOOK_APP_ID') || empty(FACEBOOK_APP_ID)) {
            setFlash('error', 'Chức năng đăng nhập Facebook chưa được cấu hình App ID.');
            $this->redirect(url('auth/login'));
            return;
        }

        $state = bin2hex(random_bytes(16));
        $_SESSION['facebook_oauth_state'] = $state;

        $redirectUri = function_exists('getFacebookRedirectUri') ? getFacebookRedirectUri() : (defined('FACEBOOK_REDIRECT_URI') ? FACEBOOK_REDIRECT_URI : url('auth/facebook-callback'));

        $params = [
            'client_id' => FACEBOOK_APP_ID,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => 'public_profile',
            'response_type' => 'code',
        ];

        $authUrl = 'https://www.facebook.com/v19.0/dialog/oauth?' . http_build_query($params);
        header('Location: ' . $authUrl);
        exit;
    }

    /**
     * Xử lý callback sau khi người dùng xác thực từ Facebook
     */
    public function facebookCallback() {
        if ($this->isLoggedIn()) {
            $this->redirect(url());
            return;
        }

        if (!empty($_GET['error'])) {
            setFlash('error', 'Bạn đã hủy đăng nhập bằng Facebook.');
            $this->redirect(url('auth/login'));
            return;
        }

        $code = $_GET['code'] ?? '';
        $state = $_GET['state'] ?? '';
        $savedState = $_SESSION['facebook_oauth_state'] ?? '';
        unset($_SESSION['facebook_oauth_state']);

        if (empty($code) || empty($state) || $state !== $savedState) {
            setFlash('error', 'Yêu cầu đăng nhập không hợp lệ hoặc phiên đã hết hạn. Vui lòng thử lại.');
            $this->redirect(url('auth/login'));
            return;
        }

        // 1. Đổi code lấy Access Token của Facebook
        $redirectUri = function_exists('getFacebookRedirectUri') ? getFacebookRedirectUri() : (defined('FACEBOOK_REDIRECT_URI') ? FACEBOOK_REDIRECT_URI : url('auth/facebook-callback'));
        $tokenUrl = 'https://graph.facebook.com/v19.0/oauth/access_token?' . http_build_query([
            'client_id' => FACEBOOK_APP_ID,
            'client_secret' => FACEBOOK_APP_SECRET,
            'redirect_uri' => $redirectUri,
            'code' => $code,
        ]);

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $tokenData = ($httpCode === 200 && $response) ? json_decode($response, true) : null;
        if (!$tokenData || empty($tokenData['access_token'])) {
            setFlash('error', 'Không thể kết nối xác thực với Facebook. Vui lòng thử lại.');
            $this->redirect(url('auth/login'));
            return;
        }

        $accessToken = $tokenData['access_token'];

        // 2. Lấy thông tin người dùng từ Facebook Graph API
        $userUrl = 'https://graph.facebook.com/me?fields=id,name,email,picture.width(400).height(400)&access_token=' . urlencode($accessToken);
        $chUser = curl_init($userUrl);
        curl_setopt($chUser, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chUser, CURLOPT_TIMEOUT, 15);
        curl_setopt($chUser, CURLOPT_SSL_VERIFYPEER, false);
        $userResponse = curl_exec($chUser);
        $userHttpCode = curl_getinfo($chUser, CURLINFO_HTTP_CODE);

        $fbUser = ($userHttpCode === 200 && $userResponse) ? json_decode($userResponse, true) : null;
        if (!$fbUser || empty($fbUser['id'])) {
            setFlash('error', 'Không thể lấy thông tin tài khoản từ Facebook.');
            $this->redirect(url('auth/login'));
            return;
        }

        $facebookId = $fbUser['id'];
        $name = trim($fbUser['name'] ?? 'Khách hàng Facebook');
        $email = !empty($fbUser['email']) ? strtolower(trim($fbUser['email'])) : null;
        $picture = $fbUser['picture']['data']['url'] ?? null;

        // 3. Tìm tài khoản trong database
        // 3a. Tìm theo facebook_id
        $user = $this->userModel->findByFacebookId($facebookId);

        if (!$user && $email) {
            // 3b. Tìm theo email
            $user = $this->userModel->findByEmail($email);
            if ($user) {
                $this->userModel->updateFacebookId($user['id'], $facebookId, $picture);
                $user = $this->userModel->findById($user['id']);
            }
        }

        if (!$user) {
            // 3c. Tạo mới tài khoản
            $baseUsername = $email ? explode('@', $email)[0] : 'fb_' . substr($facebookId, -6);
            $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '', $baseUsername);
            if (strlen($baseUsername) < 4) {
                $baseUsername = 'fb_user_' . substr(md5(uniqid()), 0, 5);
            }

            $username = $baseUsername;
            $counter = 1;
            while ($this->userModel->usernameExists($username)) {
                $username = $baseUsername . '_' . $counter;
                $counter++;
            }

            $userEmail = $email ?? ($username . '@facebook.user');

            $newUserId = $this->userModel->createFromFacebook([
                'username' => $username,
                'email' => $userEmail,
                'full_name' => $name,
                'facebook_id' => $facebookId,
                'avatar' => $picture,
            ]);

            if (!$newUserId) {
                setFlash('error', 'Không thể tạo tài khoản từ Facebook. Vui lòng thử lại.');
                $this->redirect(url('auth/login'));
                return;
            }

            $user = $this->userModel->findById($newUserId);
        }

        if (!$user) {
            setFlash('error', 'Đăng nhập Facebook thất bại. Vui lòng thử lại.');
            $this->redirect(url('auth/login'));
            return;
        }

        $this->doLoginUser($user, 'Đăng nhập Facebook thành công! Chào mừng ' . $user['full_name']);
    }

    /**
     * Fallback alias cho facebook callback
     */
    public function facebook_callback() {
        $this->facebookCallback();
    }

    /**
     * Gửi yêu cầu cURL đổi code lấy Access Token của Google
     * @param string $code
     * @return array|null
     */
    private function exchangeGoogleCode($code) {
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        $redirectUri = function_exists('getGoogleRedirectUri') ? getGoogleRedirectUri() : GOOGLE_REDIRECT_URI;
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'code' => $code,
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code'
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }

        error_log('Google OAuth Token Error (' . $httpCode . '): ' . $response);
        return null;
    }

    /**
     * Gửi yêu cầu cURL lấy thông tin userinfo từ Google
     * @param string $accessToken
     * @return array|null
     */
    private function getGoogleUserInfo($accessToken) {
        $ch = curl_init('https://www.googleapis.com/oauth2/v3/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }

        error_log('Google OAuth UserInfo Error (' . $httpCode . '): ' . $response);
        return null;
    }

    /**
     * Thiết lập session đăng nhập cho người dùng
     * @param array $user
     * @param string|null $message
     * @return void
     */
    private function doLoginUser($user, $message = null) {
        if ($user['status'] !== 'active') {
            setFlash('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.');
            $this->redirect(url('auth/login'));
            return;
        }

        // Lưu session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role_name'] ?? 'customer';
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '',
            'address' => $user['address'] ?? '',
            'city' => $user['city'] ?? '',
            'district' => $user['district'] ?? '',
            'ward' => $user['ward'] ?? '',
            'avatar' => $user['avatar'] ?? null,
            'role' => $user['role_name'] ?? 'customer',
        ];
        
        // Merge giỏ hàng khách với giỏ của user
        if (isset($_SESSION['cart_session_id'])) {
            $cartModel = new CartModel();
            $cartModel->mergeGuestCart($user['id'], $_SESSION['cart_session_id']);
        }
        
        // Cập nhật số lượng giỏ hàng
        $this->updateCartCount();
        
        // Cập nhật thời gian đăng nhập
        $this->userModel->updateLastLogin($user['id']);
        
        setFlash('success', $message ?? ('Đăng nhập thành công! Chào mừng ' . $user['full_name']));
        
        // Redirect theo role
        $redirect = $_SESSION['redirect_after_login'] ?? null;
        unset($_SESSION['redirect_after_login']);
        
        if (($user['role_name'] ?? '') === 'admin' || ($user['role_name'] ?? '') === 'staff') {
            $this->redirect($redirect ?? url('admin/dashboard'));
        } else {
            $this->redirect($redirect ?? url());
        }
    }
    
    private function updateCartCount() {
        $cartModel = new CartModel();
        $cart = $cartModel->getOrCreate($this->getUserId());
        $_SESSION['cart_count'] = $cartModel->getItemCount($cart['id']);
    }
}
?>
