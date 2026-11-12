<?php
class AddressModel {
    private $db;
    public function __construct() { $this->db = new Database(); }
    public function getForUser($userId) {
        return $this->db->query("SELECT * FROM user_addresses WHERE user_id=:uid ORDER BY is_default DESC, id DESC")
            ->bind(':uid', $userId)->resultSet();
    }
    public function save($userId, $data) {
        if (!empty($data['is_default'])) $this->db->query("UPDATE user_addresses SET is_default=0 WHERE user_id=:uid")->bind(':uid', $userId)->execute();
        if (!empty($data['id'])) {
            return $this->db->query("UPDATE user_addresses SET label=:label, recipient_name=:name, phone=:phone, address_line=:line, city=:city, district=:district, ward=:ward, is_default=:default WHERE id=:id AND user_id=:uid")
                ->bind(':label',$data['label'])->bind(':name',$data['recipient_name'])->bind(':phone',$data['phone'])->bind(':line',$data['address_line'])->bind(':city',$data['city'])->bind(':district',$data['district'])->bind(':ward',$data['ward'])->bind(':default',(int)$data['is_default'])->bind(':id',$data['id'])->bind(':uid',$userId)->execute();
        }
        return $this->db->query("INSERT INTO user_addresses (user_id,label,recipient_name,phone,address_line,city,district,ward,is_default) VALUES (:uid,:label,:name,:phone,:line,:city,:district,:ward,:default)")
            ->bind(':uid',$userId)->bind(':label',$data['label'])->bind(':name',$data['recipient_name'])->bind(':phone',$data['phone'])->bind(':line',$data['address_line'])->bind(':city',$data['city'])->bind(':district',$data['district'])->bind(':ward',$data['ward'])->bind(':default',(int)$data['is_default'])->execute();
    }
    public function delete($userId, $id) {
        return $this->db->query("DELETE FROM user_addresses WHERE id=:id AND user_id=:uid")
            ->bind(':id',$id)->bind(':uid',$userId)->execute();
    }
}
