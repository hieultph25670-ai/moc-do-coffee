<?php
/** Truy vấn dành riêng cho trang quản trị Starbucks Cafe. */
function adminCategories(mysqli $db): array {
    $result=$db->query('SELECT c.*, (SELECT COUNT(*) FROM san_pham p WHERE p.category_id=c.id AND p.active=1) AS product_count FROM danh_muc c ORDER BY c.id DESC');
    $rows=$result->fetch_all(MYSQLI_ASSOC); $result->free(); return $rows;
}
function adminProducts(mysqli $db): array {
    $result=$db->query('SELECT p.*,c.name AS category_name FROM san_pham p JOIN danh_muc c ON c.id=p.category_id ORDER BY p.id DESC');
    $rows=$result->fetch_all(MYSQLI_ASSOC); $result->free(); return $rows;
}
function adminOrders(mysqli $db): array {
    $result=$db->query('SELECT * FROM don_hang ORDER BY created_at DESC LIMIT 100');
    $rows=$result->fetch_all(MYSQLI_ASSOC); $result->free(); return $rows;
}
function adminSaveCategory(mysqli $db,int $id,string $name,string $description,int $active): void {
    if ($id) { $s=$db->prepare('UPDATE danh_muc SET name=?,description=?,active=? WHERE id=?'); $s->bind_param('ssii',$name,$description,$active,$id); }
    else { $s=$db->prepare('INSERT INTO danh_muc(name,description,active) VALUES(?,?,?)'); $s->bind_param('ssi',$name,$description,$active); }
    $s->execute(); $s->close();
}
function adminSaveProduct(mysqli $db,int $id,int $categoryId,string $name,string $description,float $price,string $image,int $featured,int $active): void {
    if ($id) { $s=$db->prepare('UPDATE san_pham SET category_id=?,name=?,description=?,price=?,image=?,featured=?,active=? WHERE id=?'); $s->bind_param('issdsiii',$categoryId,$name,$description,$price,$image,$featured,$active,$id); }
    else { $s=$db->prepare('INSERT INTO san_pham(category_id,name,description,price,image,featured,active) VALUES(?,?,?,?,?,?,?)'); $s->bind_param('issdsii',$categoryId,$name,$description,$price,$image,$featured,$active); }
    $s->execute(); $s->close();
}
function adminSaveOrderStatus(mysqli $db,int $id,string $status): void {
    $s=$db->prepare('UPDATE don_hang SET status=? WHERE id=?'); $s->bind_param('si',$status,$id); $s->execute(); $s->close();
}
