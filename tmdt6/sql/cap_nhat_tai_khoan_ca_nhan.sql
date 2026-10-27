-- Chạy một lần trên CSDL starbucks_quantri hiện có.
-- Nếu chưa cập nhật ca làm nhân viên, hãy import cap_nhat_nhan_vien_ca_lam.sql trước.
-- File này không xóa người dùng, sản phẩm hay lịch sử đơn hàng.
USE starbucks_quantri;

ALTER TABLE nguoi_dung
  ADD COLUMN gioi_tinh ENUM('nam','nu','khac') NULL,
  ADD COLUMN ngay_sinh DATE NULL,
  ADD COLUMN trang_thai_tai_khoan ENUM('active','deletion_pending','deleted') NOT NULL DEFAULT 'active';

CREATE TABLE IF NOT EXISTS yeu_cau_xoa_tai_khoan (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NULL,
 ten_nguoi_dung VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL,
 trang_thai ENUM('cho_duyet','da_duyet','tu_choi') NOT NULL DEFAULT 'cho_duyet',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 admin_xu_ly INT UNSIGNED NULL,
 thoi_gian_xu_ly DATETIME NULL,
 CONSTRAINT fk_deletion_request_user FOREIGN KEY(user_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL,
 CONSTRAINT fk_deletion_request_admin FOREIGN KEY(admin_xu_ly) REFERENCES nguoi_dung(id) ON DELETE SET NULL
) ENGINE=InnoDB;