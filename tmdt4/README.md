# Hoàng Sa Cafe (tmdt4)

Website PHP/MySQL chạy trên XAMPP. Cấu trúc dựa theo dự án mẫu `tmdt1`: `index.php` là Front Controller, `global.php` khởi tạo dùng chung, Model nằm trong `model/`, giao diện ở `view/`, API ở `api/`, trang quản trị ở `admin/`, cấu hình trong `config/` và CSDL trong `sql/`.

## Cài đặt

1. Giải nén `tmdt4.zip` vào `C:\xampp\htdocs\tmdt4`.
2. Bật Apache và MySQL trong XAMPP.
3. Mở phpMyAdmin → Import → chọn `tmdt4/sql/hoangsa.sql`.
4. Truy cập `http://localhost/tmdt4/`.
5. Nếu MySQL có mật khẩu, chỉnh `config/database.php` hoặc đặt biến môi trường `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`.

## Tài khoản mẫu

- Admin: `admin@hoangsacafe.vn` / `Cafe2026!`
- Khách: `minhanh@example.vn` / `Cafe2026!`
- Có thể đăng ký tài khoản khách mới ngay trên trang đăng nhập.
- Sau khi đăng nhập admin, chọn **Quản trị** trên thanh đầu trang hoặc mở `http://localhost/tmdt4/admin/`.

## Chức năng

- Trang chủ, cửa hàng, lọc danh mục, chi tiết món.
- Giỏ hàng theo phiên, cập nhật số lượng, đặt hàng và lưu chi tiết vào MySQL.
- Đăng ký, đăng nhập, đăng xuất, tài khoản và lịch sử đơn hàng.
- Admin thêm/chỉnh sửa danh mục; thêm món; sửa tên, mô tả, giá, ảnh; bật món nổi bật; ẩn món khỏi cửa hàng; và cập nhật trạng thái đơn.
- Giới thiệu, liên hệ lưu lời nhắn vào CSDL; API JSON danh sách món tại `api/index.php?action=products`.
- CSDL có 20 dòng mẫu trong từng bảng: users, categories, products, orders, order_items, reviews, contacts.


