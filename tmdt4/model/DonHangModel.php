<?php
final class DonHangModel {
    public function __construct(private mysqli $db) {}
    public function forUser(int $userId): mysqli_result {
        $q=$this->db->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC'); $q->bind_param('i',$userId); $q->execute(); return $q->get_result();
    }
}
