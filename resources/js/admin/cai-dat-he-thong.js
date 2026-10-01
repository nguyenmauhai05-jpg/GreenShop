import { renderGoogleMap, googleQuery } from '../google-map-no-key.js';
document.addEventListener('DOMContentLoaded', function () {
    const description = document.getElementById('description');
    const descriptionCount = document.getElementById('descriptionCount');
    const logoInput = document.getElementById('logoInput');
    const logoPreviewImage = document.getElementById('logoPreviewImage');
    const changeLogoButton = document.getElementById('changeLogoButton');
    const maintenanceMode = document.getElementById('maintenanceMode');
    const maintenanceConfirmRow = document.getElementById('maintenanceConfirmRow');

    const addressInput = document.getElementById('address');
    const storeLatInput = document.getElementById('store_latitude');
    const storeLngInput = document.getElementById('store_longitude');
    const storeMapElement = document.getElementById('storeLocationMap');
    const findStoreOnMap = document.getElementById('findStoreOnMap');
    const storeMapStatus = document.getElementById('storeMapStatus');
    const storeCoordinateText = document.getElementById('storeCoordinateText');

    const settingsForm = document.getElementById('systemSettingsForm');
    const storeNameInput = document.getElementById('store_name');
    const contactEmailInput = document.getElementById('contact_email');
    const phoneInput = document.getElementById('phone');
    const mailHostInput = document.getElementById('mail_host');


    // Google public iframe: the administrator enters the address manually.
    // No geocoding API/key is required and we never fabricate coordinates.
    if (storeMapElement) {
        const preview = () => {
            renderGoogleMap(storeMapElement, googleQuery(addressInput?.value, storeLatInput?.value, storeLngInput?.value));
        };
        preview();
        findStoreOnMap?.addEventListener('click', () => {
            preview();
            if (storeMapStatus) storeMapStatus.textContent = addressInput?.value.trim()
                ? 'Bản đồ Google hiển thị địa chỉ đã nhập; dùng “Mở Google Maps” để kiểm tra vị trí.'
                : 'Vui lòng nhập địa chỉ cửa hàng trước.';
        });
        addressInput?.addEventListener('input', () => {
            // If address changes, previously saved coordinates are no longer reliable.
            if (storeLatInput) storeLatInput.value = '';
            if (storeLngInput) storeLngInput.value = '';
            if (storeCoordinateText) storeCoordinateText.textContent = 'Chưa có (nhập địa chỉ thủ công)';
        });
    }

    if (description && descriptionCount) {
        description.addEventListener('input', function () {
            descriptionCount.textContent = this.value.length;
        });
    }

    if (changeLogoButton && logoInput) {
        changeLogoButton.addEventListener('click', function () {
            logoInput.click();
        });

        logoInput.addEventListener('change', function () {
            const file = this.files && this.files[0] ? this.files[0] : null;
            if (!file) return;

            const allowed = ['image/jpeg', 'image/png', 'image/webp'];

            if (!allowed.includes(file.type)) {
                alert('Logo chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.');
                this.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('Logo tối đa 2 MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = event => {
                logoPreviewImage.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    }



    const setClientError = (input, errorId, message) => {
        const error = document.getElementById(errorId);
        input?.classList.toggle('invalid', Boolean(message));

        if (error) {
            error.textContent = message || '';
            error.style.display = message ? 'block' : 'none';
        }

        return !message;
    };

    const validateStoreName = () => {
        const value = (storeNameInput?.value || '').trim();
        let message = '';

        if (!value) {
            message = 'Tên cửa hàng không được để trống.';
        } else if (value.length > 100) {
            message = 'Tên cửa hàng tối đa 100 ký tự.';
        }

        return setClientError(storeNameInput, 'storeNameClientError', message);
    };

    const validateContactEmail = () => {
        const value = (contactEmailInput?.value || '').trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let message = '';

        if (!value) {
            message = 'Email liên hệ không được để trống.';
        } else if (!emailPattern.test(value)) {
            message = 'Email liên hệ không đúng định dạng.';
        }

        return setClientError(contactEmailInput, 'contactEmailClientError', message);
    };

    const validatePhone = () => {
        const value = (phoneInput?.value || '').trim();
        let message = '';

        if (!value) {
            message = 'Số điện thoại không được để trống.';
        } else if (!/^0\d{9}$/.test(value)) {
            message = 'Số điện thoại phải có đúng 10 chữ số, bắt đầu bằng 0 và không được chứa chữ.';
        }

        return setClientError(phoneInput, 'phoneClientError', message);
    };

    const validateDescription = () => {
        const value = description?.value || '';
        const message = value.length > 500
            ? 'Mô tả cửa hàng tối đa 500 ký tự.'
            : '';

        return setClientError(description, 'descriptionClientError', message);
    };

    const validateMailHost = () => {
        const driver = document.getElementById('mail_driver')?.value || 'smtp';
        const value = (mailHostInput?.value || '').trim();
        const hostnamePattern = /^(?:localhost|(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,63}|(?:\d{1,3}\.){3}\d{1,3})$/;
        let message = '';

        if (driver === 'smtp' && !value) {
            message = 'Host là bắt buộc khi dùng SMTP.';
        } else if (value && !hostnamePattern.test(value)) {
            message = 'Host không hợp lệ. Ví dụ: smtp.gmail.com.';
        }

        return setClientError(mailHostInput, 'mailHostClientError', message);
    };

    storeNameInput?.addEventListener('input', validateStoreName);
    contactEmailInput?.addEventListener('input', validateContactEmail);
    phoneInput?.addEventListener('input', validatePhone);
    description?.addEventListener('input', function () {
        if (descriptionCount) descriptionCount.textContent = this.value.length;
        validateDescription();
    });
    mailHostInput?.addEventListener('input', validateMailHost);
    document.getElementById('mail_driver')?.addEventListener('change', validateMailHost);

    settingsForm?.addEventListener('submit', function (event) {
        const valid = [
            validateStoreName(),
            validateContactEmail(),
            validatePhone(),
            validateDescription(),
            validateMailHost(),
        ];

        if (valid.includes(false)) {
            event.preventDefault();
            settingsForm.querySelector('.invalid')?.focus();
        }
    });

    if (maintenanceMode && maintenanceConfirmRow) {
        maintenanceMode.addEventListener('change', function () {
            maintenanceConfirmRow.hidden = !this.checked;

            if (!this.checked) {
                const confirm = maintenanceConfirmRow.querySelector('input[type="checkbox"]');
                if (confirm) confirm.checked = false;
            }
        });
    }
});
