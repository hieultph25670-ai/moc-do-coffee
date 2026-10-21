<?php
/**
 * CONTROLLER API
 * Nhận request từ backend.js, kiểm tra dữ liệu, gọi Model và trả JSON.
 * Không viết câu SQL trực tiếp tại Controller.
 */
require_once __DIR__ . '/../global.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../model/UserModel.php';
require_once __DIR__ . '/../model/DanhMucModel.php';
require_once __DIR__ . '/../model/SanPhamModel.php';
require_once __DIR__ . '/../model/DonHangModel.php';
require_once __DIR__ . '/../model/PhanHoiModel.php';

$action = (string)($_GET['action'] ?? '');
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) $input = [];

try {
    switch ($action) {
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Yêu cầu POST'], 405);
            $name = trim((string)($input['fullname'] ?? ''));
            $email = strtolower(trim((string)($input['email'] ?? '')));
            $phone = trim((string)($input['phone'] ?? ''));
            $password = (string)($input['password'] ?? '');
            if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
                json_response(['error' => 'Họ tên, email hoặc mật khẩu không hợp lệ (mật khẩu tối thiểu 8 ký tự).'], 422);
            }
            taoTaiKhoan($conn, $name, $email, $phone, $password);
            json_response(['success' => true, 'message' => 'Đăng ký thành công'], 201);

        case 'login':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Yêu cầu POST'], 405);
            $login = trim((string)($input['login'] ?? ''));
            $password = (string)($input['password'] ?? '');
            $user = timTaiKhoanDangNhap($conn, $login);
            if (!$user || !password_verify($password, $user['password_hash'])) {
                json_response(['error' => 'Thông tin đăng nhập không đúng'], 401);
            }
            unset($user['password_hash']);
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            json_response(['success' => true, 'user' => $user]);

        case 'me':
            json_response(['user' => $_SESSION['user'] ?? null]);

        case 'logout':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Yêu cầu POST'], 405);
            $_SESSION = [];
            session_regenerate_id(true);
            json_response(['success' => true]);

        case 'categories':
            json_response(['categories' => layTatCaDanhMuc($conn)]);

        case 'products':
            $categoryId = (int)($_GET['category'] ?? 0);
            $productId = (int)($_GET['id'] ?? 0);
            json_response(['products' => layDanhSachSanPham($conn, $categoryId, $productId)]);

        case 'order':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Yêu cầu POST'], 405);
            $name = trim((string)($input['name'] ?? ($_SESSION['user']['fullname'] ?? '')));
            $phone = trim((string)($input['phone'] ?? ''));
            $address = trim((string)($input['address'] ?? ''));
            $method = (string)($input['payment_method'] ?? 'cod');
            $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;
            $order = taoDonHang($conn, $input['items'] ?? [], $name, $phone, $address, $method, $userId);
            json_response(['success' => true, 'code' => $order['code'], 'total' => $order['total']], 201);

        case 'feedback':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Yêu cầu POST'], 405);
            $name = trim((string)($input['name'] ?? ''));
            $email = trim((string)($input['email'] ?? ''));
            $message = trim((string)($input['message'] ?? ''));
            if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($message) < 5) {
                json_response(['error' => 'Vui lòng nhập đầy đủ thông tin'], 422);
            }
            taoPhanHoi($conn, $name, $email, $message);
            json_response(['success' => true], 201);

        default:
            json_response(['error' => 'API không tồn tại'], 404);
    }
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    json_response(['error' => $e->getCode() === 1062 ? 'Email đã được sử dụng' : 'Lỗi cơ sở dữ liệu'], $e->getCode() === 1062 ? 409 : 500);
} catch (DomainException $e) {
    json_response(['error' => $e->getMessage()], 422);
} catch (Throwable $e) {
    error_log($e->getMessage());
    json_response(['error' => 'Máy chủ gặp sự cố'], 500);
}
