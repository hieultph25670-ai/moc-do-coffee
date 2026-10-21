<?php
/** MODEL: truy vấn danh mục. */
function layTatCaDanhMuc(mysqli $conn): array {
    $result = $conn->query('SELECT * FROM categories ORDER BY id');
    return $result->fetch_all(MYSQLI_ASSOC);
}
