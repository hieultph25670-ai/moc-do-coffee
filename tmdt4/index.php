<?php
/** Front Controller của Hoàng Sa Cafe: chọn View theo tham số page. */
require_once __DIR__ . '/global.php';
$pages = [
    'index' => 'index.php', 'danhmuc' => 'danhmuc.php',
    'sanpham' => 'sanpham.php', 'chitiet' => 'chitiet.php',
    'dangnhap' => 'dangnhap.php', 'dangky' => 'dangky.php',
    'gioithieu' => 'gioithieu.php', 'lienhe' => 'lienhe.php',
    'cuahang' => 'cuahang.php', 'giohang' => 'giohang.php',
    'thanhtoan' => 'thanhtoan.php', 'taikhoan' => 'taikhoan.php',
    'dangxuat' => 'dangxuat.php', 'admin' => 'admin.php',
    'them_gio' => 'them_gio.php', 'capnhat_don' => 'capnhat_don.php',
];
$page = (string)($_GET['page'] ?? 'index');
if (!isset($pages[$page])) { http_response_code(404); exit('Không tìm thấy trang được yêu cầu.'); }
$viewPath = __DIR__ . '/view/' . $pages[$page];
if (!is_file($viewPath)) { http_response_code(404); exit('Không tìm thấy giao diện được yêu cầu.'); }
require $viewPath;
