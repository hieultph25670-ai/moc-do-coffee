# Starbucks Cafe — bản quản trị nâng cấp

Ứng dụng PHP/MySQL chạy bằng XAMPP. Bản này giữ nguyên website khách hàng, đăng nhập/đăng ký/đăng xuất, giỏ hàng, đặt món, tài khoản, lịch sử đơn và thêm trang quản trị cửa hàng.

## Cài đặt an toàn

1. Sao lưu thư mục `C:\xampp\htdocs\tmdt5` nếu bạn đã chỉnh sửa mã nguồn ở đó.
2. Giải nén thư mục `tmdt5` trong ZIP vào `C:\xampp\htdocs\` để cập nhật trang web.
3. Bật Apache và MySQL trong XAMPP.
4. Mở phpMyAdmin → Import → chọn `tmdt5/sql/starbucks_quantri.sql`.
5. Truy cập `http://localhost/tmdt5/`; trang quản trị ở `http://localhost/tmdt5/admin/`.

SQL dùng CSDL mới `starbucks_quantri`, không xóa hay sửa CSDL cũ `starbucks_tmdt5`. Chỉ import SQL mới một lần: file khởi tạo lại các bảng trong CSDL `starbucks_quantri` mỗi khi import lại, nên nếu đã có dữ liệu thật hãy export/sao lưu CSDL trước khi import lại.

Mặc định XAMPP: MySQL `localhost`, user `root`, mật khẩu trống. Nếu môi trường của bạn khác, chỉnh `config/database.php` hoặc đặt `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`.

## Tài khoản mẫu

Tất cả tài khoản mẫu dùng mật khẩu `Cafe2026!`.

- Admin chính: `admin@starbucks.vn`
- Nhân viên admin mẫu: `minhanh@example.vn`, `giabao@example.vn`, `thaovy@example.vn`
- Khách mẫu: `quochuy@example.vn` cùng các tài khoản khách còn lại trong bảng `nguoi_dung`.

## Quản trị cửa hàng

- **Tổng quan:** doanh thu đơn hoàn tất theo ngày/tháng/năm, tự chọn khoảng ngày, số đơn theo trạng thái, số sản phẩm, tồn kho thấp và sản phẩm theo danh mục.
- **Sản phẩm & tồn kho:** thêm/sửa món; menu thao tác cuối dòng để sửa, chỉnh giá, chỉnh số lượng, ẩn/hiện hoặc xóa; sản phẩm hết hàng không thể thêm vào giỏ.
- **Kho hàng:** khi đặt hàng thành công, tồn kho được khóa và trừ trong cùng giao dịch để tránh bán vượt số lượng. Hủy đơn sẽ hoàn lại kho; mở lại đơn đã hủy sẽ kiểm tra và trừ kho trở lại.
- **Nguyên liệu & định mức:** quản lý nhà cung cấp, nguyên liệu, ngưỡng tồn thấp và phiếu nhập. Mỗi món có định mức riêng theo đơn vị cụ thể (g, ml, cái…); các món menu hiện tại được bật sẵn theo nguyên liệu. Đặt hàng sẽ trừ theo định mức trong cùng giao dịch, hủy đơn sẽ hoàn lại. Món mới có thể bật **Theo nguyên liệu** sau khi khai báo đủ công thức.
- Khi kho nguyên liệu mới còn trống, hệ thống tạo công thức tham khảo có thể chỉnh sửa cho các món menu hiện tại và bật theo dõi nguyên liệu. Tồn đầu kỳ được quy đổi ước tính từ số phần sản phẩm hiện có; hãy kiểm kê và sửa số tồn/ngưỡng trong trang Nguyên liệu trước khi dùng vận hành. Định mức đồ uống phổ thông là gợi ý, không phải công thức chính thức của Starbucks.
- **Danh mục:** tạo, sửa, ẩn và xóa (danh mục còn sản phẩm sẽ không xóa được).
- **Đơn hàng:** lọc ngày/trạng thái và cập nhật trạng thái xử lý.
- **Nhân viên:** danh sách tài khoản role `admin`; thêm/sửa hồ sơ, đặt lại mật khẩu, xóa khỏi danh sách quản trị. Tài khoản có lịch sử đơn hàng được hạ quyền xuống khách để giữ nguyên liên kết lịch sử; không thể xóa tài khoản đang đăng nhập hoặc admin cuối cùng.

## Cơ sở dữ liệu

Database: `starbucks_quantri`. Kho nguyên liệu dùng các bảng `nha_cung_cap`, `nguyen_lieu`, `dinh_muc`, `phieu_nhap`, `chi_tiet_phieu_nhap` và `tieu_hao_nguyen_lieu`. Cột `san_pham.ingredient_mode` chọn tồn nguyên liệu hay tồn món. Các bảng mới được tạo tự động trên cơ sở dữ liệu hiện có khi website chạy; không cần import lại file seed và không mất dữ liệu hiện tại.

Cấu trúc mã nguồn vẫn theo mẫu MVC: `index.php` là front controller, Model trong `model/`, giao diện khách hàng trong `view/`, trang quản trị trong `admin/`, cấu hình trong `config/`, SQL trong `sql/`.
