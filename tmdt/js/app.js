// ==========================================
// MỘC ĐỎ COFFEE & TEA - APP.JS (CÓ CHỨC NĂNG XÓA/BỎ MẶT HÀNG)
// ==========================================

// 1. DỮ LIỆU GIỎ HÀNG
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Save giỏ hàng vào LocalStorage
function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
}

// Cập nhật giao diện số lượng & render lại modal giỏ hàng
function updateCartUI() {
    // 1. Cập nhật Badge số lượng ở Header
    const cartCountElement = document.getElementById('cartCount');
    if (cartCountElement) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCountElement.textContent = totalItems;
    }

    // 2. Render lại danh sách sản phẩm trong Modal Giỏ Hàng (nếu đang mở)
    renderCartModalItems();
}

// Thêm sản phẩm vào giỏ
function addToCart(name, price, image) {
    const existingItem = cart.find(item => item.name === name);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            name: name,
            price: price,
            image: image,
            quantity: 1
        });
    }
    saveCart();
    showToast(`Đã thêm "${name}" vào giỏ!`);
}

// Xóa 1 sản phẩm khỏi giỏ hàng
function removeFromCart(index) {
    const removedName = cart[index].name;
    cart.splice(index, 1);
    saveCart();
    showToast(`Đã xóa "${removedName}" khỏi giỏ hàng.`);
}

// Thay đổi số lượng (+ / -)
function changeQuantity(index, delta) {
    if (cart[index]) {
        cart[index].quantity += delta;
        if (cart[index].quantity <= 0) {
            removeFromCart(index);
        } else {
            saveCart();
        }
    }
}

