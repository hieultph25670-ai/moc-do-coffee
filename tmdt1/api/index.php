<?php
require_once __DIR__.'/../config/auth.php';
header('Content-Type: application/json; charset=utf-8');
$action = (string)($_GET['action'] ?? '');
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) $input = [];
try {
    switch ($action) {
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error'=>'Yêu cầu POST'],405);
            $name=trim((string)($input['fullname']??'')); $email=strtolower(trim((string)($input['email']??'')));
            $phone=trim((string)($input['phone']??'')); $password=(string)($input['password']??'');
            if (mb_strlen($name)<2 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<8) json_response(['error'=>'Họ tên, email hoặc mật khẩu không hợp lệ (mật khẩu tối thiểu 8 ký tự).'],422);
            $hash=password_hash($password,PASSWORD_DEFAULT);
            $stmt=$conn->prepare('INSERT INTO users(fullname,email,phone,password_hash) VALUES(?,?,?,?)');
            $stmt->bind_param('ssss',$name,$email,$phone,$hash); $stmt->execute();
            json_response(['success'=>true,'message'=>'Đăng ký thành công'],201);
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error'=>'Yêu cầu POST'],405);
            $login=trim((string)($input['login']??'')); $pass=(string)($input['password']??'');
            $stmt=$conn->prepare('SELECT id,fullname,email,phone,role,password_hash FROM users WHERE email=? OR fullname=? LIMIT 1');
            $stmt->bind_param('ss',$login,$login); $stmt->execute(); $u=$stmt->get_result()->fetch_assoc();
            if (!$u || !password_verify($pass,$u['password_hash'])) json_response(['error'=>'Thông tin đăng nhập không đúng'],401);
            session_regenerate_id(true); unset($u['password_hash']); $_SESSION['user']=$u;
            json_response(['success'=>true,'user'=>$u]);
        case 'me': json_response(['user'=>$_SESSION['user']??null]);
        case 'logout':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error'=>'Yêu cầu POST'],405);
            $_SESSION=[]; session_regenerate_id(true); json_response(['success'=>true]);
        case 'categories':
            $result=$conn->query('SELECT * FROM categories ORDER BY id'); json_response(['categories'=>$result->fetch_all(MYSQLI_ASSOC)]);
        case 'products':
            $category=(int)($_GET['category']??0); $id=(int)($_GET['id']??0);
            $sql='SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id=c.id WHERE p.is_active=1';
            if ($id>0) {$sql.=' AND p.id=?'; $s=$conn->prepare($sql);$s->bind_param('i',$id);}
            elseif ($category>0) {$sql.=' AND p.category_id=? ORDER BY p.id';$s=$conn->prepare($sql);$s->bind_param('i',$category);}
            else $s=$conn->prepare($sql.' ORDER BY p.id');
            $s->execute();json_response(['products'=>$s->get_result()->fetch_all(MYSQLI_ASSOC)]);
        case 'order':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error'=>'Yêu cầu POST'],405);
            $items=$input['items']??[]; if (!is_array($items)|| !$items || count($items)>100) json_response(['error'=>'Giỏ hàng trống hoặc không hợp lệ'],422);
            $name=trim((string)($input['name']??($_SESSION['user']['fullname']??'')));
            $phone=trim((string)($input['phone']??''));$address=trim((string)($input['address']??''));
            $method=(string)($input['payment_method']??'cod');
            if (!in_array($method,['cod','bank_transfer'],true)||mb_strlen($name)<2||mb_strlen($address)<5) json_response(['error'=>'Cần nhập tên và địa chỉ giao hàng hợp lệ'],422);
            $conn->begin_transaction();
            $sum=0; $orderItems=[];
            $sel=$conn->prepare('SELECT id,name,price,stock FROM products WHERE id=? AND is_active=1 FOR UPDATE');
            foreach ($items as $item) {
                $id=(int)($item['id']??0);$qty=(int)($item['quantity']??0);
                if($id<1||$qty<1||$qty>99) throw new DomainException('Số lượng hoặc ID sản phẩm không hợp lệ');
                $sel->bind_param('i',$id);$sel->execute();$pr=$sel->get_result()->fetch_assoc();
                if(!$pr||$pr['stock']<$qty) throw new DomainException('Sản phẩm không tồn tại hoặc không đủ tồn kho: '.$id);
                $sum+=(float)$pr['price']*$qty;$orderItems[]=[$pr,$qty];
            }
            $code='SB'.strtoupper(bin2hex(random_bytes(5)));$uid=$_SESSION['user']['id']??null;
            $insert=$conn->prepare('INSERT INTO orders(code,user_id,customer_name,phone,address,total,payment_method) VALUES(?,?,?,?,?,?,?)');
            $insert->bind_param('sisssds',$code,$uid,$name,$phone,$address,$sum,$method);$insert->execute();$oid=$conn->insert_id;
            $insertItem=$conn->prepare('INSERT INTO order_items(order_id,product_id,product_name,price,quantity) VALUES(?,?,?,?,?)');
            $stock=$conn->prepare('UPDATE products SET stock=stock-? WHERE id=?');
            foreach($orderItems as [$pr,$qty]) {$pid=(int)$pr['id'];$pn=$pr['name'];$price=(float)$pr['price'];$insertItem->bind_param('iisdi',$oid,$pid,$pn,$price,$qty);$insertItem->execute();$stock->bind_param('ii',$qty,$pid);$stock->execute();}
            $conn->commit();json_response(['success'=>true,'code'=>$code,'total'=>$sum],201);
        case 'feedback':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error'=>'Yêu cầu POST'],405);
            $name=trim((string)($input['name']??''));$email=trim((string)($input['email']??''));$msg=trim((string)($input['message']??''));
            if(mb_strlen($name)<2||!filter_var($email,FILTER_VALIDATE_EMAIL)||mb_strlen($msg)<5) json_response(['error'=>'Vui lòng nhập đầy đủ thông tin'],422);
            $s=$conn->prepare('INSERT INTO feedback(name,email,message) VALUES(?,?,?)');$s->bind_param('sss',$name,$email,$msg);$s->execute();json_response(['success'=>true],201);
        default:json_response(['error'=>'API không tồn tại'],404);
    }
} catch (mysqli_sql_exception $e) {
    if($conn->errno) {try{$conn->rollback();}catch(Throwable $ignored){}}
    error_log($e->getMessage());
    json_response(['error'=>$e->getCode()===1062?'Email đã được sử dụng':'Lỗi cơ sở dữ liệu'], $e->getCode()===1062?409:500);
} catch (DomainException $e) {
    $conn->rollback();json_response(['error'=>$e->getMessage()],422);
} catch (Throwable $e) {
    error_log($e->getMessage());json_response(['error'=>'Máy chủ gặp sự cố'],500);
}
