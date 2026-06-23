document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");
    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");

    function showError(input, message) {
        removeError(input);

        input.classList.add("is-invalid");

        const error = document.createElement("small");
        error.className = "field-error";
        error.innerText = message;

        if (input.parentElement.classList.contains("password-wrapper")) {
            input.parentElement.insertAdjacentElement("afterend", error);
        } else {
            input.insertAdjacentElement("afterend", error);
        }
    }

    function removeError(input) {
        input.classList.remove("is-invalid");

        let error;

        if (input.parentElement.classList.contains("password-wrapper")) {
            error = input.parentElement.nextElementSibling;
        } else {
            error = input.nextElementSibling;
        }

        if (error && error.classList.contains("field-error")) {
            error.remove();
        }
    }

    function isValidEmail(email) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailPattern.test(email);
    }

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener("click", function () {
            const isPassword = passwordInput.getAttribute("type") === "password";

            passwordInput.setAttribute("type", isPassword ? "text" : "password");

            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    }

    if (form) {
        form.addEventListener("submit", function (e) {
            let isValid = true;

            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            removeError(emailInput);
            removeError(passwordInput);

            if (email === "") {
                showError(emailInput, "Email is required.");
                isValid = false;
            } else if (!isValidEmail(email)) {
                showError(emailInput, "Please enter a valid email address.");
                isValid = false;
            }

            if (password === "") {
                showError(passwordInput, "Password is required.");
                isValid = false;
            } else if (password.length < 6) {
                showError(passwordInput, "Password must be at least 6 characters.");
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    if (emailInput) {
        emailInput.addEventListener("input", function () {
            removeError(emailInput);
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener("input", function () {
            removeError(passwordInput);
        });
    }
});