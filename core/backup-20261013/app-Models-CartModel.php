<?php
/**
 * CartModel - Quản lý giỏ hàng
 */
class CartModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getOrCreate($userId = null, $sessionId = null) {
        if ($userId) {
            $cart = $this->db->query("SELECT * FROM carts WHERE user_id = :user_id ORDER BY id DESC LIMIT 1")
                ->bind(':user_id', $userId)->single();
        } else {
            $cart = $this->db->query("SELECT * FROM carts WHERE session_id = :session_id ORDER BY id DESC LIMIT 1")
                ->bind(':session_id', $sessionId)->single();
        }
        
        if (!$cart) {
            $this->db->query("INSERT INTO carts (user_id, session_id) VALUES (:user_id, :session_id)")
                ->bind(':user_id', $userId)->bind(':session_id', $sessionId)->execute();
            $cartId = $this->db->lastInsertId();
            return ['id' => $cartId, 'user_id' => $userId, 'session_id' => $sessionId, 'total_items' => 0, 'total_price' => 0];
        }
        
        return $cart;
    }
    
    public function addItem($cartId, $productId, $quantity, $price) {
        $existing = $this->db->query("SELECT * FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id")
            ->bind(':cart_id', $cartId)->bind(':product_id', $productId)->single();
        
        if ($existing) {
            $this->db->query("UPDATE cart_items SET quantity = quantity + :qty WHERE cart_id = :cart_id AND product_id = :product_id")
                ->bind(':qty', $quantity)->bind(':cart_id', $cartId)->bind(':product_id', $productId)->execute();
        } else {
            $this->db->query("INSERT INTO cart_items (cart_id, product_id, quantity, price) VALUES (:cart_id, :product_id, :quantity, :price)")
                ->bind(':cart_id', $cartId)->bind(':product_id', $productId)->bind(':quantity', $quantity)->bind(':price', $price)->execute();
        }
        
        $this->updateTotals($cartId);
    }
    
    public function updateItem($cartId, $itemId, $quantity) {
        if ($quantity <= 0) {
            $this->removeItem($cartId, $itemId);
            return;
        }
        $this->db->query("UPDATE cart_items SET quantity = :qty WHERE id = :id AND cart_id = :cart_id")
            ->bind(':qty', $quantity)->bind(':id', $itemId)->bind(':cart_id', $cartId)->execute();
        $this->updateTotals($cartId);
    }
    
    public function removeItem($cartId, $itemId) {
        $this->db->query("DELETE FROM cart_items WHERE id = :id AND cart_id = :cart_id")
            ->bind(':id', $itemId)->bind(':cart_id', $cartId)->execute();
        $this->updateTotals($cartId);
    }
    
    public function getItems($cartId) {
        return $this->db->query("SELECT ci.*, p.name as product_name, p.slug as product_slug, p.main_image, p.stock_quantity, p.price_sale, p.price_discount, b.name as brand_name FROM cart_items ci LEFT JOIN products p ON ci.product_id = p.id LEFT JOIN brands b ON p.brand_id = b.id WHERE ci.cart_id = :cart_id")
            ->bind(':cart_id', $cartId)->resultSet();
    }
    
    public function updateTotals($cartId) {
        $result = $this->db->query("SELECT SUM(quantity) as total_items, SUM(quantity * price) as total_price FROM cart_items WHERE cart_id = :cart_id")
            ->bind(':cart_id', $cartId)->single();
        
        $this->db->query("UPDATE carts SET total_items = :items, total_price = :price WHERE id = :id")
            ->bind(':items', $result['total_items'] ?? 0)
            ->bind(':price', $result['total_price'] ?? 0)
            ->bind(':id', $cartId)->execute();
    }
    
    public function clearCart($cartId) {
        $this->db->query("DELETE FROM cart_items WHERE cart_id = :cart_id")->bind(':cart_id', $cartId)->execute();
        $this->db->query("UPDATE carts SET total_items=0, total_price=0 WHERE id=:id")->bind(':id', $cartId)->execute();
    }
    
    public function getCart($cartId) {
        return $this->db->query("SELECT * FROM carts WHERE id = :id")->bind(':id', $cartId)->single();
    }
    
    public function mergeGuestCart($userId, $sessionId) {
        $guestCart = $this->db->query("SELECT * FROM carts WHERE session_id = :session_id")->bind(':session_id', $sessionId)->single();
        $userCart = $this->getOrCreate($userId);
        
        if (!$guestCart) return;
        
        $guestItems = $this->getItems($guestCart['id']);
        foreach ($guestItems as $item) {
            $this->addItem($userCart['id'], $item['product_id'], $item['quantity'], $item['price']);
        }
        
        $this->db->query("DELETE FROM carts WHERE session_id = :session_id")->bind(':session_id', $sessionId)->execute();
    }
    
    public function getItemCount($cartId) {
        $result = $this->db->query("SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = :cart_id")->bind(':cart_id', $cartId)->single();
        return $result['total'] ?? 0;
    }
}
?>
