<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'starbucks_tmdt5';
try {
    $db = new mysqli($host, $username, $password, $database);
    $db->set_charset('utf8mb4');
    // Widen the original VARCHAR(100) once so existing databases can store full image links.
    $column = $db->query("SELECT CHARACTER_MAXIMUM_LENGTH AS length FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='san_pham' AND COLUMN_NAME='image'")->fetch_assoc();
    if ($column && (int)$column['length'] < 1000) {
        $db->query('ALTER TABLE san_pham MODIFY image VARCHAR(1000) NOT NULL');
    }
} catch (mysqli_sql_exception $ex) {
    http_response_code(500);
    error_log('Database connection failed: '.$ex->getMessage());
    exit('Không thể kết nối MySQL. Hãy kiểm tra XAMPP và import sql/starbucks.sql.');
}
