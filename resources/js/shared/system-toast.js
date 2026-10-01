const DEFAULT_DURATION = 3000;

const normalizeType = (type) => {
    const value = String(type || 'success').toLowerCase();
    return ['success', 'error', 'warning', 'info'].includes(value) ? value : 'success';
};

const iconFor = (type) => {
    switch (normalizeType(type)) {
        case 'error': return '!';
        case 'warning': return '!';
        case 'info': return 'i';
        default: return '✓';
    }
};

const getRegion = () => {
    let region = document.getElementById('gsSystemToastRegion');

    if (!region) {
        region = document.createElement('div');
        region.id = 'gsSystemToastRegion';
        region.className = 'gs-system-toast-region';
        region.setAttribute('aria-live', 'polite');
        region.setAttribute('aria-atomic', 'false');
        document.body.appendChild(region);
    }

    return region;
};

const dismiss = (toast) => {
    if (!toast || toast.dataset.closing === '1') return;

    toast.dataset.closing = '1';
    toast.classList.add('is-leaving');
    window.setTimeout(() => toast.remove(), 230);
};

const show = (message, type = 'success', duration = DEFAULT_DURATION) => {
    const text = String(message ?? '').trim();
    if (!text) return null;

    const safeType = normalizeType(type);
    const safeDuration = Number.isFinite(Number(duration)) && Number(duration) > 0
        ? Number(duration)
        : DEFAULT_DURATION;

    const toast = document.createElement('div');
    toast.className = `gs-system-toast gs-system-toast--${safeType}`;
    toast.setAttribute('role', safeType === 'error' ? 'alert' : 'status');
    toast.style.setProperty('--gs-toast-duration', `${safeDuration}ms`);

    const icon = document.createElement('span');
    icon.className = 'gs-system-toast__icon';
    icon.setAttribute('aria-hidden', 'true');
    icon.textContent = iconFor(safeType);

    const content = document.createElement('span');
    content.className = 'gs-system-toast__message';
    content.textContent = text;

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'gs-system-toast__close';
    closeButton.setAttribute('aria-label', 'Đóng thông báo');
    closeButton.title = 'Đóng';
    closeButton.textContent = '×';

    const progress = document.createElement('span');
    progress.className = 'gs-system-toast__progress';
    progress.setAttribute('aria-hidden', 'true');

    toast.append(icon, content, closeButton, progress);
    getRegion().appendChild(toast);

    const timer = window.setTimeout(() => dismiss(toast), safeDuration);

    closeButton.addEventListener('click', (event) => {
        event.stopPropagation();
        window.clearTimeout(timer);
        dismiss(toast);
    });

    return toast;
};

const removeLegacyFlash = (messages) => {
    const texts = messages
        .map((item) => String(item?.message ?? '').trim())
        .filter(Boolean);

    if (!texts.length) return;

    const candidates = document.querySelectorAll([
        '.alert',
        '.toast',
        '.cart-toast-wrapper',
        '.shop-toast-container',
        '.gs-toast',
        '.orders-alert',
        '.report-alert',
        '.category-alert',
        '.admin-order-alert',
        '.plant-alert',
        '.settings-alert',
        '.review-alert',
        '.voucher-alert'
    ].join(','));

    candidates.forEach((element) => {
        if (element.closest('#gsSystemToastRegion')) return;

        const content = String(element.textContent || '').replace(/\s+/g, ' ').trim();
        if (texts.some((text) => content.includes(text))) {
            element.remove();
        }
    });
};

const readServerMessages = () => {
    const node = document.getElementById('gsSystemToastData');
    if (!node) return [];

    try {
        const parsed = JSON.parse(node.textContent || '[]');
        return Array.isArray(parsed) ? parsed : [];
    } catch (_) {
        return [];
    }
};

const api = { show, dismiss };
window.GreenShopToast = api;
window.showGreenShopToast = show;

// Các alert() cũ trong JS cũng được đưa về cùng một kiểu toast.
window.alert = (message) => show(message, 'warning', DEFAULT_DURATION);

document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('gs-system-toast-enabled');

    const messages = readServerMessages();
    removeLegacyFlash(messages);

    const seen = new Set();
    messages.forEach((item) => {
        const message = String(item?.message ?? '').trim();
        const type = normalizeType(item?.type);
        if (!message) return;

        const key = `${type}:${message}`;
        if (seen.has(key)) return;
        seen.add(key);

        show(message, type, DEFAULT_DURATION);
    });
});
