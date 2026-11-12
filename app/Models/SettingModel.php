<?php
/**
 * SettingModel - Quản lý cấu hình hệ thống
 */
class SettingModel {
    private $db;
    private $keyCol;
    private $valueCol;
    
    public function __construct() {
        $this->db = new Database();
        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            $this->keyCol = '`key`';
            $this->valueCol = '`value`';
        } else {
            $this->keyCol = '[key]';
            $this->valueCol = '[value]';
        }
    }
    
    /**
     * Lấy giá trị cấu hình theo key
     */
    public function get($key, $default = '') {
        $result = $this->db->query("SELECT {$this->valueCol} FROM settings WHERE {$this->keyCol} = :key")
            ->bind(':key', $key)->single();
        return $result ? $result['value'] : $default;
    }
    
    /**
     * Thiết lập/Cập nhật cấu hình
     */
    public function set($key, $value) {
        $exists = $this->db->query("SELECT {$this->keyCol} FROM settings WHERE {$this->keyCol} = :key")
            ->bind(':key', $key)->single();
            
        if ($exists) {
            return $this->db->query("UPDATE settings SET {$this->valueCol} = :value, updated_at = CURRENT_TIMESTAMP WHERE {$this->keyCol} = :key")
                ->bind(':key', $key)->bind(':value', $value)->execute();
        } else {
            return $this->db->query("INSERT INTO settings ({$this->keyCol}, {$this->valueCol}) VALUES (:key, :value)")
                ->bind(':key', $key)->bind(':value', $value)->execute();
        }
    }
}
?>
