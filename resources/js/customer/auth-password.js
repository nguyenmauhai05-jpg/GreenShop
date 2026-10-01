window.togglePassword = function (inputId, button) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    button.textContent = isPassword ? '🙈' : '👁';
};

function getClientErrorElement(input) {
    const group = input.closest('.form-group');
    if (!group) return null;

    let error = group.querySelector('.client-error-message');
    if (!error) {
        error = document.createElement('span');
        error.className = 'error-message client-error-message';
        const wrapper = input.closest('.input-wrapper');
        (wrapper || input).insertAdjacentElement('afterend', error);
    }
    return error;
}

function setClientError(input, message) {
    const error = getClientErrorElement(input);
    input.classList.toggle('is-invalid', Boolean(message));
    input.setAttribute('aria-invalid', message ? 'true' : 'false');
    if (error) {
        error.textContent = message || '';
        error.hidden = !message;
    }
}

function validateEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

function validateRegisterField(input, form) {
    const value = input.value.trim();

    switch (input.name) {
        case 'ho_ten':
            if (!value) return 'Vui lòng nhập họ tên.';
            if (value.length < 2) return 'Họ tên phải có ít nhất 2 ký tự.';
            if (value.length > 150) return 'Họ tên không được vượt quá 150 ký tự.';
            return '';
        case 'email':
            if (!value) return 'Vui lòng nhập email.';
            if (!validateEmail(value)) return 'Email không đúng định dạng.';
            if (value.length > 255) return 'Email không được vượt quá 255 ký tự.';
            return '';
        case 'so_dien_thoai':
            if (!value) return 'Vui lòng nhập số điện thoại.';
            if (!/^0\d{9}$/.test(value)) return 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.';
            return '';
        case 'mat_khau':
            if (!input.value) return 'Vui lòng nhập mật khẩu.';
            if (input.value.length < 8) return 'Mật khẩu phải có ít nhất 8 ký tự.';
            if (input.value.length > 72) return 'Mật khẩu không được vượt quá 72 ký tự.';
            return '';
        case 'xac_nhan_mat_khau': {
            if (!input.value) return 'Vui lòng nhập xác nhận mật khẩu.';
            const password = form.querySelector('[name="mat_khau"]')?.value || '';
            if (input.value !== password) return 'Xác nhận mật khẩu không khớp.';
            return '';
        }
        default:
            return '';
    }
}

function validateLoginField(input) {
    const value = input.value.trim();

    if (input.name === 'email') {
        if (!value) return 'Vui lòng nhập email.';
        if (!validateEmail(value)) return 'Email không đúng định dạng.';
        if (value.length > 255) return 'Email không được vượt quá 255 ký tự.';
    }

    if (input.name === 'mat_khau') {
        if (!input.value) return 'Vui lòng nhập mật khẩu.';
        if (input.value.length > 72) return 'Mật khẩu không được vượt quá 72 ký tự.';
    }

    return '';
}

function initAuthValidation() {
    document.querySelectorAll('form[data-auth-form]').forEach((form) => {
        const type = form.dataset.authForm;
        const fields = Array.from(form.querySelectorAll('input[name]'))
            .filter((input) => input.type !== 'hidden');

        const validateField = (input) => {
            const message = type === 'register'
                ? validateRegisterField(input, form)
                : validateLoginField(input);
            setClientError(input, message);
            return !message;
        };

        fields.forEach((input) => {
            input.addEventListener('blur', () => validateField(input));
            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid')) validateField(input);
                if (type === 'register' && input.name === 'mat_khau') {
                    const confirm = form.querySelector('[name="xac_nhan_mat_khau"]');
                    if (confirm?.value) validateField(confirm);
                }
            });
        });

        form.addEventListener('submit', (event) => {
            let firstInvalid = null;
            fields.forEach((input) => {
                if (!validateField(input) && !firstInvalid) firstInvalid = input;
            });

            if (firstInvalid) {
                event.preventDefault();
                firstInvalid.focus();
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initAuthValidation);
