<?php
require_once __DIR__.'/../config/auth.php';
require_once __DIR__.'/../model/AdminModel.php';
require_admin();
$allowed=['products','categories','orders'];
$requestedTab = $_GET['tab'] ?? 'products';
$tab = in_array($requestedTab, $allowed, true) ? $requestedTab : 'products';
$notice='';
if($_SERVER['REQUEST_METHOD']==='POST') {
    try {
        check_csrf();
        $id=(int)($_POST['id']??0);
        $action=(string)($_POST['action']??'');
        if($action==='save_category') {
            $name=trim($_POST['name']??''); $description=trim($_POST['description']??'');
            $active=isset($_POST['active'])?1:0;
            if($name==='') throw new DomainException('Vui lòng nhập tên danh mục.');
            adminSaveCategory($db,$id,$name,$description,$active);
            header('Location: index.php?tab=categories&saved=1'); exit;
        }
        if($action==='save_product') {
            $categoryId=(int)($_POST['category_id']??0); $name=trim($_POST['name']??'');
            $description=trim($_POST['description']??''); $price=(float)($_POST['price']??0);
            $image=trim($_POST['image']??''); $featured=isset($_POST['featured'])?1:0; $active=isset($_POST['active'])?1:0;
            $check=$db->prepare('SELECT id FROM danh_muc WHERE id=?'); $check->bind_param('i',$categoryId); $check->execute();
            $categoryExists=$check->get_result()->num_rows>0; $check->close();
            $validImage=$image==='' || preg_match('/^photo-[A-Za-z0-9_-]+$/',$image) || filter_var($image,FILTER_VALIDATE_URL);
            $imageScheme=$image==='' || preg_match('/^photo-[A-Za-z0-9_-]+$/',$image) || in_array(strtolower((string)parse_url($image,PHP_URL_SCHEME)),['http','https'],true);
            if($name==='' || $price<0 || strlen($image)>1000 || !$validImage || !$imageScheme || !$categoryExists) throw new DomainException('Kiểm tra tên món, giá, danh mục hoặc đường dẫn ảnh (cần là link http/https trực tiếp).');
            adminSaveProduct($db,$id,$categoryId,$name,$description,$price,$image,$featured,$active);
            header('Location: index.php?tab=products&saved=1'); exit;
        }
        if($action==='save_order') {
            $status=(string)($_POST['status']??'');
            if(!in_array($status,['pending','confirmed','preparing','shipping','completed','cancelled'],true)) throw new DomainException('Trạng thái đơn không hợp lệ.');
            adminSaveOrderStatus($db,$id,$status);
            header('Location: index.php?tab=orders&saved=1'); exit;
        }
        throw new DomainException('Thao tác không hợp lệ.');
    } catch(Throwable $ex) { $notice=$ex instanceof DomainException?$ex->getMessage():'Không lưu được dữ liệu. Hãy kiểm tra lại thông tin.'; }
}
$categories=adminCategories($db); $products=adminProducts($db); $orders=adminOrders($db);
$editId=(int)($_GET['edit']??0); $editing=null;
if($editId){ foreach($tab==='categories'?$categories:$products as $row){if((int)$row['id']===$editId){$editing=$row;break;}} }
$statusLabel=['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','preparing'=>'Đang pha chế','shipping'=>'Đang giao','completed'=>'Hoàn tất','cancelled'=>'Đã hủy'];
?><!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Quản trị | Starbucks Cafe</title><link rel="stylesheet" href="../css/style.css"><style>
body{background:#f6f3ec}.admin-head{background:#344a3b;color:#fff;padding:22px 6%;display:flex;justify-content:space-between;align-items:center}.admin-head h1{font-size:26px;margin:0}.admin-head a{color:#fff}.admin-main{max-width:1250px;margin:24px auto;padding:0 20px}.admin-tabs{display:flex;gap:9px;flex-wrap:wrap;margin:20px 0}.admin-tabs a,.admin-button{display:inline-block;padding:10px 14px;background:#fff;border:1px solid #ddd9cf;color:#344a3b;text-decoration:none;font-size:13px;cursor:pointer}.admin-tabs a.active,.admin-button.primary{background:#344a3b;color:white;border-color:#344a3b}.admin-card{background:#fffefa;padding:22px;margin:16px 0;border:1px solid #e7e4db}.admin-card h2{font-size:23px;margin:0 0 18px}.admin-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:12px}.admin-card label{display:block;font-size:12px;font-weight:600;margin:10px 0}.admin-card input,.admin-card textarea,.admin-card select{width:100%;padding:10px;border:1px solid #d8d5cc;background:white;font:14px 'DM Sans';margin-top:6px}.admin-card input[type=checkbox]{width:auto;margin-right:7px}.admin-actions{display:flex;gap:8px;margin-top:12px;align-items:center}.admin-table{width:100%;border-collapse:collapse;font-size:12px}.admin-table th,.admin-table td{text-align:left;padding:11px;border-bottom:1px solid #e7e4db;vertical-align:top}.admin-table th{color:#77776f}.admin-scroll{overflow:auto}.admin-note{font-size:12px;color:#77776f}.admin-error{padding:12px;background:#fae8e4;color:#8a3026}.admin-ok{padding:12px;background:#eaf2e9;color:#2e5036}.admin-link{color:#344a3b;text-decoration:underline}.admin-danger{color:#9e2b2b}.admin-photo{width:56px;height:56px;object-fit:cover}.admin-inline{display:inline} .admin-head div{font-size:13px}@media(max-width:650px){.admin-head{display:block}.admin-head div{margin-top:10px}.admin-main{padding:0 12px}.admin-card{padding:16px}}
</style></head><body>
<header class="admin-head"><h1>☕ Starbucks Cafe · Quản trị</h1><div>Xin chào <?=e(user()['name']??user()['email']??'Admin')?> · <a href="../index.php?page=index">Xem cửa hàng</a> · <a href="../index.php?page=dangxuat">Đăng xuất</a></div></header>
<main class="admin-main"><nav class="admin-tabs"><?php foreach(['products'=>'Món & giá','categories'=>'Danh mục','orders'=>'Đơn hàng'] as $key=>$label):?><a class="<?=$tab===$key?'active':''?>" href="?tab=<?=$key?>"><?=e($label)?></a><?php endforeach;?></nav>
<?php if($notice):?><p class="admin-error"><?=e($notice)?></p><?php elseif(isset($_GET['saved'])):?><p class="admin-ok">✓ Đã lưu thay đổi.</p><?php endif;?>
<?php if($tab==='categories'):?>
<section class="admin-card"><h2><?=$editing?'Chỉnh sửa':'Thêm'?> danh mục</h2><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save_category"><input type="hidden" name="id" value="<?=e($editing['id']??0)?>"><div class="admin-grid"><label>Tên danh mục<input required name="name" maxlength="80" value="<?=e($editing['name']??'')?>"></label><label>Mô tả<input name="description" maxlength="255" value="<?=e($editing['description']??'')?>"></label></div><label><input type="checkbox" name="active" value="1" <?=(!$editing||$editing['active'])?'checked':''?>> Đang hiển thị</label><div class="admin-actions"><button class="admin-button primary">Lưu danh mục</button><?php if($editing):?><a class="admin-link" href="?tab=categories">Hủy sửa</a><?php endif;?></div></form></section>
<section class="admin-card admin-scroll"><h2>Danh sách danh mục</h2><table class="admin-table"><thead><tr><th>ID</th><th>Tên</th><th>Mô tả</th><th>Món</th><th>Trạng thái</th><th></th></tr></thead><tbody><?php foreach($categories as $c):?><tr><td><?=$c['id']?></td><td><?=e($c['name'])?></td><td><?=e($c['description'])?></td><td><?=$c['product_count']?></td><td><?=$c['active']?'Đang hiển thị':'Đã ẩn'?></td><td><a class="admin-link" href="?tab=categories&edit=<?=$c['id']?>">Chỉnh sửa</a></td></tr><?php endforeach;?></tbody></table></section>
<?php elseif($tab==='products'):?>
<section class="admin-card"><h2><?=$editing?'Chỉnh sửa món':'Thêm món mới'?></h2><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save_product"><input type="hidden" name="id" value="<?=e($editing['id']??0)?>"><div class="admin-grid"><label>Tên món<input required name="name" maxlength="140" value="<?=e($editing['name']??'')?>"></label><label>Danh mục<select name="category_id" required><?php foreach($categories as $c):?><option value="<?=$c['id']?>" <?=($editing['category_id']??0)==$c['id']?'selected':''?>><?=e($c['name'])?><?=$c['active']?'':' (đã ẩn)'?></option><?php endforeach;?></select></label><label>Giá (VNĐ)<input required type="number" name="price" min="0" step="1000" value="<?=e($editing['price']??'')?>"></label><label>Link ảnh trực tiếp (http/https)<input type="text" inputmode="url" name="image" maxlength="1000" placeholder="https://example.com/cafe.jpg hoặc photo-..." value="<?=e($editing['image']??'photo-1461023058943-07fcbe16d735')?>"><small>Dán địa chỉ ảnh trực tiếp hoặc mã ảnh Unsplash bắt đầu bằng photo-.</small></label></div><label>Mô tả<textarea name="description" rows="3"><?=e($editing['description']??'')?></textarea></label><div class="admin-grid"><label><input type="checkbox" name="featured" value="1" <?=!empty($editing['featured'])?'checked':''?>> Món nổi bật</label><label><input type="checkbox" name="active" value="1" <?=(!$editing||$editing['active'])?'checked':''?>> Đang bán trên cửa hàng</label></div><div class="admin-actions"><button class="admin-button primary">Lưu món</button><?php if($editing):?><a class="admin-link" href="?tab=products">Hủy sửa</a><?php endif;?></div></form></section>
<section class="admin-card admin-scroll"><h2>Danh sách món</h2><table class="admin-table"><thead><tr><th>Ảnh</th><th>Tên món</th><th>Danh mục</th><th>Giá</th><th>Trạng thái</th><th></th></tr></thead><tbody><?php foreach($products as $p):?><tr><td><img class="admin-photo" src="<?=e(image_url($p['image']))?>" alt=""></td><td><b><?=e($p['name'])?></b><br><span class="admin-note"><?=e($p['description'])?></span></td><td><?=e($p['category_name'])?></td><td><?=money($p['price'])?></td><td><?=$p['active']?'Đang bán':'Đã ẩn'?></td><td><a class="admin-link" href="?tab=products&edit=<?=$p['id']?>">Chỉnh sửa</a></td></tr><?php endforeach;?></tbody></table></section>
<?php else:?>
<section class="admin-card admin-scroll"><h2>Đơn hàng gần đây</h2><table class="admin-table"><thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Địa chỉ</th><th>Tổng tiền</th><th>Trạng thái</th><th></th></tr></thead><tbody><?php foreach($orders as $o):?><tr><td>#<?=$o['id']?></td><td><?=e($o['customer_name'])?><br><?=e($o['phone'])?></td><td><?=e($o['address'])?></td><td><?=money($o['total'])?></td><td><?=e($statusLabel[$o['status']]??$o['status'])?></td><td><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save_order"><input type="hidden" name="id" value="<?=$o['id']?>"><select name="status"><?php foreach($statusLabel as $key=>$label):?><option value="<?=$key?>" <?=$o['status']===$key?'selected':''?>><?=e($label)?></option><?php endforeach;?></select><button class="admin-button primary">Lưu</button></form></td></tr><?php endforeach;?></tbody></table></section>
<?php endif;?></main></body></html>
