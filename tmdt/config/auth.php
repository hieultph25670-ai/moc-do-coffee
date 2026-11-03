<?php
require_once __DIR__ . '/../global.php';
function require_admin(): void {
    $current=user();
    if (!$current || ($current['role'] ?? '') !== 'admin') {
        http_response_code(403); exit('403: Chỉ quản trị viên được truy cập. <a href="../index.php?page=dangnhap">Đăng nhập</a>');
    }
}
function require_staff(): void {
    $current=user();
    if (!$current || ($current['role'] ?? '') !== 'staff' || !empty($current['ngay_nghi_viec']) || ($current['trang_thai_tai_khoan'] ?? '') !== 'active') {
        http_response_code(403); exit('403: Chỉ nhân viên đang làm việc được truy cập. <a href="../index.php?page=dangnhap">Đăng nhập</a>');
    }
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function check_csrf(): void {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf'] ?? ''))) { http_response_code(419); exit('CSRF không hợp lệ'); }
}
function h(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
