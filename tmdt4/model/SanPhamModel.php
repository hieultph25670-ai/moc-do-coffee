<?php
final class SanPhamModel {
    public function __construct(private mysqli $db) {}
    public function all(): mysqli_result { return $this->db->query('SELECT * FROM products WHERE active=1 ORDER BY id'); }
    public function find(int $id): ?array {
        $q=$this->db->prepare('SELECT * FROM products WHERE id=? AND active=1'); $q->bind_param('i',$id); $q->execute();
        return $q->get_result()->fetch_assoc() ?: null;
    }
}
