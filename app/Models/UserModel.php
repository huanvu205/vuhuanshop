<?php
/**
 * UserModel - Quản lý người dùng
 */
class UserModel {
    /**
     * @var Database
     */
    private $db;

    /**
     * @var bool
     */
    private $resetTokensReady = false;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Tìm người dùng theo ID
     * @param int|string $id
     * @return array|false
     */
    public function findById($id) {
        return $this->db->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = :id")
            ->bind(':id', $id)->single();
    }

    public function getProfileStats($userId) {
        return $this->db->query("SELECT
                (SELECT COUNT(*) FROM orders WHERE user_id = :orders_user_id) AS order_count,
                (SELECT COUNT(*) FROM wishlists WHERE user_id = :wishlist_user_id) AS wishlist_count,
                (SELECT COUNT(*) FROM reviews WHERE user_id = :reviews_user_id) AS review_count")
            ->bind(':orders_user_id', $userId)
            ->bind(':wishlist_user_id', $userId)
            ->bind(':reviews_user_id', $userId)
            ->single();
    }
    
    /**
     * Tìm người dùng theo Email
     * @param string $email
     * @return array|false
     */
    public function findByEmail($email) {
        return $this->db->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.email = :email")
            ->bind(':email', $email)->single();
    }
    
    /**
     * Tìm người dùng theo Username
     * @param string $username
     * @return array|false
     */
    public function findByUsername($username) {
        return $this->db->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.username = :username")
            ->bind(':username', $username)->single();
    }
    
    /**
     * Tìm người dùng theo Email hoặc Username
     * @param string $login
     * @return array|false
     */
    public function findByEmailOrUsername($login) {
        return $this->db->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.email = :email OR u.username = :username")
            ->bind(':email', $login)->bind(':username', $login)->single();
    }
    
    /**
     * Tìm người dùng theo Google ID
     * @param string $googleId
     * @return array|null
     */
    public function findByGoogleId($googleId) {
        try {
            return $this->db->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.google_id = :google_id")
                ->bind(':google_id', $googleId)->single();
        } catch (\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Tạo tài khoản mới từ tài khoản Google
     * @param array $data
     * @return int|string|false
     */
    public function createFromGoogle($data) {
        try {
            $this->db->query("INSERT INTO users (username, email, password, full_name, role_id, status, google_id, avatar) VALUES (:username, :email, :password, :full_name, 3, 'active', :google_id, :avatar)")
                ->bind(':username', $data['username'])
                ->bind(':email', $data['email'])
                ->bind(':password', password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT))
                ->bind(':full_name', $data['full_name'])
                ->bind(':google_id', $data['google_id'])
                ->bind(':avatar', $data['avatar'] ?? null)
                ->execute();
            return $this->db->lastInsertId();
        } catch (\Throwable $e) {
            $this->db->query("INSERT INTO users (username, email, password, full_name, role_id, status) VALUES (:username, :email, :password, :full_name, 3, 'active')")
                ->bind(':username', $data['username'])
                ->bind(':email', $data['email'])
                ->bind(':password', password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT))
                ->bind(':full_name', $data['full_name'])
                ->execute();
            return $this->db->lastInsertId();
        }
    }
    
    /**
     * Cập nhật Google ID cho tài khoản người dùng
     * @param int|string $id
     * @param string $googleId
     * @param string|null $avatar
     * @return bool
     */
    public function updateGoogleId($id, $googleId, $avatar = null) {
        try {
            $sql = "UPDATE users SET google_id = :google_id";
            if (!empty($avatar)) {
                $sql .= ", avatar = COALESCE(avatar, :avatar)";
            }
            $sql .= " WHERE id = :id";
            $stmt = $this->db->query($sql)->bind(':google_id', $googleId)->bind(':id', $id);
            if (!empty($avatar)) {
                $stmt->bind(':avatar', $avatar);
            }
            return (bool) $stmt->execute();
        } catch (\Throwable $e) {
            return false;
        }
    }
    
    /**
     * Tìm người dùng theo Facebook ID
     * @param string $facebookId
     * @return array|null
     */
    public function findByFacebookId($facebookId) {
        try {
            return $this->db->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.facebook_id = :facebook_id")
                ->bind(':facebook_id', $facebookId)->single();
        } catch (\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Tạo tài khoản mới từ tài khoản Facebook
     * @param array $data
     * @return int|string|false
     */
    public function createFromFacebook($data) {
        try {
            $this->db->query("INSERT INTO users (username, email, password, full_name, role_id, status, facebook_id, avatar) VALUES (:username, :email, :password, :full_name, 3, 'active', :facebook_id, :avatar)")
                ->bind(':username', $data['username'])
                ->bind(':email', $data['email'])
                ->bind(':password', password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT))
                ->bind(':full_name', $data['full_name'])
                ->bind(':facebook_id', $data['facebook_id'])
                ->bind(':avatar', $data['avatar'] ?? null)
                ->execute();
            return $this->db->lastInsertId();
        } catch (\Throwable $e) {
            $this->db->query("INSERT INTO users (username, email, password, full_name, role_id, status) VALUES (:username, :email, :password, :full_name, 3, 'active')")
                ->bind(':username', $data['username'])
                ->bind(':email', $data['email'])
                ->bind(':password', password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT))
                ->bind(':full_name', $data['full_name'])
                ->execute();
            return $this->db->lastInsertId();
        }
    }
    
    /**
     * Cập nhật Facebook ID cho tài khoản người dùng
     * @param int|string $id
     * @param string $facebookId
     * @param string|null $avatar
     * @return bool
     */
    public function updateFacebookId($id, $facebookId, $avatar = null) {
        try {
            $sql = "UPDATE users SET facebook_id = :facebook_id";
            if (!empty($avatar)) {
                $sql .= ", avatar = COALESCE(avatar, :avatar)";
            }
            $sql .= " WHERE id = :id";
            $stmt = $this->db->query($sql)->bind(':facebook_id', $facebookId)->bind(':id', $id);
            if (!empty($avatar)) {
                $stmt->bind(':avatar', $avatar);
            }
            return (bool) $stmt->execute();
        } catch (\Throwable $e) {
            return false;
        }
    }
    
    /**
     * Tạo tài khoản người dùng mới
     * @param array $data
     * @return int|string|false
     */
    public function create($data) {
        $this->db->query("INSERT INTO users (username, email, password, full_name, phone, role_id, status) VALUES (:username, :email, :password, :full_name, :phone, 3, 'active')")
            ->bind(':username', $data['username'])
            ->bind(':email', $data['email'])
            ->bind(':password', password_hash($data['password'], PASSWORD_BCRYPT))
            ->bind(':full_name', $data['full_name'])
            ->bind(':phone', $data['phone'] ?? null)
            ->execute();
        return $this->db->lastInsertId();
    }
    
    /**
     * Cập nhật thông tin hồ sơ người dùng
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function updateProfile($id, $data) {
        $sql = "UPDATE users SET full_name=:full_name, phone=:phone, address=:address, city=:city, district=:district, ward=:ward";
        if (isset($data['email'])) {
            $sql .= ", email=:email";
        }
        $sql .= " WHERE id=:id";
        
        $stmt = $this->db->query($sql)
            ->bind(':full_name', $data['full_name'])
            ->bind(':phone', $data['phone'] ?? null)
            ->bind(':address', $data['address'] ?? null)
            ->bind(':city', $data['city'] ?? null)
            ->bind(':district', $data['district'] ?? null)
            ->bind(':ward', $data['ward'] ?? null)
            ->bind(':id', $id);
            
        if (isset($data['email'])) {
            $stmt->bind(':email', $data['email']);
        }
        
        return (bool) $stmt->execute();
    }
    
    /**
     * Cập nhật mật khẩu người dùng
     * @param int|string $id
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword($id, $newPassword) {
        return (bool) $this->db->query("UPDATE users SET password=:password WHERE id=:id")
            ->bind(':password', password_hash($newPassword, PASSWORD_BCRYPT))
            ->bind(':id', $id)
            ->execute();
    }
    
    /**
     * Cập nhật ảnh đại diện người dùng
     * @param int|string $id
     * @param string $avatar
     * @return bool
     */
    public function updateAvatar($id, $avatar) {
        return (bool) $this->db->query("UPDATE users SET avatar=:avatar WHERE id=:id")
            ->bind(':avatar', $avatar)->bind(':id', $id)->execute();
    }
    
    /**
     * Cập nhật thời gian đăng nhập lần cuối
     * @param int|string $id
     * @return bool
     */
    public function updateLastLogin($id) {
        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            return (bool) $this->db->query("UPDATE users SET last_login=NOW() WHERE id=:id")
                ->bind(':id', $id)->execute();
        } else {
            return (bool) $this->db->query("UPDATE users SET last_login=GETDATE() WHERE id=:id")
                ->bind(':id', $id)->execute();
        }
    }
    
