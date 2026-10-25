# Starbucks TMDT — Bản hoàn thiện PHP + MySQL

## 1. Chạy bằng XAMPP

1. Giải nén và sao chép **thư mục `tmdt1`** vào `C:\xampp\htdocs\tmdt1`.
2. Trong XAMPP Control Panel, bật **Apache** và **MySQL**.
3. Mở `http://localhost/phpmyadmin`, vào tab **Import** và chọn `tmdt1/sql/starbucks.sql` (file tự tạo database `starbucks`).
4. Mở `http://localhost/tmdt1/html/index.html` để xem giao diện ban đầu.
5. Mở **`http://localhost/tmdt1/html/cuahang.html`** để xem danh sách sản phẩm **đọc trực tiếp từ MySQL**, thêm giỏ và tạo đơn hàng.
6. Đăng ký: `http://localhost/tmdt1/html/dangky.html`, đăng nhập: `http://localhost/tmdt1/html/dangnhap.html`.
7. Để tạo quyền admin, đăng ký tài khoản trước, sau đó vào **phpMyAdmin → starbucks → SQL** chạy:
   ```sql
   UPDATE users SET role='admin' WHERE email='email-ban-vua-dang-ky@example.com';
   ```
   Đăng xuất/đăng nhập lại để phiên PHP cập nhật role. Truy cập `http://localhost/tmdt1/admin/index.php`.

## 2. Tổ chức file

- `html/*.html`: Giữ nguyên phần lớn giao diện HTML/CSS/JS mà bạn đã làm.
- `html/backend.js`: Kết nối form đăng ký/đăng nhập tới PHP/MySQL (không còn dùng localStorage để lưu mật khẩu).
- `html/cuahang.html`: Trang cửa hàng dữ liệu thật, có giỏ hàng và tạo đơn.
- `api/index.php`: API register/login/me/logout/categories/products/order/feedback.
- `config/database.php`: thông số kết nối MySQL, mặc định XAMPP root không mật khẩu.
- `config/auth.php`: xác thực session, CSRF, kiểm tra quyền admin.
- `admin/index.php`: CRUD danh mục/sản phẩm, trạng thái đơn hàng, danh sách user và phản hồi.
- `sql/starbucks.sql`: bảng, khóa ngoại và dữ liệu mẫu.

## 3. Lưu ý quan trọng

- **Trang `cuahang.html` là luồng MySQL hoàn chỉnh**. Các trang `sanpham.html`, `danhmuc.html`, `chitiet.html` gốc vẫn hiển thị dữ liệu mẫu và dùng giỏ `localStorage` theo code gốc, chưa đồng bộ vào cơ sở dữ liệu. Không sử dụng popup chuyển khoản gốc `checkout.js` như xác nhận đơn thật.
- Thanh toán chuyển khoản trên `cuahang.html` **chỉ ghi trạng thái chờ**; admin xác nhận thủ công. Chưa tích hợp ngân hàng thực hay cổng thanh toán.
- Dữ liệu đăng ký cũ lưu trong `localStorage` **không được tự động di chuyển** vì có mật khẩu dạng rõ; hãy đăng ký lại.
- Không mở PHP qua `file:///` hoặc Live Server; phải dùng URL `http://localhost/...` qua Apache.
- Một số hình ảnh và thư viện giao diện ban đầu dùng CDN nên cần Internet.
- Đây là project học tập; trước khi đưa lên Internet nên bổ sung HTTPS, giới hạn tốc độ đăng nhập và quy trình phục hồi tồn kho khi huỷ đơn.
