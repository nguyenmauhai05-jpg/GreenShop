export function normalizeLocationName(value = '') {
    return String(value)
        .trim()
        .toLocaleLowerCase('vi')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/^(thanh pho|tp\.?|tinh|phuong|xa|dac khu|thi tran)\s+/i, '')
        .replace(/[^a-z0-9]+/g, ' ')
        .trim();
}

export function matchingOption(select, value = '') {
    const wanted = normalizeLocationName(value);
    if (!wanted) return '';

    const found = [...select.options].find(
        (option) => normalizeLocationName(option.value) === wanted
    );

    return found ? found.value : '';
}

export function appendOption(select, value, label = value) {
    if (!value) return;
    if ([...select.options].some((option) => option.value === value)) return;

    const option = document.createElement('option');
    option.value = value;
    option.textContent = label;
    select.appendChild(option);
}

export function displayAdministrativeName(item = {}) {
    const name = String(item.name || '').trim();
    if (!name) return '';

    if (/^(phường|xã|đặc khu|thị trấn|tỉnh|thành phố)\s/i.test(name)) {
        return name;
    }

    const type = String(item.division_type || '').toLocaleLowerCase('vi');
    if (type.includes('phường')) return `Phường ${name}`;
    if (type.includes('xã')) return `Xã ${name}`;
    if (type.includes('đặc khu')) return `Đặc khu ${name}`;
    if (type.includes('thị trấn')) return `Thị trấn ${name}`;
    if (type.includes('thành phố')) return `Thành phố ${name}`;
    if (type.includes('tỉnh')) return `Tỉnh ${name}`;

    return name;
}

export async function apiJson(url) {
    const response = await fetch(url, {
        method: 'GET',
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error(`API HTTP ${response.status}`);
    }

    return response.json();
}

export function normalizeProvinceName(value = '') {
    const name = String(value).trim();
    const lower = name.toLocaleLowerCase('vi');

    if (lower.includes('hồ chí minh')) return 'TP. Hồ Chí Minh';
    if (lower.includes('đà nẵng')) return 'Đà Nẵng';
    if (lower.includes('hà nội')) return 'Hà Nội';

    return name;
}

export function chooseWardFromAddress(address = {}) {
    return address.ward
        || address.quarter
        || address.suburb
        || address.neighbourhood
        || address.village
        || address.town
        || '';
}

export function buildStreetAddress(address = {}, displayName = '') {
    const parts = [
        address.house_number,
        address.road || address.pedestrian || address.residential || address.path,
    ].filter(Boolean);

    if (parts.length) return parts.join(' ');

    const fallback = String(displayName || '')
        .split(',')
        .map((part) => part.trim())
        .filter(Boolean);

    return fallback.slice(0, 2).join(', ');
}
