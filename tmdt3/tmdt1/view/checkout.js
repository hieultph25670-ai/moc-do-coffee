/* =========================================================
   THANH TOÁN DÙNG CHUNG: BILL + QR CHUYỂN KHOẢN
   Được dùng cho danhmuc.php và sanpham.php
   ========================================================= */
(function () {
    document.addEventListener("DOMContentLoaded", function () {

        // ✅ SỬA THÔNG TIN TÀI KHOẢN NGÂN HÀNG CỦA BẠN TẠI ĐÂY:
        const BANK_INFO = {
              bankId: "970422",          //   https://api.vietqr.io/v2/banks
                accountNo: "0110148468888",   // Số tài khoản nhận tiền
                accountName: "Lê Trung Hiếu", // Tên chủ tài khoản
                bankName: "MB Bank"        // Tên ngân hàng hiển thị
        };

        // 1. CHÈN CSS CHO POPUP THANH TOÁN
        if (!document.getElementById("checkoutStyle")) {
            const style = document.createElement("style");
            style.id = "checkoutStyle";
            style.textContent = `
            .checkout-overlay { position: fixed; inset: 0; z-index: 100001; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(0,0,0,0.65); opacity: 0; visibility: hidden; transition: 0.25s; }
            .checkout-overlay.active { opacity: 1; visibility: visible; }
            .checkout-box { width: 100%; max-width: 460px; max-height: 88vh; display: flex; flex-direction: column; overflow: hidden; border-radius: 16px; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
            .checkout-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; color: white; background: #006241; }
            .checkout-header h3 { font-size: 17px; margin: 0; }
            .checkout-close { border: 0; background: transparent; color: white; font-size: 26px; cursor: pointer; line-height: 1; }
            .checkout-body { flex: 1; padding: 20px; overflow-y: auto; }
            .bill-code { margin-bottom: 14px; padding: 10px 14px; border-radius: 10px; background: #eaf3ee; font-size: 13px; }
            .bill-code strong { color: #006241; }
            .bill-table { width: 100%; margin-bottom: 14px; border-collapse: collapse; font-size: 13.5px; }
            .bill-table th, .bill-table td { padding: 8px 4px; border-bottom: 1px solid #e5e9e6; text-align: left; }
            .bill-table th { color: #626b67; font-size: 12px; text-transform: uppercase; }
            .bill-table td:last-child, .bill-table th:last-child { text-align: right; white-space: nowrap; }
            .bill-total { display: flex; justify-content: space-between; margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #006241; color: white; font-size: 16px; font-weight: bold; }
            .bill-total strong { font-size: 19px; }
            .qr-box { margin-bottom: 8px; padding: 16px; border: 2px dashed #006241; border-radius: 12px; text-align: center; background: #f1f8f5; }
            .qr-box img { width: 240px; max-width: 100%; height: auto; margin: 0 auto 10px; background: white; border-radius: 8px; }
            .qr-bank-info { color: #1e3932; font-size: 13.5px; line-height: 1.7; }
            .qr-bank-info b { color: #006241; }
            .qr-note { margin-top: 10px; color: #626b67; font-size: 12.5px; font-style: italic; }
            .checkout-footer { padding: 14px 20px; border-top: 1px solid #e5e9e6; background: #f9f9f9; }
            .btn-done { width: 100%; padding: 12px; border: 0; border-radius: 99px; background: #006241; color: white; font-size: 15px; font-weight: bold; cursor: pointer; }
            .btn-done:hover { background: #004c36; }`;
            document.head.appendChild(style);
        }

        // 2. CHÈN KHUNG POPUP THANH TOÁN
        if (!document.getElementById("checkoutOverlay")) {
            document.body.insertAdjacentHTML("beforeend", `
            <div class="checkout-overlay" id="checkoutOverlay">
                <div class="checkout-box">
                    <div class="checkout-header">
                        <h3><i class="fa-solid fa-receipt"></i> Hóa Đơn Thanh Toán</h3>
                        <button class="checkout-close" id="closeCheckoutBtn">&times;</button>
                    </div>
                    <div class="checkout-body" id="checkoutBody"></div>
                    <div class="checkout-footer">
                        <button class="btn-done" id="doneOrderBtn">
                            <i class="fa-solid fa-circle-check"></i> Tôi đã thanh toán - Hoàn tất
                        </button>
                    </div>
                </div>
            </div>`);
        }

        const overlay = document.getElementById("checkoutOverlay");
        const toast = document.getElementById("toast");

        function showToast(msg) {
            if (!toast) return;
            toast.textContent = msg;
            toast.classList.add("show");
            setTimeout(() => toast.classList.remove("show"), 2500);
        }

        // 3. XỬ LÝ BẤM "TIẾN HÀNH THANH TOÁN"
        const checkoutBtn = document.getElementById("checkoutBtn");
        if (checkoutBtn) {
            checkoutBtn.addEventListener("click", function (e) {
                e.preventDefault();

                let cart = [];
                try {
                    cart = JSON.parse(localStorage.getItem("starbucks_cart")) || [];
                } catch (err) {
                    cart = [];
                }

                if (cart.length === 0) {
                    showToast("Giỏ hàng đang trống, không thể thanh toán!");
                    return;
                }

                const total = cart.reduce((sum, i) => sum + Number(i.price) * Number(i.quantity), 0);
                const orderCode = "SB" + Date.now().toString().slice(-6);

                // QR chuyển khoản qua VietQR (nhúng sẵn số tiền + nội dung)
                const qrUrl = `https://img.vietqr.io/image/${BANK_INFO.bankId}-${BANK_INFO.accountNo}-compact2.png?amount=${total}&addInfo=${orderCode}`;

                // Lấy tên khách hàng đang đăng nhập (nếu có)
                let customerName = "Khách";
                try {
                    const u = JSON.parse(localStorage.getItem("currentUser") || sessionStorage.getItem("currentUser"));
                    if (u) customerName = u.fullname || u.name || u.username || u.email || "Khách";
                } catch (err) { }

                document.getElementById("checkoutBody").innerHTML = `
                    <div class="bill-code">
                        Khách hàng: <strong>${customerName}</strong><br>
                        Mã đơn hàng: <strong>${orderCode}</strong> — ${new Date().toLocaleString("vi-VN")}
                    </div>
                    <table class="bill-table">
                        <tr><th>Sản phẩm</th><th>SL</th><th>Thành tiền</th></tr>
                        ${cart.map(i => `<tr><td>${i.name}</td><td>${i.quantity}</td><td>${(Number(i.price) * Number(i.quantity)).toLocaleString("vi-VN")}đ</td></tr>`).join("")}
                    </table>
                    <div class="bill-total"><span>Tổng cộng:</span><strong>${total.toLocaleString("vi-VN")}đ</strong></div>
                    <div class="qr-box">
                        <img src="${qrUrl}" alt="Mã QR chuyển khoản">
                        <div class="qr-bank-info">
                            Ngân hàng: <b>${BANK_INFO.bankName}</b><br>
                            Số tài khoản: <b>${BANK_INFO.accountNo}</b><br>
                            Chủ tài khoản: <b>${BANK_INFO.accountName}</b><br>
                            Số tiền: <b>${total.toLocaleString("vi-VN")}đ</b> — Nội dung: <b>${orderCode}</b>
                        </div>
                        <p class="qr-note">Vui lòng quét mã QR hoặc chuyển khoản theo thông tin trên. Cảm ơn bạn đã mua hàng!</p>
                    </div>`;

                overlay.classList.add("active");
            });
        }

        // 4. ĐÓNG POPUP
        const closeCheckoutBtn = document.getElementById("closeCheckoutBtn");
        if (closeCheckoutBtn) {
            closeCheckoutBtn.addEventListener("click", () => overlay.classList.remove("active"));
        }
        overlay.addEventListener("click", (e) => {
            if (e.target === overlay) overlay.classList.remove("active");
        });
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && overlay.classList.contains("active")) overlay.classList.remove("active");
        });

        // 5. HOÀN TẤT ĐƠN HÀNG: XÓA GIỎ + IN HÓA ĐƠN
        const doneOrderBtn = document.getElementById("doneOrderBtn");
        if (doneOrderBtn) {
            doneOrderBtn.addEventListener("click", function () {
                overlay.classList.remove("active");

                // Lưu nội dung hóa đơn để in (bao gồm cả QR)
                const savedBill = document.getElementById("checkoutBody").innerHTML;

                // Xóa giỏ + cập nhật giao diện
                localStorage.setItem("starbucks_cart", "[]");
                const countEl = document.getElementById("cartCount");
                const floatingCount = document.getElementById("floatingCartCount");
                if (countEl) countEl.textContent = "0";
                if (floatingCount) floatingCount.textContent = "0";
                const totalEl = document.getElementById("cartTotalPrice");
                if (totalEl) totalEl.textContent = "0đ";
                const body = document.getElementById("cartModalBody");
                if (body) body.innerHTML = `<div class="cart-empty-msg" style="text-align:center; padding: 25px 0; color:#626b67;"><i class="fa-solid fa-cart-flatbed" style="font-size:32px; margin-bottom:8px; display:block;"></i>Giỏ hàng của bạn đang trống!</div>`;

                showToast("Cảm ơn bạn đã đặt hàng! Đơn hàng đã được ghi nhận.");

                // In hóa đơn sản phẩm đã mua
                printBill(savedBill);
            });
        }

        // ===== IN HÓA ĐƠN (mở giao diện in của trình duyệt) =====
        function printBill(billHTML) {
            const printHTML = `
                <div style="font-family: Arial, Helvetica, sans-serif; max-width: 420px; margin: 0 auto; color:#1e3932;">
                    <div style="text-align:center; border-bottom:2px solid #006241; padding-bottom:12px; margin-bottom:16px;">
                        <div style="font-size:22px; font-weight:bold; color:#006241; letter-spacing:2px;">STARBUCKS</div>
                        <div style="font-size:12px; color:#626b67;">HÓA ĐƠN THANH TOÁN</div>
                    </div>
                    ${billHTML}
                    <div style="margin-top:18px; padding-top:12px; border-top:1px solid #ccc; text-align:center; font-size:12px; color:#626b67;">
                        Cảm ơn bạn đã mua hàng tại Starbucks!<br>
                        Hotline: 1900-xxxx — www.starbucks.com.vn
                    </div>
                </div>`;

            const iframe = document.createElement("iframe");
            iframe.style.position = "fixed";
            iframe.style.right = "0";
            iframe.style.bottom = "0";
            iframe.style.width = "0";
            iframe.style.height = "0";
            iframe.style.border = "0";
            document.body.appendChild(iframe);

            const doc = iframe.contentWindow.document;
            doc.open();
            doc.write(`<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>Hóa đơn - Starbucks</title>
                <style>
                    body { margin: 24px; }
                    .bill-code { margin-bottom: 14px; padding: 10px 14px; border-radius: 8px; background: #eaf3ee; font-size: 13px; }
                    .bill-code strong { color: #006241; }
                    .bill-table { width: 100%; margin-bottom: 14px; border-collapse: collapse; font-size: 13.5px; }
                    .bill-table th, .bill-table td { padding: 8px 4px; border-bottom: 1px solid #e5e9e6; text-align: left; }
                    .bill-table th { color: #626b67; font-size: 12px; text-transform: uppercase; }
                    .bill-table td:last-child, .bill-table th:last-child { text-align: right; white-space: nowrap; }
                    .bill-total { display: flex; justify-content: space-between; margin-bottom: 16px; padding: 12px 14px; border-radius: 8px; background: #006241; color: white; font-size: 16px; font-weight: bold; }
                    .bill-total strong { font-size: 19px; }
                    .qr-box { margin: 0 auto 8px; padding: 12px; border: 2px dashed #006241; border-radius: 8px; text-align: center; background: #f1f8f5; }
                    .qr-box img { width: 200px; max-width: 100%; height: auto; margin: 0 auto 8px; }
                    .qr-bank-info { font-size: 12.5px; line-height: 1.6; }
                    .qr-bank-info b { color: #006241; }
                    .qr-note { margin-top: 8px; color: #626b67; font-size: 11.5px; font-style: italic; }
                    @media print { body { margin: 0; } }
                </style></head><body>${printHTML}</body></html>`);
            doc.close();

            // Chờ ảnh QR tải xong rồi mới mở giao diện in
            iframe.contentWindow.focus();
            const qrImg = doc.querySelector(".qr-box img");
            if (qrImg && !qrImg.complete) {
                qrImg.onload = () => setTimeout(() => iframe.contentWindow.print(), 300);
            } else {
                setTimeout(() => iframe.contentWindow.print(), 300);
            }
        }
    });
})();
