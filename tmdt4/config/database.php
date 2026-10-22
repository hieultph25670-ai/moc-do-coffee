<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'hoang_sa_cafe';
try {
    $db = new mysqli($host, $username, $password, $database);
    $db->set_charset('utf8mb4');
} catch (mysqli_sql_exception $ex) {
    http_response_code(500);
    error_log('Database connection failed: '.$ex->getMessage());
    exit('Không thể kết nối MySQL. Hãy kiểm tra XAMPP và import sql/hoangsa.sql.');
}
