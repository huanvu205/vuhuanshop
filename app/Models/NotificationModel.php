<?php
class NotificationModel {
    private $db;
    public function __construct() { $this->db = new Database(); }
    
    public function create($userId, $type, $title, $message, $data = null) {
        return $this->db->query("INSERT INTO notifications (user_id, type, title, message, data) VALUES (:uid, :type, :title, :message, :data)")
            ->bind(':uid', $userId)->bind(':type', $type)->bind(':title', $title)->bind(':message', $message)
            ->bind(':data', $data ? json_encode($data) : null)->execute();
    }

    public function createForStaff($type, $title, $message, $data = null) {
        $staff = $this->db->query("SELECT u.id FROM users u INNER JOIN roles r ON r.id = u.role_id WHERE r.name IN ('admin', 'staff')")->resultSet();
        $created = 0;
        foreach ($staff as $user) {
            if ($this->create((int)$user['id'], $type, $title, $message, $data)) {
                $created++;
            }
        }
        return $created;
    }
    
    public function getUserNotifications($userId, $limit = 10) {
        return $this->db->query("SELECT * FROM notifications WHERE user_id=:uid ORDER BY created_at DESC LIMIT :limit")
            ->bind(':uid', $userId)->bind(':limit', $limit)->resultSet();
    }
    
    public function getUnreadCount($userId) {
        $result = $this->db->query("SELECT COUNT(*) as total FROM notifications WHERE user_id=:uid AND is_read=0")->bind(':uid', $userId)->single();
        return $result['total'] ?? 0;
    }
    
    public function markRead($id, $userId) {
        return $this->db->query("UPDATE notifications SET is_read=1, read_at=NOW() WHERE id=:id AND user_id=:uid")
            ->bind(':id', $id)->bind(':uid', $userId)->execute();
    }
    
    public function markAllRead($userId) {
        return $this->db->query("UPDATE notifications SET is_read=1, read_at=NOW() WHERE user_id=:uid AND is_read=0")
            ->bind(':uid', $userId)->execute();
    }
}
?>
