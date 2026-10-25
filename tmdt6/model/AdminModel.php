<?php
function adminCategories(mysqli $db): array {
    $r=$db->query('SELECT c.*, (SELECT COUNT(*) FROM san_pham p WHERE p.category_id=c.id) product_count, COALESCE((SELECT SUM(p.stock) FROM san_pham p WHERE p.category_id=c.id),0) stock_total FROM danh_muc c ORDER BY c.name');
    $rows=$r->fetch_all(MYSQLI_ASSOC); $r->free(); return $rows;
}
function adminProducts(mysqli $db): array {
    $r=$db->query('SELECT p.*,c.name category_name FROM san_pham p JOIN danh_muc c ON c.id=p.category_id ORDER BY p.id DESC');
    $rows=$r->fetch_all(MYSQLI_ASSOC); $r->free(); return $rows;
}
function adminOrders(mysqli $db, string $from='', string $to='', string $status=''): array {
    $sql='SELECT * FROM don_hang WHERE DATE(created_at) BETWEEN ? AND ?';
    if ($status!=='') $sql.=' AND status=?';
    $sql.=' ORDER BY created_at DESC LIMIT 200'; $s=$db->prepare($sql);
    if ($status!=='') $s->bind_param('sss',$from,$to,$status); else $s->bind_param('ss',$from,$to);
    $s->execute(); $rows=$s->get_result()->fetch_all(MYSQLI_ASSOC); $s->close(); return $rows;
}
function adminStaff(mysqli $db): array {
    $r=$db->query("SELECT id,name,email,phone,gioi_tinh gender,ngay_sinh birth_date,loai_hinh_lam_viec employment_type,TIME_FORMAT(ca_bat_dau,'%H:%i') shift_start,TIME_FORMAT(ca_ket_thuc,'%H:%i') shift_end,ngay_nghi_viec employment_end_date,trang_thai_tai_khoan account_status,created_at FROM nguoi_dung WHERE role='staff' ORDER BY (ngay_nghi_viec IS NOT NULL),id");
    $rows=$r->fetch_all(MYSQLI_ASSOC); $r->free(); return $rows;
}
function adminAttendanceSummary(mysqli $db,string $from,string $to): array {
    $s=$db->prepare("SELECT u.id,u.name,u.email,u.ngay_nghi_viec employment_end_date,COUNT(a.id) days_worked,COALESCE(SUM(a.tong_gio),0) total_hours,ROUND(COALESCE(SUM(a.tong_gio),0)*20000) salary,SUM(a.gio_vao IS NOT NULL AND a.gio_ra IS NULL) open_shifts FROM nguoi_dung u LEFT JOIN cham_cong a ON a.user_id=u.id AND a.ngay_lam BETWEEN ? AND ? WHERE u.role='staff' GROUP BY u.id ORDER BY (u.ngay_nghi_viec IS NOT NULL),u.name");
    $s->bind_param('ss',$from,$to);$s->execute();$rows=$s->get_result()->fetch_all(MYSQLI_ASSOC);$s->close();return $rows;
}
function adminAttendanceRecords(mysqli $db,string $from,string $to): array {
    $s=$db->prepare('SELECT a.*,u.name staff_name,u.email FROM cham_cong a JOIN nguoi_dung u ON u.id=a.user_id WHERE a.ngay_lam BETWEEN ? AND ? ORDER BY a.ngay_lam DESC,a.gio_vao DESC LIMIT 500');
    $s->bind_param('ss',$from,$to);$s->execute();$rows=$s->get_result()->fetch_all(MYSQLI_ASSOC);$s->close();return $rows;
}
function adminSaveCategory(mysqli $db,int $id,string $name,string $description,int $active): void {
    if($id){$s=$db->prepare('UPDATE danh_muc SET name=?,description=?,active=? WHERE id=?');$s->bind_param('ssii',$name,$description,$active,$id);}
    else{$s=$db->prepare('INSERT INTO danh_muc(name,description,active) VALUES(?,?,?)');$s->bind_param('ssi',$name,$description,$active);}
    $s->execute();$s->close();
}
function adminSaveProduct(mysqli $db,int $id,int $categoryId,string $name,string $description,float $price,string $image,int $featured,int $active,int $stock): void {
    if($id){$s=$db->prepare('UPDATE san_pham SET category_id=?,name=?,description=?,price=?,image=?,featured=?,active=?,stock=? WHERE id=?');$s->bind_param('issdsiiii',$categoryId,$name,$description,$price,$image,$featured,$active,$stock,$id);}
    else{$s=$db->prepare('INSERT INTO san_pham(category_id,name,description,price,image,featured,active,stock) VALUES(?,?,?,?,?,?,?,?)');$s->bind_param('issdsiii',$categoryId,$name,$description,$price,$image,$featured,$active,$stock);}
    $s->execute();$s->close();
}
function adminSaveOrderStatus(mysqli $db,int $id,string $status): void {
 $db->begin_transaction();try{
  $q=$db->prepare('SELECT status FROM don_hang WHERE id=? FOR UPDATE');$q->bind_param('i',$id);$q->execute();$order=$q->get_result()->fetch_assoc();$q->close();if(!$order)throw new DomainException('Không tìm thấy đơn hàng.');$old=$order['status'];
  if($old!==$status&&($old==='cancelled'||$status==='cancelled')){$q=$db->prepare('SELECT product_id,quantity FROM chi_tiet_don_hang WHERE order_id=?');$q->bind_param('i',$id);$q->execute();$items=$q->get_result()->fetch_all(MYSQLI_ASSOC);$q->close();foreach($items as $item){if($status==='cancelled'){$u=$db->prepare('UPDATE san_pham SET stock=stock+? WHERE id=?');}else{$u=$db->prepare('UPDATE san_pham SET stock=stock-? WHERE id=? AND stock>=?');$u->bind_param('iii',$item['quantity'],$item['product_id'],$item['quantity']);$u->execute();if($u->affected_rows!==1){$u->close();throw new DomainException('Không thể mở lại đơn vì tồn kho hiện không đủ.');}$u->close();continue;}$u->bind_param('ii',$item['quantity'],$item['product_id']);$u->execute();$u->close();}}
  $u=$db->prepare('UPDATE don_hang SET status=? WHERE id=?');$u->bind_param('si',$status,$id);$u->execute();$u->close();$db->commit();
 }catch(Throwable $e){$db->rollback();throw $e;}
}
function adminSaveStaff(mysqli $db,int $id,string $name,string $email,string $phone,string $employmentType,string $shiftStart,string $shiftEnd,string $password): void {
    if($id){
        if($password!==''){$hash=password_hash($password,PASSWORD_DEFAULT);$s=$db->prepare("UPDATE nguoi_dung SET name=?,email=?,phone=?,loai_hinh_lam_viec=?,ca_bat_dau=?,ca_ket_thuc=?,password=?,role='staff' WHERE id=? AND role='staff'");$s->bind_param('sssssssi',$name,$email,$phone,$employmentType,$shiftStart,$shiftEnd,$hash,$id);}
        else{$s=$db->prepare("UPDATE nguoi_dung SET name=?,email=?,phone=?,loai_hinh_lam_viec=?,ca_bat_dau=?,ca_ket_thuc=?,role='staff' WHERE id=? AND role='staff'");$s->bind_param('ssssssi',$name,$email,$phone,$employmentType,$shiftStart,$shiftEnd,$id);}
    } else {$hash=password_hash($password,PASSWORD_DEFAULT);$s=$db->prepare("INSERT INTO nguoi_dung(name,email,phone,password,role,loai_hinh_lam_viec,ca_bat_dau,ca_ket_thuc) VALUES(?,?,?,?,'staff',?,?,?)");$s->bind_param('sssssss',$name,$email,$phone,$hash,$employmentType,$shiftStart,$shiftEnd);}
    $s->execute();$s->close();
}function adminRevenueSeries(mysqli $db,string $from,string $to,string $period): array {
    $fmt=$period==='year'?'%Y':($period==='month'?'%Y-%m':'%Y-%m-%d');
    $sql="SELECT DATE_FORMAT(created_at, ?) label, SUM(total) revenue FROM don_hang WHERE status='completed' AND DATE(created_at) BETWEEN ? AND ? GROUP BY label ORDER BY label";
    $s=$db->prepare($sql);$s->bind_param('sss',$fmt,$from,$to);$s->execute();$rows=$s->get_result()->fetch_all(MYSQLI_ASSOC);$s->close();return $rows;
}
function adminDashboardStats(mysqli $db,string $from,string $to): array {
    $s=$db->prepare("SELECT COALESCE(SUM(CASE WHEN status='completed' THEN total ELSE 0 END),0) revenue, COUNT(*) orders, SUM(status='pending') pending FROM don_hang WHERE DATE(created_at) BETWEEN ? AND ?");$s->bind_param('ss',$from,$to);$s->execute();$stats=$s->get_result()->fetch_assoc();$s->close();
    $r=$db->query('SELECT COUNT(*) products, COALESCE(SUM(stock),0) stock, SUM(stock<5) low_stock FROM san_pham');$stats=array_merge($stats,$r->fetch_assoc());$r->free();
    $r=$db->query("SELECT status,COUNT(*) total FROM don_hang GROUP BY status");$stats['statuses']=$r->fetch_all(MYSQLI_ASSOC);$r->free();
$r=$db->query("SELECT c.name,COALESCE(SUM(d.quantity),0) quantity FROM danh_muc c LEFT JOIN san_pham p ON p.category_id=c.id LEFT JOIN don_hang o ON o.status<>'cancelled' LEFT JOIN chi_tiet_don_hang d ON d.order_id=o.id AND d.product_id=p.id GROUP BY c.id ORDER BY quantity DESC LIMIT 8");$stats['categories']=$r->fetch_all(MYSQLI_ASSOC);$r->free();
    $r=$db->query("SELECT id,name,stock FROM san_pham WHERE stock<5 ORDER BY stock,name LIMIT 8");$stats['low_products']=$r->fetch_all(MYSQLI_ASSOC);$r->free();return $stats;
}
