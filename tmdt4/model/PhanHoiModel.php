<?php
final class PhanHoiModel {
    public function __construct(private mysqli $db) {}
    public function save(string $name,string $email,string $message): void {
        $q=$this->db->prepare('INSERT INTO contacts(name,email,message) VALUES(?,?,?)'); $q->bind_param('sss',$name,$email,$message); $q->execute();
    }
}
