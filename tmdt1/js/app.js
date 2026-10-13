document.addEventListener("DOMContentLoaded", () => {
    console.log("Đã kết nối file app.js thành công!");

    // 1. TỰ ĐỘNG CHÈN KHUNG POPUP GIỎ HÀNG VÀO TRANG NẾU CHƯA CÓ
    function initCartModal() {
        if (!document.getElementById("cartModal")) {
            const modalHTML = `
            <div class="cart-modal-overlay" id="cartModal">
                <div class="cart-modal-container">
                    <div class="cart-modal-header">
                        <h3><i class="fa-solid fa-bag-shopping"></i> Giỏ Hàng Của Bạn</h3>
                        <button class="cart-modal-close" id="closeCartBtn">&times;</button>
                    </div>
                    <div class="cart-modal-body" id="cartModalBody"></div>
                    <div class="cart-modal-footer">
                        <div class="cart-total-box">
                            <span>Tổng tiền thanh toán:</span>
                            <strong id="cartModalTotal">0đ</strong>
                        </div>
                        <button class="btn btn-dark btn-checkout" onclick="alert('Cảm ơn bạn đã đặt hàng!')">Thanh Toán Ngay</button>
                    </div>
                </div>
            </div>`;
            document.body.insertAdjacentHTML("beforeend", modalHTML);
        }

        // Tự động chèn CSS cho Popup Giỏ hàng
        if (!document.getElementById("cartModalStyle")) {
            const style = document.createElement("style");
            style.id = "cartModalStyle";
            style.textContent = `
                .cart-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 99999; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: 0.25s; }
                .cart-modal-overlay.active { opacity: 1; visibility: visible; }
                .cart-modal-container { background: #fff; width: 90%; max-width: 500px; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; max-height: 85vh; transform: translateY(20px); transition: 0.25s; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
                .cart-modal-overlay.active .cart-modal-container { transform: translateY(0); }
                .cart-modal-header { background: #006241; color: white; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; }
                .cart-modal-close { background: none; border: none; color: white; font-size: 26px; cursor: pointer; line-height: 1; }
                .cart-modal-body { padding: 20px; overflow-y: auto; flex: 1; }
                .cart-empty-msg { text-align: center; color: #626b67; padding: 25px 0; font-size: 15px; }
                .cart-item-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e9e6; gap: 10px; }
                .cart-item-info { flex: 1; }
                .cart-item-title { font-weight: bold; color: #1e3932; font-size: 15px; margin-bottom: 3px; }
                .cart-item-price { color: #006241; font-size: 13px; font-weight: bold; }
                .cart-item-actions { display: flex; align-items: center; gap: 8px; }
                .cart-qty-btn { width: 28px; height: 28px; border-radius: 50%; border: 1px solid #ccc; background: white; cursor: pointer; font-weight: bold; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: 0.15s; }
                .cart-qty-btn:hover { background: #006241; color: white; border-color: #006241; }
                .cart-remove-btn { background: none; border: none; color: #d9534f; cursor: pointer; font-size: 16px; margin-left: 6px; padding: 4px; }
                .cart-modal-footer { padding: 16px 20px; border-top: 1px solid #e5e9e6; background: #f9f9f9; }
                .cart-total-box { display: flex; justify-content: space-between; font-size: 16px; margin-bottom: 12px; }
                .cart-total-box strong { color: #006241; font-size: 19px; }
                .btn-checkout { width: 100%; padding: 12px; font-size: 15px; font-weight: bold; border-radius: 99px; cursor: pointer; }
            `;
            document.head.appendChild(style);
        }
    }

    initCartModal();

    // 2. KHAI BÁO CÁC PHẦN TỬ VÀ MẢNG GIỎ HÀNG
    let cart = JSON.parse(localStorage.getItem("starbucks_cart")) || [];
    const cartCount = document.getElementById("cartCount");
    const cartButton = document.getElementById("cartButton");
    const toast = document.getElementById("toast");

    const cartModal = document.getElementById("cartModal");
    const closeCartBtn = document.getElementById("closeCartBtn");
    const cartModalBody = document.getElementById("cartModalBody");
    const cartModalTotal = document.getElementById("cartModalTotal");

    let toastTimer;

    // Hiển thị thông báo Toast góc dưới
    function showToast(message) {
        if (!toast) return;
        toast.textContent = message;
        toast.classList.add("show");
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove("show"), 2800);
    }

    // Cập nhật số lượng đếm trên icon giỏ hàng
    function updateCartCount() {
        if (cartCount) {
            const totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartCount.textContent = String(totalQty);
        }
    }

    // Lưu giỏ hàng vào bộ nhớ máy
    function saveCart() {
        localStorage.setItem("starbucks_cart", JSON.stringify(cart));
        updateCartCount();
        renderCartModal();
    }

    // 3. XỬ LÝ NÚT "THÊM VÀO GIỎ" TRÊN CÁC SẢN PHẨM
    const addBtns = document.querySelectorAll(".add-to-cart-btn");
    addBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();

            // Đọc tên và giá từ thuộc tính data-
            let name = btn.getAttribute("data-name");
            let price = Number(btn.getAttribute("data-price"));

            // Nếu không tìm thấy trong data-, tự động quét từ thẻ HTML
            if (!name) {
                const card = btn.closest(".product-card");
                if (card) {
                    const titleEl = card.querySelector("h3");
                    const priceEl = card.querySelector(".product-price");
                    if (titleEl) name = titleEl.textContent.trim();
                    if (priceEl) price = Number(priceEl.textContent.replace(/[^0-9]/g, ""));
                }
            }

            name = name || "Sản phẩm";
            price = price || 50000;

            // Kiểm tra xem món đã có trong giỏ chưa
            const existing = cart.find(item => item.name === name);
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({ name, price, quantity: 1 });
            }

            saveCart();
            showToast(`Đã thêm "${name}" vào giỏ hàng.`);
        });
    });

    // 4. HIỂN THỊ CÁC MÓN TRONG POPUP GIỎ HÀNG
    function renderCartModal() {
        if (!cartModalBody) return;

        if (cart.length === 0) {
            cartModalBody.innerHTML = `<p class="cart-empty-msg">Giỏ hàng của bạn đang trống.</p>`;
            if (cartModalTotal) cartModalTotal.textContent = "0đ";
            return;
        }

        let total = 0;
        cartModalBody.innerHTML = cart.map((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            return `
                <div class="cart-item-row">
                    <div class="cart-item-info">
                        <div class="cart-item-title">${item.name}</div>
                        <div class="cart-item-price">${item.price.toLocaleString("vi-VN")}đ</div>
                    </div>
                    <div class="cart-item-actions">
                        <button class="cart-qty-btn" onclick="changeQty(${index}, -1)">-</button>
                        <span style="font-weight:bold; min-width:20px; text-align:center;">${item.quantity}</span>
                        <button class="cart-qty-btn" onclick="changeQty(${index}, 1)">+</button>
                        <button class="cart-remove-btn" onclick="removeItem(${index})" title="Xóa món">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join("");

        if (cartModalTotal) {
            cartModalTotal.textContent = total.toLocaleString("vi-VN") + "đ";
        }
    }

    // Hàm toàn cục tăng/giảm số lượng
    window.changeQty = function(index, delta) {
        if (cart[index]) {
            cart[index].quantity += delta;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            saveCart();
        }
    };

    // Hàm toàn cục xóa món
    window.removeItem = function(index) {
        if (cart[index]) {
            cart.splice(index, 1);
            saveCart();
        }
    };

    // 5. BẮT SỰ KIỆN CLICK MỞ / ĐÓNG POPUP GIỎ HÀNG
    if (cartButton) {
        cartButton.addEventListener("click", (e) => {
            e.preventDefault();
            renderCartModal();
            if (cartModal) cartModal.classList.add("active");
        });
    }

    if (closeCartBtn) {
        closeCartBtn.addEventListener("click", () => {
            if (cartModal) cartModal.classList.remove("active");
        });
    }

    if (cartModal) {
        cartModal.addEventListener("click", (e) => {
            if (e.target === cartModal) cartModal.classList.remove("active");
        });
    }

    // Toggle menu điện thoại
    const menuToggle = document.getElementById("menuToggle");
    const navLinks = document.getElementById("navLinks");
    if (menuToggle && navLinks) {
        menuToggle.addEventListener("click", () => {
            const isOpen = navLinks.classList.toggle("open");
            menuToggle.innerHTML = isOpen ? '<i class="fa-solid fa-xmark"></i>' : '<i class="fa-solid fa-bars"></i>';
        });
    }

    // Khởi tạo hiển thị ban đầu
    updateCartCount();
});