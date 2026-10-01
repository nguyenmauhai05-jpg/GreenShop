import { renderGoogleMap, googleQuery } from '../google-map-no-key.js';
import {
    apiJson,
    appendOption,
    buildStreetAddress,
    chooseWardFromAddress,
    displayAdministrativeName,
    matchingOption,
    normalizeLocationName,
    normalizeProvinceName,
} from './location-utils.js';

(() => {
    const config = window.GreenShopProfile || {};
    const form = document.getElementById('profileForm');
    if (!form) return;

    const editButton = document.getElementById('editButton');
    const cancelButton = document.getElementById('cancelButton');
    const formActions = document.getElementById('formActions');
    const chooseAvatarButton = document.getElementById('chooseAvatarButton');
    const avatarInput = document.getElementById('avatarInput');
    const avatarImage = document.getElementById('avatarImage');
    const avatarFallback = document.getElementById('avatarFallback');
    const editControls = [...form.querySelectorAll('[data-edit-control]')];
    const province = document.getElementById('tinh_thanh');
    const ward = document.getElementById('phuong_xa');
    const addressDetail = document.getElementById('dia_chi');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const mapElement = document.getElementById('profileMap');
    const useCurrentLocationButton = document.getElementById('useCurrentLocationButton');
    const findAddressOnMapButton = document.getElementById('findAddressOnMapButton');
    const mapLocationStatus = document.getElementById('mapLocationStatus');
    const mapLocationBadge = document.getElementById('mapLocationBadge');
    const mapEditActions = [...form.querySelectorAll('[data-map-edit-action]')];
    const cancelModal = document.getElementById('cancelModal');
    const keepEditingButton = document.getElementById('keepEditingButton');
    const confirmCancelButton = document.getElementById('confirmCancelButton');
    const sidebar = document.getElementById('accountSidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const locationData = config.locationData || {};
    const administrativeApiBase = String(config.administrativeApiBase || 'https://provinces.open-api.vn/api/v2').replace(/\/$/, '');
    const provinceCodes = new Map();

    let editing = config.oldHasErrors === true || form.dataset.editing === '1';
    let snapshot = null;
    let initialAvatarSrc = avatarImage && !avatarImage.hidden ? avatarImage.src : '';

    function fallbackProvinces(current = '') {
        province.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';
        Object.keys(locationData).forEach((name) => appendOption(province, name));
        appendOption(province, current);
        province.value = matchingOption(province, current) || current;
    }

    function fallbackWards(selectedProvince, currentWard = '') {
        ward.innerHTML = '<option value="">Chọn phường/xã</option>';
        const wards = locationData[selectedProvince] || [];
        wards.forEach((name) => {
            let label = name;
            if (!/^(phường|xã|đặc khu|thị trấn)\s/i.test(label)) {
                label = `Phường/Xã ${label}`;
            }
            appendOption(ward, name, label);
        });
        appendOption(ward, currentWard);
        ward.value = matchingOption(ward, currentWard) || currentWard;
    }

    async function populateProvinces() {
        const current = province.dataset.current || province.value || '';
        province.innerHTML = '<option value="">Đang tải tỉnh/thành phố...</option>';
        province.disabled = true;

        try {
            const data = await apiJson(`${administrativeApiBase}/p/`);
            if (!Array.isArray(data)) throw new Error('Sai định dạng API tỉnh');

            province.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';
            provinceCodes.clear();

            data.forEach((item) => {
                const label = displayAdministrativeName(item);
                if (!label) return;
                appendOption(province, label, label);
                provinceCodes.set(label, item.code);
            });

            const selected = matchingOption(province, current);
            if (current && !selected) appendOption(province, current);
            province.value = selected || current || '';
        } catch (error) {
            console.error('Province API error:', error);
            fallbackProvinces(current);
        } finally {
            province.disabled = !editing;
        }
    }

    async function populateWards(selectedProvince, currentWard = '') {
        ward.innerHTML = '<option value="">Chọn phường/xã</option>';

        if (!selectedProvince) {
            ward.disabled = true;
            return;
        }

        let provinceCode = provinceCodes.get(selectedProvince);

        if (!provinceCode) {
            const matchedProvince = matchingOption(province, selectedProvince);
            if (matchedProvince) provinceCode = provinceCodes.get(matchedProvince);
        }

        if (!provinceCode) {
            fallbackWards(selectedProvince, currentWard);
            ward.disabled = !editing;
            return;
        }

        ward.innerHTML = '<option value="">Đang tải phường/xã...</option>';
        ward.disabled = true;

        try {
            // Endpoint v2 chính xác: lấy 1 tỉnh với depth=2 để nhận mảng wards.
            const data = await apiJson(
                `${administrativeApiBase}/p/${encodeURIComponent(provinceCode)}?depth=2`
            );

            const wards = Array.isArray(data?.wards) ? data.wards : [];
            if (!wards.length) throw new Error('API không trả wards');

            ward.innerHTML = '<option value="">Chọn phường/xã</option>';

            wards.forEach((item) => {
                const label = displayAdministrativeName(item);
                appendOption(ward, label, label);
            });

            const selectedWard = matchingOption(ward, currentWard);
            if (currentWard && !selectedWard) appendOption(ward, currentWard);
            ward.value = selectedWard || currentWard || '';
        } catch (error) {
            console.error('Ward API error:', error);
            fallbackWards(selectedProvince, currentWard);
        } finally {
            ward.disabled = !editing;
        }
    }

    async function initializeAdministrativeAddress() {
        const oldProvince = province.dataset.current || '';
        const oldWard = ward.dataset.current || '';

        await populateProvinces();

        const matchedProvince = matchingOption(province, oldProvince);
        if (matchedProvince) province.value = matchedProvince;

        await populateWards(province.value, oldWard);
    }

    initializeAdministrativeAddress();

    province.addEventListener('change', async () => {
        await populateWards(province.value, '');
    });

    function captureSnapshot() {
        const data = {};
        editControls.forEach((control) => {
            data[control.name || control.id] = control.type === 'checkbox' ? control.checked : control.value;
        });
        data.__avatar = initialAvatarSrc;
        return data;
    }

    function hasChanges() {
        if (!snapshot) return false;
        return editControls.some((control) => {
            const key = control.name || control.id;
            const current = control.type === 'checkbox' ? control.checked : control.value;
            return current !== snapshot[key];
        }) || (avatarInput && avatarInput.files && avatarInput.files.length > 0);
    }

    function setEditing(state) {
        editing = state;
        editControls.forEach((control) => {
            control.disabled = !state;
        });
        chooseAvatarButton.disabled = !state;
        mapEditActions.forEach((button) => {
            button.disabled = !state;
        });
        formActions.hidden = !state;
        editButton.hidden = state;

        if (state) {
            snapshot = captureSnapshot();
            const first = form.querySelector('#ho_ten');
            if (first && !config.oldHasErrors) setTimeout(() => first.focus(), 30);
        }
    }

    if (editing) {
        editControls.forEach((control) => control.disabled = false);
        chooseAvatarButton.disabled = false;
        mapEditActions.forEach((button) => {
            button.disabled = false;
        });
        formActions.hidden = false;
        editButton.hidden = true;
        snapshot = captureSnapshot();
    } else {
        setEditing(false);
    }

    editButton.addEventListener('click', () => setEditing(true));

    chooseAvatarButton.addEventListener('click', () => {
        if (!editing) return;
        avatarInput.click();
    });

    avatarInput.addEventListener('change', () => {
        const file = avatarInput.files && avatarInput.files[0];
        if (!file) return;

        const allowed = ['image/jpeg', 'image/png'];
        if (!allowed.includes(file.type)) {
            alert('Định dạng ảnh không hợp lệ. Chỉ chấp nhận file JPG, JPEG hoặc PNG.');
            avatarInput.value = '';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            alert('Kích thước ảnh không được vượt quá 2 MB.');
            avatarInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            avatarImage.src = event.target.result;
            avatarImage.hidden = false;
            avatarFallback.style.display = 'none';
        };
        reader.onerror = () => {
            alert('Không thể tải ảnh lên. Vui lòng chọn ảnh khác.');
            avatarInput.value = '';
        };
        reader.readAsDataURL(file);
    });

    function openCancelModal() {
        cancelModal.classList.add('open');
        cancelModal.setAttribute('aria-hidden', 'false');
    }

    function closeCancelModal() {
        cancelModal.classList.remove('open');
        cancelModal.setAttribute('aria-hidden', 'true');
    }

    function restoreSnapshot() {
        if (!snapshot) return;
        editControls.forEach((control) => {
            const key = control.name || control.id;
            if (!(key in snapshot)) return;
            if (control.type === 'checkbox') control.checked = snapshot[key];
            else control.value = snapshot[key];
        });

        province.value = matchingOption(province, snapshot.tinh_thanh || '') || snapshot.tinh_thanh || '';
        populateWards(province.value, snapshot.phuong_xa || '');

        if (avatarInput) avatarInput.value = '';
        if (initialAvatarSrc) {
            avatarImage.src = initialAvatarSrc;
            avatarImage.hidden = false;
            avatarFallback.style.display = 'none';
        } else {
            avatarImage.src = '';
            avatarImage.hidden = true;
            avatarFallback.style.display = '';
        }
    }

    function cancelEditing() {
        // Sau khi backend trả validation error, tải lại trang để khôi phục đúng dữ liệu đã lưu trong DB.
        if (config.oldHasErrors && config.profileUrl) {
            window.location.href = config.profileUrl;
            return;
        }

        restoreSnapshot();
        closeCancelModal();
        setEditing(false);
        document.querySelectorAll('.field-error').forEach((node) => node.remove());
        document.querySelectorAll('.is-invalid').forEach((node) => node.classList.remove('is-invalid'));
    }

    cancelButton.addEventListener('click', () => {
        if (hasChanges()) openCancelModal();
        else cancelEditing();
    });
    keepEditingButton.addEventListener('click', closeCancelModal);
    confirmCancelButton.addEventListener('click', cancelEditing);
    cancelModal.querySelector('.confirm-modal__backdrop').addEventListener('click', closeCancelModal);

    window.addEventListener('beforeunload', (event) => {
        if (!editing || !hasChanges()) return;
        event.preventDefault();
        event.returnValue = '';
    });

    form.addEventListener('submit', () => {
        // Đảm bảo input file luôn được gửi cùng multipart/form-data,
        // kể cả khi trạng thái edit vừa bị thay đổi bởi JS.
        if (avatarInput) avatarInput.disabled = false;

        // Không hiện cảnh báo beforeunload sau khi người dùng chủ động lưu.
        editing = false;
    });


    // Public Google Maps: manual address entry. GPS may populate coordinates,
    // but Google cannot return the selected pin without a Maps Platform key.
    const previewAddress = () => {
        const parts = [addressDetail?.value, ward?.value, province?.value].filter(Boolean);
        return parts.length ? [...parts, 'Việt Nam'].join(', ') : '';
    };
    const updateMapPreview = () => renderGoogleMap(mapElement,
        googleQuery(previewAddress(), latitudeInput?.value, longitudeInput?.value));
    const mapNotice = (message) => {
        if (mapLocationStatus) mapLocationStatus.textContent = message;
        if (mapLocationBadge) mapLocationBadge.textContent = 'Nhập địa chỉ';
    };
    function clearOldPoint() {
        if (latitudeInput) latitudeInput.value = '';
        if (longitudeInput) longitudeInput.value = '';
    }
    // Editing the address invalidates a previous location; checkout can resolve
    // an address later through the existing non-Google backend shipping service.
    addressDetail?.addEventListener('input', () => { if (editing) clearOldPoint(); });
    province?.addEventListener('change', () => { if (editing) clearOldPoint(); });
    ward?.addEventListener('change', () => { if (editing) clearOldPoint(); });
    findAddressOnMapButton?.addEventListener('click', () => {
        updateMapPreview();
        mapNotice('Bản đồ Google đang xem trước địa chỉ nhập thủ công. Kiểm tra bằng nút “Mở Google Maps”.');
    });
    useCurrentLocationButton?.addEventListener('click', () => {
        if (!navigator.geolocation) { mapNotice('Trình duyệt không hỗ trợ GPS; vui lòng nhập địa chỉ.'); return; }
        navigator.geolocation.getCurrentPosition(position => {
            latitudeInput.value = position.coords.latitude.toFixed(7);
            longitudeInput.value = position.coords.longitude.toFixed(7);
            // GPS is for map preview only; manual address fields remain authoritative.
            renderGoogleMap(mapElement, `${latitudeInput.value},${longitudeInput.value}`);
            mapNotice('Đã hiển thị vị trí hiện tại; vẫn cần nhập địa chỉ giao hàng thủ công.');
        }, () => mapNotice('Không lấy được GPS; hãy nhập địa chỉ thủ công.'));
    });
    updateMapPreview();

    const toast = document.getElementById('pageToast');
    if (toast) {
        const closeToast = () => {
            toast.classList.add('is-hiding');
            setTimeout(() => toast.remove(), 220);
        };
        toast.querySelector('.toast-close')?.addEventListener('click', closeToast);
        setTimeout(closeToast, 4500);
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        sidebarBackdrop.classList.remove('open');
    }
    mobileMenuButton?.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        sidebarBackdrop.classList.toggle('open');
    });
    sidebarBackdrop?.addEventListener('click', closeSidebar);
    sidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeSidebar));
})();
