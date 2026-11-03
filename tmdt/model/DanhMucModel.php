<?php
final class DanhMucModel {
    public function __construct(private mysqli $db) {}
    public function all(): mysqli_result { return $this->db->query('SELECT * FROM danh_muc WHERE active=1 ORDER BY id'); }
}
