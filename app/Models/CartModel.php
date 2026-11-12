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
    
    public function addItem($cartId, $productId, $quantity, $price, $variantId = null) {
        $existing = $this->db->query("SELECT * FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id AND ((:variant_id IS NULL AND variant_id IS NULL) OR variant_id=:variant_id)")
            ->bind(':cart_id', $cartId)
            ->bind(':product_id', $productId)
            ->bind(':variant_id', $variantId)
            ->single();
        
        if ($existing) {
            $saved = $this->db->query("UPDATE cart_items SET quantity = quantity + :qty, price = :price WHERE id = :id")
                ->bind(':qty', $quantity)
                ->bind(':price', $price)
                ->bind(':id', $existing['id'])
                ->execute();
        } else {
            $saved = $this->db->query("INSERT INTO cart_items (cart_id, product_id, quantity, price, variant_id) VALUES (:cart_id, :product_id, :quantity, :price, :variant_id)")
                ->bind(':cart_id', $cartId)
                ->bind(':product_id', $productId)
                ->bind(':quantity', $quantity)
                ->bind(':price', $price)
                ->bind(':variant_id', $variantId)
                ->execute();
        }

        if (!$saved) {
            return false;
        }
        
        $this->updateTotals($cartId);
        return true;
    }

    public function getProductQuantity($cartId, $productId, $variantId = null) {
        $result = $this->db->query("SELECT COALESCE(SUM(quantity), 0) as quantity FROM cart_items WHERE cart_id=:cart_id AND product_id=:product_id AND ((:variant_id IS NULL AND variant_id IS NULL) OR variant_id=:variant_id)")
            ->bind(':cart_id', $cartId)->bind(':product_id', $productId)->bind(':variant_id', $variantId)->single();
        return (int)($result['quantity'] ?? 0);
    }

    public function getItemForCart($cartId, $itemId) {
        return $this->db->query("SELECT ci.id,ci.quantity,ci.variant_id,p.id AS product_id,p.status,CASE WHEN ci.variant_id IS NULL THEN p.stock_quantity ELSE v.stock_quantity END AS stock_quantity FROM cart_items ci INNER JOIN products p ON p.id=ci.product_id LEFT JOIN product_variants v ON v.id=ci.variant_id WHERE ci.id=:item_id AND ci.cart_id=:cart_id")
            ->bind(':item_id', $itemId)->bind(':cart_id', $cartId)->single();
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
        return $this->db->query("SELECT ci.*,p.name AS product_name,p.slug AS product_slug,p.main_image,CASE WHEN ci.variant_id IS NULL THEN p.stock_quantity ELSE v.stock_quantity END AS stock_quantity,p.price_sale,p.price_discount,p.status,b.name AS brand_name,v.variant_name,v.variant_name AS variant,v.sku AS variant_sku,v.price_adjustment FROM cart_items ci LEFT JOIN products p ON ci.product_id=p.id LEFT JOIN product_variants v ON v.id=ci.variant_id LEFT JOIN brands b ON p.brand_id=b.id WHERE ci.cart_id=:cart_id")
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
        // A failed query also returns an empty result set in Database. Do not
        // delete the guest cart if its stored totals show that it has items.
        if (empty($guestItems) && (int)($guestCart['total_items'] ?? 0) > 0) {
            error_log('Guest cart merge skipped: cart items could not be read for cart ' . (int)$guestCart['id']);
            return;
        }

        foreach ($guestItems as $item) {
            if (!$this->addItem($userCart['id'], $item['product_id'], $item['quantity'], $item['price'], $item['variant_id'] ?? null)) {
                error_log('Guest cart merge stopped: item could not be saved for cart ' . (int)$guestCart['id']);
                return;
            }

            // Remove each successfully copied row so a later retry cannot
            // merge it twice if another row fails partway through the merge.
            $removed = $this->db->query("DELETE FROM cart_items WHERE id = :id AND cart_id = :cart_id")
                ->bind(':id', $item['id'])
                ->bind(':cart_id', $guestCart['id'])
                ->execute();
            if (!$removed) {
                error_log('Guest cart merge stopped: copied item could not be cleared from source cart ' . (int)$guestCart['id']);
                return;
            }
        }

        $this->updateTotals($guestCart['id']);
        
        $this->db->query("DELETE FROM carts WHERE session_id = :session_id")->bind(':session_id', $sessionId)->execute();
    }
    
    public function getItemCount($cartId) {
        $result = $this->db->query("SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = :cart_id")->bind(':cart_id', $cartId)->single();
        return $result['total'] ?? 0;
    }
}
?>
