
const cart = new Map();

const cartCount = document.getElementById("cartCount");
const cartOverlay = document.getElementById("cartOverlay");
const cartItems = document.getElementById("cartItems");
const cartTotal = document.getElementById("cartTotal");
const toast = document.getElementById("toast");

let toastTimer;

function formatMoney(number) {
    return new Intl.NumberFormat("vi-VN").format(number) + "đ";
}

function showToast(message) {
    toast.textContent = message;
    toast.classList.add("show");

    clearTimeout(toastTimer);

    toastTimer = setTimeout(() => {
        toast.classList.remove("show");
    }, 2200);
}

// Thêm sản phẩm vào giỏ hàng
document.querySelectorAll("[data-add]").forEach(button => {
    button.addEventListener("click", () => {
        const product = button.closest(".product-card");

        const name = product.dataset.name;
        const price = Number(product.dataset.price);

        if (cart.has(name)) {
            cart.get(name).quantity++;
        } else {
            cart.set(name, {
                name: name,
                price: price,
                quantity: 1
            });
        }

        renderCart();
        showToast("Đã thêm " + name + " vào giỏ hàng!");
    });
});

// Hiển thị giỏ hàng
function renderCart() {
    const items = Array.from(cart.values());

    const count = items.reduce(
        (sum, item) => sum + item.quantity, 0
    );

    const total = items.reduce(
        (sum, item) => sum + item.price * item.quantity, 0
    );

    cartCount.textContent = count;
    cartTotal.textContent = formatMoney(total);

    if (items.length === 0) {
        cartItems.innerHTML =
            '<p class="empty-cart">Giỏ hàng đang trống.</p>';
        return;
    }

    cartItems.innerHTML = items.map(item => `
        <div class="cart-row">
            <div>
                <strong>${item.name}</strong>
                <small>${formatMoney(item.price)} / món</small>
            </div>

            <div class="quantity">
                <button data-change="-1"
                    data-name="${item.name}">−</button>

                <span>${item.quantity}</span>

                <button data-change="1"
                    data-name="${item.name}">+</button>
            </div>
        </div>
    `).join("");
}

// Tăng hoặc giảm số lượng
cartItems.addEventListener("click", event => {
    const button = event.target.closest("[data-change]");

    if (!button) return;

    const name = button.dataset.name;
    const item = cart.get(name);

    if (!item) return;

    item.quantity += Number(button.dataset.change);

    if (item.quantity <= 0) {
        cart.delete(name);
    }

    renderCart();
});

// Mở giỏ hàng
document.getElementById("cartOpen").addEventListener("click", () => {
    cartOverlay.classList.add("open");
});

// Đóng giỏ hàng
function closeCart() {
    cartOverlay.classList.remove("open");
}

document.getElementById("cartClose")
    .addEventListener("click", closeCart);

document.querySelector("[data-close-cart]")
    .addEventListener("click", closeCart);

document.addEventListener("keydown", event => {
    if (event.key === "Escape") {
        closeCart();
        closeSearch();
    }
});

// Lọc sản phẩm theo danh mục
const products = document.querySelectorAll(".product-card");

document.querySelectorAll(".category").forEach(button => {
    button.addEventListener("click", () => {
        document.querySelectorAll(".category").forEach(category => {
            category.classList.remove("active");
        });

        button.classList.add("active");

        const selected = button.dataset.category;

        products.forEach(product => {
            const matches =
                selected === "all" ||
                product.dataset.category === selected;

            product.classList.toggle("hidden", !matches);
        });
    });
});

// Mở và đóng tìm kiếm
const searchPanel = document.getElementById("searchPanel");
const searchInput = document.getElementById("searchInput");

document.getElementById("searchToggle")
    .addEventListener("click", () => {
        searchPanel.classList.toggle("open");

        if (searchPanel.classList.contains("open")) {
            searchInput.focus();
        }
    });

function closeSearch() {
    searchPanel.classList.remove("open");
}

document.getElementById("searchClose")
    .addEventListener("click", closeSearch);

// Tìm kiếm sản phẩm
document.getElementById("searchForm")
    .addEventListener("submit", event => {
        event.preventDefault();

        const keyword = searchInput.value
            .trim()
            .toLocaleLowerCase("vi");

        if (!keyword) {
            showToast("Vui lòng nhập tên sản phẩm!");
            return;
        }

        let found = 0;

        products.forEach(product => {
            const name = product.dataset.name
                .toLocaleLowerCase("vi");

            const matches = name.includes(keyword);

            product.classList.toggle("hidden", !matches);

            if (matches) found++;
        });

        document.querySelectorAll(".category").forEach(button => {
            button.classList.remove("active");
        });

        document.querySelector(
            '.category[data-category="all"]'
        ).classList.add("active");

        closeSearch();

        document.getElementById("menu")
            .scrollIntoView({ behavior: "smooth" });

        showToast(
            found > 0
                ? "Tìm thấy " + found + " sản phẩm."
                : "Không tìm thấy sản phẩm phù hợp."
        );
    });

// Nút đặt hàng demo
document.getElementById("checkoutButton")
    .addEventListener("click", () => {
        if (cart.size === 0) {
            showToast("Bạn chưa chọn sản phẩm!");
            return;
        }

        showToast(
            "Đặt hàng demo thành công! Chưa có hệ thống thanh toán."
        );
    });

// Menu dành cho điện thoại
const menuToggle = document.getElementById("menuToggle");
const mainNav = document.getElementById("mainNav");

menuToggle.addEventListener("click", () => {
    mainNav.classList.toggle("open");

    menuToggle.textContent =
        mainNav.classList.contains("open") ? "✕" : "☰";
});

mainNav.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", () => {
        mainNav.classList.remove("open");
        menuToggle.textContent = "☰";
    });
});

renderCart();
