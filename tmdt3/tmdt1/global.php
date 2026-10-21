<?php
/**
 * Điểm khởi tạo dùng chung của dự án.
 * Controller gọi file này để có kết nối CSDL và session.
 */
require_once __DIR__ . '/config/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
