CREATE DATABASE IF NOT EXISTS hoang_cafe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hoang_cafe;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS users (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,fullname VARCHAR(120) NOT NULL,email VARCHAR(190) NOT NULL UNIQUE,phone VARCHAR(25) NULL,password_hash VARCHAR(255) NOT NULL,role ENUM('customer','admin') NOT NULL DEFAULT 'customer',gender ENUM('male','female','other') NULL,birth_date DATE NULL,created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS categories (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) NOT NULL,slug VARCHAR(130) NOT NULL UNIQUE,description TEXT NULL,is_active TINYINT(1) NOT NULL DEFAULT 1,sort_order INT NOT NULL DEFAULT 0) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS products (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,category_id INT UNSIGNED NOT NULL,name VARCHAR(190) NOT NULL,description TEXT NULL,price DECIMAL(12,2) NOT NULL,image_url VARCHAR(600) NULL,stock INT UNSIGNED NOT NULL DEFAULT 20,is_active TINYINT(1) NOT NULL DEFAULT 1,is_featured TINYINT(1) NOT NULL DEFAULT 0,created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,CONSTRAINT fk_hoang_product_category FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE RESTRICT) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS orders (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,code VARCHAR(35) NOT NULL UNIQUE,user_id INT UNSIGNED NULL,customer_name VARCHAR(120) NOT NULL,phone VARCHAR(25) NULL,address VARCHAR(350) NULL,note TEXT NULL,total DECIMAL(12,2) NOT NULL,status ENUM('pending','confirmed','preparing','shipping','completed','cancelled') NOT NULL DEFAULT 'pending',payment_method ENUM('cod','bank_transfer') NOT NULL DEFAULT 'cod',payment_status ENUM('unpaid','reported','paid') NOT NULL DEFAULT 'unpaid',payment_reported_at DATETIME NULL,payment_confirmed_at DATETIME NULL,created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,CONSTRAINT fk_hoang_order_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,INDEX idx_hoang_orders_created(created_at),INDEX idx_hoang_orders_status(status)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS order_items (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,order_id INT UNSIGNED NOT NULL,product_id INT UNSIGNED NULL,product_name VARCHAR(190) NOT NULL,price DECIMAL(12,2) NOT NULL,quantity INT UNSIGNED NOT NULL,CONSTRAINT fk_hoang_item_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,CONSTRAINT fk_hoang_item_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL,UNIQUE KEY uq_hoang_order_product(order_id,product_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS feedback (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) NOT NULL,email VARCHAR(190) NOT NULL,message TEXT NOT NULL,created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,UNIQUE KEY uq_hoang_feedback(email,created_at)) ENGINE=InnoDB;

INSERT INTO users(id,fullname,email,phone,password_hash,role,gender,birth_date) VALUES
(1,'Chủ quán Hoàng','admin@hoangcafe.vn','0900000000','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','admin','other','1998-01-01'),
(2,'Nguyễn Minh Anh','nguyen.minh.anh@example.com','0901000001','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','female','1998-01-01'),
(3,'Trần Gia Bảo','tran.gia.bao@example.com','0901000002','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','male','1999-02-02'),
(4,'Lê Thảo Vy','le.thao.vy@example.com','0901000003','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','female','2000-03-03'),
(5,'Phạm Quốc Huy','pham.quoc.huy@example.com','0901000004','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','male','2001-04-04'),
(6,'Võ Ngọc Hà','vo.ngoc.ha@example.com','0901000005','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','female','2002-05-05'),
(7,'Đặng Tuấn Khang','dang.tuan.khang@example.com','0901000006','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','male','2003-06-06'),
(8,'Bùi Khánh Linh','bui.khanh.linh@example.com','0901000007','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','female','1998-07-07'),
(9,'Đỗ Đức Anh','do.duc.anh@example.com','0901000008','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','male','1999-08-08'),
(10,'Ngô Phương Thảo','ngo.phuong.thao@example.com','0901000009','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','female','2000-09-09'),
(11,'Hoàng Nhật Nam','hoang.nhat.nam@example.com','0901000010','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','male','2001-10-10'),
(12,'Dương Mai Chi','duong.mai.chi@example.com','0901000011','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','female','2002-11-11'),
(13,'Vũ Thanh Tùng','vu.thanh.tung@example.com','0901000012','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','male','2003-12-12'),
(14,'Đinh Bảo Ngọc','dinh.bao.ngoc@example.com','0901000013','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','female','1998-01-13'),
(15,'Lý Hoàng Phúc','ly.hoang.phuc@example.com','0901000014','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','male','1999-02-14'),
(16,'Mai Ngọc Yến','mai.ngoc.yen@example.com','0901000015','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','female','2000-03-15'),
(17,'Trương Nhật Quang','truong.nhat.quang@example.com','0901000016','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','male','2001-04-16'),
(18,'Nguyễn Hồng Nhung','nguyen.hong.nhung@example.com','0901000017','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','female','2002-05-17'),
(19,'Phan Minh Khoa','phan.minh.khoa@example.com','0901000018','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','male','2003-06-18'),
(20,'Lê Ngọc Anh','le.ngoc.anh@example.com','0901000019','$2y$10$HTG04JmmJhHzstmzF3k.WeSpeT6zjFOSToFEyAD0.VYMMvzi5/NQS','customer','female','1998-07-19')
ON DUPLICATE KEY UPDATE fullname=VALUES(fullname),role=VALUES(role);

INSERT INTO categories(id,name,slug,description,is_active,sort_order) VALUES
(1,'Cà phê Espresso','ca-phe-espresso','Hương espresso đậm, hậu vị cân bằng.',1,1),
(2,'Cà phê Việt Nam','ca-phe-viet-nam','Cà phê phin thơm vị hạt rang.',1,2),
(3,'Cà phê sữa','ca-phe-sua','Cà phê hòa quyện cùng sữa.',1,3),
(4,'Cà phê đặc biệt','ca-phe-dac-biet','Công thức riêng của Hoàng Cafe.',1,4),
(5,'Cold Brew','cold-brew','Ủ lạnh chậm, vị êm dịu.',1,5),
(6,'Matcha','matcha','Matcha thơm dịu, tuyển chọn.',1,6),
(7,'Trà trái cây','tra-trai-cay','Trà thanh mát cùng trái cây.',1,7),
(8,'Trà sữa','tra-sua','Trà sữa béo nhẹ, dễ uống.',1,8),
(9,'Chocolate','chocolate','Chocolate thơm ngậy.',1,9),
(10,'Đá xay','da-xay','Đá xay mát lạnh.',1,10),
(11,'Bánh ngọt','banh-ngot','Bánh nướng thơm mỗi ngày.',1,11),
(12,'Bánh mì','banh-mi','Bữa nhẹ kèm cà phê.',1,12),
(13,'Combo','combo','Kết hợp thức uống và bánh.',1,13),
(14,'Cà phê đá','ca-phe-da','Cà phê Việt phục vụ lạnh.',1,14),
(15,'Cà phê nóng','ca-phe-nong','Thức uống nóng cho ngày dịu dàng.',1,15),
(16,'Sữa tươi','sua-tuoi','Sữa tươi và thức uống nhẹ.',1,16),
(17,'Trà nóng','tra-nong','Trà nóng thơm dịu.',1,17),
(18,'Soda','soda','Soda sủi bọt mát lành.',1,18),
(19,'Sinh tố','sinh-to','Trái cây xay tươi.',1,19),
(20,'Món theo mùa','mon-theo-mua','Hương vị theo mùa.',1,20)
ON DUPLICATE KEY UPDATE name=VALUES(name),description=VALUES(description),sort_order=VALUES(sort_order);

INSERT INTO products(id,category_id,name,description,price,image_url,stock,is_active,is_featured) VALUES
(1,1,'Espresso đôi','Hai shot espresso, đậm đà và cân bằng.',35000,'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?auto=format&fit=crop&w=900&q=85',26,1,1),
(2,2,'Americano','Espresso pha cùng nước nóng, vị thanh sạch.',39000,'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=85',20,1,1),
(3,3,'Cappuccino','Espresso, sữa nóng và lớp bọt mịn.',49000,'https://images.unsplash.com/photo-1572442388796-11668a67e53d?auto=format&fit=crop&w=900&q=85',18,1,1),
(4,4,'Latte caramel','Latte sữa tươi điểm caramel nhà làm.',55000,'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=900&q=85',4,1,1),
(5,5,'Cà phê sữa đá','Cà phê phin hòa sữa đặc, đậm vị Việt.',32000,'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=900&q=85',30,1,1),
(6,6,'Bạc xỉu','Sữa dịu béo cùng chút cà phê thơm.',35000,'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?auto=format&fit=crop&w=900&q=85',17,1,0),
(7,7,'Cà phê cốt dừa','Cà phê Việt xay cùng cốt dừa mịn.',52000,'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=900&q=85',9,1,1),
(8,8,'Mocha nóng','Espresso, chocolate và sữa nóng.',52000,'https://images.unsplash.com/photo-1572442388796-11668a67e53d?auto=format&fit=crop&w=900&q=85',11,1,0),
(9,9,'Cold brew cam','Cold brew ủ lạnh kết hợp cam tươi.',49000,'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=900&q=85',14,1,1),
(10,10,'Cold brew sữa yến mạch','Vị cà phê êm dịu cùng sữa yến mạch.',52000,'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=900&q=85',12,1,0),
(11,11,'Matcha latte','Matcha Nhật và sữa tươi mịn màng.',52000,'https://images.unsplash.com/photo-1515823064-d6e0c04616a7?auto=format&fit=crop&w=900&q=85',16,1,1),
(12,12,'Trà đào cam sả','Trà thanh mát cùng đào, cam và sả.',45000,'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=900&q=85',21,1,1),
(13,13,'Trà vải hoa nhài','Trà hoa nhài thơm dịu cùng vải tươi.',45000,'https://images.unsplash.com/photo-1544787219-7f47ccb76574?auto=format&fit=crop&w=900&q=85',19,1,0),
(14,14,'Trà sữa ô long','Ô long rang nhẹ, sữa tươi và trân châu.',49000,'https://images.unsplash.com/photo-1558857563-b371033873b8?auto=format&fit=crop&w=900&q=85',10,1,0),
(15,15,'Chocolate đá','Chocolate nguyên chất xay cùng sữa tươi.',48000,'https://images.unsplash.com/photo-1572490122747-3968b75cc699?auto=format&fit=crop&w=900&q=85',13,1,0),
(16,16,'Matcha đá xay','Matcha xay mát lạnh, phủ kem béo.',55000,'https://images.unsplash.com/photo-1515823064-d6e0c04616a7?auto=format&fit=crop&w=900&q=85',3,1,0),
(17,17,'Croissant bơ','Bánh sừng bò nướng thơm, nhiều lớp.',39000,'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=900&q=85',15,1,1),
(18,18,'Bánh chuối nướng','Bánh chuối mềm thơm, vừa ngọt.',35000,'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&w=900&q=85',8,1,0),
(19,19,'Bánh mì trứng','Bánh mì giòn kẹp trứng và rau tươi.',29000,'https://images.unsplash.com/photo-1525351484163-7529414344d8?auto=format&fit=crop&w=900&q=85',22,1,0),
(20,20,'Combo cà phê & bánh','Cà phê sữa đá cùng croissant bơ.',65000,'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=85',10,1,1)
ON DUPLICATE KEY UPDATE category_id=VALUES(category_id),name=VALUES(name),description=VALUES(description),price=VALUES(price),image_url=VALUES(image_url),is_featured=VALUES(is_featured);

INSERT INTO orders(id,code,user_id,customer_name,phone,address,note,total,status,payment_method,payment_status,payment_reported_at,payment_confirmed_at,created_at) VALUES
(1,'HG-DEMO-001',3,'Trần Gia Bảo','0901000002','11 Nguyễn Huệ, Quận 1','',104000,'completed','cod','paid',NULL,'2026-10-01 11:15:00','2026-10-01 11:15:00'),
(2,'HG-DEMO-002',4,'Lê Thảo Vy','0901000003','12 Nguyễn Huệ, Quận 1','',147000,'completed','bank_transfer','paid','2026-10-02 14:15:00','2026-10-02 14:15:00','2026-10-02 14:15:00'),
(3,'HG-DEMO-003',5,'Phạm Quốc Huy','0901000004','13 Nguyễn Huệ, Quận 1','',35000,'completed','cod','paid',NULL,'2026-10-03 17:15:00','2026-10-03 17:15:00'),
(4,'HG-DEMO-004',6,'Võ Ngọc Hà','0901000005','14 Nguyễn Huệ, Quận 1','Ít ngọt, vui lòng gọi trước',104000,'completed','bank_transfer','paid','2026-10-04 20:15:00','2026-10-04 20:15:00','2026-10-04 20:15:00'),
(5,'HG-DEMO-005',7,'Đặng Tuấn Khang','0901000006','15 Nguyễn Huệ, Quận 1','',144000,'completed','cod','paid',NULL,'2026-10-05 10:15:00','2026-10-05 10:15:00'),
(6,'HG-DEMO-006',8,'Bùi Khánh Linh','0901000007','16 Nguyễn Huệ, Quận 1','',39000,'completed','bank_transfer','paid','2026-10-06 13:15:00','2026-10-06 13:15:00','2026-10-06 13:15:00'),
(7,'HG-DEMO-007',9,'Đỗ Đức Anh','0901000008','17 Nguyễn Huệ, Quận 1','',98000,'completed','cod','paid',NULL,'2026-10-07 16:15:00','2026-10-07 16:15:00'),
(8,'HG-DEMO-008',10,'Ngô Phương Thảo','0901000009','18 Nguyễn Huệ, Quận 1','Ít ngọt, vui lòng gọi trước',165000,'completed','bank_transfer','paid','2026-10-08 19:15:00','2026-10-08 19:15:00','2026-10-08 19:15:00'),
(9,'HG-DEMO-009',11,'Hoàng Nhật Nam','0901000010','19 Nguyễn Huệ, Quận 1','',49000,'completed','cod','paid',NULL,'2026-10-09 09:15:00','2026-10-09 09:15:00'),
(10,'HG-DEMO-010',12,'Dương Mai Chi','0901000011','20 Nguyễn Huệ, Quận 1','',104000,'completed','bank_transfer','paid','2026-10-10 12:15:00','2026-10-10 12:15:00','2026-10-10 12:15:00'),
(11,'HG-DEMO-011',13,'Vũ Thanh Tùng','0901000012','21 Nguyễn Huệ, Quận 1','',117000,'completed','cod','paid',NULL,'2026-10-11 15:15:00','2026-10-11 15:15:00'),
(12,'HG-DEMO-012',14,'Đinh Bảo Ngọc','0901000013','22 Nguyễn Huệ, Quận 1','Ít ngọt, vui lòng gọi trước',55000,'completed','bank_transfer','paid','2026-10-12 18:15:00','2026-10-12 18:15:00','2026-10-12 18:15:00'),
(13,'HG-DEMO-013',15,'Lý Hoàng Phúc','0901000014','23 Nguyễn Huệ, Quận 1','',104000,'completed','cod','paid',NULL,'2026-10-13 08:15:00','2026-10-13 08:15:00'),
(14,'HG-DEMO-014',16,'Mai Ngọc Yến','0901000015','24 Nguyễn Huệ, Quận 1','',105000,'completed','bank_transfer','paid','2026-10-14 11:15:00','2026-10-14 11:15:00','2026-10-14 11:15:00'),
(15,'HG-DEMO-015',17,'Trương Nhật Quang','0901000016','25 Nguyễn Huệ, Quận 1','',32000,'pending','cod','unpaid',NULL,NULL,'2026-10-15 14:15:00'),
(16,'HG-DEMO-016',18,'Nguyễn Hồng Nhung','0901000017','26 Nguyễn Huệ, Quận 1','Ít ngọt, vui lòng gọi trước',90000,'confirmed','bank_transfer','paid','2026-10-16 17:15:00','2026-10-16 17:15:00','2026-10-16 17:15:00'),
(17,'HG-DEMO-017',19,'Phan Minh Khoa','0901000018','27 Nguyễn Huệ, Quận 1','',87000,'preparing','cod','paid',NULL,'2026-10-17 20:15:00','2026-10-17 20:15:00'),
(18,'HG-DEMO-018',20,'Lê Ngọc Anh','0901000019','28 Nguyễn Huệ, Quận 1','',35000,'shipping','bank_transfer','paid','2026-10-18 10:15:00','2026-10-18 10:15:00','2026-10-18 10:15:00'),
(19,'HG-DEMO-019',2,'Nguyễn Minh Anh','0901000001','29 Nguyễn Huệ, Quận 1','',90000,'cancelled','cod','paid',NULL,'2026-10-19 13:15:00','2026-10-19 13:15:00'),
(20,'HG-DEMO-020',3,'Trần Gia Bảo','0901000002','30 Nguyễn Huệ, Quận 1','Ít ngọt, vui lòng gọi trước',195000,'cancelled','bank_transfer','paid','2026-10-20 16:15:00','2026-10-20 16:15:00','2026-10-20 16:15:00')
ON DUPLICATE KEY UPDATE status=VALUES(status),payment_status=VALUES(payment_status),total=VALUES(total);

INSERT INTO order_items(order_id,product_id,product_name,price,quantity) VALUES
(1,7,'Cà phê cốt dừa',52000,2),
(2,14,'Trà sữa ô long',49000,3),
(3,1,'Espresso đôi',35000,1),
(4,8,'Mocha nóng',52000,2),
(5,15,'Chocolate đá',48000,3),
(6,2,'Americano',39000,1),
(7,9,'Cold brew cam',49000,2),
(8,16,'Matcha đá xay',55000,3),
(9,3,'Cappuccino',49000,1),
(10,10,'Cold brew sữa yến mạch',52000,2),
(11,17,'Croissant bơ',39000,3),
(12,4,'Latte caramel',55000,1),
(13,11,'Matcha latte',52000,2),
(14,18,'Bánh chuối nướng',35000,3),
(15,5,'Cà phê sữa đá',32000,1),
(16,12,'Trà đào cam sả',45000,2),
(17,19,'Bánh mì trứng',29000,3),
(18,6,'Bạc xỉu',35000,1),
(19,13,'Trà vải hoa nhài',45000,2),
(20,20,'Combo cà phê & bánh',65000,3)
ON DUPLICATE KEY UPDATE product_name=VALUES(product_name),price=VALUES(price),quantity=VALUES(quantity);

INSERT INTO feedback(name,email,message,created_at) VALUES
('Nguyễn Minh Anh','nguyen.minh.anh@example.com','Cà phê thơm, giao nhanh.','2026-10-01 09:20:00'),
('Trần Gia Bảo','tran.gia.bao@example.com','Không gian quán rất dễ chịu.','2026-10-02 10:20:00'),
('Lê Thảo Vy','le.thao.vy@example.com','Bánh ngon, sẽ quay lại.','2026-10-03 11:20:00'),
('Phạm Quốc Huy','pham.quoc.huy@example.com','Đóng gói cẩn thận, cảm ơn quán.','2026-10-04 12:20:00'),
('Võ Ngọc Hà','vo.ngoc.ha@example.com','Cà phê thơm, giao nhanh.','2026-10-05 13:20:00'),
('Đặng Tuấn Khang','dang.tuan.khang@example.com','Không gian quán rất dễ chịu.','2026-10-06 14:20:00'),
('Bùi Khánh Linh','bui.khanh.linh@example.com','Bánh ngon, sẽ quay lại.','2026-10-07 15:20:00'),
('Đỗ Đức Anh','do.duc.anh@example.com','Đóng gói cẩn thận, cảm ơn quán.','2026-10-08 16:20:00'),
('Ngô Phương Thảo','ngo.phuong.thao@example.com','Cà phê thơm, giao nhanh.','2026-10-09 17:20:00'),
('Hoàng Nhật Nam','hoang.nhat.nam@example.com','Không gian quán rất dễ chịu.','2026-10-10 18:20:00'),
('Dương Mai Chi','duong.mai.chi@example.com','Bánh ngon, sẽ quay lại.','2026-10-11 09:20:00'),
('Vũ Thanh Tùng','vu.thanh.tung@example.com','Đóng gói cẩn thận, cảm ơn quán.','2026-10-12 10:20:00'),
('Đinh Bảo Ngọc','dinh.bao.ngoc@example.com','Cà phê thơm, giao nhanh.','2026-10-13 11:20:00'),
('Lý Hoàng Phúc','ly.hoang.phuc@example.com','Không gian quán rất dễ chịu.','2026-10-14 12:20:00'),
('Mai Ngọc Yến','mai.ngoc.yen@example.com','Bánh ngon, sẽ quay lại.','2026-10-15 13:20:00'),
('Trương Nhật Quang','truong.nhat.quang@example.com','Đóng gói cẩn thận, cảm ơn quán.','2026-10-16 14:20:00'),
('Nguyễn Hồng Nhung','nguyen.hong.nhung@example.com','Cà phê thơm, giao nhanh.','2026-10-17 15:20:00'),
('Phan Minh Khoa','phan.minh.khoa@example.com','Không gian quán rất dễ chịu.','2026-10-18 16:20:00'),
('Lê Ngọc Anh','le.ngoc.anh@example.com','Bánh ngon, sẽ quay lại.','2026-10-19 17:20:00'),
('Nguyễn Minh Anh','nguyen.minh.anh@example.com','Đóng gói cẩn thận, cảm ơn quán.','2026-10-20 18:20:00')
ON DUPLICATE KEY UPDATE message=VALUES(message);
