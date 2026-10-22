<?php
final class UserModel {
    public function __construct(private mysqli $db) {}
    public function findByEmail(string $email): ?array {
        $q=$this->db->prepare('SELECT * FROM users WHERE email=? LIMIT 1'); $q->bind_param('s',$email); $q->execute();
        return $q->get_result()->fetch_assoc() ?: null;
    }
}
