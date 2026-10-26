# Hoàng Cafe · Website quản lý quán cà phê

## Cài đặt trên XAMPP

1. Đặt thư mục tmdt1 trong C:\xampp\htdocs\tmdt1.
2. Bật Apache và MySQL.
3. Mở phpMyAdmin → Import và import sql/bep_moc.sql. File tạo CSDL riêng hoang_cafe, không sửa CSDL bep_moc cũ.
4. Cửa hàng: http://localhost/tmdt1/
5. Quản trị: http://localhost/tmdt1/admin/index.php

## Tài khoản quản trị mẫu

- Email: admin@hoangcafe.vn
- Mật khẩu: Cafe2026!

Đổi mật khẩu sau lần đăng nhập đầu tiên nếu triển khai sử dụng thật.

## Chức năng

- Cửa hàng: danh mục, tìm kiếm/lọc/sắp xếp thực đơn, xem chi tiết, giỏ hàng, đặt hàng, COD/chuyển khoản, lịch sử đơn.
- Tài khoản: đăng ký, đăng nhập/đăng xuất, sửa hồ sơ, đổi mật khẩu, xem trạng thái và chi tiết đơn.
- Quản trị: tổng quan doanh thu, lọc ngày, quản lý sản phẩm/giá/tồn kho/hiển thị, danh mục, trạng thái đơn, khách hàng và phản hồi.
- Hủy đơn trên admin sẽ hoàn lại tồn kho. Đặt đơn sẽ giữ/trừ kho.

## CSDL mẫu

File SQL có 20 bản ghi cho mỗi bảng: users, categories, products, orders, order_items, feedback.

## CSS

Cửa hàng và admin dùng chung css/hoang-cafe.css. Sau thay đổi giao diện, tải lại bằng Ctrl+F5. Chạy qua Apache localhost, không mở PHP bằng file://.