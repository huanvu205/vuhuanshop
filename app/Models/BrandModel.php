<?php
/**
 * BrandModel - Quản lý thương hiệu
 */
class BrandModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getAll($status = null) {
        if ($status) {
            return $this->db->query("SELECT b.*, (SELECT COUNT(*) FROM products WHERE brand_id = b.id AND status='active') as product_count FROM brands b WHERE b.status = :status ORDER BY b.display_order ASC, b.name ASC")
                ->bind(':status', $status)->resultSet();
        }
        return $this->db->query("SELECT b.*, (SELECT COUNT(*) FROM products WHERE brand_id = b.id AND status='active') as product_count FROM brands b ORDER BY b.display_order ASC, b.name ASC")
            ->resultSet();
    }
    
    public function getActive() {
        return $this->db->query("SELECT * FROM brands WHERE status='active' ORDER BY display_order ASC, name ASC")->resultSet();
    }
    
    public function findById($id) {
        return $this->db->query("SELECT * FROM brands WHERE id = :id")->bind(':id', $id)->single();
    }
    
    public function findBySlug($slug) {
        return $this->db->query("SELECT * FROM brands WHERE slug = :slug")->bind(':slug', $slug)->single();
    }
    
    public function create($data) {
        $slug = createSlug($data['name']);
        $this->db->query("INSERT INTO brands (name, slug, description, logo, website, country, display_order, status) VALUES (:name, :slug, :description, :logo, :website, :country, :display_order, :status)")
            ->bind(':name', $data['name'])
            ->bind(':slug', $slug)
            ->bind(':description', $data['description'] ?? null)
            ->bind(':logo', $data['logo'] ?? null)
            ->bind(':website', $data['website'] ?? null)
            ->bind(':country', $data['country'] ?? null)
            ->bind(':display_order', $data['display_order'] ?? 0)
            ->bind(':status', $data['status'] ?? 'active')
            ->execute();
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $slug = createSlug($data['name']);
        $sql = "UPDATE brands SET name=:name, slug=:slug, description=:description, website=:website, country=:country, display_order=:display_order, status=:status";
        $stmt = $this->db->query($sql . " WHERE id=:id")
            ->bind(':name', $data['name'])
            ->bind(':slug', $slug)
            ->bind(':description', $data['description'] ?? null)
            ->bind(':website', $data['website'] ?? null)
            ->bind(':country', $data['country'] ?? null)
            ->bind(':display_order', $data['display_order'] ?? 0)
            ->bind(':status', $data['status'] ?? 'active')
            ->bind(':id', $id);
        return $stmt->execute();
    }
    
    public function updateLogo($id, $logo) {
        return $this->db->query("UPDATE brands SET logo=:logo WHERE id=:id")
            ->bind(':logo', $logo)->bind(':id', $id)->execute();
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM brands WHERE id = :id")->bind(':id', $id)->execute();
    }
    
    public function getPopular($limit = 10) {
        return $this->db->query("SELECT b.*, (SELECT COUNT(*) FROM products p WHERE p.brand_id = b.id AND p.status='active') as product_count FROM brands b WHERE b.status='active' ORDER BY product_count DESC LIMIT :limit")
            ->bind(':limit', $limit)->resultSet();
    }
    
    public function count($search = '') {
        if ($search) {
            $result = $this->db->query("SELECT COUNT(*) as total FROM brands WHERE name LIKE :s OR country LIKE :s")
                ->bind(':s', '%' . $search . '%')->single();
        } else {
            $result = $this->db->query("SELECT COUNT(*) as total FROM brands")->single();
        }
        return $result['total'] ?? 0;
    }
    
    public function search($keyword) {
        return $this->db->query("SELECT * FROM brands WHERE name LIKE :kw OR country LIKE :kw ORDER BY name ASC")
            ->bind(':kw', '%' . $keyword . '%')->resultSet();
    }
}
?>
