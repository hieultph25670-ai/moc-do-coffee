document.addEventListener("DOMContentLoaded", function () {
    // 1. Quản lý Toggle Menu Mobile
    const menuToggle = document.getElementById("menuToggle");
    const navLinks = document.getElementById("navLinks");
    if (menuToggle && navLinks) {
        menuToggle.addEventListener("click", () => {
            navLinks.classList.toggle("open");
        });
    }

    // 2. Quản lý Giỏ hàng qua LocalStorage
    let cart = JSON.parse(localStorage.getItem("starbucks_cart")) || [];

    const cartCountEl = document.getElementById("cartCount");
    const cartButton = document.getElementById("cartButton");
    const cartModal = document.getElementById("cartModal");
    const closeCartModal = document.getElementById("closeCartModal");
    const cartModalBody = document.getElementById("cartModalBody");
    const cartTotalPrice = document.getElementById("cartTotalPrice");
    const toast = document.getElementById("toast");

    function openCart() {
        if (!cartModal) return;
        renderCartModal();
        cartModal.classList.add("active");
    }

    function closeCart() {
        if (!cartModal) return;
        cartModal.classList.remove("active");
    }

    function updateCartUI() {
        const totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);
        if (cartCountEl) cartCountEl.textContent = totalQty;
        localStorage.setItem("starbucks_cart", JSON.stringify(cart));
        renderCartModal();
    }

    function showToast(msg) {
        if (!toast) return;
        toast.textContent = msg;
        toast.classList.add("show");
        setTimeout(() => toast.classList.remove("show"), 2000);
    }

    // Sự kiện Thêm món vào giỏ
    document.querySelectorAll(".add-to-cart-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const name = this.getAttribute("data-name");
            const price = parseInt(this.getAttribute("data-price"));
            const img = this.getAttribute("data-img") || "";

            const existing = cart.find((i) => i.name === name);
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({ name, price, img, quantity: 1 });
            }

            updateCartUI();
            showToast(`Đã thêm "${name}" vào giỏ hàng!`);
        });
    });

    // Render món trong giỏ hàng
    function renderCartModal() {
        if (!cartModalBody) return;

        if (cart.length === 0) {
            cartModalBody.innerHTML = `<div class="cart-empty-msg" style="text-align:center; padding: 25px 0; color:#626b67;"><i class="fa-solid fa-cart-flatbed" style="font-size:32px; margin-bottom:8px; display:block;"></i>Giỏ hàng của bạn đang trống!</div>`;
            if (cartTotalPrice) cartTotalPrice.textContent = "0đ";
            return;
        }

        let html = "";
        let total = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            html += `
                <div class="cart-item-row" style="display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #e5e9e6;">
                    <img src="${item.img}" alt="${item.name}" class="cart-item-img" style="width:50px; height:50px; border-radius:8px; object-fit:cover;">
                    <div class="cart-item-details" style="flex:1;">
                        <div class="cart-item-title" style="font-weight:700; font-size:14px; color:#1e3932;">${item.name}</div>
                        <div class="cart-item-price" style="font-size:12px; color:#006241; font-weight:700;">${item.price.toLocaleString("vi-VN")}đ</div>
                    </div>
                    <div class="cart-item-actions" style="display:flex; align-items:center; gap:6px;">
                        <button class="cart-qty-btn" onclick="changeQty(${index}, -1)" style="width:26px; height:26px; border-radius:50%; border:1px solid #ccc; background:#fff; cursor:pointer;">-</button>
                        <span class="cart-item-qty" style="font-size:13px; font-weight:700; min-width:18px; text-align:center;">${item.quantity}</span>
                        <button class="cart-qty-btn" onclick="changeQty(${index}, 1)" style="width:26px; height:26px; border-radius:50%; border:1px solid #ccc; background:#fff; cursor:pointer;">+</button>
                        <button class="cart-remove-btn" onclick="removeItem(${index})" style="background:none; border:none; color:#d9534f; cursor:pointer; margin-left:6px;"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                </div>
            `;
        });

        cartModalBody.innerHTML = html;
        if (cartTotalPrice) cartTotalPrice.textContent = total.toLocaleString("vi-VN") + "đ";
    }

    // Tăng / giảm số lượng món
    window.changeQty = function (idx, change) {
        if (cart[idx]) {
            cart[idx].quantity += change;
            if (cart[idx].quantity <= 0) cart.splice(idx, 1);
            updateCartUI();
        }
    };

    // Xóa món khỏi giỏ
    window.removeItem = function (idx) {
        if (cart[idx]) {
            cart.splice(idx, 1);
            updateCartUI();
        }
    };

    // Sự kiện mở/đóng Giỏ hàng
    if (cartButton) {
        cartButton.addEventListener("click", openCart);
    }

    if (closeCartModal) {
        closeCartModal.addEventListener("click", function (e) {
            e.stopPropagation();
            closeCart();
        });
    }

    if (cartModal) {
        cartModal.addEventListener("click", function (e) {
            if (e.target === cartModal) {
                closeCart();
            }
        });
    }

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeCart();
        }
    });

    // 3. Lọc sản phẩm theo món riêng biệt khi bấm vào Sidebar Danh mục
    const searchInput = document.getElementById("searchInput");
    const filterItems = document.querySelectorAll("#filterList li");
    const productCards = document.querySelectorAll("#productGrid .product-card");

    let selectedItem = "all";
    let searchVal = "";

    function filterProducts() {
        productCards.forEach((card) => {
            const itemAttribute = card.getAttribute("data-item");
            const cardName = card.querySelector("h3").textContent.toLowerCase();

            // Nếu chọn "all" thì hiện hết, chọn món cụ thể thì chỉ hiện duy nhất món đó
            const matchItem = (selectedItem === "all" || itemAttribute === selectedItem);
            const matchSearch = cardName.includes(searchVal);

            if (matchItem && matchSearch) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    }

    // Sự kiện tìm kiếm ô text
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            searchVal = e.target.value.toLowerCase().trim();
            filterProducts();
        });
    }

    // Sự kiện bấm chọn món ở Sidebar
    filterItems.forEach((li) => {
        li.addEventListener("click", function () {
            filterItems.forEach((el) => el.classList.remove("active"));
            this.classList.add("active");

            selectedItem = this.getAttribute("data-item");
            filterProducts();
        });
    });

    updateCartUI();
});