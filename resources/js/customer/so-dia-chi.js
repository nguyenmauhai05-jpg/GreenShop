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
    const config = window.GreenShopAddressBook || {};

    const modal = document.getElementById('addressModal');
    const form = document.getElementById('addressForm');
    const modalTitle = document.getElementById('addressModalTitle');
    const methodInput = document.getElementById('addressMethod');
    const addressIdInput = document.getElementById('address_id');
    const modeInput = document.getElementById('address_mode');
    const recipientInput = document.getElementById('address_recipient');
    const phoneInput = document.getElementById('address_phone');
    const province = document.getElementById('address_province');
    const ward = document.getElementById('address_ward');
    const detailInput = document.getElementById('address_detail');
    const latitudeInput = document.getElementById('address_latitude');
    const longitudeInput = document.getElementById('address_longitude');
    const defaultInput = document.getElementById('address_default');
    const modeButtons = [...document.querySelectorAll('[data-address-mode]')];
    const mapPanel = document.getElementById('addressMapPanel');
    const mapElement = document.getElementById('addressBookMap');
    const mapStatus = document.getElementById('addressMapStatus');
    const currentLocationButton = document.getElementById('addressUseCurrentLocation');
    const findOnMapButton = document.getElementById('addressFindOnMap');
    const openAddButton = document.getElementById('openAddAddressButton');
    const addButtons = [...document.querySelectorAll('[data-open-add-address]')];
    const closeButtons = [...document.querySelectorAll('[data-close-address-modal]')];
    const editButtons = [...document.querySelectorAll('[data-edit-address]')];
    const deleteForms = [...document.querySelectorAll('[data-delete-address-form]')];
    const sidebar = document.getElementById('accountSidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const mobileMenuButton = document.getElementById('mobileMenuButton');

    if (!modal || !form || !province || !ward || !detailInput) return;

    const administrativeApiBase = String(
        config.administrativeApiBase || 'https://provinces.open-api.vn/api/v2'
    ).replace(/\/$/, '');


    const provinceCodes = new Map();

    let currentMode = 'manual';

    async function populateProvinces(current = '') {
        province.innerHTML = '<option value="">Đang tải tỉnh/thành phố...</option>';
        province.disabled = true;

        try {
            const data = await apiJson(`${administrativeApiBase}/p/`);
            if (!Array.isArray(data)) throw new Error('Sai dữ liệu tỉnh/thành phố');

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
            province.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';
            if (current) appendOption(province, current);
            province.value = current || '';
        } finally {
            province.disabled = false;
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
            const matched = matchingOption(province, selectedProvince);
            if (matched) provinceCode = provinceCodes.get(matched);
        }

        if (!provinceCode) {
            if (currentWard) appendOption(ward, currentWard);
            ward.value = currentWard || '';
            ward.disabled = false;
            return;
        }

        ward.innerHTML = '<option value="">Đang tải phường/xã...</option>';
        ward.disabled = true;

        try {
            const data = await apiJson(
                `${administrativeApiBase}/p/${encodeURIComponent(provinceCode)}?depth=2`
            );
            const wards = Array.isArray(data?.wards) ? data.wards : [];
            if (!wards.length) throw new Error('API không trả danh sách phường/xã');

            ward.innerHTML = '<option value="">Chọn phường/xã</option>';
            wards.forEach((item) => {
                const label = displayAdministrativeName(item);
                if (label) appendOption(ward, label, label);
            });

            const selected = matchingOption(ward, currentWard);
            if (currentWard && !selected) appendOption(ward, currentWard);
            ward.value = selected || currentWard || '';
        } catch (error) {
            console.error('Ward API error:', error);
            ward.innerHTML = '<option value="">Chọn phường/xã</option>';
            if (currentWard) appendOption(ward, currentWard);
            ward.value = currentWard || '';
        } finally {
            ward.disabled = false;
        }
    }

    province.addEventListener('change', async () => {
        await populateWards(province.value, '');
        clearCoordinatesIfManual();
    });

    ward.addEventListener('change', clearCoordinatesIfManual);
    detailInput.addEventListener('input', clearCoordinatesIfManual);

    function clearCoordinatesIfManual() {
        latitudeInput.value = '';
        longitudeInput.value = '';
    }

    function setMapStatus(message, type = 'normal') {
        if (!mapStatus) return;
        mapStatus.textContent = message;
        mapStatus.dataset.type = type;
    }

    function typedAddress() {
        const parts = [detailInput?.value, ward?.value, province?.value].filter(Boolean);
        return parts.length ? [...parts, 'Việt Nam'].join(', ') : ''; 
    }
    function refreshMapPosition() {
        renderGoogleMap(mapElement, googleQuery(typedAddress(), latitudeInput.value, longitudeInput.value));
    }

    function setMode(mode) {
        currentMode = 'map';
        modeInput.value = currentMode;

        modeButtons.forEach((button) => {
            button.classList.toggle('active', button.dataset.addressMode === currentMode);
        });

        if (mapPanel) mapPanel.hidden = currentMode !== 'map';

        if (currentMode === 'map') {
            refreshMapPosition();
        }
    }

    modeButtons.forEach((button) => {
        button.addEventListener('click', () => setMode(button.dataset.addressMode));
    });

    function searchAddressOnMap() {
        refreshMapPosition();
        setMapStatus('Xem trước địa chỉ nhập thủ công trên Google Maps. Nhấn “Mở Google Maps” để kiểm tra.');
    }
    function useCurrentLocation() {
        if (!navigator.geolocation) { setMapStatus('Không hỗ trợ GPS; vui lòng nhập địa chỉ.'); return; }
        navigator.geolocation.getCurrentPosition(position => {
            latitudeInput.value = position.coords.latitude.toFixed(7);
            longitudeInput.value = position.coords.longitude.toFixed(7);
            renderGoogleMap(mapElement, `${latitudeInput.value},${longitudeInput.value}`);
            setMapStatus('Đã xem trước GPS. Hãy nhập đầy đủ địa chỉ thủ công trước khi lưu.');
        }, () => setMapStatus('Không lấy được vị trí. Nhập địa chỉ thủ công.'));
    }
    currentLocationButton?.addEventListener('click', useCurrentLocation);
    findOnMapButton?.addEventListener('click', searchAddressOnMap);

    function resetFormForAdd() {
        form.action = config.createUrl;
        methodInput.value = '';
        addressIdInput.value = '';
        modalTitle.textContent = 'Thêm địa chỉ mới';
        recipientInput.value = config.defaultRecipient || '';
        phoneInput.value = config.defaultPhone || '';
        detailInput.value = '';
        latitudeInput.value = '';
        longitudeInput.value = '';
        defaultInput.checked = false;
        setMapStatus('Nhập địa chỉ thủ công để xem Google Maps.');

        populateProvinces('').then(() => populateWards('', ''));
        setMode('map');
    }

    async function populateFormFromButton(button) {
        form.action = button.dataset.updateUrl;
        methodInput.value = 'PUT';
        addressIdInput.value = button.dataset.addressId || '';
        modalTitle.textContent = 'Chỉnh sửa địa chỉ';
        recipientInput.value = button.dataset.recipient || '';
        phoneInput.value = button.dataset.phone || '';
        detailInput.value = button.dataset.detail || '';
        latitudeInput.value = button.dataset.latitude || '';
        longitudeInput.value = button.dataset.longitude || '';
        defaultInput.checked = button.dataset.default === '1';

        await populateProvinces(button.dataset.province || '');
        await populateWards(province.value, button.dataset.ward || '');

        setMode('map'); // Manual entry works without geocoding or saved coordinates.
        refreshMapPosition();
    }

    function openModal() {
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('address-modal-open');
        if (currentMode === 'map') setTimeout(refreshMapPosition, 80);
    }

    function closeModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('address-modal-open');
    }

    openAddButton?.addEventListener('click', () => {
        resetFormForAdd();
        openModal();
    });

    addButtons.forEach((button) => {
        button.addEventListener('click', () => {
            resetFormForAdd();
            openModal();
        });
    });

    editButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            await populateFormFromButton(button);
            openModal();
        });
    });

    closeButtons.forEach((button) => button.addEventListener('click', closeModal));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('open')) closeModal();
    });

    deleteForms.forEach((deleteForm) => {
        deleteForm.addEventListener('submit', (event) => {
            if (!window.confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) {
                event.preventDefault();
            }
        });
    });

    function closeSidebar() {
        sidebar?.classList.remove('open');
        sidebarBackdrop?.classList.remove('open');
    }

    mobileMenuButton?.addEventListener('click', () => {
        sidebar?.classList.toggle('open');
        sidebarBackdrop?.classList.toggle('open');
    });
    sidebarBackdrop?.addEventListener('click', closeSidebar);
    sidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeSidebar));

    const toast = document.getElementById('pageToast');
    if (toast) {
        const closeToast = () => {
            toast.classList.add('is-hiding');
            setTimeout(() => toast.remove(), 220);
        };
        toast.querySelector('.toast-close')?.addEventListener('click', closeToast);
        setTimeout(closeToast, 4500);
    }

    async function restoreOldFormAfterValidation() {
        if (!config.oldHasErrors) {
            await populateProvinces('');
            return;
        }

        const old = config.old || {};
        const editButton = old.address_id
            ? editButtons.find((button) => String(button.dataset.addressId) === String(old.address_id))
            : null;

        if (editButton) {
            form.action = editButton.dataset.updateUrl;
            methodInput.value = 'PUT';
            modalTitle.textContent = 'Chỉnh sửa địa chỉ';
        } else {
            form.action = config.createUrl;
            methodInput.value = '';
            modalTitle.textContent = 'Thêm địa chỉ mới';
        }

        addressIdInput.value = old.address_id || '';
        recipientInput.value = old.nguoi_nhan || config.defaultRecipient || '';
        phoneInput.value = old.so_dien_thoai || config.defaultPhone || '';
        detailInput.value = old.dia_chi || '';
        latitudeInput.value = old.latitude || '';
        longitudeInput.value = old.longitude || '';
        defaultInput.checked = String(old.mac_dinh || '') === '1';

        await populateProvinces(old.tinh_thanh || '');
        await populateWards(province.value, old.phuong_xa || '');
        setMode('map');
        openModal();
    }

    restoreOldFormAfterValidation();
})();
