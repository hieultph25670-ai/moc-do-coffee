<?php
require_once __DIR__ . '/config/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function user(): ?array {
    static $checked=false; static $current=null;
    if($checked)return $current;
    $checked=true;$session=$_SESSION['user']??null;if(!$session||empty($session['id']))return null;
    global $db;
    try{$q=$db->prepare("SELECT id,name,email,phone,role,gioi_tinh,ngay_sinh,trang_thai_tai_khoan,ngay_nghi_viec,loai_hinh_lam_viec,ca_bat_dau,ca_ket_thuc FROM nguoi_dung WHERE id=? AND trang_thai_tai_khoan IN ('active','deletion_pending')");$q->bind_param('i',$session['id']);$q->execute();$row=$q->get_result()->fetch_assoc();$q->close();if(!$row){unset($_SESSION['user']);return null;}$_SESSION['user']=array_merge($session,$row);$current=$_SESSION['user'];return $current;}catch(Throwable $error){$current=$session;return $current;}
}
function go(string $url): never { header('Location: '.$url); exit; }
function require_login(): void { if (!user()) go('index.php?page=dangnhap'); }
function money(float|int|string $amount): string { return number_format((float)$amount, 0, ',', '.') . '₫'; }
function quantity_text(float|int|string $amount): string { $value=number_format((float)$amount,3,',','.');return rtrim(rtrim($value,'0'),','); }
function flash(?string $message = null): string { if ($message !== null) { $_SESSION['flash']=$message; return ''; } $value=$_SESSION['flash']??''; unset($_SESSION['flash']); return $value; }
function image_url(string $name): string {
    $name=trim($name);
    if (filter_var($name,FILTER_VALIDATE_URL)) {
        $scheme=strtolower((string)parse_url($name,PHP_URL_SCHEME));
        if (in_array($scheme,['http','https'],true)) return $name;
    }
    if (preg_match('/^photo-[A-Za-z0-9_-]+$/',$name)) return 'https://images.unsplash.com/'.$name.'?auto=format&fit=crop&w=900&q=80';
    return 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=900&q=80';
}
/** Returns configured per-serving ingredient quantities, keyed by ingredient id. */
function cafeRecipe(mysqli $db, int $productId, bool $lock=false): array {
    $mode=$db->prepare('SELECT ingredient_mode FROM san_pham WHERE id=?');$mode->bind_param('i',$productId);$mode->execute();$enabled=(int)($mode->get_result()->fetch_assoc()['ingredient_mode']??0);$mode->close();if(!$enabled)return [];
    $sql='SELECT r.ingredient_id,r.luong,i.ten,i.don_vi,i.ton_kho FROM dinh_muc r JOIN nguyen_lieu i ON i.id=r.ingredient_id WHERE r.product_id=? AND i.active=1 ORDER BY r.ingredient_id'.($lock?' FOR UPDATE':'');
    $q=$db->prepare($sql);$q->bind_param('i',$productId);$q->execute();$rows=$q->get_result()->fetch_all(MYSQLI_ASSOC);$q->close();return $rows;
}
/** Consume recipe ingredients in the same transaction as creating the order. */
function cafeConsumeOrder(mysqli $db,int $orderId,int $productId,int $quantity): bool {
    $recipe=cafeRecipe($db,$productId,true);if(!$recipe)return false;
    foreach($recipe as $line){$used=(float)$line['luong']*$quantity;$q=$db->prepare('UPDATE nguyen_lieu SET ton_kho=ton_kho-? WHERE id=? AND ton_kho>=?');$ingredientId=(int)$line['ingredient_id'];$q->bind_param('did',$used,$ingredientId,$used);$q->execute();$ok=$q->affected_rows===1;$q->close();if(!$ok)throw new DomainException('Không đủ '.$line['ten'].' trong kho để pha món đã chọn.');
        $q=$db->prepare('INSERT INTO tieu_hao_nguyen_lieu(order_id,ingredient_id,so_luong,reversed) VALUES(?,?,?,0) ON DUPLICATE KEY UPDATE so_luong=so_luong+VALUES(so_luong),reversed=0');$q->bind_param('iid',$orderId,$ingredientId,$used);$q->execute();$q->close();$others=$db->prepare('SELECT DISTINCT product_id FROM dinh_muc WHERE ingredient_id=? AND product_id<>?');$others->bind_param('ii',$ingredientId,$productId);$others->execute();foreach($others->get_result()->fetch_all(MYSQLI_ASSOC) as $other)cafeRefreshProductStock($db,(int)$other['product_id']);$others->close();}
    return true;
}
function cafeChangeOrderInventory(mysqli $db,int $orderId,bool $restore): void {
    $q=$db->prepare('SELECT ingredient_id,so_luong,reversed FROM tieu_hao_nguyen_lieu WHERE order_id=? ORDER BY ingredient_id FOR UPDATE');$q->bind_param('i',$orderId);$q->execute();$rows=$q->get_result()->fetch_all(MYSQLI_ASSOC);$q->close();
    foreach($rows as $row){$ingredientId=(int)$row['ingredient_id'];$qty=(float)$row['so_luong'];if($restore){if(!(int)$row['reversed']){$u=$db->prepare('UPDATE nguyen_lieu SET ton_kho=ton_kho+? WHERE id=?');$u->bind_param('di',$qty,$ingredientId);$u->execute();$u->close();$u=$db->prepare('UPDATE tieu_hao_nguyen_lieu SET reversed=1 WHERE order_id=? AND ingredient_id=?');$u->bind_param('ii',$orderId,$ingredientId);$u->execute();$u->close();}}elseif((int)$row['reversed']){$u=$db->prepare('UPDATE nguyen_lieu SET ton_kho=ton_kho-? WHERE id=? AND ton_kho>=?');$u->bind_param('did',$qty,$ingredientId,$qty);$u->execute();$ok=$u->affected_rows===1;$u->close();if(!$ok)throw new DomainException('Không thể mở lại đơn: tồn kho nguyên liệu không đủ.');$u=$db->prepare('UPDATE tieu_hao_nguyen_lieu SET reversed=0 WHERE order_id=? AND ingredient_id=?');$u->bind_param('ii',$orderId,$ingredientId);$u->execute();$u->close();}$affected=$db->prepare('SELECT DISTINCT product_id FROM dinh_muc WHERE ingredient_id=?');$affected->bind_param('i',$ingredientId);$affected->execute();foreach($affected->get_result()->fetch_all(MYSQLI_ASSOC) as $product)cafeRefreshProductStock($db,(int)$product['product_id']);$affected->close();}
}
function cafeRefreshProductStock(mysqli $db,int $productId): void {
    $q=$db->prepare('SELECT MIN(FLOOR(i.ton_kho/r.luong)) AS portions,COUNT(*) AS line_count FROM dinh_muc r JOIN nguyen_lieu i ON i.id=r.ingredient_id AND i.active=1 WHERE r.product_id=?');$q->bind_param('i',$productId);$q->execute();$row=$q->get_result()->fetch_assoc();$q->close();if((int)$row['line_count']>0){$stock=max(0,min(2147483647,(int)$row['portions']));$u=$db->prepare('UPDATE san_pham SET stock=? WHERE id=? AND ingredient_mode=1');$u->bind_param('ii',$stock,$productId);$u->execute();$u->close();}
}
/** Install an editable, clearly approximate starter recipe set on a fresh inventory. */
function cafeEnsureStarterRecipes(mysqli $db): void {
    $existing=(int)$db->query('SELECT COUNT(*) total FROM nguyen_lieu')->fetch_assoc()['total'];if($existing>0)return;
    $lock=(int)$db->query("SELECT GET_LOCK('tmdt6_starter_recipe_seed',5) acquired")->fetch_assoc()['acquired'];if($lock!==1)return;
    try{
        if((int)$db->query('SELECT COUNT(*) total FROM nguyen_lieu')->fetch_assoc()['total']>0)return;
        $ingredients=[
            'Hạt cà phê'=>['g',2000],'Sữa đặc'=>['ml',2000],'Sữa tươi'=>['ml',2000],'Đá viên'=>['g',2000],
            'Cam tươi'=>['g',1000],'Trà đen'=>['g',1000],'Đào ngâm'=>['g',1000],'Sả tươi'=>['g',500],
            'Bột matcha'=>['g',500],'Bột chocolate'=>['g',1000],'Syrup cà phê'=>['ml',1000],'Kem tươi'=>['g',500],
            'Trà ô long'=>['g',1000],'Trân châu'=>['g',1000],'Croissant'=>['cái',2],'Bánh chuối'=>['cái',2],
            'Sữa yến mạch'=>['ml',1000],'Nước cốt dừa'=>['ml',1000],'Trà hoa nhài'=>['g',1000],'Syrup caramel'=>['ml',500],
            'Vải ngâm'=>['g',1000],'Hạt chia'=>['g',500],'Syrup chanh dây'=>['ml',1000],'Soda'=>['ml',2000]
        ];
        $recipes=[
            'Cà phê sữa đá'=>['Hạt cà phê'=>20,'Sữa đặc'=>30,'Đá viên'=>100],
            'Americano'=>['Hạt cà phê'=>18],
            'Bạc xỉu Starbucks'=>['Hạt cà phê'=>15,'Sữa đặc'=>20,'Sữa tươi'=>100,'Đá viên'=>100],
            'Cold brew cam'=>['Hạt cà phê'=>25,'Cam tươi'=>25,'Đá viên'=>100],
            'Trà đào cam sả'=>['Trà đen'=>8,'Đào ngâm'=>40,'Cam tươi'=>20,'Sả tươi'=>5,'Đá viên'=>100],
            'Matcha latte'=>['Bột matcha'=>5,'Sữa tươi'=>150,'Đá viên'=>80],
            'Chocolate nóng'=>['Bột chocolate'=>25,'Sữa tươi'=>180],
            'Cà phê đá xay'=>['Hạt cà phê'=>18,'Sữa tươi'=>80,'Syrup cà phê'=>20,'Đá viên'=>140,'Kem tươi'=>20],
            'Trà sữa ô long'=>['Trà ô long'=>8,'Sữa tươi'=>100,'Trân châu'=>50,'Sữa đặc'=>15,'Đá viên'=>100],
            'Croissant bơ'=>['Croissant'=>1],
            'Cà phê đen đá'=>['Hạt cà phê'=>20,'Đá viên'=>100],
            'Cappuccino'=>['Hạt cà phê'=>18,'Sữa tươi'=>120],
            'Latte caramel'=>['Hạt cà phê'=>18,'Sữa tươi'=>150,'Syrup caramel'=>15],
            'Trà vải hoa nhài'=>['Trà hoa nhài'=>8,'Vải ngâm'=>40,'Hạt chia'=>5,'Đá viên'=>100],
            'Chocolate đá'=>['Bột chocolate'=>25,'Sữa tươi'=>100,'Đá viên'=>120],
            'Bánh chuối nướng'=>['Bánh chuối'=>1],
            'Cold brew sữa yến mạch'=>['Hạt cà phê'=>25,'Sữa yến mạch'=>100,'Đá viên'=>100],
            'Matcha dừa'=>['Bột matcha'=>5,'Nước cốt dừa'=>120,'Đá viên'=>100],
            'Soda chanh dây'=>['Syrup chanh dây'=>30,'Soda'=>180,'Đá viên'=>100],
            'Combo cà phê & bánh'=>['Hạt cà phê'=>20,'Sữa đặc'=>30,'Đá viên'=>100,'Croissant'=>1]
        ];
        $ingredients['Ly giấy']=['cái',2];$ingredients['Nắp ly']=['cái',2];$ingredients['Ống hút']=['cái',2];$ingredients['Hộp đựng bánh']=['cái',2];
        $recipes['Croissant bơ']['Hộp đựng bánh']=1;$recipes['Bánh chuối nướng']['Hộp đựng bánh']=1;$recipes['Combo cà phê & bánh']['Hộp đựng bánh']=1;
        foreach(['Cà phê sữa đá','Americano','Bạc xỉu Starbucks','Cold brew cam','Trà đào cam sả','Matcha latte','Chocolate nóng','Cà phê đá xay','Trà sữa ô long','Cà phê đen đá','Cappuccino','Latte caramel','Trà vải hoa nhài','Chocolate đá','Cold brew sữa yến mạch','Matcha dừa','Soda chanh dây','Combo cà phê & bánh'] as $beverage){$recipes[$beverage]['Ly giấy']=1;$recipes[$beverage]['Nắp ly']=1;}
        foreach(['Cà phê sữa đá','Cold brew cam','Trà đào cam sả','Matcha latte','Cà phê đá xay','Trà sữa ô long','Cà phê đen đá','Latte caramel','Trà vải hoa nhài','Chocolate đá','Cold brew sữa yến mạch','Matcha dừa','Soda chanh dây'] as $coldDrink)$recipes[$coldDrink]['Ống hút']=1;
        $products=[];$result=$db->query('SELECT id,name,stock FROM san_pham');foreach($result->fetch_all(MYSQLI_ASSOC) as $product)$products[$product['name']]=$product;$result->free();
        $db->begin_transaction();try{$ingredientIds=[];$insert=$db->prepare('INSERT INTO nguyen_lieu(ten,don_vi,ton_kho,ton_toi_thieu) VALUES(?,?,0,?)');foreach($ingredients as $name=>$definition){[$unit,$minimum]=$definition;$insert->bind_param('ssd',$name,$unit,$minimum);$insert->execute();$ingredientIds[$name]=(int)$db->insert_id;}$insert->close();
            $line=$db->prepare('INSERT INTO dinh_muc(product_id,ingredient_id,luong) VALUES(?,?,?)');$opening=array_fill_keys(array_keys($ingredients),0.0);$productIds=[];foreach($recipes as $productName=>$parts){if(!isset($products[$productName]))continue;$product=$products[$productName];$productId=(int)$product['id'];$productIds[]=$productId;foreach($parts as $ingredientName=>$amount){$ingredientId=$ingredientIds[$ingredientName];$line->bind_param('iid',$productId,$ingredientId,$amount);$line->execute();$opening[$ingredientName]+=$amount*(int)$product['stock'];}}$line->close();
            $update=$db->prepare('UPDATE nguyen_lieu SET ton_kho=? WHERE id=?');foreach($opening as $name=>$quantity){$ingredientId=$ingredientIds[$name];$update->bind_param('di',$quantity,$ingredientId);$update->execute();}$update->close();$db->query('UPDATE san_pham p JOIN dinh_muc r ON r.product_id=p.id SET p.ingredient_mode=1');foreach(array_unique($productIds) as $productId)cafeRefreshProductStock($db,$productId);$db->commit();
        }catch(Throwable $error){$db->rollback();throw $error;}
    }finally{$db->query("SELECT RELEASE_LOCK('tmdt6_starter_recipe_seed')");}
}
/** Add packaging as stock-consuming components for installations seeded before v2. */
function cafeEnsureStarterPackaging(mysqli $db): void {
    $check=$db->prepare("SELECT id FROM nguyen_lieu WHERE ten='Ly giấy' LIMIT 1");$check->execute();$exists=$check->get_result()->num_rows>0;$check->close();if($exists)return;
    $beverages=['Cà phê sữa đá','Americano','Bạc xỉu Starbucks','Cold brew cam','Trà đào cam sả','Matcha latte','Chocolate nóng','Cà phê đá xay','Trà sữa ô long','Cà phê đen đá','Cappuccino','Latte caramel','Trà vải hoa nhài','Chocolate đá','Cold brew sữa yến mạch','Matcha dừa','Soda chanh dây','Combo cà phê & bánh'];
    $cold=['Cà phê sữa đá','Cold brew cam','Trà đào cam sả','Matcha latte','Cà phê đá xay','Trà sữa ô long','Cà phê đen đá','Latte caramel','Trà vải hoa nhài','Chocolate đá','Cold brew sữa yến mạch','Matcha dừa','Soda chanh dây'];
    $db->begin_transaction();try{$ids=[];$insert=$db->prepare('INSERT INTO nguyen_lieu(ten,don_vi,ton_kho,ton_toi_thieu) VALUES(?,?,0,2)');foreach(['Ly giấy','Nắp ly','Ống hút'] as $name){$unit='cái';$insert->bind_param('ss',$name,$unit);$insert->execute();$ids[$name]=(int)$db->insert_id;}$insert->close();
        $products=[];$r=$db->query('SELECT id,name,stock FROM san_pham');foreach($r->fetch_all(MYSQLI_ASSOC) as $p)$products[$p['name']]=$p;$r->free();$line=$db->prepare('INSERT INTO dinh_muc(product_id,ingredient_id,luong) VALUES(?,?,1) ON DUPLICATE KEY UPDATE luong=luong');$quantities=array_fill_keys(array_keys($ids),0.0);$affected=[];foreach($beverages as $name){if(!isset($products[$name]))continue;$p=$products[$name];$affected[]=(int)$p['id'];foreach(['Ly giấy','Nắp ly'] as $material){$ingredientId=$ids[$material];$productId=(int)$p['id'];$line->bind_param('ii',$productId,$ingredientId);$line->execute();$quantities[$material]+=(int)$p['stock'];}if(in_array($name,$cold,true)){$material='Ống hút';$ingredientId=$ids[$material];$productId=(int)$p['id'];$line->bind_param('ii',$productId,$ingredientId);$line->execute();$quantities[$material]+=(int)$p['stock'];}}$line->close();$update=$db->prepare('UPDATE nguyen_lieu SET ton_kho=? WHERE id=?');foreach($ids as $name=>$ingredientId){$quantity=$quantities[$name];$update->bind_param('di',$quantity,$ingredientId);$update->execute();}$update->close();foreach(array_unique($affected) as $productId)cafeRefreshProductStock($db,$productId);$db->commit();
    }catch(Throwable $error){$db->rollback();throw $error;}
}
function cafeEnsureCakePackaging(mysqli $db): void {
    $check=$db->prepare("SELECT id FROM nguyen_lieu WHERE ten='Hộp đựng bánh' LIMIT 1");$check->execute();$exists=$check->get_result()->num_rows>0;$check->close();if($exists)return;
    $db->begin_transaction();try{$name='Hộp đựng bánh';$unit='cái';$insert=$db->prepare('INSERT INTO nguyen_lieu(ten,don_vi,ton_kho,ton_toi_thieu) VALUES(?,?,0,2)');$insert->bind_param('ss',$name,$unit);$insert->execute();$ingredientId=(int)$db->insert_id;$insert->close();$products=[];$r=$db->query("SELECT id,name,stock FROM san_pham WHERE name IN ('Croissant bơ','Bánh chuối nướng','Combo cà phê & bánh')");foreach($r->fetch_all(MYSQLI_ASSOC) as $product)$products[$product['name']]=$product;$r->free();$line=$db->prepare('INSERT INTO dinh_muc(product_id,ingredient_id,luong) VALUES(?,?,1) ON DUPLICATE KEY UPDATE luong=luong');$opening=0.0;$affected=[];foreach($products as $product){$productId=(int)$product['id'];$line->bind_param('ii',$productId,$ingredientId);$line->execute();$opening+=(int)$product['stock'];$affected[]=$productId;}$line->close();$u=$db->prepare('UPDATE nguyen_lieu SET ton_kho=? WHERE id=?');$u->bind_param('di',$opening,$ingredientId);$u->execute();$u->close();foreach($affected as $productId)cafeRefreshProductStock($db,$productId);$db->commit();}catch(Throwable $error){$db->rollback();throw $error;}
}
cafeEnsureStarterRecipes($db);
cafeEnsureStarterPackaging($db);
cafeEnsureCakePackaging($db);
$db->query("UPDATE nguyen_lieu SET gia_tham_khao=CASE ten WHEN 'Hạt cà phê' THEN 160 WHEN 'Sữa đặc' THEN 45 WHEN 'Sữa tươi' THEN 32 WHEN 'Đá viên' THEN 1 WHEN 'Cam tươi' THEN 25 WHEN 'Trà đen' THEN 300 WHEN 'Đào ngâm' THEN 35 WHEN 'Sả tươi' THEN 20 WHEN 'Bột matcha' THEN 1100 WHEN 'Bột chocolate' THEN 250 WHEN 'Syrup cà phê' THEN 110 WHEN 'Kem tươi' THEN 180 WHEN 'Trà ô long' THEN 400 WHEN 'Trân châu' THEN 35 WHEN 'Croissant' THEN 12000 WHEN 'Bánh chuối' THEN 15000 WHEN 'Sữa yến mạch' THEN 55 WHEN 'Nước cốt dừa' THEN 45 WHEN 'Trà hoa nhài' THEN 350 WHEN 'Syrup caramel' THEN 110 WHEN 'Vải ngâm' THEN 40 WHEN 'Hạt chia' THEN 180 WHEN 'Syrup chanh dây' THEN 90 WHEN 'Soda' THEN 18 WHEN 'Ly giấy' THEN 450 WHEN 'Nắp ly' THEN 180 WHEN 'Ống hút' THEN 150 WHEN 'Hộp đựng bánh' THEN 1200 ELSE 0 END WHERE gia_tham_khao IS NULL");
require_once __DIR__ . '/config/auth.php';
