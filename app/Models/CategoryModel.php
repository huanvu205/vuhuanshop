<?php
/**
 * CategoryModel - Quản lý danh mục sản phẩm
 */
class CategoryModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getAll($status = 'active') {
        if ($status) {
            return $this->db->query("SELECT c.*, p.name as parent_name, (SELECT COUNT(*) FROM products WHERE category_id = c.id AND status='active') as product_count FROM categories c LEFT JOIN categories p ON c.parent_id = p.id WHERE c.status = :status ORDER BY c.display_order ASC, c.name ASC")
                ->bind(':status', $status)->resultSet();
        }
        return $this->db->query("SELECT c.*, p.name as parent_name, (SELECT COUNT(*) FROM products WHERE category_id = c.id AND status='active') as product_count FROM categories c LEFT JOIN categories p ON c.parent_id = p.id ORDER BY c.display_order ASC, c.name ASC")
            ->resultSet();
    }
    
    public function getActive() {
        return $this->db->query("SELECT * FROM categories WHERE status='active' AND parent_id IS NULL ORDER BY display_order ASC")->resultSet();
    }
    
    public function findById($id) {
        return $this->db->query("SELECT c.*, p.name as parent_name FROM categories c LEFT JOIN categories p ON c.parent_id = p.id WHERE c.id = :id")
            ->bind(':id', $id)->single();
    }
    
    public function findBySlug($slug) {
        return $this->db->query("SELECT * FROM categories WHERE slug = :slug")
            ->bind(':slug', $slug)->single();
    }
    
    public function create($data) {
        $slug = createSlug($data['name']);
        $this->db->query("INSERT INTO categories (name, slug, description, parent_id, display_order, status, image) VALUES (:name, :slug, :description, :parent_id, :display_order, :status, :image)")
            ->bind(':name', $data['name'])
            ->bind(':slug', $slug)
            ->bind(':description', $data['description'] ?? null)
            ->bind(':parent_id', $data['parent_id'] ?? null)
            ->bind(':display_order', $data['display_order'] ?? 0)
            ->bind(':status', $data['status'] ?? 'active')
            ->bind(':image', $data['image'] ?? null)
            ->execute();
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $slug = createSlug($data['name']);
        $imageSql = array_key_exists('image', $data) ? ', image=:image' : '';
        $query = $this->db->query("UPDATE categories SET name=:name, slug=:slug, description=:description, parent_id=:parent_id, display_order=:display_order, status=:status{$imageSql} WHERE id=:id")
            ->bind(':name', $data['name'])
            ->bind(':slug', $slug)
            ->bind(':description', $data['description'] ?? null)
            ->bind(':parent_id', $data['parent_id'] ?? null)
            ->bind(':display_order', $data['display_order'] ?? 0)
            ->bind(':status', $data['status'] ?? 'active');
        if (array_key_exists('image', $data)) {
            $query->bind(':image', $data['image']);
        }
        return $query->bind(':id', $id)->execute();
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM categories WHERE id = :id")
            ->bind(':id', $id)->execute();
    }
    
    public function getWithProductCount() {
        return $this->db->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.status='active') as product_count FROM categories c WHERE c.status='active' AND c.parent_id IS NULL ORDER BY c.display_order ASC LIMIT 8")
            ->resultSet();
    }
    
    public function getTree() {
        $all = $this->getAll();
        $tree = [];
        foreach ($all as $cat) {
            if (!$cat['parent_id']) {
                $cat['children'] = [];
                foreach ($all as $child) {
                    if ($child['parent_id'] == $cat['id']) {
                        $cat['children'][] = $child;
                    }
                }
                $tree[] = $cat;
            }
        }
        return $tree;
    }
    
    public function search($keyword) {
        return $this->db->query("SELECT * FROM categories WHERE name LIKE :kw ORDER BY name ASC")
            ->bind(':kw', '%' . $keyword . '%')->resultSet();
    }
    
    public function count($search = '') {
        if ($search) {
            $result = $this->db->query("SELECT COUNT(*) as total FROM categories WHERE name LIKE :s OR description LIKE :s")
                ->bind(':s', '%' . $search . '%')->single();
        } else {
            $result = $this->db->query("SELECT COUNT(*) as total FROM categories")->single();
        }
        return $result['total'] ?? 0;
    }
}
?>
