document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.filter-toggle').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const filterGroup = toggle.closest('.filter-group');

            if (!filterGroup) {
                return;
            }

            const isCollapsed = filterGroup.classList.toggle('collapsed');
            toggle.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
        });
    });

    const priceRange = document.getElementById('priceRange');
    const maxPriceInput = document.getElementById('maxPriceInput');

    if (priceRange && maxPriceInput) {
        priceRange.addEventListener('input', () => {
            maxPriceInput.value = priceRange.value;
        });

        maxPriceInput.addEventListener('input', () => {
            const value = Number.parseInt(maxPriceInput.value || '0', 10);
            const max = Number.parseInt(priceRange.max || '0', 10);
            priceRange.value = String(Math.min(Math.max(value, 0), max));
        });
    }

    const toast = document.getElementById('shopToast');

    if (!toast) {
        return;
    }

    const closeButton = toast.querySelector('.shop-toast-close');
    let isHidden = false;

    const hideToast = () => {
        if (isHidden || !toast.isConnected) {
            return;
        }

        isHidden = true;
        toast.classList.add('is-hiding');
        window.setTimeout(() => toast.remove(), 250);
    };

    closeButton?.addEventListener('click', hideToast);
    window.setTimeout(hideToast, 3500);
});
