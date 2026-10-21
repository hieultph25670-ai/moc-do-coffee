# Starbucks TMDT — PHP + MySQL, tổ chức theo MVC cơ bản

## 1. Chạy bằng XAMPP

1. Giải nén thư mục `tmdt1` vào `C:\xampp\htdocs\tmdt1`.
2. Bật Apache và MySQL trong XAMPP.
3. Mở phpMyAdmin → Import → chọn `tmdt1/sql/starbucks.sql`.
4. Truy cập `http://localhost/tmdt1/` (Controller chính).
5. Cửa hàng đọc dữ liệu MySQL: `http://localhost/tmdt1/index.php?page=cuahang`.
6. Đăng ký/đăng nhập qua trang tương ứng trong website.
7. Cấp quyền admin bằng SQL:
   ```sql
   UPDATE users SET role='admin' WHERE email='email-ban-vua-dang-ky@example.com';
   ```
   Sau đó đăng xuất/đăng nhập lại và mở `http://localhost/tmdt1/admin/index.php`.

## 2. Cấu trúc MVC sau khi sắp xếp

- `index.php`: Front Controller, nhận `page` và chọn giao diện trong `view/`.
- `global.php`: khởi tạo kết nối CSDL và session dùng chung.
- `model/`: xử lý truy vấn và nghiệp vụ dữ liệu; Model không xuất HTML.
- `view/`: chứa các giao diện PHP; phần HTML/CSS/JavaScript gốc được giữ lại.
- `api/index.php`: Controller API nhận request từ JavaScript, gọi Model và trả JSON.
- `admin/index.php`: Controller quản trị, gọi `AdminModel.php` để đọc/ghi dữ liệu; phần HTML quản trị vẫn nằm trong file này để giữ nguyên cấu trúc chức năng hiện có.
- `config/`: cấu hình kết nối và xác thực.
- `sql/`: cấu trúc và dữ liệu mẫu MySQL.
- `css/`, `js/`, `images/`: tài nguyên tĩnh (giữ thư mục hiện có).

## 3. Các Model

- `model/UserModel.php`: đăng ký và tìm tài khoản đăng nhập.
- `model/DanhMucModel.php`: lấy danh sách danh mục.
- `model/SanPhamModel.php`: lấy danh sách/chi tiết sản phẩm và sản phẩm phục vụ đặt hàng.
- `model/DonHangModel.php`: tạo đơn, lưu chi tiết, trừ tồn kho trong transaction.
- `model/PhanHoiModel.php`: lưu phản hồi liên hệ.
- `model/AdminModel.php`: truy vấn và cập nhật dữ liệu trong trang quản trị.

## 4. Tên file và chức năng được giữ lại

Các giao diện trong `view/` đã đổi sang đuôi `.php`: `index.php`, `danhmuc.php`, `sanpham.php`, `chitiet.php`, `dangnhap.php`, `dangky.php`, `gioithieu.php`, `lienhe.php`, `cuahang.php`. Hai file JavaScript vẫn giữ nguyên.

Mở trang chủ qua Controller tại `http://localhost/tmdt1/`. Các liên kết điều hướng đều được chuyển qua `index.php?page=...`.

## 5. Lưu ý

- Các trang giao diện giữ nguyên phần HTML/CSS/JavaScript; chỉ đổi đuôi file sang `.php`. `view/cuahang.php` là trang cửa hàng sử dụng API MySQL.
- `api/index.php` là Controller API; câu lệnh SQL của các nghiệp vụ công khai đã được chuyển sang `model/`.
- `admin/index.php` vẫn giữ nguyên giao diện quản trị; thao tác dữ liệu đã được chuyển sang `model/AdminModel.php`.
- Thanh toán chỉ ghi nhận đơn hàng và trạng thái ban đầu `pending`; admin cập nhật trạng thái trong trang quản trị. Không có kết nối ngân hàng thực.
- Không mở file PHP bằng `file:///` hoặc Live Server; cần truy cập qua Apache (`http://localhost/...`).
