<?php
require_once __DIR__ . '/../global.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$action = (string)($_GET['action'] ?? 'products');
if ($action === 'products') { $result=$db->query('SELECT p.id,p.name,p.description,p.price,p.image,p.featured,c.name AS category FROM san_pham p JOIN danh_muc c ON c.id=p.category_id WHERE p.active=1 ORDER BY p.id');echo json_encode($result->fetch_all(MYSQLI_ASSOC),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit; }
http_response_code(404);echo json_encode(['error'=>'Không tìm thấy chức năng API.'],JSON_UNESCAPED_UNICODE);