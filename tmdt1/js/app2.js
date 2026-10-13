document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registerForm");

    if (!form) return;

    const fields = {
        fullname: document.getElementById("fullname"),
        email: document.getElementById("email"),
        phone: document.getElementById("phone"),
        password: document.getElementById("password"),
        confirmPassword: document.getElementById("confirmPassword"),
        terms: document.getElementById("terms")
    };

    const submitButton = form.querySelector(".btn-submit");

    // Tao vung thong bao
    const feedback = document.createElement("div");

    feedback.className = "form-feedback";
    feedback.id = "form-feedback";
    feedback.setAttribute("role", "status");
    feedback.setAttribute("aria-live", "polite");
    feedback.hidden = true;

    submitButton.insertAdjacentElement("afterend", feedback);

    // HIEN / AN MAT KHAU
    document.querySelectorAll(".toggle-password").forEach(function (button) {
        button.addEventListener("click", function () {
            const input = document.getElementById(button.dataset.target);

            if (!input) return;

            const show = input.type === "password";

            input.type = show ? "text" : "password";

            button.setAttribute("aria-pressed", String(show));
            button.setAttribute(
                "aria-label",
                show ? "Ẩn mật khẩu" : "Hiện mật khẩu"
            );

            const icon = button.querySelector("i");

            if (icon) {
                icon.classList.toggle("fa-eye", !show);
                icon.classList.toggle("fa-eye-slash", show);
            }
        });
    });

    // HIEN THI LOI
    function showError(name, message) {
        const field = fields[name];
        const error = document.getElementById(name + "-error");

        if (name === "terms") {
            if (error) error.textContent = message;

            field.setAttribute(
                "aria-invalid",
                message ? "true" : "false"
            );

            return;
        }

        if (!field) return;

        const wrap = field.closest(".input-wrap");

        if (wrap) {
            wrap.classList.toggle("has-error", Boolean(message));
        }

        field.setAttribute(
            "aria-invalid",
            message ? "true" : "false"
        );

        if (error) error.textContent = message;
    }

    function clearError(name) {
        showError(name, "");
    }

    function showFeedback(message, type = "success") {
        feedback.className = "form-feedback";

        if (type === "error") {
            feedback.classList.add("error");
        }

        const icon = document.createElement("i");

        icon.className = type === "error"
            ? "fa-solid fa-circle-exclamation"
            : "fa-solid fa-circle-check";

        feedback.replaceChildren(
            icon,
            document.createTextNode(message)
        );

        feedback.hidden = false;
    }

    function hideFeedback() {
        feedback.hidden = true;
        feedback.replaceChildren();
    }

    // KIEM TRA HO TEN
    function validateFullname() {
        const value = fields.fullname.value.trim();

        if (!value) {
            showError("fullname", "Vui lòng nhập họ và tên.");
            return false;
        }

        if (value.length < 2 || value.length > 100) {
            showError(
                "fullname",
                "Họ và tên phải có từ 2 đến 100 ký tự."
            );
            return false;
        }

        if (/\d/.test(value)) {
            showError(
                "fullname",
                "Họ và tên không được chứa chữ số."
            );
            return false;
        }

        clearError("fullname");
        return true;
    }

    // KIEM TRA EMAIL
    function validateEmail() {
        const value = fields.email.value.trim();

        if (!value) {
            showError("email", "Vui lòng nhập địa chỉ email.");
            return false;
        }

        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!pattern.test(value)) {
            showError("email", "Địa chỉ email chưa hợp lệ.");
            return false;
        }

        clearError("email");
        return true;
    }

    // KIEM TRA SO DIEN THOAI VIET NAM
    function validatePhone() {
        const value = fields.phone.value.trim();
        const normalized = value.replace(/[\s().-]/g, "");

        if (!value) {
            showError("phone", "Vui lòng nhập số điện thoại.");
            return false;
        }

        const pattern = /^(?:0\d{9}|\+84\d{9}|84\d{9})$/;

        if (!pattern.test(normalized)) {
            showError(
                "phone",
                "Nhập số điện thoại Việt Nam gồm 10 số."
            );
            return false;
        }

        clearError("phone");
        return true;
    }

    // KIEM TRA MAT KHAU
    function validatePassword() {
        const value = fields.password.value;

        if (!value) {
            showError("password", "Vui lòng nhập mật khẩu.");
            return false;
        }

        if (value.length < 6) {
            showError(
                "password",
                "Mật khẩu phải có ít nhất 6 ký tự."
            );
            return false;
        }

        if (value.length > 128) {
            showError(
                "password",
                "Mật khẩu không được vượt quá 128 ký tự."
            );
            return false;
        }

        clearError("password");
        return true;
    }

    // XAC NHAN MAT KHAU
    function validateConfirmPassword() {
        if (!fields.confirmPassword.value) {
            showError(
                "confirmPassword",
                "Vui lòng nhập lại mật khẩu."
            );
            return false;
        }

        if (
            fields.confirmPassword.value !== fields.password.value
        ) {
            showError(
                "confirmPassword",
                "Mật khẩu xác nhận không khớp."
            );
            return false;
        }

        clearError("confirmPassword");
        return true;
    }

    // KIEM TRA DIEU KHOAN
    function validateTerms() {
        if (!fields.terms.checked) {
            showError(
                "terms",
                "Bạn cần đồng ý với điều khoản để tiếp tục."
            );
            return false;
        }

        clearError("terms");
        return true;
    }

    // KIEM TRA TOAN BO FORM
    function validateForm() {
        const results = [
            validateFullname(),
            validateEmail(),
            validatePhone(),
            validatePassword(),
            validateConfirmPassword(),
            validateTerms()
        ];

        return results.every(Boolean);
    }

    // KIEM TRA KHI NHAP DU LIEU
    const validators = {
        fullname: validateFullname,
        email: validateEmail,
        phone: validatePhone,
        password: validatePassword,
        confirmPassword: validateConfirmPassword
    };

    Object.entries(validators).forEach(function ([name, validator]) {
        const field = fields[name];

        field.addEventListener("input", function () {
            if (field.getAttribute("aria-invalid") === "true") {
                validator();
            }

            if (
                name === "password" &&
                fields.confirmPassword.value &&
                fields.confirmPassword.getAttribute("aria-invalid") === "true"
            ) {
                validateConfirmPassword();
            }

            hideFeedback();
        });

        field.addEventListener("blur", function () {
            if (field.value.trim()) {
                validator();
            }
        });
    });

    fields.terms.addEventListener("change", function () {
        if (fields.terms.checked) {
            clearError("terms");
        }

        hideFeedback();
    });

    // XU LY KHI NHAN NUT DANG KY
    form.addEventListener("submit", function (event) {
        event.preventDefault();
        hideFeedback();

        if (!validateForm()) {
            const firstInvalid = form.querySelector(
                '[aria-invalid="true"]'
            );

            if (firstInvalid) {
                firstInvalid.focus();
            }

            return;
        }

        showFeedback(
            "Thông tin hợp lệ! Bạn có thể kết nối form với hệ thống đăng ký.",
            "success"
        );
    });
});