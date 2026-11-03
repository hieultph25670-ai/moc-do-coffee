-- Chạy một lần trên CSDL starbucks_quantri hiện tại.
ALTER TABLE nguoi_dung
 MODIFY role ENUM('customer','staff','admin') NOT NULL DEFAULT 'customer',
 ADD COLUMN ngay_nghi_viec DATE NULL AFTER ca_ket_thuc;

CREATE TABLE cham_cong (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 ngay_lam DATE NOT NULL,
 gio_vao DATETIME NOT NULL,
 gio_ra DATETIME NULL,
 tong_gio DECIMAL(7,2) NOT NULL DEFAULT 0,
 UNIQUE KEY uq_cham_cong_nhan_vien_ngay(user_id,ngay_lam),
 KEY idx_cham_cong_ngay(ngay_lam),
 CONSTRAINT fk_cham_cong_nhan_vien FOREIGN KEY(user_id) REFERENCES nguoi_dung(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Giữ bihieudeptrai@gmail.com làm admin; chuyển những admin hiện tại còn lại thành staff.
UPDATE nguoi_dung SET role='staff' WHERE role='admin' AND email<>'bihieudeptrai@gmail.com';
UPDATE nguoi_dung SET role='admin' WHERE email='bihieudeptrai@gmail.com';
