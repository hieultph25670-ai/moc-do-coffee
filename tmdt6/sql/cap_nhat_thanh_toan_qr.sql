-- Chạy một lần trên CSDL starbucks_quantri hiện có.
-- Thêm trạng thái thanh toán chuyển khoản; không thay đổi đơn cũ (mặc định chưa thanh toán).
USE starbucks_quantri;

ALTER TABLE don_hang
  ADD COLUMN payment_status ENUM('unpaid','reported','paid') NOT NULL DEFAULT 'unpaid',
  ADD COLUMN payment_reported_at DATETIME NULL;