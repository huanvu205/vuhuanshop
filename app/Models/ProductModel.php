<?php
require_once __DIR__.'/../Helpers/Helper.php';
/**
 * ProductModel - Quản lý sản phẩm
 */
class ProductModel {
    /** @var Database */
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }

    public function getVariants($productId, $activeOnly = true) {
        $sql = "SELECT * FROM product_variants WHERE product_id=:id" . ($activeOnly ? " AND is_active=1" : "") . " ORDER BY id ASC";
        return $this->db->query($sql)->bind(':id', $productId)->resultSet();
    }

    public function getVariantById($variantId, $productId = null) {
        $sql = "SELECT * FROM product_variants WHERE id=:id AND is_active=1";
        if ($productId !== null) $sql .= " AND product_id=:product_id";
        $stmt = $this->db->query($sql)->bind(':id', $variantId);
        if ($productId !== null) $stmt->bind(':product_id', $productId);
        return $stmt->single();
    }

    public function saveVariants($productId, array $rows) {
        $keptIds = [];
        foreach ($rows as $row) {
            $sku = trim((string)($row['sku'] ?? ''));
            $name = trim((string)($row['name'] ?? ''));
            if ($sku === '' && $name === '') continue;
            if ($sku === '' || $name === '') return false;
            $attrs = trim((string)($row['attributes_json'] ?? ''));
            if ($attrs !== '' && json_decode($attrs, true) === null) return false;
            $variantId = (int)($row['id'] ?? 0);
            if ($variantId > 0) {
                $this->db->query("UPDATE product_variants SET sku=:sku,variant_name=:name,attributes_json=:attrs,price_adjustment=:adjustment,stock_quantity=:stock,is_active=1,updated_at=CURRENT_TIMESTAMP WHERE id=:id AND product_id=:product")
                    ->bind(':sku', $sku)->bind(':name', $name)->bind(':attrs', $attrs ?: null)
                    ->bind(':adjustment', (float)($row['price_adjustment'] ?? 0))->bind(':stock', max(0, (int)($row['stock_quantity'] ?? 0)))
                    ->bind(':id', $variantId)->bind(':product', $productId)->execute();
                $keptIds[] = $variantId;
            } else {
                $this->db->query("INSERT INTO product_variants (product_id,sku,variant_name,attributes_json,price_adjustment,stock_quantity,is_active) VALUES (:product,:sku,:name,:attrs,:adjustment,:stock,1)")
                    ->bind(':product', $productId)->bind(':sku', $sku)->bind(':name', $name)->bind(':attrs', $attrs ?: null)
                    ->bind(':adjustment', (float)($row['price_adjustment'] ?? 0))->bind(':stock', max(0, (int)($row['stock_quantity'] ?? 0)))->execute();
                $newId = (int)$this->db->lastInsertId();
                if ($newId > 0) $keptIds[] = $newId;
            }
        }
        if ($keptIds) {
            $placeholders = [];
            foreach ($keptIds as $i => $id) $placeholders[] = (int)$id;
            $this->db->query("UPDATE product_variants SET is_active=0,updated_at=CURRENT_TIMESTAMP WHERE product_id=:product AND id NOT IN (" . implode(',', $placeholders) . ")")
                ->bind(':product', $productId)->execute();
        } else {
            $this->db->query("UPDATE product_variants SET is_active=0,updated_at=CURRENT_TIMESTAMP WHERE product_id=:product")
                ->bind(':product', $productId)->execute();
        }
        $stock = $this->db->query("SELECT COUNT(*) AS total,COALESCE(SUM(stock_quantity),0) AS quantity FROM product_variants WHERE product_id=:product AND is_active=1")
            ->bind(':product', $productId)->single();
        if ((int)($stock['total'] ?? 0) > 0) {
            $this->db->query("UPDATE products SET stock_quantity=:quantity WHERE id=:product")
                ->bind(':quantity', (int)$stock['quantity'])->bind(':product', $productId)->execute();
            $this->db->query("UPDATE inventory SET quantity=:quantity WHERE product_id=:product")
                ->bind(':quantity', (int)$stock['quantity'])->bind(':product', $productId)->execute();
        }
        return true;
    }
    
    /**
     * Lấy danh sách sản phẩm với filter
     */
    public function getList($filters = [], $page = 1, $limit = 12, $sort = 'newest') {
        $offset = ($page - 1) * $limit;
        $where = ["p.status = 'active'"];
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        if (!empty($filters['brand_id'])) {
            $where[] = "p.brand_id = :brand_id";
            $params[':brand_id'] = $filters['brand_id'];
        }
        if (!empty($filters['min_price'])) {
            $where[] = "COALESCE(p.price_discount, p.price_sale) >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where[] = "COALESCE(p.price_discount, p.price_sale) <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE :search1 OR p.product_code LIKE :search2 OR b.name LIKE :search3)";
            $sVal = '%' . trim($filters['search']) . '%';
            $params[':search1'] = $sVal;
            $params[':search2'] = $sVal;
            $params[':search3'] = $sVal;
        }
        
        $whereStr = "WHERE " . implode(" AND ", $where);
        
        $orderBy = match($sort) {
            'price_asc' => 'COALESCE(p.price_discount, p.price_sale) ASC',
            'price_desc' => 'COALESCE(p.price_discount, p.price_sale) DESC',
            'best_selling' => 'p.view_count DESC',
            'rating' => 'p.rating_avg DESC',
            default => 'p.created_at DESC',
        };
        
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name, b.slug as brand_slug, b.logo as brand_logo FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                $whereStr 
                ORDER BY $orderBy 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->query($sql)->bind(':limit', $limit)->bind(':offset', $offset);
        foreach ($params as $k => $v) $stmt->bind($k, $v);
        return $stmt->resultSet();
    }
    
    public function countList($filters = []) {
        $where = ["p.status = 'active'"];
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        if (!empty($filters['brand_id'])) {
            $where[] = "p.brand_id = :brand_id";
            $params[':brand_id'] = $filters['brand_id'];
        }
        if (!empty($filters['min_price'])) {
            $where[] = "COALESCE(p.price_discount, p.price_sale) >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where[] = "COALESCE(p.price_discount, p.price_sale) <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE :search1 OR p.product_code LIKE :search2 OR b.name LIKE :search3)";
            $sVal = '%' . trim($filters['search']) . '%';
            $params[':search1'] = $sVal;
            $params[':search2'] = $sVal;
            $params[':search3'] = $sVal;
        }
        
        $whereStr = "WHERE " . implode(" AND ", $where);
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM products p LEFT JOIN brands b ON p.brand_id = b.id $whereStr");
        foreach ($params as $k => $v) $stmt->bind($k, $v);
        $result = $stmt->single();
        return $result['total'] ?? 0;
    }
    
    /**
     * @param int|string $id
     * @return array|false
     */
    public function findById($id) {
        return $this->db->query("SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name, b.slug as brand_slug, b.logo as brand_logo FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE p.id = :id")
            ->bind(':id', $id)->single();
    }
    
    /**
     * @param string $slug
     * @return array|false
     */
    public function findBySlug($slug) {
        return $this->db->query("SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name, b.slug as brand_slug, b.logo as brand_logo FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE p.slug = :slug AND p.status = 'active'")
            ->bind(':slug', $slug)->single();
    }
    
    public function getFeatured($limit = 8) {
        return $this->db->query("SELECT p.*, b.name as brand_name FROM products p LEFT JOIN brands b ON p.brand_id = b.id WHERE p.status='active' AND p.is_featured=1 ORDER BY p.created_at DESC LIMIT :limit")
            ->bind(':limit', $limit)->resultSet();
    }
    
    public function getNew($limit = 8) {
        return $this->db->query("SELECT p.*, b.name as brand_name FROM products p LEFT JOIN brands b ON p.brand_id = b.id WHERE p.status='active' ORDER BY p.is_newest DESC, p.created_at DESC LIMIT :limit")
            ->bind(':limit', $limit)->resultSet();
    }
    
    public function getBestSelling($limit = 8) {
        return $this->db->query("SELECT p.*, b.name as brand_name, (SELECT COALESCE(SUM(od.quantity), 0) FROM order_details od WHERE od.product_id = p.id) as total_sold FROM products p LEFT JOIN brands b ON p.brand_id = b.id WHERE p.status='active' ORDER BY p.is_best_selling DESC, total_sold DESC LIMIT :limit")
            ->bind(':limit', $limit)->resultSet();
    }
    
    public function getHotDeal($limit = 8) {
        return $this->db->query("SELECT p.*, b.name as brand_name FROM products p LEFT JOIN brands b ON p.brand_id = b.id WHERE p.status='active' AND p.is_hot_deal=1 AND p.price_discount IS NOT NULL ORDER BY p.discount_percent DESC LIMIT :limit")
            ->bind(':limit', $limit)->resultSet();
    }
    
    /**
     * @param int|string $productId
     * @param int|string $categoryId
     * @param int $limit
     * @return array
     */
    public function getRelated($productId, $categoryId, $limit = 6) {
        return $this->db->query("SELECT p.*, b.name as brand_name FROM products p LEFT JOIN brands b ON p.brand_id = b.id WHERE p.status='active' AND p.category_id = :cat_id AND p.id != :id ORDER BY RAND() LIMIT :limit")
            ->bind(':cat_id', $categoryId)->bind(':id', $productId)->bind(':limit', $limit)->resultSet();
    }
    
    /**
     * @param string $keyword
     * @param int $limit
     * @return array
     */
    public function search($keyword, $limit = 10) {
        $limit = max(1, (int)$limit);
        $cleanKw = trim($keyword);
        $kw = '%' . $cleanKw . '%';
        $sql = "SELECT p.*, b.name as brand_name FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.status = 'active' 
                AND (p.name LIKE :kw1 OR p.product_code LIKE :kw2 OR b.name LIKE :kw3) 
                ORDER BY p.rating_avg DESC 
                LIMIT {$limit}";
        return $this->db->query($sql)
            ->bind(':kw1', $kw)
            ->bind(':kw2', $kw)
            ->bind(':kw3', $kw)
            ->resultSet();
    }
    
    /**
     * @param int|string $id
     * @return void
     */
    public function incrementView($id) {
        $this->db->query("UPDATE products SET view_count = view_count + 1 WHERE id = :id")->bind(':id', $id)->execute();
    }
    
    /**
     * @param int|string $productId
     * @return array
     */
    public function getImages($productId) {
        return $this->db->query("SELECT * FROM product_images WHERE product_id = :id ORDER BY display_order ASC, is_main DESC")
            ->bind(':id', $productId)->resultSet();
    }
    
    /**
     * @param int|string $productId
     * @return array
     */
    public function getSpecs($productId) {
        return $this->db->query("SELECT * FROM product_specs WHERE product_id = :id ORDER BY display_order ASC")
            ->bind(':id', $productId)->resultSet();
    }
    
    /**
     * @param array $data
     * @return int|string
     */
    public function create($data) {
        $slug = createSlug($data['name']);
        // Make slug unique
        $existing = $this->findBySlug($slug);
        if ($existing) $slug .= '-' . time();
        
        $this->db->query("INSERT INTO products (product_code, name, slug, category_id, brand_id, description, short_description, price_cost, price_sale, price_discount, discount_percent, main_image, status, stock_quantity, is_featured, is_hot_deal, is_best_selling, is_newest, variants_json) VALUES (:product_code, :name, :slug, :category_id, :brand_id, :description, :short_description, :price_cost, :price_sale, :price_discount, :discount_percent, :main_image, :status, :stock_quantity, :is_featured, :is_hot_deal, :is_best_selling, :is_newest, :variants_json)")
            ->bind(':product_code', $data['product_code'])
            ->bind(':name', $data['name'])
            ->bind(':slug', $slug)
            ->bind(':category_id', $data['category_id'])
            ->bind(':brand_id', $data['brand_id'])
            ->bind(':description', $data['description'] ?? null)
            ->bind(':short_description', $data['short_description'] ?? null)
            ->bind(':price_cost', $data['price_cost'])
            ->bind(':price_sale', $data['price_sale'])
            ->bind(':price_discount', $data['price_discount'] ?? null)
            ->bind(':discount_percent', $data['discount_percent'] ?? 0)
            ->bind(':main_image', $data['main_image'] ?? null)
            ->bind(':status', $data['status'] ?? 'active')
            ->bind(':stock_quantity', $data['stock_quantity'] ?? 0)
            ->bind(':is_featured', $data['is_featured'] ?? 0)
            ->bind(':is_hot_deal', $data['is_hot_deal'] ?? 0)
            ->bind(':is_best_selling', $data['is_best_selling'] ?? 0)
            ->bind(':is_newest', $data['is_newest'] ?? 0)
            ->bind(':variants_json', $data['variants_json'] ?? null)
            ->execute();
        
        $productId = $this->db->lastInsertId();
        
        // Add to inventory
        $this->db->query("INSERT INTO inventory (product_id, quantity) VALUES (:product_id, :quantity)")
            ->bind(':product_id', $productId)
            ->bind(':quantity', $data['stock_quantity'] ?? 0)
            ->execute();
        
        return $productId;
    }
    
    /**
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE products SET product_code=:product_code, name=:name, category_id=:category_id, brand_id=:brand_id, description=:description, short_description=:short_description, price_cost=:price_cost, price_sale=:price_sale, price_discount=:price_discount, discount_percent=:discount_percent, status=:status, stock_quantity=:stock_quantity, is_featured=:is_featured, is_hot_deal=:is_hot_deal, is_best_selling=:is_best_selling, is_newest=:is_newest, variants_json=:variants_json";
        
        if (!empty($data['main_image'])) {
            $sql .= ", main_image=:main_image";
        }
        
        $stmt = $this->db->query($sql . " WHERE id=:id")
            ->bind(':product_code', $data['product_code'])
            ->bind(':name', $data['name'])
            ->bind(':category_id', $data['category_id'])
            ->bind(':brand_id', $data['brand_id'])
            ->bind(':description', $data['description'] ?? null)
            ->bind(':short_description', $data['short_description'] ?? null)
            ->bind(':price_cost', $data['price_cost'])
            ->bind(':price_sale', $data['price_sale'])
            ->bind(':price_discount', $data['price_discount'] ?? null)
            ->bind(':discount_percent', $data['discount_percent'] ?? 0)
            ->bind(':status', $data['status'] ?? 'active')
            ->bind(':stock_quantity', $data['stock_quantity'] ?? 0)
            ->bind(':is_featured', $data['is_featured'] ?? 0)
            ->bind(':is_hot_deal', $data['is_hot_deal'] ?? 0)
            ->bind(':is_best_selling', $data['is_best_selling'] ?? 0)
            ->bind(':is_newest', $data['is_newest'] ?? 0)
            ->bind(':variants_json', $data['variants_json'] ?? null)
            ->bind(':id', $id);
        
        if (!empty($data['main_image'])) {
            $stmt->bind(':main_image', $data['main_image']);
        }
        
        if (!$stmt->execute()) {
            return false;
        }

        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            $this->db->query("INSERT INTO inventory (product_id, quantity) VALUES (:product_id, :quantity)
                ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)")
                ->bind(':product_id', $id)
                ->bind(':quantity', $data['stock_quantity'] ?? 0)
                ->execute();
        }

        return true;
    }
    
    /**
     * @param int|string $id
     * @return bool
     */
    public function delete($id) {
        return $this->db->query("UPDATE products SET status='discontinued' WHERE id=:id")->bind(':id', $id)->execute();
    }
    
    /**
     * @param int|string $productId
     * @param string $imagePath
     * @param int $isMain
     * @return bool
     */
    public function addImage($productId, $imagePath, $isMain = 0) {
        return $this->db->query("INSERT INTO product_images (product_id, image_path, is_main) VALUES (:product_id, :image_path, :is_main)")
            ->bind(':product_id', $productId)
            ->bind(':image_path', $imagePath)
            ->bind(':is_main', $isMain)
            ->execute();
    }
    
    /**
     * @param int|string $imageId
     * @return bool
     */
    public function deleteImage($imageId) {
        return $this->db->query("DELETE FROM product_images WHERE id=:id")->bind(':id', $imageId)->execute();
    }
    
    /**
     * @param int|string $productId
     * @param string $specName
     * @param string $specValue
     * @param int $order
     * @return bool
     */
    public function addSpec($productId, $specName, $specValue, $order = 0) {
        return $this->db->query("INSERT INTO product_specs (product_id, spec_name, spec_value, display_order) VALUES (:product_id, :spec_name, :spec_value, :display_order)")
            ->bind(':product_id', $productId)
            ->bind(':spec_name', $specName)
            ->bind(':spec_value', $specValue)
            ->bind(':display_order', $order)
            ->execute();
    }
    
    /**
     * @param int|string $productId
     * @return bool
     */
    public function deleteSpecs($productId) {
        return $this->db->query("DELETE FROM product_specs WHERE product_id=:id")->bind(':id', $productId)->execute();
    }
    
    /**
     * @param int $page
     * @param int $limit
     * @param string $search
     * @param string $category
     * @param string $brand
     * @param string $status
     * @return array
     */
    public function getAllAdmin($page = 1, $limit = 20, $search = '', $category = '', $brand = '', $status = '') {
        $offset = ($page - 1) * $limit;
        $where = ["1=1"];
        $params = [];
        
        if ($search) {
            $where[] = "(p.name LIKE :search OR p.product_code LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if ($category) {
            $where[] = "p.category_id = :category_id";
            $params[':category_id'] = $category;
        }
        if ($brand) {
            $where[] = "p.brand_id = :brand_id";
            $params[':brand_id'] = $brand;
        }
        if ($status) {
            $where[] = "p.status = :status";
            $params[':status'] = $status;
        }
        
        $whereStr = "WHERE " . implode(" AND ", $where);
        $stmt = $this->db->query("SELECT p.*, c.name as category_name, b.name as brand_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id $whereStr ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset")
            ->bind(':limit', $limit)->bind(':offset', $offset);
        foreach ($params as $k => $v) $stmt->bind($k, $v);
        return $stmt->resultSet();
    }
    
    /**
     * @param string $search
     * @param string $category
     * @param string $brand
     * @param string $status
     * @return int
     */
    public function countAdmin($search = '', $category = '', $brand = '', $status = '') {
        $where = ["1=1"];
        $params = [];
        if ($search) { $where[] = "(p.name LIKE :search OR p.product_code LIKE :search)"; $params[':search'] = '%' . $search . '%'; }
        if ($category) { $where[] = "p.category_id = :category_id"; $params[':category_id'] = $category; }
        if ($brand) { $where[] = "p.brand_id = :brand_id"; $params[':brand_id'] = $brand; }
        if ($status) { $where[] = "p.status = :status"; $params[':status'] = $status; }
        $whereStr = "WHERE " . implode(" AND ", $where);
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM products p $whereStr");
        foreach ($params as $k => $v) $stmt->bind($k, $v);
        $result = $stmt->single();
        return $result['total'] ?? 0;
    }
    
    /**
     * @return array
     */
    public function getStats() {
        return $this->db->query("SELECT COUNT(*) as total, SUM(CASE WHEN status='active' THEN 1 ELSE 0 END) as active, SUM(CASE WHEN stock_quantity < 10 THEN 1 ELSE 0 END) as low_stock, SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock FROM products")->single();
    }
    
    /**
     * @param int $limit
     * @return array
     */
    public function getTopSelling($limit = 10) {
        $limitSql = (defined('DB_TYPE') && DB_TYPE === 'mysql')
            ? 'LIMIT :limit'
            : 'OFFSET 0 ROWS FETCH NEXT :limit ROWS ONLY';
        return $this->db->query("SELECT p.*, sales.total_sold, sales.total_revenue
            FROM products p
            INNER JOIN (
                SELECT od.product_id, SUM(od.quantity) AS total_sold, SUM(od.subtotal) AS total_revenue
                FROM order_details od
                INNER JOIN orders o ON o.id = od.order_id
                WHERE o.status = 'delivered' OR o.payment_status = 'paid'
                GROUP BY od.product_id
                HAVING SUM(od.quantity) > 0
            ) sales ON sales.product_id = p.id
            ORDER BY sales.total_sold DESC, sales.total_revenue DESC
            $limitSql")
            ->bind(':limit', max(1, (int)$limit))->resultSet();
    }
    
    /**
     * @param int|string $id
     * @param int $quantity
     * @return bool
     */
    public function updateStock($id, $quantity) {
        return $this->db->query("UPDATE products SET stock_quantity = :quantity WHERE id = :id")
            ->bind(':quantity', $quantity)->bind(':id', $id)->execute();
    }
    
    /**
     * @param int $threshold
     * @return array
     */
    public function getLowStock($threshold = 10) {
        return $this->db->query("SELECT p.*, b.name as brand_name FROM products p LEFT JOIN brands b ON p.brand_id = b.id WHERE p.status='active' AND p.stock_quantity <= :threshold ORDER BY p.stock_quantity ASC")
            ->bind(':threshold', $threshold)->resultSet();
    }
    
    /**
     * Lấy danh sách sản phẩm active cho sitemap và catalog
     * @param int $limit
     * @return array
     */
    public function getActive($limit = 100) {
        return $this->getList([], 1, $limit);
    }
}
?>
