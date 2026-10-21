<?php
/** MODEL: lưu phản hồi liên hệ. */
function taoPhanHoi(mysqli $conn, string $name, string $email, string $message): void {
    $stmt = $conn->prepare('INSERT INTO feedback(name,email,message) VALUES(?,?,?)');
    $stmt->bind_param('sss', $name, $email, $message);
    $stmt->execute();
}
