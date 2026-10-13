
document.addEventListener("DOMContentLoaded", () => {
    console.log("Đã kết nối thành công file js/app.js!");

    const cartButtons = document.querySelectorAll(".add-to-cart-btn");
    const cartCount = document.getElementById("cartCount");
    const cartButton = document.getElementById("cartButton");
    const toast = document.getElementById("toast");

    const menuToggle = document.getElementById("menuToggle");
    const navLinks = document.getElementById("navLinks");

    const newsletterForm = document.getElementById("newsletterForm");
    const storeLink = document.getElementById("storeLink");

    let totalItems = 0;
    const cart = [];
    let toastTimer;

    // Hiển thị thông báo
    function showToast(message) {
        if (!toast) return;

        toast.textContent = message;
        toast.classList.add("show");

        clearTimeout(toastTimer);

        toastTimer = setTimeout(() => {
            toast.classList.remove("show");
        }, 2800);
    }

    // Cập nhật số lượng giỏ hàng
    function updateCartCount() {
        if (cartCount) {
            cartCount.textContent = String(totalItems);
        }
    }

    // Thêm sản phẩm vào giỏ hàng
    cartButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const productName = button.dataset.name || "Sản phẩm";
            const productPrice = Number(button.dataset.price || 0);

            cart.push({
                name: productName,
                price: productPrice
            });

            totalItems++;

            updateCartCount();

            showToast(`Đã thêm "${productName}" vào giỏ hàng.`);
        });
    });

    // Xem giỏ hàng
    if (cartButton) {
        cartButton.addEventListener("click", () => {
            if (cart.length === 0) {
                showToast("Giỏ hàng của bạn đang trống.");
                return;
            }

            const summary = cart.reduce((result, item) => {
                result[item.name] = (result[item.name] || 0) + 1;
                return result;
            }, {});

            const lines = Object.entries(summary).map(([name, quantity]) => {
                return `${name} x ${quantity}`;
            });

            const totalPrice = cart.reduce((sum, item) => {
                return sum + item.price;
            }, 0);

            const formattedTotal =
                totalPrice.toLocaleString("vi-VN") + "đ";

            alert(
                "GIỎ HÀNG CỦA BẠN\n\n" +
                lines.join("\n") +
                "\n\nTổng tạm tính: " + formattedTotal +
                "\n\nGiỏ hàng minh họa phục vụ bài tập giao diện."
            );
        });
    }

    // Mở và đóng menu trên điện thoại
    if (menuToggle && navLinks) {
        menuToggle.addEventListener("click", () => {
            const isOpen = navLinks.classList.toggle("open");

            menuToggle.setAttribute(
                "aria-expanded",
                String(isOpen)
            );

            menuToggle.setAttribute(
                "aria-label",
                isOpen ? "Đóng menu" : "Mở menu"
            );

            menuToggle.innerHTML = isOpen
                ? '<i class="fa-solid fa-xmark"></i>'
                : '<i class="fa-solid fa-bars"></i>';
        });

        navLinks.querySelectorAll("a").forEach((link) => {
            link.addEventListener("click", () => {
                navLinks.classList.remove("open");

                menuToggle.setAttribute("aria-expanded", "false");
                menuToggle.setAttribute("aria-label", "Mở menu");

                menuToggle.innerHTML =
                    '<i class="fa-solid fa-bars"></i>';
            });
        });
    }

    // Xử lý form đăng ký email minh họa
    if (newsletterForm) {
        newsletterForm.addEventListener("submit", (event) => {
            event.preventDefault();

            const emailInput =
                document.getElementById("newsletterEmail");

            if (!emailInput || !emailInput.checkValidity()) {
                if (emailInput) {
                    emailInput.reportValidity();
                }

                return;
            }

            showToast(
                "Đăng ký thành công! Cảm ơn bạn đã quan tâm."
            );

            newsletterForm.reset();
        });
    }

    // Nút tìm cửa hàng
    if (storeLink) {
        storeLink.addEventListener("click", (event) => {
            event.preventDefault();

            showToast(
                "Chức năng tìm cửa hàng sẽ được bổ sung sau."
            );
        });
    }

    updateCartCount();
});
