<?php
/** MODEL quản trị: toàn bộ truy vấn dữ liệu của trang admin. */
function adminLuuDanhMuc(mysqli $conn, int $id, string $name, string $slug, string $description): void {
    if ($id) {
        $s=$conn->prepare('UPDATE categories SET name=?,slug=?,description=? WHERE id=?');
        $s->bind_param('sssi',$name,$slug,$description,$id);
    } else {
        $s=$conn->prepare('INSERT INTO categories(name,slug,description) VALUES(?,?,?)');
        $s->bind_param('sss',$name,$slug,$description);
    }
    $s->execute();
}
function adminLuuSanPham(mysqli $conn, int $id, int $cid, string $name, string $description, float $price, string $image, int $stock): void {
    if ($id) {
        $s=$conn->prepare('UPDATE products SET category_id=?,name=?,description=?,price=?,image_url=?,stock=? WHERE id=?');
        $s->bind_param('issdsii',$cid,$name,$description,$price,$image,$stock,$id);
    } else {
        $s=$conn->prepare('INSERT INTO products(category_id,name,description,price,image_url,stock) VALUES(?,?,?,?,?,?)');
        $s->bind_param('issdsi',$cid,$name,$description,$price,$image,$stock);
    }
    $s->execute();
}
function adminXoaDanhMuc(mysqli $conn, int $id): void {
    $s=$conn->prepare('DELETE FROM categories WHERE id=?'); $s->bind_param('i',$id); $s->execute();
}
function adminAnSanPham(mysqli $conn, int $id): void {
    $s=$conn->prepare('UPDATE products SET is_active=0 WHERE id=?'); $s->bind_param('i',$id); $s->execute();
}
function adminCapNhatTrangThaiDon(mysqli $conn, string $status, int $id): void {
    $s=$conn->prepare('UPDATE orders SET status=? WHERE id=?'); $s->bind_param('si',$status,$id); $s->execute();
}
function adminLayDanhMuc(mysqli $conn): array {
    return $conn->query('SELECT * FROM categories ORDER BY id')->fetch_all(MYSQLI_ASSOC);
}
function adminLaySanPham(mysqli $conn): array {
    return $conn->query('SELECT p.*,c.name category_name FROM products p JOIN categories c ON c.id=p.category_id WHERE p.is_active=1 ORDER BY p.id DESC')->fetch_all(MYSQLI_ASSOC);
}
function adminLayDonHang(mysqli $conn): array {
    return $conn->query('SELECT * FROM orders ORDER BY id DESC LIMIT 100')->fetch_all(MYSQLI_ASSOC);
}
function adminLayPhanHoi(mysqli $conn): array {
    return $conn->query('SELECT * FROM feedback ORDER BY id DESC LIMIT 100')->fetch_all(MYSQLI_ASSOC);
}
function adminLayNguoiDung(mysqli $conn): array {
    return $conn->query('SELECT id,fullname,email,role,created_at FROM users ORDER BY id DESC LIMIT 100')->fetch_all(MYSQLI_ASSOC);
}
function adminLayChiTietDon(mysqli $conn, int $orderId): array {
    $s=$conn->prepare('SELECT product_name,quantity,price FROM order_items WHERE order_id=?');
    $s->bind_param('i',$orderId); $s->execute();
    return $s->get_result()->fetch_all(MYSQLI_ASSOC);
}
