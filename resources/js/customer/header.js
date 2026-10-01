document.addEventListener('DOMContentLoaded', () => {
    const searchBox = document.querySelector('.header-search');
    const searchButton = document.getElementById('headerSearchToggle');
    const searchInput = document.getElementById('headerSearchInput');
    const searchForm = document.getElementById('headerSearchForm');

    if (!searchBox || !searchButton || !searchInput || !searchForm) {
        return;
    }

    let clickTimer = null;

    const openSearch = () => {
        searchBox.classList.add('active');
        searchButton.setAttribute('aria-expanded', 'true');
        window.setTimeout(() => searchInput.focus(), 100);
    };

    const closeSearch = () => {
        searchBox.classList.remove('active');
        searchButton.setAttribute('aria-expanded', 'false');
        searchInput.blur();
    };

    searchButton.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        window.clearTimeout(clickTimer);

        clickTimer = window.setTimeout(() => {
            if (!searchBox.classList.contains('active')) {
                openSearch();
                return;
            }

            const keyword = searchInput.value.trim();

            if (keyword === '') {
                searchInput.focus();
                return;
            }

            searchInput.value = keyword;
            searchForm.requestSubmit();
        }, 250);
    });

    searchButton.addEventListener('dblclick', (event) => {
        event.preventDefault();
        event.stopPropagation();
        window.clearTimeout(clickTimer);
        closeSearch();
    });

    searchBox.addEventListener('click', (event) => {
        event.stopPropagation();
    });

    searchForm.addEventListener('submit', (event) => {
        const keyword = searchInput.value.trim();

        if (keyword === '') {
            event.preventDefault();
            openSearch();
            return;
        }

        searchInput.value = keyword;
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') {
            return;
        }

        const keyword = searchInput.value.trim();

        if (keyword === '') {
            event.preventDefault();
            searchInput.focus();
            return;
        }

        searchInput.value = keyword;
    });

    document.addEventListener('click', (event) => {
        if (!searchBox.contains(event.target)) {
            closeSearch();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            window.clearTimeout(clickTimer);
            closeSearch();
        }
    });
});


// AJAX ADD TO CART
(() => {
    const showToast = (message, type = 'success') => {
        if (window.GreenShopToast?.show) {
            window.GreenShopToast.show(message, type, 3000);
            return;
        }

        window.alert(message);
    };

    const updateCartBadge = (count) => {
        if (!Number.isFinite(Number(count))) {
            return;
        }

        const cartButton = document.querySelector('.cart-button');
        if (!cartButton) {
            return;
        }

        let badge = cartButton.querySelector('.cart-count');
        const numericCount = Math.max(0, Number.parseInt(count, 10) || 0);

        if (numericCount <= 0) {
            badge?.remove();
            return;
        }

        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'cart-count';
            cartButton.appendChild(badge);
        }

        badge.textContent = numericCount > 99 ? '99+' : String(numericCount);
    };

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('form[data-ajax-cart="true"]');
        if (!form) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        if (form.dataset.submitting === 'true') {
            return;
        }

        const submitButton = form.querySelector('button[type="submit"]');
        const originalHtml = submitButton?.innerHTML ?? '';
        form.dataset.submitting = 'true';

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('is-loading');
            submitButton.textContent = 'Đang thêm...';
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (response.status === 401) {
                window.location.href = form.dataset.loginUrl || '/dang-nhap';
                return;
            }

            let payload = {};
            try {
                payload = await response.json();
            } catch (_) {
                payload = {};
            }

            if (!response.ok || payload.success === false) {
                const firstValidationError = payload.errors
                    ? Object.values(payload.errors).flat()[0]
                    : null;
                showToast(
                    firstValidationError || payload.message || 'Không thể thêm sản phẩm vào giỏ hàng.',
                    'error'
                );
                return;
            }

            updateCartBadge(payload.cart_count);
            showToast(payload.message || 'Đã thêm sản phẩm vào giỏ hàng.');
        } catch (error) {
            console.error('Add to cart request failed:', error);
            showToast('Kết nối không ổn định. Vui lòng thử lại.', 'error');
        } finally {
            form.dataset.submitting = 'false';
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.classList.remove('is-loading');
                submitButton.innerHTML = originalHtml;
            }
        }
    });
})();
