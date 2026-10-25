CREATE DATABASE IF NOT EXISTS bep_moc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bep_moc;

CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 fullname VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 phone VARCHAR(25) NULL,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
 gender ENUM('male','female','other') NULL,
 birth_date DATE NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS categories (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 slug VARCHAR(130) NOT NULL UNIQUE,
 description TEXT NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS products (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 category_id INT UNSIGNED NOT NULL,
 name VARCHAR(190) NOT NULL,
 description TEXT NULL,
 price DECIMAL(12,2) NOT NULL,
 image_url VARCHAR(600) NULL,
 stock INT UNSIGNED NOT NULL DEFAULT 20,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 is_featured TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_bm_product_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS orders (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 code VARCHAR(35) NOT NULL UNIQUE,
 user_id INT UNSIGNED NULL,
 customer_name VARCHAR(120) NOT NULL,
 phone VARCHAR(25) NULL,
 address VARCHAR(350) NULL,
 note TEXT NULL,
 total DECIMAL(12,2) NOT NULL,
 status ENUM('pending','confirmed','preparing','shipping','completed','cancelled') NOT NULL DEFAULT 'pending',
 payment_method ENUM('cod','bank_transfer') NOT NULL DEFAULT 'cod',
 payment_status ENUM('unpaid','reported','paid') NOT NULL DEFAULT 'unpaid',
 payment_reported_at DATETIME NULL,
 payment_confirmed_at DATETIME NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_bm_order_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
 INDEX idx_bm_orders_created (created_at), INDEX idx_bm_orders_status (status)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS order_items (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 order_id INT UNSIGNED NOT NULL,
 product_id INT UNSIGNED NULL,
 product_name VARCHAR(190) NOT NULL,
 price DECIMAL(12,2) NOT NULL,
 quantity INT UNSIGNED NOT NULL,
 CONSTRAINT fk_bm_item_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
 CONSTRAINT fk_bm_item_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS feedback (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL,
 message TEXT NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO users(id,fullname,email,phone,password_hash,role,gender,birth_date) VALUES
(1,'Chủ quán Bếp Mộc','admin@bepmoc.vn','0900000000','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','admin','other','1998-01-01'),
(2,'Nguyễn Minh Anh','minhanh@example.com','0901000001','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','female','2001-02-12'),
(3,'Trần Gia Bảo','giabao@example.com','0901000002','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','male','2000-06-22'),
(4,'Lê Thảo Vy','thaovy@example.com','0901000003','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','female','2002-09-14'),
(5,'Phạm Quốc Huy','quochuy@example.com','0901000004','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','male','1999-04-03'),
(6,'Võ Ngọc Hà','ngocha@example.com','0901000005','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','female','2001-12-18'),
(7,'Đặng Tuấn Khang','tuankhang@example.com','0901000006','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','male','2000-10-09'),
(8,'Bùi Khánh Linh','khanhlinh@example.com','0901000007','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','female','2002-07-11'),
(9,'Đỗ Đức Anh','ducanh@example.com','0901000008','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','male','1998-08-28'),
(10,'Ngô Phương Thảo','phuongthao@example.com','0901000009','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','female','2001-11-02'),
(11,'Hoàng Nhật Nam','nhatnam@example.com','0901000010','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','male','2000-03-19'),
(12,'Dương Mai Chi','maichi@example.com','0901000011','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','female','2003-05-07'),
(13,'Vũ Thanh Tùng','thanhtung@example.com','0901000012','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','male','1999-01-27'),
(14,'Đinh Bảo Ngọc','baongoc@example.com','0901000013','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','female','2002-02-25'),
(15,'Lý Hoàng Phúc','hoangphuc@example.com','0901000014','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','male','1998-12-04'),
(16,'Mai Ngọc Yến','ngocyen@example.com','0901000015','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','female','2001-03-15'),
(17,'Trương Nhật Quang','nhatquang@example.com','0901000016','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','male','2000-05-21'),
(18,'Nguyễn Hồng Nhung','hongnhung@example.com','0901000017','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','female','2002-10-30'),
(19,'Phan Minh Khoa','minhkhoa@example.com','0901000018','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','male','1999-07-08'),
(20,'Tài khoản dùng thử','demo@example.com','0901000019','$2y$10$YX8pbDEJIktks4gsYwJP5uA6lYJLXuYGktzDTSS8heIcKCCPlLS3W','customer','other','2001-01-01')
ON DUPLICATE KEY UPDATE fullname=VALUES(fullname),role=VALUES(role);

INSERT INTO categories(id,name,slug,description,is_active,sort_order) VALUES
(1,'Cơm nhà','com-nha','Mâm cơm nóng hổi, đủ vị thân quen.',1,1),(2,'Bún & phở','bun-pho','Nước dùng ninh thơm, ăn nóng ngon hơn.',1,2),(3,'Mì & nui','mi-nui','Món mì và nui làm mới mỗi ngày.',1,3),(4,'Món cuốn','mon-cuon','Cuốn tươi, nhẹ bụng, nhiều rau.',1,4),(5,'Món ăn nhẹ','mon-an-nhe','Món nhỏ dùng giữa buổi.',1,5),(6,'Món chay','mon-chay','Lựa chọn thanh đạm từ rau củ.',1,6),(7,'Combo','combo','Phần ăn kèm nước với mức giá dễ chịu.',1,7),(8,'Đồ uống','do-uong','Trà, nước trái cây và thức uống nhà làm.',1,8),(9,'Tráng miệng','trang-mieng','Chút ngọt khép lại bữa ăn.',1,9),(10,'Món đặc biệt','mon-dac-biet','Món theo mùa của Bếp Mộc.',1,10)
ON DUPLICATE KEY UPDATE name=VALUES(name),description=VALUES(description),sort_order=VALUES(sort_order);

INSERT INTO products(id,category_id,name,description,price,image_url,stock,is_active,is_featured) VALUES
(1,1,'Cơm gà nướng mật ong','Gà nướng vàng, cơm dẻo, rau theo ngày.',59000,'https://images.unsplash.com/photo-1532550907401-a500c9a57435?auto=format&fit=crop&w=900&q=85',20,1,1),
(2,1,'Cơm sườn nướng','Sườn ướp sả nướng thơm, ăn cùng đồ chua.',55000,'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=900&q=85',18,1,1),
(3,1,'Cơm cá kho tộ','Cá kho đậm đà, cơm trắng và canh rau.',62000,'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=900&q=85',12,1,0),
(4,1,'Cơm thịt kho trứng','Thịt kho mềm, trứng thấm vị, chuẩn cơm nhà.',52000,'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=85',15,1,0),
(5,2,'Phở bò tái','Nước dùng thơm quế hồi, bò tái mềm.',65000,'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=900&q=85',14,1,1),
(6,2,'Bún bò Huế','Nước dùng cay nhẹ, chả và thịt bò.',62000,'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=900&q=85',9,1,0),
(7,2,'Bún thịt nướng','Thịt nướng, rau thơm, đậu phộng và nước mắm.',55000,'https://images.unsplash.com/photo-1559847844-5315695dadae?auto=format&fit=crop&w=900&q=85',17,1,0),
(8,2,'Bún riêu cua','Riêu cua, đậu hũ và cà chua trong nước dùng thanh.',57000,'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&w=900&q=85',4,1,0),
(9,3,'Mì xào bò rau củ','Mì xào nóng với bò mềm và rau theo mùa.',59000,'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=900&q=85',16,1,0),
(10,3,'Nui sốt bò bằm','Nui mềm, sốt cà chua thịt bò nấu tại bếp.',52000,'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=900&q=85',11,1,0),
(11,4,'Gỏi cuốn tôm thịt','Cuốn tươi với tôm, thịt, bún và rau sống.',39000,'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=85',22,1,0),
(12,4,'Cuốn chay đậu hũ','Đậu hũ áp chảo, rau xanh và sốt mè rang.',35000,'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=900&q=85',8,1,0),
(13,5,'Chả giò nhà làm','Vỏ giòn, nhân thịt rau củ, dùng kèm nước chấm.',32000,'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=85',26,1,0),
(14,5,'Khoai lang kén','Khoai lang bùi thơm, chiên vàng giòn.',28000,'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=900&q=85',19,1,0),
(15,6,'Đậu hũ sốt nấm','Đậu hũ non và nấm sốt tiêu xanh.',49000,'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=900&q=85',7,1,0),
(16,6,'Cơm rau củ kho quẹt','Rau củ luộc theo ngày, kho quẹt chay.',51000,'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=85',13,1,0),
(17,7,'Combo cơm gà & trà tắc','Cơm gà nướng mật ong kèm trà tắc nhà làm.',69000,'https://images.unsplash.com/photo-1532550907401-a500c9a57435?auto=format&fit=crop&w=900&q=85',10,1,1),
(18,8,'Trà tắc mật ong','Trà thanh mát, tắc tươi và mật ong.',22000,'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=900&q=85',30,1,0),
(19,9,'Chè khúc bạch','Chè mát nhẹ với nhãn và hạnh nhân.',29000,'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=900&q=85',6,1,0),
(20,10,'Lẩu mini gà lá é','Phần lẩu nhỏ thơm lá é, dành cho một người.',89000,'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=85',5,1,1)
ON DUPLICATE KEY UPDATE name=VALUES(name),description=VALUES(description),price=VALUES(price),image_url=VALUES(image_url),is_featured=VALUES(is_featured);

INSERT INTO orders(id,code,user_id,customer_name,phone,address,note,total,status,payment_method,payment_status,payment_reported_at,payment_confirmed_at,created_at) VALUES
(1,'BM-DEMO-001',2,'Nguyễn Minh Anh','0901000001','12 Nguyễn Huệ, Quận 1','Ít hành',59000,'completed','cod','paid',NULL,'2026-10-01 09:25:00','2026-10-01 09:00:00'),
(2,'BM-DEMO-002',3,'Trần Gia Bảo','0901000002','22 Lê Lợi, Quận 1','',55000,'completed','bank_transfer','paid','2026-10-02 10:18:00','2026-10-02 10:25:00','2026-10-02 10:00:00'),
(3,'BM-DEMO-003',4,'Lê Thảo Vy','0901000003','8 Pasteur, Quận 1','',65000,'completed','cod','paid',NULL,'2026-10-03 12:10:00','2026-10-03 11:40:00'),
(4,'BM-DEMO-004',5,'Phạm Quốc Huy','0901000004','30 Hai Bà Trưng, Quận 1','',62000,'completed','bank_transfer','paid','2026-10-04 13:45:00','2026-10-04 13:52:00','2026-10-04 13:00:00'),
(5,'BM-DEMO-005',6,'Võ Ngọc Hà','0901000005','18 Võ Văn Tần, Quận 3','',55000,'completed','cod','paid',NULL,'2026-10-05 08:30:00','2026-10-05 08:10:00'),
(6,'BM-DEMO-006',7,'Đặng Tuấn Khang','0901000006','41 Điện Biên Phủ, Bình Thạnh','',57000,'completed','bank_transfer','paid','2026-10-06 09:16:00','2026-10-06 09:21:00','2026-10-06 09:00:00'),
(7,'BM-DEMO-007',8,'Bùi Khánh Linh','0901000007','5 Nguyễn Đình Chiểu, Quận 1','',39000,'completed','cod','paid',NULL,'2026-10-07 14:05:00','2026-10-07 13:40:00'),
(8,'BM-DEMO-008',9,'Đỗ Đức Anh','0901000008','20 Trần Hưng Đạo, Quận 5','',52000,'completed','bank_transfer','paid','2026-10-08 08:12:00','2026-10-08 08:17:00','2026-10-08 08:00:00'),
(9,'BM-DEMO-009',10,'Ngô Phương Thảo','0901000009','9 Nguyễn Thị Minh Khai, Quận 1','',65000,'completed','cod','paid',NULL,'2026-10-09 12:20:00','2026-10-09 12:00:00'),
(10,'BM-DEMO-010',11,'Hoàng Nhật Nam','0901000010','44 Phạm Viết Chánh, Phú Nhuận','',69000,'completed','bank_transfer','paid','2026-10-10 09:46:00','2026-10-10 09:50:00','2026-10-10 09:30:00'),
(11,'BM-DEMO-011',12,'Dương Mai Chi','0901000011','17 Trường Sa, Bình Thạnh','',92000,'completed','cod','paid',NULL,'2026-10-11 10:10:00','2026-10-11 09:50:00'),
(12,'BM-DEMO-012',13,'Vũ Thanh Tùng','0901000012','3 Nguyễn Văn Trỗi, Phú Nhuận','',110000,'completed','bank_transfer','paid','2026-10-12 16:20:00','2026-10-12 16:27:00','2026-10-12 16:00:00'),
(13,'BM-DEMO-013',14,'Đinh Bảo Ngọc','0901000013','28 Cách Mạng Tháng 8, Quận 3','',83000,'completed','cod','paid',NULL,'2026-10-13 08:25:00','2026-10-13 08:00:00'),
(14,'BM-DEMO-014',15,'Lý Hoàng Phúc','0901000014','11 Nguyễn Cư Trinh, Quận 1','',96000,'completed','bank_transfer','paid','2026-10-14 13:08:00','2026-10-14 13:12:00','2026-10-14 12:40:00'),
(15,'BM-DEMO-015',16,'Mai Ngọc Yến','0901000015','7 Bạch Đằng, Bình Thạnh','',102000,'completed','cod','paid',NULL,'2026-10-15 09:20:00','2026-10-15 09:00:00'),
(16,'BM-DEMO-016',17,'Trương Nhật Quang','0901000016','36 Nam Kỳ Khởi Nghĩa, Quận 1','',75000,'completed','bank_transfer','paid','2026-10-16 10:45:00','2026-10-16 10:51:00','2026-10-16 10:15:00'),
(17,'BM-DEMO-017',18,'Nguyễn Hồng Nhung','0901000017','9 Nguyễn Trãi, Quận 5','',99000,'completed','cod','paid',NULL,'2026-10-17 11:30:00','2026-10-17 11:00:00'),
(18,'BM-DEMO-018',19,'Phan Minh Khoa','0901000018','2 Hoàng Văn Thụ, Tân Bình','',87000,'completed','bank_transfer','paid','2026-10-18 14:35:00','2026-10-18 14:40:00','2026-10-18 14:00:00'),
(19,'BM-DEMO-019',20,'Tài khoản demo','0901000019','12 Nguyễn Huệ, Quận 1','',105000,'completed','cod','paid',NULL,'2026-10-19 08:40:00','2026-10-19 08:10:00'),
(20,'BM-DEMO-020',2,'Nguyễn Minh Anh','0901000001','12 Nguyễn Huệ, Quận 1','Giao trước 9h',68000,'completed','bank_transfer','paid','2026-10-20 08:15:00','2026-10-20 08:20:00','2026-10-20 08:00:00')
ON DUPLICATE KEY UPDATE status=VALUES(status),payment_status=VALUES(payment_status);

INSERT INTO order_items(order_id,product_id,product_name,price,quantity) VALUES
(1,1,'Cơm gà nướng mật ong',59000,1),(2,2,'Cơm sườn nướng',55000,1),(3,5,'Phở bò tái',65000,1),(4,6,'Bún bò Huế',62000,1),(5,7,'Bún thịt nướng',55000,1),(6,8,'Bún riêu cua',57000,1),(7,11,'Gỏi cuốn tôm thịt',39000,1),(8,10,'Nui sốt bò bằm',52000,1),(9,5,'Phở bò tái',65000,1),(10,17,'Combo cơm gà & trà tắc',69000,1),(11,3,'Cơm cá kho tộ',62000,1),(12,2,'Cơm sườn nướng',55000,2),(13,13,'Chả giò nhà làm',32000,1),(14,20,'Lẩu mini gà lá é',89000,1),(15,1,'Cơm gà nướng mật ong',59000,1),(16,9,'Mì xào bò rau củ',59000,1),(17,15,'Đậu hũ sốt nấm',49000,1),(18,4,'Cơm thịt kho trứng',52000,1),(19,16,'Cơm rau củ kho quẹt',51000,1),(20,18,'Trà tắc mật ong',22000,1);

INSERT INTO feedback(name,email,message,created_at) VALUES
('Nguyễn Minh Anh','minhanh@example.com','Cơm gà thơm, giao nhanh.','2026-10-01 10:00:00'),('Trần Gia Bảo','giabao@example.com','Sườn mềm và phần ăn vừa đủ.','2026-10-02 11:00:00'),('Lê Thảo Vy','thaovy@example.com','Phở thơm, nước dùng thanh.','2026-10-03 12:00:00'),('Phạm Quốc Huy','quochuy@example.com','Bún bò đậm vị, đóng gói kỹ.','2026-10-04 14:00:00'),('Võ Ngọc Hà','ngocha@example.com','Bún thịt nướng ngon.','2026-10-05 09:00:00'),('Đặng Tuấn Khang','tuankhang@example.com','Món ăn nóng khi nhận.','2026-10-06 10:00:00'),('Bùi Khánh Linh','khanhlinh@example.com','Gỏi cuốn tươi và nước chấm ngon.','2026-10-07 15:00:00'),('Đỗ Đức Anh','ducanh@example.com','Nui sốt bò bằm rất vừa miệng.','2026-10-08 09:00:00'),('Ngô Phương Thảo','phuongthao@example.com','Phở bò ngon, sẽ đặt lại.','2026-10-09 13:00:00'),('Hoàng Nhật Nam','nhatnam@example.com','Combo tiện, trà tắc mát.','2026-10-10 10:00:00'),('Dương Mai Chi','maichi@example.com','Cá kho đậm đà như cơm nhà.','2026-10-11 11:00:00'),('Vũ Thanh Tùng','thanhtung@example.com','Cơm sườn ngon, phần đầy đặn.','2026-10-12 17:00:00'),('Đinh Bảo Ngọc','baongoc@example.com','Chả giò giòn và ít dầu.','2026-10-13 09:00:00'),('Lý Hoàng Phúc','hoangphuc@example.com','Lẩu mini vừa một người ăn.','2026-10-14 14:00:00'),('Mai Ngọc Yến','ngocyen@example.com','Cơm gà mềm, sốt ngon.','2026-10-15 10:00:00'),('Trương Nhật Quang','nhatquang@example.com','Mì xào nhiều rau, nêm vừa.','2026-10-16 11:00:00'),('Nguyễn Hồng Nhung','hongnhung@example.com','Món chay rất ngon.','2026-10-17 12:00:00'),('Phan Minh Khoa','minhkhoa@example.com','Đặt hàng dễ, đóng gói sạch.','2026-10-18 15:00:00'),('Tài khoản demo','demo@example.com','Rau củ tươi, kho quẹt hợp vị.','2026-10-19 09:00:00'),('Nguyễn Minh Anh','minhanh@example.com','Trà tắc thơm và không quá ngọt.','2026-10-20 09:00:00');

-- Tài khoản demo: admin@bepmoc.vn và khách hàng dùng Cafe2026!
-- Để đặt admin mới qua tài khoản đã đăng ký: UPDATE users SET role='admin' WHERE email='email-cua-ban@example.com';

