-- Chạy một lần trên CSDL starbucks_quantri hiện có.
-- File này chỉ thêm cột và cập nhật ca mặc định cho admin; không xóa dữ liệu.
USE starbucks_quantri;

ALTER TABLE nguoi_dung
  ADD COLUMN loai_hinh_lam_viec ENUM('part_time','full_time') NOT NULL DEFAULT 'full_time' AFTER role,
  ADD COLUMN ca_bat_dau TIME NULL AFTER loai_hinh_lam_viec,
  ADD COLUMN ca_ket_thuc TIME NULL AFTER ca_bat_dau;

UPDATE nguoi_dung
SET ca_bat_dau='08:00:00', ca_ket_thuc='17:00:00'
WHERE role='admin' AND (ca_bat_dau IS NULL OR ca_ket_thuc IS NULL);