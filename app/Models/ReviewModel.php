<?php
/**
 * ReviewModel - Quản lý đánh giá sản phẩm
 */
class ReviewModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getByProduct($productId, $page = 1, $limit = 5) {
        $offset = ($page - 1) * $limit;
        return $this->db->query("SELECT r.*, u.full_name, u.avatar FROM reviews r LEFT JOIN users u ON r.user_id = u.id WHERE r.product_id = :product_id AND r.status = 'approved' ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset")
            ->bind(':product_id', $productId)->bind(':limit', $limit)->bind(':offset', $offset)->resultSet();
    }
    
    public function countByProduct($productId) {
        $result = $this->db->query("SELECT COUNT(*) as total FROM reviews WHERE product_id = :product_id AND status = 'approved'")
            ->bind(':product_id', $productId)->single();
        return $result['total'] ?? 0;
    }
    
    public function getRatingStats($productId) {
        return $this->db->query("SELECT rating, COUNT(*) as count FROM reviews WHERE product_id = :product_id AND status = 'approved' GROUP BY rating ORDER BY rating DESC")
            ->bind(':product_id', $productId)->resultSet();
    }
    
    public function create($data) {
        $this->db->query("INSERT INTO reviews (product_id, user_id, order_id, rating, title, comment, status) VALUES (:product_id, :user_id, :order_id, :rating, :title, :comment, 'pending')")
            ->bind(':product_id', $data['product_id'])
            ->bind(':user_id', $data['user_id'])
            ->bind(':order_id', $data['order_id'] ?? null)
            ->bind(':rating', $data['rating'])
            ->bind(':title', $data['title'] ?? null)
            ->bind(':comment', $data['comment'])
            ->execute();
        return $this->db->lastInsertId();
    }
    
    public function updateStatus($id, $status) {
        $result = $this->db->query("UPDATE reviews SET status=:status WHERE id=:id")
            ->bind(':status', $status)->bind(':id', $id)->execute();
        
        if ($result) {
            // Update product rating
            $review = $this->findById($id);
            if ($review) {
                $this->updateProductRating($review['product_id']);
            }
        }
        
        return $result;
    }
    
    public function updateProductRating($productId) {
        return $this->db->query("UPDATE products SET rating_avg = (SELECT AVG(rating) FROM reviews WHERE product_id = :pid AND status = 'approved'), rating_count = (SELECT COUNT(*) FROM reviews WHERE product_id = :pid AND status = 'approved') WHERE id = :pid")
            ->bind(':pid', $productId)->execute();
    }
    
    public function findById($id) {
        return $this->db->query("SELECT r.*, u.full_name, p.name as product_name FROM reviews r LEFT JOIN users u ON r.user_id = u.id LEFT JOIN products p ON r.product_id = p.id WHERE r.id = :id")
            ->bind(':id', $id)->single();
    }
    
    public function getAll($page = 1, $limit = 20, $search = '', $status = '', $rating = '') {
        $offset = ($page - 1) * $limit;
        $where = "WHERE 1=1";
        $params = [];
        if ($search) {
            $where .= " AND (u.full_name LIKE :search OR p.name LIKE :search OR r.comment LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if ($status) { $where .= " AND r.status = :status"; $params[':status'] = $status; }
        if ($rating) { $where .= " AND r.rating = :rating"; $params[':rating'] = $rating; }
        
        $stmt = $this->db->query("SELECT r.*, u.full_name, p.name as product_name FROM reviews r LEFT JOIN users u ON r.user_id = u.id LEFT JOIN products p ON r.product_id = p.id $where ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset")
            ->bind(':limit', $limit)->bind(':offset', $offset);
        foreach ($params as $k => $v) $stmt->bind($k, $v);
        return $stmt->resultSet();
    }
    
    public function count($search = '', $status = '', $rating = '') {
        $where = "WHERE 1=1";
        $params = [];
        if ($search) { $where .= " AND (u.full_name LIKE :search OR p.name LIKE :search)"; $params[':search'] = '%' . $search . '%'; }
        if ($status) { $where .= " AND r.status = :status"; $params[':status'] = $status; }
        if ($rating) { $where .= " AND r.rating = :rating"; $params[':rating'] = $rating; }
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM reviews r LEFT JOIN users u ON r.user_id = u.id LEFT JOIN products p ON r.product_id = p.id $where");
        foreach ($params as $k => $v) $stmt->bind($k, $v);
        $result = $stmt->single();
        return $result['total'] ?? 0;
    }
    
    public function delete($id) {
        $review = $this->findById($id);
        $result = $this->db->query("DELETE FROM reviews WHERE id=:id")->bind(':id', $id)->execute();
        if ($result && $review) $this->updateProductRating($review['product_id']);
        return $result;
    }
    
    public function hasReviewed($userId, $productId) {
        $result = $this->db->query("SELECT id FROM reviews WHERE user_id=:uid AND product_id=:pid")
            ->bind(':uid', $userId)->bind(':pid', $productId)->single();
        return $result !== false;
    }

    /** UC09: only allow a review for an item in a delivered order owned by the user. */
    public function hasDeliveredPurchase($userId, $productId, $orderId = null) {
        $sql = "SELECT od.id FROM order_details od INNER JOIN orders o ON o.id = od.order_id
                WHERE o.user_id = :uid AND od.product_id = :pid AND o.status = 'delivered'";
        if ($orderId) $sql .= " AND o.id = :oid";
        $stmt = $this->db->query($sql)->bind(':uid', $userId)->bind(':pid', $productId);
        if ($orderId) $stmt->bind(':oid', $orderId);
        return $stmt->single() !== false;
    }
    
    public function getUserReviews($userId) {
        return $this->db->query("SELECT r.*, p.name as product_name, p.main_image, p.slug as product_slug FROM reviews r LEFT JOIN products p ON r.product_id = p.id WHERE r.user_id = :uid ORDER BY r.created_at DESC")
            ->bind(':uid', $userId)->resultSet();
    }
}
?>