    /**
     * Xác thực mật khẩu
     * @param array $user
     * @param string $password
     * @return bool
     */
    public function verifyPassword($user, $password) {
        return password_verify($password, $user['password']);
    }
    
    /**
     * Lấy danh sách người dùng phân trang
     * @param int $page
     * @param int $limit
     * @param string $search
     * @param string $status
     * @return array
     */
    public function getAll($page = 1, $limit = 20, $search = '', $status = '') {
        $offset = ($page - 1) * $limit;
        $where = "WHERE u.role_id != 1";
        $params = [];
        
        if ($search) {
            $where .= " AND (u.full_name LIKE :search OR u.email LIKE :search OR u.phone LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if ($status) {
            $where .= " AND u.status = :status";
            $params[':status'] = $status;
        }
        
        $stmt = $this->db->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id $where ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bind(':limit', $limit)->bind(':offset', $offset);
        foreach ($params as $k => $v) $stmt->bind($k, $v);
        return $stmt->resultSet();
    }
    
    /**
     * Đếm tổng số người dùng phù hợp
     * @param string $search
     * @param string $status
     * @return int
     */
    public function count($search = '', $status = '') {
        $where = "WHERE u.role_id != 1";
        $params = [];
        if ($search) {
            $where .= " AND (u.full_name LIKE :search OR u.email LIKE :search OR u.phone LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if ($status) {
            $where .= " AND u.status = :status";
            $params[':status'] = $status;
        }
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users u $where");
        foreach ($params as $k => $v) $stmt->bind($k, $v);
        $result = $stmt->single();
        return (int) ($result['total'] ?? 0);
    }
    
