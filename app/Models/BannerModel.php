<?php
class BannerModel {
    private $db;
    public function __construct() { $this->db = new Database(); }
    
    public function getActive($position = 'home_slider') {
        return $this->db->query("SELECT * FROM banners WHERE status='active' AND position=:pos AND (start_date IS NULL OR start_date <= NOW()) AND (end_date IS NULL OR end_date >= NOW()) ORDER BY display_order ASC")
            ->bind(':pos', $position)->resultSet();
    }
    
    public function getAll() {
        return $this->db->query("SELECT * FROM banners ORDER BY display_order ASC, created_at DESC")->resultSet();
    }
    
    public function findById($id) {
        return $this->db->query("SELECT * FROM banners WHERE id=:id")->bind(':id', $id)->single();
    }
    
    public function create($data) {
        $this->db->query("INSERT INTO banners (title, image, link, position, display_order, status, start_date, end_date) VALUES (:title, :image, :link, :position, :order, :status, :start, :end)")
            ->bind(':title', $data['title'])
            ->bind(':image', $data['image'])
            ->bind(':link', $data['link'] ?? null)
            ->bind(':position', $data['position'] ?? 'home_slider')
            ->bind(':order', $data['display_order'] ?? 0)
            ->bind(':status', $data['status'] ?? 'active')
            ->bind(':start', $data['start_date'] ?? null)
            ->bind(':end', $data['end_date'] ?? null)
            ->execute();
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        return $this->db->query("UPDATE banners SET title=:title, link=:link, position=:position, display_order=:order, status=:status, start_date=:start, end_date=:end WHERE id=:id")
            ->bind(':title', $data['title'])
            ->bind(':link', $data['link'] ?? null)
            ->bind(':position', $data['position'] ?? 'home_slider')
            ->bind(':order', $data['display_order'] ?? 0)
            ->bind(':status', $data['status'] ?? 'active')
            ->bind(':start', $data['start_date'] ?? null)
            ->bind(':end', $data['end_date'] ?? null)
            ->bind(':id', $id)->execute();
    }
    
    public function updateImage($id, $image) {
        return $this->db->query("UPDATE banners SET image=:image WHERE id=:id")->bind(':image', $image)->bind(':id', $id)->execute();
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM banners WHERE id=:id")->bind(':id', $id)->execute();
    }
}
?>
