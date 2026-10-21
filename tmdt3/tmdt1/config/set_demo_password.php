
<?php
require_once __DIR__ . '/database.php';

// Mat khau dung de dang nhap cac tai khoan mau
$password = 'Starbucks123';

// PHP tu tao ma bam cho mat khau
$hash = password_hash($password, PASSWORD_DEFAULT);

// Cap nhat mat khau cho 20 tai khoan mau
$stmt = $conn->prepare(
    "UPDATE users
     SET password_hash = ?
     WHERE email LIKE '%@example.com'"
);

$stmt->bind_param('s', $hash);
$stmt->execute();

echo "Da cap nhat mat khau cho "
    . $stmt->affected_rows
    . " tai khoan mau.";

$stmt->close();
$conn->close();
?>
