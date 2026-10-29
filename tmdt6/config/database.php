<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'starbucks_quantri';
try {
    $db = new mysqli($host, $username, $password, $database);
    $db->set_charset('utf8mb4');
    // Ingredient inventory tables are created in place so existing installations
    // can use the new cafe stock module without re-importing the destructive seed SQL.
    $db->multi_query("CREATE TABLE IF NOT EXISTS nha_cung_cap (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, ten VARCHAR(140) NOT NULL, lien_he VARCHAR(120) NOT NULL DEFAULT '', dien_thoai VARCHAR(30) NOT NULL DEFAULT '', email VARCHAR(190) NOT NULL DEFAULT '', dia_chi VARCHAR(255) NOT NULL DEFAULT '', ghi_chu VARCHAR(500) NOT NULL DEFAULT '', active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS nguyen_lieu (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, ten VARCHAR(140) NOT NULL, don_vi VARCHAR(20) NOT NULL, gia_tham_khao DECIMAL(12,2) NULL DEFAULT NULL, ton_kho DECIMAL(12,3) NOT NULL DEFAULT 0, ton_toi_thieu DECIMAL(12,3) NOT NULL DEFAULT 0, supplier_id INT UNSIGNED NULL, active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CONSTRAINT fk_ingredient_supplier FOREIGN KEY(supplier_id) REFERENCES nha_cung_cap(id) ON DELETE SET NULL) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS dinh_muc (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_id INT UNSIGNED NOT NULL, ingredient_id INT UNSIGNED NOT NULL, luong DECIMAL(12,3) NOT NULL, UNIQUE KEY uq_recipe_product_ingredient(product_id,ingredient_id), CONSTRAINT fk_recipe_product FOREIGN KEY(product_id) REFERENCES san_pham(id) ON DELETE CASCADE, CONSTRAINT fk_recipe_ingredient FOREIGN KEY(ingredient_id) REFERENCES nguyen_lieu(id) ON DELETE RESTRICT) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS phieu_nhap (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, supplier_id INT UNSIGNED NULL, ghi_chu VARCHAR(500) NOT NULL DEFAULT '', created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CONSTRAINT fk_receipt_supplier FOREIGN KEY(supplier_id) REFERENCES nha_cung_cap(id) ON DELETE SET NULL) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS lich_su_cung_cap_san_pham (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, supplier_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, so_luong DECIMAL(12,3) NOT NULL, don_vi VARCHAR(20) NOT NULL DEFAULT 'ly', don_gia DECIMAL(12,2) NOT NULL DEFAULT 0, ghi_chu VARCHAR(500) NOT NULL DEFAULT '', created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CONSTRAINT fk_product_delivery_supplier FOREIGN KEY(supplier_id) REFERENCES nha_cung_cap(id) ON DELETE CASCADE, CONSTRAINT fk_product_delivery_product FOREIGN KEY(product_id) REFERENCES san_pham(id) ON DELETE CASCADE) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS chi_tiet_phieu_nhap (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, receipt_id INT UNSIGNED NOT NULL, ingredient_id INT UNSIGNED NOT NULL, so_luong DECIMAL(12,3) NOT NULL, don_gia DECIMAL(12,2) NOT NULL DEFAULT 0, CONSTRAINT fk_receipt_line_receipt FOREIGN KEY(receipt_id) REFERENCES phieu_nhap(id) ON DELETE CASCADE, CONSTRAINT fk_receipt_line_ingredient FOREIGN KEY(ingredient_id) REFERENCES nguyen_lieu(id) ON DELETE RESTRICT) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS tieu_hao_nguyen_lieu (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id INT UNSIGNED NOT NULL, ingredient_id INT UNSIGNED NOT NULL, so_luong DECIMAL(14,3) NOT NULL, reversed TINYINT(1) NOT NULL DEFAULT 0, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY uq_order_ingredient(order_id,ingredient_id), CONSTRAINT fk_usage_order FOREIGN KEY(order_id) REFERENCES don_hang(id) ON DELETE CASCADE, CONSTRAINT fk_usage_ingredient FOREIGN KEY(ingredient_id) REFERENCES nguyen_lieu(id) ON DELETE RESTRICT) ENGINE=InnoDB;");
    while ($db->more_results()) { $db->next_result(); if ($result=$db->store_result()) $result->free(); }
    $deliveryUnitColumn=$db->query("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='lich_su_cung_cap_san_pham' AND COLUMN_NAME='don_vi'")->fetch_row();
    if (!$deliveryUnitColumn) $db->query("ALTER TABLE lich_su_cung_cap_san_pham ADD COLUMN don_vi VARCHAR(20) NOT NULL DEFAULT 'ly' AFTER so_luong");
    $marketPriceColumn=$db->query("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='nguyen_lieu' AND COLUMN_NAME='gia_tham_khao'")->fetch_row();
    if (!$marketPriceColumn) $db->query('ALTER TABLE nguyen_lieu ADD COLUMN gia_tham_khao DECIMAL(12,2) NULL DEFAULT NULL AFTER don_vi');
    // Editable starter references are per stored unit (g, ml, or piece); preserve user-entered values.
    $db->query("UPDATE nguyen_lieu SET gia_tham_khao=CASE ten
        WHEN 'Hạt cà phê' THEN 160 WHEN 'Sữa đặc' THEN 45 WHEN 'Sữa tươi' THEN 32 WHEN 'Đá viên' THEN 1
        WHEN 'Cam tươi' THEN 25 WHEN 'Trà đen' THEN 300 WHEN 'Đào ngâm' THEN 35 WHEN 'Sả tươi' THEN 20
        WHEN 'Bột matcha' THEN 1100 WHEN 'Bột chocolate' THEN 250 WHEN 'Syrup cà phê' THEN 110 WHEN 'Kem tươi' THEN 180
        WHEN 'Trà ô long' THEN 400 WHEN 'Trân châu' THEN 35 WHEN 'Croissant' THEN 12000 WHEN 'Bánh chuối' THEN 15000
        WHEN 'Sữa yến mạch' THEN 55 WHEN 'Nước cốt dừa' THEN 45 WHEN 'Trà hoa nhài' THEN 350 WHEN 'Syrup caramel' THEN 110
        WHEN 'Vải ngâm' THEN 40 WHEN 'Hạt chia' THEN 180 WHEN 'Syrup chanh dây' THEN 90 WHEN 'Soda' THEN 18
        WHEN 'Ly giấy' THEN 450 WHEN 'Nắp ly' THEN 180 WHEN 'Ống hút' THEN 150 WHEN 'Hộp đựng bánh' THEN 1200
        ELSE 0 END WHERE gia_tham_khao IS NULL");
    $modeColumn=$db->query("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='san_pham' AND COLUMN_NAME='ingredient_mode'")->fetch_row();
    if (!$modeColumn) $db->query('ALTER TABLE san_pham ADD COLUMN ingredient_mode TINYINT(1) NOT NULL DEFAULT 0 AFTER stock');
    // Widen the original VARCHAR(100) once so existing databases can store full image links.
    $column = $db->query("SELECT CHARACTER_MAXIMUM_LENGTH AS length FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='san_pham' AND COLUMN_NAME='image'")->fetch_assoc();
    if ($column && (int)$column['length'] < 1000) {
        $db->query('ALTER TABLE san_pham MODIFY image VARCHAR(1000) NOT NULL');
    }
} catch (mysqli_sql_exception $ex) {
    http_response_code(500);
    error_log('Database connection failed: '.$ex->getMessage());
    exit('Không thể kết nối MySQL. Hãy kiểm tra XAMPP và import sql/starbucks_quantri.sql.');
}
