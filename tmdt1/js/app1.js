
const loginForm = document.getElementById("loginForm");
const username = document.getElementById("username");
const password = document.getElementById("password");
const message = document.getElementById("message");
const togglePassword = document.getElementById("togglePassword");

// Hiện hoặc ẩn mật khẩu
togglePassword.addEventListener("click", function () {
    if (password.type === "password") {
        password.type = "text";
        togglePassword.textContent = "Ẩn";
    } else {
        password.type = "password";
        togglePassword.textContent = "Hiện";
    }
});

// Đăng nhập thử
loginForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const user = username.value.trim();
    const pass = password.value;

    if (user === "" || pass === "") {
        message.style.color = "red";
        message.textContent = "Vui lòng nhập đầy đủ thông tin.";
        return;
    }

    if (user === "hieudepzai" && pass === "123") {
    window.location.href = "index.html";
} else {
    message.style.color = "red";
    message.textContent =
        "Tên đăng nhập hoặc mật khẩu không chính xác.";
}
});

// Quên mật khẩu
document.getElementById("forgotPassword")
    .addEventListener("click", function (event) {
        event.preventDefault();

        message.style.color = "#006241";
        message.textContent =
            "Vui lòng liên hệ quản trị viên để được hỗ trợ.";
    });