// 2. TẠO VÀ QUẢN LÝ MODAL GIỎ HÀNG (BẢNG XEM & XÓA ĐỒ)
function createCartModal() {
    if (document.getElementById('cartModal')) return;

    const modalHTML = `
        <div id="cartModal" class="cart-modal">
            <div class="cart-modal-overlay" id="cartOverlay"></div>
            <div class="cart-modal-content">
                <div class="cart-modal-header">
                    <h3>🛒 Giỏ hàng của bạn</h3>
                    <button id="cartCloseBtn" class="cart-close-btn">&times;</button>
                </div>
                <div class="cart-modal-body" id="cartModalBody">
                    <!-- Danh sách sản phẩm sẽ hiện ở đây -->
                </div>
                <div class="cart-modal-footer">
                    <div class="cart-total">
                        <strong>Tổng tiền:</strong>
                        <span id="cartTotalPrice">0đ</span>
                    </div>
                    <button class="checkout-btn" onclick="alert('Cảm ơn bạn đã đặt hàng tại Mộc Đỏ Coffee!')">Thanh toán ngay</button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // CSS trực tiếp cho Modal Giỏ hàng
    const style = document.createElement('style');
    style.textContent = `
        .cart-modal { display: none; position: fixed; inset: 0; z-index: 9999; }
        .cart-modal.active { display: block; }
        .cart-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); }
        .cart-modal-content {
            position: fixed; top: 0; right: 0; bottom: 0; width: 100%; max-width: 400px;
            background: #fff; z-index: 10000; display: flex; flex-direction: column;
            box-shadow: -4px 0 15px rgba(0,0,0,0.2); animation: slideIn 0.3s ease;
        }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
        .cart-modal-header { padding: 15px 20px; background: #78350f; color: #fff; display: flex; justify-content: space-between; align-items: center; }
        .cart-modal-header h3 { margin: 0; font-size: 18px; }
        .cart-close-btn { background: none; border: none; color: #fff; font-size: 24px; cursor: pointer; }
        .cart-modal-body { padding: 20px; flex: 1; overflow-y: auto; }
        .cart-item { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .cart-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; }
        .cart-item-info { flex: 1; }
        .cart-item-title { font-weight: 600; font-size: 14px; margin-bottom: 4px; color: #333; }
        .cart-item-price { color: #b45309; font-size: 13px; font-weight: 600; }
        .cart-item-qty { display: flex; align-items: center; gap: 6px; margin-top: 6px; }
        .qty-btn { width: 22px; height: 22px; border: 1px solid #ccc; background: #f9f9f9; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .delete-btn { background: none; border: none; color: #ef4444; font-size: 18px; cursor: pointer; padding: 4px; }
        .cart-modal-footer { padding: 20px; border-top: 1px solid #eee; background: #fdfbf7; }
        .cart-total { display: flex; justify-content: space-between; font-size: 16px; margin-bottom: 15px; }
        .cart-total span { color: #b45309; font-weight: bold; }
        .checkout-btn { width: 100%; padding: 12px; background: #b45309; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px; }
        .checkout-btn:hover { background: #92400e; }
        .empty-cart-msg { text-align: center; color: #777; margin-top: 40px; }
    `;
    document.head.appendChild(style);

    // Bắt sự kiện Đóng Modal
    document.getElementById('cartCloseBtn').addEventListener('click', closeCartModal);
    document.getElementById('cartOverlay').addEventListener('click', closeCartModal);
}

// Render dữ liệu từng món ăn trong Modal
function renderCartModalItems() {
    const body = document.getElementById('cartModalBody');
    const totalPriceEl = document.getElementById('cartTotalPrice');
    if (!body) return;

    if (cart.length === 0) {
        body.innerHTML = '<p class="empty-cart-msg">Giỏ hàng của bạn đang trống ☕</p>';
        if (totalPriceEl) totalPriceEl.textContent = '0đ';
        return;
    }

    let totalMoney = 0;
    body.innerHTML = cart.map((item, index) => {
        // Chuyển chuỗi giá tiền (vd: "25.000đ") thành số để tính toán
        const numericPrice = parseInt(item.price.replace(/[^\d]/g, '')) || 0;
        totalMoney += numericPrice * item.quantity;

        return `
            <div class="cart-item">
                <img src="${item.image}" alt="${item.name}">
                <div class="cart-item-info">
                    <div class="cart-item-title">${item.name}</div>
                    <div class="cart-item-price">${item.price}</div>
                    <div class="cart-item-qty">
                        <button class="qty-btn" onclick="changeQuantity(${index}, -1)">-</button>
                        <span>${item.quantity}</span>
                        <button class="qty-btn" onclick="changeQuantity(${index}, 1)">+</button>
                    </div>
                </div>
                <button class="delete-btn" onclick="removeFromCart(${index})" title="Xóa món này">🗑️</button>
            </div>
        `;
    }).join('');

    if (totalPriceEl) {
        totalPriceEl.textContent = totalMoney.toLocaleString('vi-VN') + 'đ';
    }
}

// Mở Modal Giỏ Hàng
function openCartModal() {
    createCartModal();
    renderCartModalItems();
    document.getElementById('cartModal').classList.add('active');
}

// Đóng Modal Giỏ Hàng
function closeCartModal() {
    const modal = document.getElementById('cartModal');
    if (modal) modal.classList.remove('active');
}

// 3. THÔNG BÁO TOAST
function showToast(message) {
    let toast = document.getElementById('toast-notification');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast-notification';
        toast.style.cssText = `
            position: fixed; bottom: 20px; right: 20px; background: #78350f; color: #fff;
            padding: 10px 18px; border-radius: 6px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            font-size: 13px; z-index: 10001; transition: all 0.3s ease; opacity: 0; transform: translateY(20px);
        `;
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
    }, 2000);
}

// 4. KÍCH HOẠT SỰ KIỆN KHI TRANG NẠP XONG
document.addEventListener('DOMContentLoaded', () => {
    updateCartUI();

    // Nút mở Giỏ hàng ở Header
    const cartOpenBtn = document.getElementById('cartOpen');
    if (cartOpenBtn) {
        cartOpenBtn.addEventListener('click', openCartModal);
    }

    // Nút "Thêm vào giỏ"
    const addButtons = document.querySelectorAll('.add-cart-btn');
    addButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const card = e.target.closest('.product-card');
            if (card) {
                const name = card.querySelector('h3') ? card.querySelector('h3').textContent.trim() : 'Sản phẩm';
                const price = card.querySelector('.price') ? card.querySelector('.price').textContent.trim() : '0đ';
                const image = card.querySelector('img') ? card.querySelector('img').src : '';
                addToCart(name, price, image);
            }
        });
    });

    // Toggle Mobile Menu
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', () => mainNav.classList.toggle('active'));
    }

    // Thanh Tìm kiếm
    const searchToggle = document.getElementById('searchToggle');
    const searchPanel = document.getElementById('searchPanel');
    const searchClose = document.getElementById('searchClose');
    const searchInput = document.getElementById('searchInput');

    if (searchToggle && searchPanel) {
        searchToggle.addEventListener('click', () => {
            searchPanel.classList.add('active');
            if (searchInput) searchInput.focus();
        });
    }
    if (searchClose && searchPanel) {
        searchClose.addEventListener('click', () => searchPanel.classList.remove('active'));
    }

    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const keyword = searchInput.value.toLowerCase().trim();
            document.querySelectorAll('.product-card').forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                card.style.display = title.includes(keyword) ? 'block' : 'none';
            });
            if (searchPanel) searchPanel.classList.remove('active');
        });
    }
});