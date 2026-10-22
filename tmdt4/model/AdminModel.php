<?php
/** Truy vấn dành riêng cho trang quản trị Hoàng Sa Cafe. */
function adminCategories(mysqli $db): array {
    return $db->query('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id=c.id AND p.active=1) AS product_count FROM categories c ORDER BY c.id DESC')->fetch_all(MYSQLI_ASSOC);
}
function adminProducts(mysqli $db): array {
    return $db->query('SELECT p.*,c.name AS category_name FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC')->fetch_all(MYSQLI_ASSOC);
}
function adminOrders(mysqli $db): array {
    return $db->query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 100')->fetch_all(MYSQLI_ASSOC);
}
function adminSaveCategory(mysqli $db,int $id,string $name,string $description,int $active): void {
    if ($id) { $s=$db->prepare('UPDATE categories SET name=?,description=?,active=? WHERE id=?'); $s->bind_param('ssii',$name,$description,$active,$id); }
    else { $s=$db->prepare('INSERT INTO categories(name,description,active) VALUES(?,?,?)'); $s->bind_param('ssi',$name,$description,$active); }
    $s->execute();
}
function adminSaveProduct(mysqli $db,int $id,int $categoryId,string $name,string $description,float $price,string $image,int $featured,int $active): void {
    if ($id) { $s=$db->prepare('UPDATE products SET category_id=?,name=?,description=?,price=?,image=?,featured=?,active=? WHERE id=?'); $s->bind_param('issdsiii',$categoryId,$name,$description,$price,$image,$featured,$active,$id); }
    else { $s=$db->prepare('INSERT INTO products(category_id,name,description,price,image,featured,active) VALUES(?,?,?,?,?,?,?)'); $s->bind_param('issdsii',$categoryId,$name,$description,$price,$image,$featured,$active); }
    $s->execute();
}
function adminSaveOrderStatus(mysqli $db,int $id,string $status): void {
    $s=$db->prepare('UPDATE orders SET status=? WHERE id=?'); $s->bind_param('si',$status,$id); $s->execute();
}
