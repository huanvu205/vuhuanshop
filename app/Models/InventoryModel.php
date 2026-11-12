<?php
class InventoryModel {
    private $db;
    public function __construct() { $this->db = new Database(); }
    
    public function getAll($page = 1, $limit = 20, $search = '', $lowStock = false) {
        $offset = ($page - 1) * $limit;
        $where = "WHERE 1=1";
        $params = [];
        if ($search) { $where .= " AND (p.name LIKE :s OR p.product_code LIKE :s)"; $params[':s'] = '%' . $search . '%'; }
        if ($lowStock) { $where .= " AND p.stock_quantity <= i.reorder_level"; }
        
        $stmt = $this->db->query("SELECT i.*, p.stock_quantity AS quantity, p.name, p.product_code, p.stock_quantity, b.name as brand_name, c.name as category_name FROM inventory i LEFT JOIN products p ON i.product_id = p.id LEFT JOIN brands b ON p.brand_id = b.id LEFT JOIN categories c ON p.category_id = c.id $where ORDER BY p.stock_quantity ASC LIMIT :limit OFFSET :offset")
            ->bind(':limit', $limit)->bind(':offset', $offset);
        foreach ($params as $k => $v) $stmt->bind($k, $v);
        return $stmt->resultSet();
    }
    
    public function count($search = '', $lowStock = false) {
        $where = "WHERE 1=1";
        if ($search) $where .= " AND (p.name LIKE '%" . addslashes($search) . "%')";
        if ($lowStock) $where .= " AND p.stock_quantity <= i.reorder_level";
        $result = $this->db->query("SELECT COUNT(*) as total FROM inventory i LEFT JOIN products p ON i.product_id = p.id $where")->single();
        return $result['total'] ?? 0;
    }
    
    public function findByProduct($productId) {
        return $this->db->query("SELECT * FROM inventory WHERE product_id=:pid")->bind(':pid', $productId)->single();
    }
    
    public function import($productId, $quantity, $notes, $createdBy) {
        $this->db->query("UPDATE inventory SET quantity=quantity+:qty WHERE product_id=:pid")->bind(':qty', $quantity)->bind(':pid', $productId)->execute();
        $this->db->query("UPDATE products SET stock_quantity=stock_quantity+:qty WHERE id=:pid")->bind(':qty', $quantity)->bind(':pid', $productId)->execute();
        $this->db->query("INSERT INTO inventory_logs (product_id, transaction_type, quantity, reference_type, notes, created_by) VALUES (:pid, 'in', :qty, 'manual_import', :notes, :by)")
            ->bind(':pid', $productId)->bind(':qty', $quantity)->bind(':notes', $notes)->bind(':by', $createdBy)->execute();
    }
    
    public function getLogs($productId = null, $page = 1, $limit = 30) {
        $offset = ($page - 1) * $limit;
        $where = $productId ? "WHERE il.product_id=:pid" : "";
        $stmt = $this->db->query("SELECT il.*, p.name as product_name, u.full_name as created_by_name FROM inventory_logs il LEFT JOIN products p ON il.product_id = p.id LEFT JOIN users u ON il.created_by = u.id $where ORDER BY il.created_at DESC LIMIT :limit OFFSET :offset")
            ->bind(':limit', $limit)->bind(':offset', $offset);
        if ($productId) $stmt->bind(':pid', $productId);
        return $stmt->resultSet();
    }
    
    public function updateReorderLevel($productId, $level) {
        return $this->db->query("UPDATE inventory SET reorder_level=:level WHERE product_id=:pid")->bind(':level', $level)->bind(':pid', $productId)->execute();
    }
    
    public function getLowStockProducts($threshold = null) {
        $where = $threshold ? "WHERE p.stock_quantity <= :threshold" : "WHERE p.stock_quantity <= i.reorder_level";
        $stmt = $this->db->query("SELECT i.*, p.stock_quantity AS quantity, p.name, p.product_code FROM inventory i LEFT JOIN products p ON i.product_id = p.id $where ORDER BY p.stock_quantity ASC");
        if ($threshold) $stmt->bind(':threshold', $threshold);
        return $stmt->resultSet();
    }
}
?>
