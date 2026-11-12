<?php
class WishlistModel {
    private $db;
    public function __construct() { $this->db = new Database(); }
    
    public function add($userId, $productId) {
        if ($this->isWishlisted($userId, $productId)) return true;
        return $this->db->query("INSERT INTO wishlists (user_id, product_id) VALUES (:uid, :pid)")
            ->bind(':uid', $userId)->bind(':pid', $productId)->execute();
    }
    
    public function remove($userId, $productId) {
        return $this->db->query("DELETE FROM wishlists WHERE user_id=:uid AND product_id=:pid")
            ->bind(':uid', $userId)->bind(':pid', $productId)->execute();
    }
    
    public function isWishlisted($userId, $productId) {
        $result = $this->db->query("SELECT id FROM wishlists WHERE user_id=:uid AND product_id=:pid")
            ->bind(':uid', $userId)->bind(':pid', $productId)->single();
        return $result !== false;
    }
    
    public function getUserWishlist($userId) {
        return $this->db->query("SELECT w.*, p.name, p.slug, p.main_image, p.price_sale, p.price_discount, p.stock_quantity, b.name as brand_name FROM wishlists w LEFT JOIN products p ON w.product_id = p.id LEFT JOIN brands b ON p.brand_id = b.id WHERE w.user_id = :uid ORDER BY w.created_at DESC")
            ->bind(':uid', $userId)->resultSet();
    }
    
    public function clearAll($userId) {
        return $this->db->query("DELETE FROM wishlists WHERE user_id=:uid")
            ->bind(':uid', $userId)->execute();
    }
    
    public function toggle($userId, $productId) {
        if ($this->isWishlisted($userId, $productId)) {
            $this->remove($userId, $productId);
            return false;
        } else {
            $this->add($userId, $productId);
            return true;
        }
    }
}
?>
