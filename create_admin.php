<?php
/**
 * Script khởi tạo/khôi phục tài khoản Admin trên Hosting
 */
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/core/Config.php';
require_once __DIR__ . '/core/Database.php';

$db = new Database();
echo "<pre>=== KHỞI TẠO TÀI KHOẢN ADMIN ===\n";

try {
    // 1. Kiểm tra và nạp bảng vai trò (roles) nếu chưa có dữ liệu
    $rolesCount = $db->query("SELECT COUNT(*) as count FROM roles")->single();
    if ($rolesCount['count'] == 0) {
        echo "Đang nạp dữ liệu bảng vai trò (roles)...\n";
        $db->query("INSERT INTO roles (id, name, description) VALUES 
            (1, 'admin', 'Quản trị viên'),
            (2, 'employee', 'Nhân viên'),
            (3, 'customer', 'Khách hàng'),
            (4, 'guest', 'Khách vãng lai')")->execute();
    }

    // 2. Khởi tạo tài khoản admin mặc định
    $admin = $db->query("SELECT * FROM users WHERE email = 'admin@vuhuanshop.vn' OR username = 'admin'")->single();
    $hashedPassword = password_hash('Admin@123', PASSWORD_BCRYPT);

    if ($admin) {
        echo "Tài khoản admin đã tồn tại. Đang khôi phục mật khẩu về 'Admin@123' và kích hoạt hoạt động...\n";
        $db->query("UPDATE users SET password = :password, role_id = 1, status = 'active' WHERE id = :id")
            ->bind(':password', $hashedPassword)
            ->bind(':id', $admin['id'])
            ->execute();
        echo "Khôi phục tài khoản admin thành công!\n";
    } else {
        echo "Tài khoản admin chưa tồn tại. Đang tạo mới...\n";
        $db->query("INSERT INTO users (username, email, password, full_name, phone, role_id, status) VALUES 
            ('admin', 'admin@vuhuanshop.vn', :password, 'Admin', '0123456789', 1, 'active')")
            ->bind(':password', $hashedPassword)
            ->execute();
        echo "Tạo tài khoản admin mới thành công!\n";
    }

    // 3. Hiển thị danh sách tài khoản hiện tại để xác nhận
    $users = $db->query("SELECT u.id, u.username, u.email, r.name as role_name, u.status FROM users u LEFT JOIN roles r ON u.role_id = r.id")->resultSet();
    echo "\n=== DANH SÁCH TÀI KHOẢN TRÊN HỆ THỐNG ===\n";
    foreach ($users as $u) {
        echo "ID: {$u['id']} | Username: {$u['username']} | Email: {$u['email']} | Quyền: {$u['role_name']} | Trạng thái: {$u['status']}\n";
    }

} catch (Exception $e) {
    echo "LỖI TRUY VẤN: " . $e->getMessage() . "\n";
}
echo "</pre>";
?>
