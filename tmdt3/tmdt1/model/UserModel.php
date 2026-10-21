<?php
/** MODEL: các thao tác dữ liệu tài khoản. Không xuất HTML/JSON tại đây. */
function taoTaiKhoan(mysqli $conn, string $name, string $email, string $phone, string $password): void {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users(fullname,email,phone,password_hash) VALUES(?,?,?,?)');
    $stmt->bind_param('ssss', $name, $email, $phone, $hash);
    $stmt->execute();
}
function timTaiKhoanDangNhap(mysqli $conn, string $login): ?array {
    $stmt = $conn->prepare('SELECT id,fullname,email,phone,role,password_hash FROM users WHERE email=? OR fullname=? LIMIT 1');
    $stmt->bind_param('ss', $login, $login);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    return $user ?: null;
}
