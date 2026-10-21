<?php
/** Front Controller: tiếp nhận yêu cầu và nạp View tương ứng. */
require_once __DIR__ . '/global.php';

$pages = [
    'index' => 'index.php',
    'danhmuc' => 'danhmuc.php',
    'sanpham' => 'sanpham.php',
    'chitiet' => 'chitiet.php',
    'dangnhap' => 'dangnhap.php',
    'dangky' => 'dangky.php',
    'gioithieu' => 'gioithieu.php',
    'lienhe' => 'lienhe.php',
    'cuahang' => 'cuahang.php',
];

$page = (string)($_GET['page'] ?? 'index');
if (!isset($pages[$page])) {
    http_response_code(404);
    exit('Không tìm thấy trang được yêu cầu.');
}

$viewPath = __DIR__ . '/view/' . $pages[$page];
if (!is_file($viewPath)) {
    http_response_code(404);
    exit('Không tìm thấy giao diện được yêu cầu.');
}

// Các liên kết và tài nguyên tương đối trong View được tính từ thư mục view/.
require $viewPath;
