# Starbucks Cafe (tmdt5)

Website PHP/MySQL chạy trên XAMPP. Cấu trúc dựa theo dự án mẫu `tmdt1`: `index.php` là Front Controller, `global.php` khởi tạo dùng chung, Model nằm trong `model/`, giao diện ở `view/`, API ở `api/`, trang quản trị ở `admin/`, cấu hình trong `config/` và CSDL trong `sql/`.

## Cài đặt

1. Giải nén `tmdt5.zip` thẳng vào `C:\xampp\htdocs\` (bên trong ZIP đã có thư mục `tmdt5`).
2. Bật Apache và MySQL trong XAMPP.
3. Mở phpMyAdmin → Import → chọn `tmdt5/sql/starbucks.sql`.
4. Truy cập `http://localhost/tmdt5/`.
5. Nếu MySQL có mật khẩu, chỉnh `config/database.php` hoặc đặt biến môi trường `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`.

## Tài khoản mẫu

- Admin: `admin@starbucks.vn` / `Cafe2026!`
- Khách: `minhanh@example.vn` / `Cafe2026!`
- Có thể đăng ký tài khoản khách mới ngay trên trang đăng nhập.
- Sau khi đăng nhập admin, chọn **Quản trị** trên thanh đầu trang hoặc mở `http://localhost/tmdt5/admin/`.

## Chức năng

- Trang chủ, cửa hàng, lọc danh mục, chi tiết món.
- Giỏ hàng theo phiên, cập nhật số lượng, đặt hàng và lưu chi tiết vào MySQL.
- Đăng ký, đăng nhập, đăng xuất, tài khoản và lịch sử đơn hàng.
- Admin thêm/chỉnh sửa danh mục; thêm món; sửa tên, mô tả, giá; dán link ảnh trực tiếp http/https hoặc dùng mã Unsplash; bật món nổi bật; ẩn món khỏi cửa hàng; và cập nhật trạng thái đơn.
- Giới thiệu, liên hệ lưu lời nhắn vào CSDL; API JSON danh sách món tại `api/index.php?action=products`.
- CSDL tên `starbucks_tmdt5`; các bảng dùng tên tiếng Việt không dấu: `nguoi_dung`, `danh_muc`, `san_pham`, `don_hang`, `chi_tiet_don_hang`, `danh_gia`, `lien_he`. Mỗi bảng có 20 bản ghi mẫu.

Cột lưu ảnh được tự nâng từ `VARCHAR(100)` lên `VARCHAR(1000)` khi website kết nối CSDL cũ lần đầu; không cần import lại SQL. Link ảnh cần là địa chỉ trực tiếp tới tệp ảnh, ví dụ kết thúc bằng `.jpg`, `.png` hoặc `.webp`; một số trang chặn nhúng ảnh bên ngoài.


