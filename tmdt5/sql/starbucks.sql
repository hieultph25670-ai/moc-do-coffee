CREATE DATABASE IF NOT EXISTS starbucks_tmdt5 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE starbucks_tmdt5;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS lien_he, danh_gia, chi_tiet_don_hang, don_hang, san_pham, danh_muc, nguoi_dung;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE nguoi_dung (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE, phone VARCHAR(30) NOT NULL DEFAULT '',
 password VARCHAR(255) NOT NULL, role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE danh_muc (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(80) NOT NULL,
 description VARCHAR(255) NOT NULL DEFAULT '', active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;
CREATE TABLE san_pham (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, category_id INT UNSIGNED NOT NULL,
 name VARCHAR(140) NOT NULL, description TEXT NOT NULL, price DECIMAL(12,0) NOT NULL,
 image VARCHAR(1000) NOT NULL, featured TINYINT(1) NOT NULL DEFAULT 0,
 active TINYINT(1) NOT NULL DEFAULT 1,
 CONSTRAINT fk_product_category FOREIGN KEY(category_id) REFERENCES danh_muc(id)
) ENGINE=InnoDB;
CREATE TABLE don_hang (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id INT UNSIGNED NOT NULL,
 customer_name VARCHAR(120) NOT NULL, phone VARCHAR(30) NOT NULL,
 address VARCHAR(255) NOT NULL, note VARCHAR(500) NOT NULL DEFAULT '',
 total DECIMAL(12,0) NOT NULL, status ENUM('pending','confirmed','preparing','shipping','completed','cancelled') NOT NULL DEFAULT 'pending',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_order_user FOREIGN KEY(user_id) REFERENCES nguoi_dung(id)
) ENGINE=InnoDB;
CREATE TABLE chi_tiet_don_hang (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id INT UNSIGNED NOT NULL,
 product_id INT UNSIGNED NOT NULL, product_name VARCHAR(140) NOT NULL,
 price DECIMAL(12,0) NOT NULL, quantity INT UNSIGNED NOT NULL,
 CONSTRAINT fk_item_order FOREIGN KEY(order_id) REFERENCES don_hang(id) ON DELETE CASCADE,
 CONSTRAINT fk_item_product FOREIGN KEY(product_id) REFERENCES san_pham(id)
) ENGINE=InnoDB;
CREATE TABLE danh_gia (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id INT UNSIGNED NOT NULL,
 product_id INT UNSIGNED NOT NULL, rating TINYINT UNSIGNED NOT NULL,
 content VARCHAR(500) NOT NULL, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT chk_review_rating CHECK(rating BETWEEN 1 AND 5),
 CONSTRAINT fk_review_user FOREIGN KEY(user_id) REFERENCES nguoi_dung(id),
 CONSTRAINT fk_review_product FOREIGN KEY(product_id) REFERENCES san_pham(id)
) ENGINE=InnoDB;
CREATE TABLE lien_he (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL, message TEXT NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Demo customer password: Cafe2026! (all sample customer accounts share it).
-- Admin login: admin@starbucks.vn / Cafe2026!
INSERT INTO nguoi_dung(name,email,phone,password,role) VALUES
('Quản trị Starbucks','admin@starbucks.vn','0900000000','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','admin'),
('Nguyễn Minh Anh','minhanh@example.vn','0901000001','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Trần Gia Bảo','giabao@example.vn','0901000002','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Lê Thảo Vy','thaovy@example.vn','0901000003','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Phạm Quốc Huy','quochuy@example.vn','0901000004','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Võ Ngọc Hà','ngocha@example.vn','0901000005','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Đặng Tuấn Khang','tuankhang@example.vn','0901000006','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Bùi Khánh Linh','khanhlinh@example.vn','0901000007','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Đỗ Đức Anh','ducanh@example.vn','0901000008','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Ngô Phương Thảo','phuongthao@example.vn','0901000009','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Hoàng Nhật Nam','nhatnam@example.vn','0901000010','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Dương Mai Chi','maichi@example.vn','0901000011','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Vũ Thanh Tùng','thanhtung@example.vn','0901000012','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Đinh Bảo Ngọc','baongoc@example.vn','0901000013','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Lý Hoàng Phúc','hoangphuc@example.vn','0901000014','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Mai Ngọc Yến','ngocyen@example.vn','0901000015','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Trương Nhật Quang','nhatquang@example.vn','0901000016','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Nguyễn Hồng Nhung','hongnhung@example.vn','0901000017','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Phan Minh Khoa','minhkhoa@example.vn','0901000018','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer'),
('Cao Tú Anh','tuanh@example.vn','0901000019','$2y$10$xCzzbsLpDxCTPTUq.sNG5uN2Wgt7.WXkrHxYnLzWxE.iOO06nZv5.','customer');

INSERT INTO danh_muc(name,description) VALUES
('Cà phê Việt','Hương vị cà phê Việt Nam'),('Espresso','Cà phê nền espresso'),('Cà phê sữa','Cà phê cùng sữa tươi hoặc sữa đặc'),('Cold brew','Ủ lạnh chậm trong nhiều giờ'),('Trà trái cây','Trà tươi và trái cây'),('Trà sữa','Trà sữa pha tại quán'),('Matcha','Matcha tuyển chọn'),('Chocolate','Đồ uống chocolate'),('Đá xay','Đồ uống xay mát lạnh'),('Bánh ngọt','Bánh dùng kèm cà phê'),('Cà phê đặc biệt','Công thức riêng Starbucks'),('Cà phê máy','Espresso pha máy'),('Trà nóng','Trà nóng dịu nhẹ'),('Trà lạnh','Trà ủ lạnh'),('Sữa tươi','Đồ uống từ sữa tươi'),('Soda','Soda trái cây'),('Sinh tố','Trái cây xay tươi'),('Đồ uống theo mùa','Món theo mùa'),('Combo','Món kết hợp'),('Khác','Món được yêu thích');

INSERT INTO san_pham(category_id,name,description,price,image,featured) VALUES
(1,'Cà phê sữa đá','Robusta rang vừa, sữa đặc béo thơm và đá mát.',35000,'photo-1461023058943-07fcbe16d735',1),
(2,'Americano','Espresso đậm vị hòa cùng nước nóng tinh khiết.',40000,'photo-1510591509098-f4fdc6d0ff04',0),
(3,'Bạc xỉu Starbucks','Sữa tươi dịu nhẹ, chút cà phê thơm nồng.',42000,'photo-1461023058943-07fcbe16d735',1),
(4,'Cold brew cam','Cà phê ủ lạnh 18 giờ, điểm chút cam tươi.',49000,'photo-1517701604599-bb29b565090c',1),
(5,'Trà đào cam sả','Trà đen, đào vàng, cam tươi và sả thơm.',45000,'photo-1556679343-c7306c1976bc',0),
(7,'Matcha latte','Matcha Nhật thơm dịu quyện sữa tươi.',52000,'photo-1515823064-d6e0c04616a7',1),
(8,'Chocolate nóng','Chocolate nguyên chất, vị đậm và mượt.',48000,'photo-1517701604599-bb29b565090c',0),
(9,'Cà phê đá xay','Cà phê xay mịn cùng kem sữa thơm béo.',55000,'photo-1461023058943-07fcbe16d735',0),
(6,'Trà sữa ô long','Ô long rang nhẹ, sữa tươi và trân châu.',49000,'photo-1556679343-c7306c1976bc',0),
(10,'Croissant bơ','Bánh nhiều lớp nướng vàng, thơm bơ Pháp.',39000,'photo-1509440159596-0249088772ff',0),
(1,'Cà phê đen đá','Cà phê phin nguyên chất, đậm đà tỉnh thức.',30000,'photo-1495474472287-4d71bcdd2085',0),
(2,'Cappuccino','Espresso cân bằng cùng lớp bọt sữa mịn.',49000,'photo-1509042239860-f550ce710b93',0),
(11,'Latte caramel','Espresso và sữa tươi, thêm caramel nhà làm.',55000,'photo-1461023058943-07fcbe16d735',1),
(5,'Trà vải hoa nhài','Trà nhài thanh mát, vải ngọt và hạt chia.',45000,'photo-1556679343-c7306c1976bc',0),
(8,'Chocolate đá','Chocolate nguyên chất xay cùng sữa tươi.',48000,'photo-1517701604599-bb29b565090c',0),
(10,'Bánh chuối nướng','Bánh chuối mềm ẩm, thơm quế và dừa.',35000,'photo-1509440159596-0249088772ff',0),
(4,'Cold brew sữa yến mạch','Cold brew dịu nhẹ với sữa yến mạch.',52000,'photo-1517701604599-bb29b565090c',0),
(7,'Matcha dừa','Matcha thơm hòa quyện cùng nước cốt dừa.',55000,'photo-1515823064-d6e0c04616a7',0),
(16,'Soda chanh dây','Soda mát lạnh, chanh dây chua ngọt tự nhiên.',42000,'photo-1513558161293-cdaf765edfd7',0),
(19,'Combo cà phê & bánh','Cà phê sữa đá và croissant bơ cho buổi sáng.',65000,'photo-1509042239860-f550ce710b93',1);

INSERT INTO don_hang(user_id,customer_name,phone,address,note,total,status,created_at) VALUES
(2,'Nguyễn Minh Anh','0901000001','12 Nguyễn Huệ, Quận 1','Ít đá',80000,'completed','2026-10-01 08:20:00'),
(3,'Trần Gia Bảo','0901000002','22 Lê Lợi, Quận 1','',90000,'completed','2026-10-02 09:15:00'),
(4,'Lê Thảo Vy','0901000003','8 Pasteur, Quận 3','',87000,'completed','2026-10-03 10:30:00'),
(5,'Phạm Quốc Huy','0901000004','30 Hai Bà Trưng, Quận 1','',98000,'completed','2026-10-04 11:10:00'),
(6,'Võ Ngọc Hà','0901000005','18 Võ Văn Tần, Quận 3','',84000,'completed','2026-10-05 08:40:00'),
(7,'Đặng Tuấn Khang','0901000006','41 Điện Biên Phủ, Bình Thạnh','',78000,'completed','2026-10-06 14:20:00'),
(8,'Bùi Khánh Linh','0901000007','5 Nguyễn Đình Chiểu, Quận 1','',104000,'completed','2026-10-07 15:05:00'),
(9,'Đỗ Đức Anh','0901000008','20 Trần Hưng Đạo, Quận 5','',93000,'completed','2026-10-08 08:15:00'),
(10,'Ngô Phương Thảo','0901000009','9 Nguyễn Thị Minh Khai, Quận 1','',97000,'completed','2026-10-09 12:00:00'),
(11,'Hoàng Nhật Nam','0901000010','44 Phan Xích Long, Phú Nhuận','',89000,'completed','2026-10-10 09:45:00'),
(12,'Dương Mai Chi','0901000011','17 Trường Sa, Bình Thạnh','',92000,'completed','2026-10-11 10:10:00'),
(13,'Vũ Thanh Tùng','0901000012','3 Nguyễn Văn Trỗi, Phú Nhuận','',110000,'completed','2026-10-12 16:20:00'),
(14,'Đinh Bảo Ngọc','0901000013','28 Cách Mạng Tháng 8, Quận 3','',83000,'completed','2026-10-13 08:25:00'),
(15,'Lý Hoàng Phúc','0901000014','11 Nguyễn Cư Trinh, Quận 1','',96000,'completed','2026-10-14 13:00:00'),
(16,'Mai Ngọc Yến','0901000015','7 Bạch Đằng, Bình Thạnh','',102000,'completed','2026-10-15 09:20:00'),
(17,'Trương Nhật Quang','0901000016','36 Nam Kỳ Khởi Nghĩa, Quận 1','',75000,'completed','2026-10-16 10:45:00'),
(18,'Nguyễn Hồng Nhung','0901000017','14 Nguyễn Trãi, Quận 5','',99000,'completed','2026-10-17 11:30:00'),
(19,'Phan Minh Khoa','0901000018','2 Hoàng Văn Thụ, Tân Bình','',87000,'completed','2026-10-18 14:35:00'),
(20,'Cao Tú Anh','0901000019','19 Lý Tự Trọng, Quận 1','',105000,'confirmed','2026-10-19 08:40:00'),
(2,'Nguyễn Minh Anh','0901000001','12 Nguyễn Huệ, Quận 1','Giao trước 9h',68000,'pending','2026-10-20 08:05:00');
INSERT INTO chi_tiet_don_hang(order_id,product_id,product_name,price,quantity) VALUES
(1,1,'Cà phê sữa đá',35000,1),(1,10,'Croissant bơ',39000,1),
(2,3,'Bạc xỉu Starbucks',42000,1),(2,16,'Bánh chuối nướng',35000,1),
(3,5,'Trà đào cam sả',45000,1),(3,1,'Cà phê sữa đá',35000,1),
(4,4,'Cold brew cam',49000,2),(5,6,'Matcha latte',52000,1),
(6,11,'Cà phê đen đá',30000,1),(6,10,'Croissant bơ',39000,1),
(7,13,'Latte caramel',55000,1),(7,16,'Bánh chuối nướng',35000,1),
(8,9,'Trà sữa ô long',49000,1),(8,1,'Cà phê sữa đá',35000,1),
(9,20,'Combo cà phê & bánh',65000,1),(9,5,'Trà đào cam sả',45000,1),
(10,12,'Cappuccino',49000,1),(10,10,'Croissant bơ',39000,1),
(11,14,'Trà vải hoa nhài',45000,1),(11,1,'Cà phê sữa đá',35000,1);
INSERT INTO danh_gia(user_id,product_id,rating,content) VALUES
(2,1,5,'Cà phê thơm, vị rất cân bằng.'),(3,3,5,'Bạc xỉu béo dịu, mình rất thích.'),
(4,5,4,'Trà đào tươi và thơm sả.'),(5,4,5,'Cold brew thanh, hậu vị dễ chịu.'),
(6,6,5,'Matcha ngon, không quá ngọt.'),(7,10,4,'Bánh giòn thơm bơ.'),
(8,11,5,'Cà phê phin chuẩn vị.'),(9,12,5,'Cappuccino lớp bọt mịn.'),
(10,13,4,'Caramel thơm, uống buổi sáng rất hợp.'),(11,14,5,'Trà vải mát và vừa miệng.'),
(12,15,4,'Chocolate đậm vị.'),(13,16,5,'Bánh chuối mềm thơm.'),
(14,17,5,'Sữa yến mạch hợp với cold brew.'),(15,18,4,'Matcha dừa thơm béo.'),
(16,19,5,'Soda mát, vị chanh dây tự nhiên.'),(17,20,5,'Combo tiện và ngon.'),
(18,2,4,'Americano đậm vừa.'),(19,8,4,'Đá xay mịn, không bị ngọt gắt.'),
(20,9,5,'Trà ô long thơm, trân châu dai.'),(2,7,4,'Chocolate nóng hợp ngày mưa.');
INSERT INTO lien_he(name,email,message) VALUES
('Nguyễn An','an@example.vn','Quán có chỗ ngồi làm việc không?'),('Trần Bình','binh@example.vn','Tôi muốn hỏi về hạt cà phê.'),
('Lê Chi','chi@example.vn','Có thể đặt bàn cuối tuần không?'),('Phạm Duy','duy@example.vn','Mình muốn góp ý về menu.'),
('Hoàng Hà','ha@example.vn','Quán có sữa hạt không?'),('Võ Huy','huy@example.vn','Xin tư vấn cà phê ít caffeine.'),
('Đặng Lan','lan@example.vn','Tôi muốn đặt số lượng cho văn phòng.'),('Bùi Minh','minh@example.vn','Giờ mở cửa ngày lễ thế nào?'),
('Đỗ Ngọc','ngoc@example.vn','Có thể đổi mức đường không?'),('Ngô Phúc','phuc@example.vn','Mình cần hóa đơn VAT.'),
('Cao Quỳnh','quynh@example.vn','Quán có thẻ thành viên không?'),('Dương Sơn','son@example.vn','Món nào bán chạy nhất?'),
('Vũ Trang','trang@example.vn','Đặt giao hàng khu vực Bình Thạnh.'),('Lý Vân','van@example.vn','Xin thông tin tuyển dụng.'),
('Mai Yến','yen@example.vn','Tôi muốn phản hồi về đơn hàng.'),('Phan Khoa','khoa@example.vn','Có bán cà phê hạt mang về?'),
('Trương Lộc','loc@example.vn','Xin địa chỉ chi nhánh.'),('Đinh Mai','mai@example.vn','Trà đào có caffeine không?'),
('Cao Nam','nam@example.vn','Tôi muốn hợp tác sự kiện.'),('Nguyễn Phương','phuong@example.vn','Cảm ơn quán đã giao nhanh!');
