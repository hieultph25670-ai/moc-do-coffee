-- Chạy một lần trên CSDL starbucks_quantri đang sử dụng.
ALTER TABLE chi_tiet_don_hang
 ADD COLUMN sugar_percent TINYINT UNSIGNED NULL AFTER quantity,
 ADD COLUMN ice_percent TINYINT UNSIGNED NULL AFTER sugar_percent;
