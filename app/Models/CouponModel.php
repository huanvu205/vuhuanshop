<?php
class CouponModel {
    private $db;
    public function __construct() { $this->db = new Database(); }
    
    public function findByCode($code) {
        $code = strtoupper(trim($code));
        return $this->db->query("SELECT * FROM coupons WHERE coupon_code=:code AND status='active' AND start_date <= NOW() AND end_date >= NOW() AND (max_uses IS NULL OR used_count < max_uses)")
            ->bind(':code', $code)->single();
    }
    
    public function isValidForUser($couponId, $userId) {
        $result = $this->db->query("SELECT id FROM coupon_uses WHERE coupon_id=:cid AND user_id=:uid")
            ->bind(':cid', $couponId)->bind(':uid', $userId)->single();
        return $result === false;
    }
    
    public function calculateDiscount($coupon, $orderAmount) {
        if ($coupon['min_order_amount'] && $orderAmount < $coupon['min_order_amount']) return 0;
        
        if ($coupon['discount_type'] === 'percent') {
            $discount = $orderAmount * $coupon['discount_value'] / 100;
            if ($coupon['max_discount']) $discount = min($discount, $coupon['max_discount']);
        } else {
            $discount = $coupon['discount_value'];
        }
        
        return min($discount, $orderAmount);
    }
    
    public function use($couponId, $orderId, $userId, $discountAmount) {
        $coupon = $this->db->query("SELECT used_count, max_uses FROM coupons WHERE id=:id FOR UPDATE")
            ->bind(':id', $couponId)->single();
        if (!$coupon || (!empty($coupon['max_uses']) && (int)$coupon['used_count'] >= (int)$coupon['max_uses'])) {
            return false;
        }
        $this->db->query("INSERT INTO coupon_uses (coupon_id, order_id, user_id, discount_amount) VALUES (:cid, :oid, :uid, :amount)")
            ->bind(':cid', $couponId)->bind(':oid', $orderId)->bind(':uid', $userId)->bind(':amount', $discountAmount)->execute();
        $this->db->query("UPDATE coupons SET used_count=used_count+1 WHERE id=:id")->bind(':id', $couponId)->execute();
        return true;
    }
    
    public function getAll($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        return $this->db->query("SELECT * FROM coupons ORDER BY created_at DESC LIMIT :limit OFFSET :offset")
            ->bind(':limit', $limit)->bind(':offset', $offset)->resultSet();
    }
    
    public function findById($id) {
        return $this->db->query("SELECT * FROM coupons WHERE id=:id")->bind(':id', $id)->single();
    }
    
    public function create($data) {
        $this->db->query("INSERT INTO coupons (coupon_code, description, discount_type, discount_value, max_discount, min_order_amount, max_uses, start_date, end_date, status) VALUES (:code, :desc, :type, :value, :max_discount, :min_order, :max_uses, :start, :end, :status)")
            ->bind(':code', strtoupper($data['coupon_code']))
            ->bind(':desc', $data['description'] ?? null)
            ->bind(':type', $data['discount_type'])
            ->bind(':value', $data['discount_value'])
            ->bind(':max_discount', $data['max_discount'] ?? null)
            ->bind(':min_order', $data['min_order_amount'] ?? null)
            ->bind(':max_uses', $data['max_uses'] ?? null)
            ->bind(':start', $data['start_date'])
            ->bind(':end', $data['end_date'])
            ->bind(':status', $data['status'] ?? 'active')
            ->execute();
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        return $this->db->query("UPDATE coupons SET description=:desc, discount_type=:type, discount_value=:value, max_discount=:max_discount, min_order_amount=:min_order, max_uses=:max_uses, start_date=:start, end_date=:end, status=:status WHERE id=:id")
            ->bind(':desc', $data['description'] ?? null)
            ->bind(':type', $data['discount_type'])
            ->bind(':value', $data['discount_value'])
            ->bind(':max_discount', $data['max_discount'] ?? null)
            ->bind(':min_order', $data['min_order_amount'] ?? null)
            ->bind(':max_uses', $data['max_uses'] ?? null)
            ->bind(':start', $data['start_date'])
            ->bind(':end', $data['end_date'])
            ->bind(':status', $data['status'] ?? 'active')
            ->bind(':id', $id)->execute();
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM coupons WHERE id=:id")->bind(':id', $id)->execute();
    }
    
    public function count() {
        $result = $this->db->query("SELECT COUNT(*) as total FROM coupons")->single();
        return $result['total'] ?? 0;
    }
}
?>
