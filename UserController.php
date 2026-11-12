<?php
class UserController extends Controller {
    private $userModel;
    private $orderModel;
    private $reviewModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
        $this->reviewModel = new ReviewModel();
    }
    
    public function profile() {
        $this->requireAuth();
        $user = $this->userModel->findById($this->getUserId());
        $this->view('user.profile', ['title' => 'Hồ sơ cá nhân - ' . APP_NAME, 'user' => $user]);
    }
    
    public function updateProfile() {
        $this->requireAuth();
        $this->validateCsrf();
        
        $data = [
            'full_name' => sanitize($this->getInput('full_name', '')),
            'phone' => sanitize($this->getInput('phone', '')),
            'address' => sanitize($this->getInput('address', '')),
            'city' => sanitize($this->getInput('city', '')),
            'district' => sanitize($this->getInput('district', '')),
            'ward' => sanitize($this->getInput('ward', '')),
        ];
        
        $email = sanitize($this->getInput('email', ''));
        
        if (empty($data['full_name'])) {
            setFlash('error', 'Họ tên không được để trống');
        } else {
            // Check email uniqueness if email is changed
            if (!empty($email) && $email !== $_SESSION['user']['email']) {
                if (!validateEmail($email)) {
                    setFlash('error', 'Email không đúng định dạng');
                    $this->redirect(url('user/profile'));
                    return;
                }
                if ($this->userModel->emailExists($email, $this->getUserId())) {
                    setFlash('error', 'Email đã được sử dụng bởi tài khoản khác');
                    $this->redirect(url('user/profile'));
                    return;
                }
                $data['email'] = $email;
            }
            
            // Thực hiện cập nhật hồ sơ trong DB
            $updated = $this->userModel->updateProfile($this->getUserId(), $data);
            if (!$updated) {
                setFlash('error', 'Cập nhật hồ sơ thất bại, vui lòng thử lại');
                $this->redirect(url('user/profile'));
                return;
            }

            // Cập nhật session
            $_SESSION['user']['full_name'] = $data['full_name'];
            if (isset($data['email'])) {
                $_SESSION['user']['email'] = $data['email'];
            }

            // Upload avatar
            if (!empty($_FILES['avatar']['name'])) {
                $result = uploadImage($_FILES['avatar'], 'avatars');
                if ($result['success']) {
                    $this->userModel->updateAvatar($this->getUserId(), $result['path']);
                    // Cập nhật lại session
                    $_SESSION['user']['avatar'] = $result['path'];
                } else {
                    setFlash('error', $result['error'] ?? 'Upload avatar thất bại');
                    $this->redirect(url('user/profile'));
                    return;
                }
            }
            
            setFlash('success', 'Cập nhật hồ sơ thành công');
        }
        
        $this->redirect(url('user/profile'));
    }
    
    public function changePassword() {
        $this->requireAuth();
        $this->validateCsrf();
        
        $currentPassword = $this->getInput('current_password', '');
        $newPassword = $this->getInput('new_password', '');
        $confirmPassword = $this->getInput('confirm_password', '');
        
        $user = $this->userModel->findById($this->getUserId());
        
        if (!$this->userModel->verifyPassword($user, $currentPassword)) {
            setFlash('error', 'Mật khẩu hiện tại không đúng');
        } elseif (strlen($newPassword) < 6) {
            setFlash('error', 'Mật khẩu mới ít nhất 6 ký tự');
        } elseif ($newPassword !== $confirmPassword) {
            setFlash('error', 'Xác nhận mật khẩu không khớp');
        } else {
            $this->userModel->updatePassword($this->getUserId(), $newPassword);
            setFlash('success', 'Đổi mật khẩu thành công');
        }
        
        $this->redirect(url('user/profile'));
    }
    
    public function orders() {
        $this->requireAuth();
        $orders = $this->orderModel->getUserOrders($this->getUserId());
        $this->view('user.orders', ['title' => 'Lịch sử mua hàng - ' . APP_NAME, 'orders' => $orders]);
    }
    
    public function reviews() {
        $this->requireAuth();
        $reviews = $this->reviewModel->getUserReviews($this->getUserId());
        $this->view('user.reviews', ['title' => 'Đánh giá của tôi - ' . APP_NAME, 'reviews' => $reviews]);
    }
}
?>
