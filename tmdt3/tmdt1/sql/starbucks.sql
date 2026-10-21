CREATE DATABASE IF NOT EXISTS starbucks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE starbucks;
CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, fullname VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE, phone VARCHAR(25), password_hash VARCHAR(255) NOT NULL,
 role ENUM('user','admin') NOT NULL DEFAULT 'user', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS categories (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL,
 slug VARCHAR(130) NOT NULL UNIQUE, description TEXT
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS products (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, category_id INT UNSIGNED NOT NULL,
 name VARCHAR(190) NOT NULL, description TEXT, price DECIMAL(12,2) NOT NULL,
 image_url VARCHAR(600), stock INT UNSIGNED NOT NULL DEFAULT 100,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS orders (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, code VARCHAR(35) NOT NULL UNIQUE,
 user_id INT UNSIGNED NULL, customer_name VARCHAR(120) NOT NULL,
 phone VARCHAR(25), address VARCHAR(350), total DECIMAL(12,2) NOT NULL,
 status ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
 payment_method ENUM('cod','bank_transfer') NOT NULL DEFAULT 'cod',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS order_items (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id INT UNSIGNED NOT NULL,
 product_id INT UNSIGNED NOT NULL, product_name VARCHAR(190) NOT NULL,
 price DECIMAL(12,2) NOT NULL, quantity INT UNSIGNED NOT NULL,
 FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
 FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS feedback (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL, message TEXT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
INSERT INTO categories (id,name,slug,description) VALUES
(1,'Cà phê','ca-phe','Đồ uống từ cà phê'),(2,'Trà','tra','Các loại trà và trà sữa'),(3,'Đá xay','da-xay','Đồ uống đá xay'),(4,'Bánh','banh','Bánh ngọt dùng kèm')
ON DUPLICATE KEY UPDATE name=VALUES(name);
INSERT INTO products(id,category_id,name,description,price,image_url,stock) VALUES
(1,1,'Caffè Latte','Espresso kết hợp sữa nóng mịn.',65000,'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=700',100),
(2,1,'Caramel Macchiato','Cà phê espresso, sữa và caramel.',79000,'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=700',100),
(3,1,'Cappuccino','Espresso và lớp bọt sữa thơm.',69000,'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=700',100),
(4,2,'Matcha Latte','Trà xanh matcha cùng sữa.',75000,'https://images.unsplash.com/photo-1515823064-d6e0c04616a7?w=700',100),
(5,3,'Coffee Frappuccino','Cà phê đá xay mát lạnh.',85000,'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=700',100),
(6,4,'Butter Croissant','Bánh sừng bò bơ giòn thơm.',49000,'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=700',100)
ON DUPLICATE KEY UPDATE name=VALUES(name);
-- Sau khi tự đăng ký tài khoản, cấp quyền quản trị trong phpMyAdmin:
-- UPDATE users SET role='admin' WHERE email='email-cua-ban@example.com';
