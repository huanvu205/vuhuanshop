<?php
/**
 * UserModel - Quản lý người dùng
 */
class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function findById($id) {
        return $this->db->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = :id")
            ->bind(':id', $id)->single();
    }
    
    public function findByEmail($email) {
        return $this->db->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.email = :email")
            ->bind(':email', $email)->single();
    }
    
    public function findByUsername($username) {
        return $this->db->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.username = :username")
            ->bind(':username', $username)->single();
    }
    
    public function findByEmailOrUsername($login) {
        return $this->db->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.email = :email OR u.username = :username")
            ->bind(':email', $login)->bind(':username', $login)->single();
    }
    
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
        
        return $stmt->execute();
    }
    
    public function updatePassword($id, $newPassword) {
        return $this->db->query("UPDATE users SET password=:password WHERE id=:id")
            ->bind(':password', password_hash($newPassword, PASSWORD_BCRYPT))
            ->bind(':id', $id)
            ->execute();
    }
    
    public function updateAvatar($id, $avatar) {
        return $this->db->query("UPDATE users SET avatar=:avatar WHERE id=:id")
            ->bind(':avatar', $avatar)->bind(':id', $id)->execute();
    }
    
    public function updateLastLogin($id) {
        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            return $this->db->query("UPDATE users SET last_login=NOW() WHERE id=:id")
                ->bind(':id', $id)->execute();
        } else {
            return $this->db->query("UPDATE users SET last_login=GETDATE() WHERE id=:id")
                ->bind(':id', $id)->execute();
        }
    }
    
    public function verifyPassword($user, $password) {
        return password_verify($password, $user['password']);
    }
    
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
        return $result['total'] ?? 0;
    }
    
    public function setStatus($id, $status) {
        return $this->db->query("UPDATE users SET status=:status WHERE id=:id")
            ->bind(':status', $status)->bind(':id', $id)->execute();
    }
    
    public function getStats() {
        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            return $this->db->query("SELECT COUNT(*) as total, SUM(CASE WHEN status='active' THEN 1 ELSE 0 END) as active, SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today FROM users WHERE role_id = 3")->single();
        } else {
            return $this->db->query("SELECT COUNT(*) as total, SUM(CASE WHEN status='active' THEN 1 ELSE 0 END) as active, SUM(CASE WHEN CONVERT(date, created_at) = CONVERT(date, GETDATE()) THEN 1 ELSE 0 END) as today FROM users WHERE role_id = 3")->single();
        }
    }
    
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->db->query($sql)->bind(':email', $email);
        if ($excludeId) {
            $stmt = $this->db->query($sql . " AND id != :id")->bind(':email', $email)->bind(':id', $excludeId);
        }
        return $stmt->single() !== false;
    }
    
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT id FROM users WHERE username = :username";
        $stmt = $this->db->query($sql)->bind(':username', $username);
        if ($excludeId) {
            $stmt = $this->db->query($sql . " AND id != :id")->bind(':username', $username)->bind(':id', $excludeId);
        }
        return $stmt->single() !== false;
    }
    
    public function saveResetToken($email, $token) {
        // Store token in session for simplicity (in production use a DB table)
        $_SESSION['reset_tokens'][$token] = ['email' => $email, 'expires' => time() + 3600];
        return true;
    }
    
    public function verifyResetToken($token) {
        if (isset($_SESSION['reset_tokens'][$token])) {
            $data = $_SESSION['reset_tokens'][$token];
            if ($data['expires'] > time()) {
                return $data['email'];
            }
        }
        return false;
    }
    
    public function updateByEmail($email, $data) {
        if (isset($data['password'])) {
            return $this->db->query("UPDATE users SET password=:password WHERE email=:email")
                ->bind(':password', password_hash($data['password'], PASSWORD_BCRYPT))
                ->bind(':email', $email)->execute();
        }
        return false;
    }
    
    public function getTopCustomers($limit = 10) {
        return $this->db->query("SELECT u.id, u.full_name, u.email, u.phone, COUNT(o.id) as order_count, SUM(o.final_amount) as total_spent FROM users u LEFT JOIN orders o ON u.id = o.user_id WHERE u.role_id = 3 GROUP BY u.id, u.full_name, u.email, u.phone ORDER BY total_spent DESC LIMIT :limit")
            ->bind(':limit', $limit)->resultSet();
    }
}
?>
