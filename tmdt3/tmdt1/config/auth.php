<?php
require_once __DIR__.'/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function json_response(array $data, int $status=200): never {
    http_response_code($status); header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store'); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit;
}
function require_admin(): void {
    if (empty($_SESSION['user']['id']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
        http_response_code(403); exit('403: Chỉ quản trị viên được truy cập. <a href="../view/dangnhap.php">Đăng nhập</a>');
    }
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function check_csrf(): void {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf'] ?? ''))) { http_response_code(419); exit('CSRF không hợp lệ'); }
}
function h(mixed $x): string {return htmlspecialchars((string)$x, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');}
