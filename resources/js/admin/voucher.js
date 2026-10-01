const config = window.GreenShopVoucherAdmin || {};
const voucherModal = document.getElementById('voucherModal');
const voucherForm = document.getElementById('voucherForm');
const createUrl = config.createUrl || '';

function localDateTimeValue(date) {
    const pad = value => String(value).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

window.openCreateVoucherModal = function () {
    voucherForm.reset();
    voucherForm.action = createUrl;
    document.getElementById('voucherMethod').value = 'POST';
    document.getElementById('voucherModalTitle').textContent = 'Thêm voucher mới';
    document.getElementById('voucherType').value = 'phan_tram';
    document.getElementById('voucherMinOrder').value = '0';
    document.getElementById('voucherQuantity').value = '100';
    document.getElementById('voucherStatus').value = '1';
    const now = new Date();
    const end = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
    document.getElementById('voucherStart').value = localDateTimeValue(now);
    document.getElementById('voucherEnd').value = localDateTimeValue(end);
    window.syncVoucherType();
    voucherModal.classList.add('show');
    document.body.style.overflow = 'hidden';
};

window.openEditVoucherModal = function (button) {
    voucherForm.reset();
    voucherForm.action = button.dataset.updateUrl;
    document.getElementById('voucherMethod').value = 'PUT';
    document.getElementById('voucherModalTitle').textContent = 'Chỉnh sửa voucher';
    document.getElementById('voucherCode').value = button.dataset.code || '';
    document.getElementById('voucherName').value = button.dataset.name || '';
    document.getElementById('voucherDescription').value = button.dataset.description || '';
    document.getElementById('voucherScope').value = button.dataset.scope || 'don_hang';
    document.getElementById('voucherType').value = button.dataset.type || 'phan_tram';
    document.getElementById('voucherValue').value = button.dataset.value || '';
    document.getElementById('voucherMax').value = button.dataset.max || '';
    document.getElementById('voucherMinOrder').value = button.dataset.minOrder || '0';
    document.getElementById('voucherQuantity').value = button.dataset.quantity || '1';
    document.getElementById('voucherStart').value = button.dataset.start || '';
    document.getElementById('voucherEnd').value = button.dataset.end || '';
    document.getElementById('voucherStatus').value = button.dataset.status === '0' ? '0' : '1';
    window.syncVoucherType();
    voucherModal.classList.add('show');
    document.body.style.overflow = 'hidden';
};

window.closeVoucherModal = function () {
    voucherModal.classList.remove('show');
    document.body.style.overflow = '';
};

window.syncVoucherType = function () {
    const isPercent = document.getElementById('voucherType').value === 'phan_tram';
    document.getElementById('maxDiscountGroup').style.display = isPercent ? '' : 'none';
    document.getElementById('voucherValue').max = isPercent ? '100' : '';
};

document.addEventListener('keydown', event => {
    if (event.key === 'Escape') window.closeVoucherModal();
});

if (config.hasErrors) {
    const old = config.old || {};
    voucherForm.action = createUrl;
    document.getElementById('voucherMethod').value = 'POST';
    document.getElementById('voucherModalTitle').textContent = 'Thêm voucher mới';
    document.getElementById('voucherCode').value = old.ma_voucher || '';
    document.getElementById('voucherName').value = old.ten_voucher || '';
    document.getElementById('voucherDescription').value = old.mo_ta || '';
    document.getElementById('voucherScope').value = old.pham_vi || 'don_hang';
    document.getElementById('voucherType').value = old.loai_giam || 'phan_tram';
    document.getElementById('voucherValue').value = old.gia_tri_giam || '';
    document.getElementById('voucherMax').value = old.giam_toi_da || '';
    document.getElementById('voucherMinOrder').value = old.don_hang_toi_thieu || '0';
    document.getElementById('voucherQuantity').value = old.so_luong || '100';
    document.getElementById('voucherStatus').value = old.trang_thai || '1';
    document.getElementById('voucherStart').value = old.ngay_bat_dau || '';
    document.getElementById('voucherEnd').value = old.ngay_ket_thuc || '';
    window.syncVoucherType();
    voucherModal.classList.add('show');
    document.body.style.overflow = 'hidden';
}
