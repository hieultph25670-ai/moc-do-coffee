<?php
/** MODEL: truy vấn sản phẩm, chỉ trả dữ liệu. */
function layDanhSachSanPham(mysqli $conn, int $categoryId = 0, int $productId = 0): array {
    $sql = 'SELECT p.*, c.name AS category_name FROM products p
            JOIN categories c ON p.category_id=c.id
            WHERE p.is_active=1';
    if ($productId > 0) {
        $sql .= ' AND p.id=?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $productId);
    } elseif ($categoryId > 0) {
        $sql .= ' AND p.category_id=? ORDER BY p.id';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $categoryId);
    } else {
        $stmt = $conn->prepare($sql . ' ORDER BY p.id');
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
function laySanPhamDeDatHang(mysqli $conn, int $id): ?array {
    $stmt = $conn->prepare('SELECT id,name,price,stock FROM products WHERE id=? AND is_active=1 FOR UPDATE');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ?: null;
}
