-- Chạy một lần trên CSDL hiện có đã có cột payment_status và payment_reported_at.
-- Đơn hiện có được chuyển sang đã thanh toán và ghi nhận thời điểm hiện tại.
ALTER TABLE don_hang ADD COLUMN payment_confirmed_at DATETIME NULL AFTER payment_reported_at;
UPDATE don_hang SET payment_status='paid', payment_reported_at=COALESCE(payment_reported_at,NOW()), payment_confirmed_at=NOW();
