<?php
class UserController extends Controller {
    private $userModel;
    private $orderModel;
    private $reviewModel;
    private $addressModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
        $this->reviewModel = new ReviewModel();
        $this->addressModel = new AddressModel();
    }
    
    public function profile() {
        $this->requireAuth();
        $user = $this->userModel->findById($this->getUserId());
        $stats = $this->userModel->getProfileStats($this->getUserId());
        if ($user && $stats) {
            $user = array_merge($user, $stats);
        }
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
            $_SESSION['user']['phone'] = $data['phone'] ?? '';
            $_SESSION['user']['address'] = $data['address'] ?? '';
            $_SESSION['user']['city'] = $data['city'] ?? '';
            $_SESSION['user']['district'] = $data['district'] ?? '';
            $_SESSION['user']['ward'] = $data['ward'] ?? '';
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

    public function addresses() {
        $this->requireAuth();
        $this->view('user.addresses', ['title' => 'Sổ địa chỉ - ' . APP_NAME, 'addresses' => $this->addressModel->getForUser($this->getUserId())]);
    }

    public function saveAddress() {
        $this->requireAuth(); $this->validateCsrf();
        $data = [
            'id' => (int)$this->getInput('id', 0), 'label' => sanitize($this->getInput('label', 'Nhà riêng')),
            'recipient_name' => sanitize($this->getInput('recipient_name', '')), 'phone' => sanitize($this->getInput('phone', '')),
            'address_line' => sanitize($this->getInput('address_line', '')), 'city' => sanitize($this->getInput('city', '')),
            'district' => sanitize($this->getInput('district', '')), 'ward' => sanitize($this->getInput('ward', '')),
            'is_default' => (int)$this->getInput('is_default', 0),
        ];
        if ($data['recipient_name'] === '' || !validatePhone($data['phone']) || $data['address_line'] === '' || $data['city'] === '') {
            setFlash('error', 'Vui lòng nhập người nhận, số điện thoại hợp lệ và địa chỉ đầy đủ.');
        } else {
            $this->addressModel->save($this->getUserId(), $data);
            setFlash('success', 'Đã lưu địa chỉ giao hàng.');
        }
        $this->redirect(url('user/addresses'));
    }

    public function deleteAddress($id) {
        $this->requireAuth(); $this->validateCsrf();
        $this->addressModel->delete($this->getUserId(), (int)$id);
        setFlash('success', 'Đã xóa địa chỉ.');
        $this->redirect(url('user/addresses'));
    }

    /**
     * Cho phép người dùng tự xóa tài khoản cá nhân (Quyền được lãng quên)
     * @return void
     */
    public function deleteAccount() {
        $this->requireAuth();
        $this->validateCsrf();
        
        $password = $this->getInput('confirm_delete_password', '');
        $userId = $this->getUserId();
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            $this->redirect(url());
            return;
        }

        // Không cho phép tài khoản Admin xóa tài khoản
        if (($user['role_id'] ?? 3) == 1) {
            setFlash('error', 'Tài khoản Quản trị viên (Admin) không thể tự xóa.');
            $this->redirect(url('user/profile'));
            return;
        }
        
        // Kiểm tra mật khẩu đối với tài khoản có mật khẩu
        if (!empty($user['password']) && !$this->userModel->verifyPassword($user, $password)) {
            setFlash('error', 'Mật khẩu xác nhận không chính xác. Không thể xóa tài khoản.');
            $this->redirect(url('user/profile'));
            return;
        }
        
        $this->userModel->deleteAccount($userId);
        
        // Hủy session đăng nhập
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        @session_start();
        setFlash('success', 'Tài khoản của bạn đã được xóa thành công khỏi hệ thống Vũ Huân Shop.');
        $this->redirect(url());
    }
}
?>
