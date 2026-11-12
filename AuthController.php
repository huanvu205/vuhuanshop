<?php
/**
 * AuthController - Xác thực người dùng
 */
class AuthController extends Controller {
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
            
            if (empty($login) || empty($password)) {
                setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
                $this->view('auth.login', ['title' => 'Đăng nhập']);
                return;
            }
            
            $user = $this->userModel->findByEmailOrUsername($login);
            
            if (!$user || !$this->userModel->verifyPassword($user, $password)) {
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
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
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
        $_SESSION = [];
        session_destroy();
        setFlash('success', 'Đã đăng xuất thành công');
        $this->redirect(url('auth/login'));
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
                    $viewData['error'] = 'Chúng tôi chưa thể gửi email lúc này. Vui lòng thử lại sau hoặc liên hệ bộ phận hỗ trợ.';
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
    
    private function updateCartCount() {
        $cartModel = new CartModel();
        $cart = $cartModel->getOrCreate($this->getUserId());
        $_SESSION['cart_count'] = $cartModel->getItemCount($cart['id']);
    }
}
?>