    /**
     * Cập nhật trạng thái người dùng
     * @param int|string $id
     * @param string $status
     * @return bool
     */
    public function setStatus($id, $status) {
        return (bool) $this->db->query("UPDATE users SET status=:status WHERE id=:id")
            ->bind(':status', $status)->bind(':id', $id)->execute();
    }
    
    /**
     * Vô hiệu hóa và xóa dữ liệu cá nhân khi người dùng tự xóa tài khoản
     * @param int|string $userId
     * @return bool
     */
    public function deleteAccount($userId) {
        return (bool) $this->db->query("UPDATE users SET status = 'deleted', full_name = 'Tài khoản đã xóa', phone = NULL WHERE id = :id")
            ->bind(':id', (int)$userId)
            ->execute();
    }
    
    /**
     * Lấy thống kê người dùng
     * @return array|false
     */
    public function getStats() {
        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            return $this->db->query("SELECT COUNT(*) as total, SUM(CASE WHEN status='active' THEN 1 ELSE 0 END) as active, SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today FROM users WHERE role_id = 3")->single();
        } else {
            return $this->db->query("SELECT COUNT(*) as total, SUM(CASE WHEN status='active' THEN 1 ELSE 0 END) as active, SUM(CASE WHEN CONVERT(date, created_at) = CONVERT(date, GETDATE()) THEN 1 ELSE 0 END) as today FROM users WHERE role_id = 3")->single();
        }
    }
    
    /**
     * Kiểm tra Email đã tồn tại chưa
     * @param string $email
     * @param int|string|null $excludeId
     * @return bool
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->db->query($sql)->bind(':email', $email);
        if ($excludeId) {
            $stmt = $this->db->query($sql . " AND id != :id")->bind(':email', $email)->bind(':id', $excludeId);
        }
        return $stmt->single() !== false;
    }
    
    /**
     * Kiểm tra Username đã tồn tại chưa
     * @param string $username
     * @param int|string|null $excludeId
     * @return bool
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT id FROM users WHERE username = :username";
        $stmt = $this->db->query($sql)->bind(':username', $username);
        if ($excludeId) {
            $stmt = $this->db->query($sql . " AND id != :id")->bind(':username', $username)->bind(':id', $excludeId);
        }
        return $stmt->single() !== false;
    }
    
    /**
     * Đảm bảo bảng password_reset_tokens tồn tại
     * @throws RuntimeException
     * @return void
     */
    private function ensureResetTokensTable() {
        if ($this->resetTokensReady) {
            return;
        }

        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            $sql = "CREATE TABLE IF NOT EXISTS password_reset_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token_hash CHAR(64) NOT NULL UNIQUE,
                expires_at DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY idx_password_reset_user_id (user_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        } else {
            $sql = "IF OBJECT_ID('dbo.password_reset_tokens', 'U') IS NULL
                BEGIN
                    CREATE TABLE dbo.password_reset_tokens (
                        id INT IDENTITY(1,1) PRIMARY KEY,
                        user_id INT NOT NULL,
                        token_hash CHAR(64) NOT NULL UNIQUE,
                        expires_at DATETIME2 NOT NULL,
                        created_at DATETIME2 NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        CONSTRAINT FK_password_reset_tokens_users
                            FOREIGN KEY (user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
                    )
                END";
        }

        if (!$this->db->query($sql)->execute()) {
            throw new RuntimeException('Unable to initialize password reset token storage.');
        }

        $this->resetTokensReady = true;
    }
    
    /**
     * Lưu token đặt lại mật khẩu cho người dùng
     * @param int|string $userId
     * @param string $token
     * @throws RuntimeException
     * @return bool
     */
    public function saveResetToken($userId, $token) {
        $this->ensureResetTokensTable();

        $deleted = $this->db->query("DELETE FROM password_reset_tokens WHERE user_id = :user_id")
            ->bind(':user_id', (int) $userId)
            ->execute();
        if (!$deleted) {
            throw new RuntimeException('Unable to invalidate previous password reset tokens.');
        }

        return (bool) $this->db->query("INSERT INTO password_reset_tokens (user_id, token_hash, expires_at) VALUES (:user_id, :token_hash, :expires_at)")
            ->bind(':user_id', (int) $userId)
            ->bind(':token_hash', hash('sha256', $token))
            ->bind(':expires_at', date('Y-m-d H:i:s', time() + 900))
            ->execute();
    }

    /**
     * Kiểm tra token đặt lại mật khẩu có hợp lệ không
     * @param string $token
     * @return int|false
     */
    public function verifyResetToken($token) {
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/i', $token)) {
            return false;
        }

        $this->ensureResetTokensTable();
        $row = $this->db->query("SELECT user_id FROM password_reset_tokens WHERE token_hash = :token_hash AND expires_at > :now")
            ->bind(':token_hash', hash('sha256', $token))
            ->bind(':now', date('Y-m-d H:i:s'))
            ->single();

        return $row ? (int) $row['user_id'] : false;
    }

    /**
     * Xóa các token đặt lại mật khẩu của người dùng
     * @param int|string $userId
     * @return bool
     */
    public function deleteResetTokensForUser($userId) {
        $this->ensureResetTokensTable();
        return (bool) $this->db->query("DELETE FROM password_reset_tokens WHERE user_id = :user_id")
            ->bind(':user_id', (int) $userId)
            ->execute();
    }
    
    /**
     * Cập nhật thông tin người dùng theo Email
     * @param string $email
     * @param array $data
     * @return bool
     */
    public function updateByEmail($email, $data) {
        if (isset($data['password'])) {
            return (bool) $this->db->query("UPDATE users SET password=:password WHERE email=:email")
                ->bind(':password', password_hash($data['password'], PASSWORD_BCRYPT))
                ->bind(':email', $email)->execute();
        }
        return false;
    }
    
    /**
     * Lấy danh sách khách hàng mua nhiều nhất
     * @param int $limit
     * @return array
     */
    public function getTopCustomers($limit = 10) {
        return $this->db->query("SELECT u.id, u.full_name, u.email, u.phone, 
            COUNT(o.id) as order_count, 
            COALESCE(SUM(o.final_amount), 0) as total_spent 
        FROM users u 
        LEFT JOIN orders o ON u.id = o.user_id AND (o.status = 'delivered' OR o.payment_status = 'paid') 
        WHERE u.role_id = 3 
        GROUP BY u.id, u.full_name, u.email, u.phone 
        ORDER BY total_spent DESC, order_count DESC 
        LIMIT :limit")
            ->bind(':limit', $limit)->resultSet();
    }
}
?>
