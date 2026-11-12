<?php
/**
 * Base Model - Lớp cơ sở cho tất cả Models
 */

class Model {
    /** @var Database */
    protected $db;
    /** @var string */
    protected $table = '';
    /** @var array */
    protected $allowedColumns = [];
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Lấy tất cả bản ghi
     * @param array $conditions
     * @param string $orderBy
     * @param int|string|null $limit
     * @return array
     */
    public function getAll($conditions = [], $orderBy = 'id DESC', $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($conditions)) {
            $sql .= " WHERE ";
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = :$key";
            }
            $sql .= implode(' AND ', $where);
        }
        
        if (!empty($orderBy)) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit !== null) {
            $sql .= " LIMIT $limit";
        }
        
        $this->db->query($sql);
        
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $this->db->bind(":$key", $value);
            }
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Lấy một bản ghi theo ID
     * @param int|string $id
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Lấy bản ghi đầu tiên khớp với điều kiện
     */
    public function getOne($conditions = []) {
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($conditions)) {
            $sql .= " WHERE ";
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = :$key";
            }
            $sql .= implode(' AND ', $where);
        }
        
        $this->db->query($sql);
        
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $this->db->bind(":$key", $value);
            }
        }
        
        return $this->db->single();
    }
    
    /**
     * Thêm bản ghi
     * @param array $data
     * @return int|string|false
     */
    public function insert($data) {
        // Lọc dữ liệu
        $data = $this->filterData($data);
        
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $this->db->query($sql);
        
        $i = 1;
        foreach ($data as $value) {
            $this->db->bind($i++, $value);
        }
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Cập nhật bản ghi
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        // Lọc dữ liệu
        $data = $this->filterData($data);
        
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = ?";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE id = ?";
        $this->db->query($sql);
        
        $i = 1;
        foreach ($data as $value) {
            $this->db->bind($i++, $value);
        }
        $this->db->bind($i, $id);
        
        return $this->db->execute();
    }
    
    /**
     * Xóa bản ghi
     * @param int|string $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Đếm bản ghi
     * @param array $conditions
     * @return int
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($conditions)) {
            $sql .= " WHERE ";
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = :$key";
            }
            $sql .= implode(' AND ', $where);
        }
        
        $this->db->query($sql);
        
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $this->db->bind(":$key", $value);
            }
        }
        
        $result = $this->db->single();
        return $result['count'] ?? 0;
    }
    
    /**
     * Lọc dữ liệu (chỉ cho phép các cột được phép)
     * @param array $data
     * @return array
     */
    protected function filterData($data) {
        if (empty($this->allowedColumns)) {
            return $data;
        }
        
        $filtered = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $this->allowedColumns)) {
                $filtered[$key] = $value;
            }
        }
        
        return $filtered;
    }
    
    /**
     * Chạy query tùy chỉnh
     * @param string $sql
     * @param array $binds
     * @return array
     */
    public function customQuery($sql, $binds = []) {
        $this->db->query($sql);
        
        foreach ($binds as $key => $value) {
            if (is_numeric($key)) {
                $this->db->bind($key + 1, $value);
            } else {
                $this->db->bind($key, $value);
            }
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Lấy dữ liệu theo trang
     * @param int $page
     * @param int $perPage
     * @param array $conditions
     * @return array
     */
    public function paginate($page = 1, $perPage = PRODUCTS_PER_PAGE, $conditions = []) {
        $offset = ($page - 1) * $perPage;
        
        $total = $this->count($conditions);
        $data = $this->getAll($conditions, 'id DESC', "$offset, $perPage");
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }
}
