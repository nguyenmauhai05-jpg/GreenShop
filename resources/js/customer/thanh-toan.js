import { renderGoogleMap, googleQuery } from '../google-map-no-key.js';
document.addEventListener('DOMContentLoaded', () => {
    const config = window.GreenShopCheckout || {};
    const form = document.getElementById('checkoutForm');
    const submitButton = document.getElementById('placeOrderButton');
    const hiddenId = document.getElementById('selectedAddressId');
    const shippingFeeValue = document.getElementById('shippingFeeValue');
    const checkoutTotalValue = document.getElementById('checkoutTotalValue');
    const shippingDistance = document.getElementById('shippingDistance');
    const shippingMapStatus = document.getElementById('shippingMapStatus');
    const retryShippingQuote = document.getElementById('retryShippingQuote');
    const shippingRouteSource = document.getElementById('shippingRouteSource');
    const shippingMapElement = document.getElementById('shippingMap');
    const summaryDistanceValue = document.getElementById('summaryDistanceValue');
    const shippingSummaryNotice = document.getElementById('shippingSummaryNotice');
    const placeOrderButtonText = document.getElementById('placeOrderButtonText');
    const selectedVoucherId = document.getElementById('selectedVoucherId');
    const voucherDiscountValue = document.getElementById('voucherDiscountValue');
    const voucherSummaryLine = document.getElementById('voucherSummaryLine');
    const voucherOptions = Array.from(document.querySelectorAll('[data-voucher-option]'));
    const voucherSuggestionBox = document.getElementById('voucherSuggestionBox');
    const voucherSuggestionProducts = document.getElementById('voucherSuggestionProducts');
    const voucherSuggestionClose = document.getElementById('voucherSuggestionClose');
    const addressListToggle = document.getElementById('addressListToggle');
    const checkoutAddressList = document.getElementById('checkoutAddressList');
    const voucherListToggle = document.getElementById('voucherListToggle');
    const checkoutVoucherList = document.getElementById('checkoutVoucherList');
    const selectedVoucherSummary = document.getElementById('selectedVoucherSummary');
    const selectedShippingMethod = document.getElementById('selectedShippingMethod');
    const shippingMethodSummary = document.getElementById('shippingMethodSummary');
    const shippingMethodOptions = Array.from(document.querySelectorAll('[data-shipping-method]'));
    const shippingViewAll = document.getElementById('shippingViewAll');
    const shippingMoreOptions = document.getElementById('shippingMoreOptions');
    const selectedShippingVoucherId = document.getElementById('selectedShippingVoucherId');
    const selectedShippingVoucherSummary = document.getElementById('selectedShippingVoucherSummary');
    const shippingVoucherOptions = Array.from(document.querySelectorAll('[data-shipping-voucher-option]'));
    const shippingVoucherSummaryLine = document.getElementById('shippingVoucherSummaryLine');
    const shippingVoucherDiscountValue = document.getElementById('shippingVoucherDiscountValue');
    const shippingVoucherBadge = selectedShippingVoucherSummary?.querySelector('[data-shipping-voucher-badge]');
    const voucherCodeInput = document.getElementById('checkoutVoucherCode');
    const applyVoucherCodeButton = document.getElementById('checkoutApplyVoucherCode');
    const voucherFeedback = document.getElementById('checkoutVoucherFeedback');
    const cartFeedback = document.getElementById('checkoutCartFeedback');

    const setExpandableList = (button, panel, open) => {
        if (!button || !panel) return;
        panel.hidden = !open;
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
        button.classList.toggle('open', open);
    };

    addressListToggle?.addEventListener('click', () => {
        setExpandableList(addressListToggle, checkoutAddressList, checkoutAddressList?.hidden);
    });

    voucherListToggle?.addEventListener('click', () => {
        setExpandableList(voucherListToggle, checkoutVoucherList, checkoutVoucherList?.hidden);
    });

    shippingViewAll?.addEventListener('click', () => {
        const open = Boolean(shippingMoreOptions?.hidden);
        setExpandableList(shippingViewAll, shippingMoreOptions, open);
        // Same chevron as the Voucher and Address expanders (CSS rotates it).
    });

    const formatMoney = value => `${Math.round(Number(value) || 0).toLocaleString('vi-VN')}đ`;
    let subtotal = Number(config.subtotal || 0);
    let addingSuggestedPlant = false;
    let pendingVoucherId = null;
    let pendingShippingVoucherId = null;
    let currentStandardShippingFee = Number(config.initialStandardShippingFee || 0);
    let currentExpressShippingFee = Number(config.initialExpressShippingFee || 0);
    let currentGhtkShippingFee = Number(config.initialGhtkShippingFee || 0);
    let currentShippingFee = 0;
    let currentShippingVoucherDiscount = Number(config.initialShippingVoucherDiscount || 0);
    let currentVoucherDiscount = Number(config.initialVoucherDiscount || 0);
    let shippingReady = Boolean(hiddenId?.value) && !config.initialShippingError;
    let isSubmitting = false;

    const setSubmitState = () => {
        if (!submitButton || isSubmitting) return;
        submitButton.disabled = !hiddenId?.value || !shippingReady || addingSuggestedPlant;
    };

    setSubmitState();

    const calculateShippingVoucherDiscountFor = (voucher, fee) => {
        if (!voucher || fee <= 0 || subtotal < Number(voucher.don_hang_toi_thieu || 0)) return 0;
        let discount = voucher.loai_giam === 'phan_tram'
            ? fee * (Number(voucher.gia_tri_giam || 0) / 100)
            : Number(voucher.gia_tri_giam || 0);
        if (voucher.giam_toi_da !== null && voucher.giam_toi_da !== undefined) {
            discount = Math.min(discount, Number(voucher.giam_toi_da || 0));
        }
        return Math.max(0, Math.min(discount, fee));
    };

    // Update prices within the shipping option cards themselves (no separate discount strip).
    // Only the selected method receives the shipping voucher discount.
    const renderShippingOptionPrices = () => {
        shippingMethodOptions.forEach(option => {
            const method = option.dataset.shippingMethod;
            const originalFee = method === 'EXPRESS' ? currentExpressShippingFee
                : (method === 'GHTK_EXPRESS' ? currentGhtkShippingFee : currentStandardShippingFee);
            const selected = method === selectedShippingMethod?.value;
            const discount = selected ? Math.min(originalFee, Math.max(0, currentShippingVoucherDiscount)) : 0;
            const price = option.querySelector('[data-shipping-price]');
            if (!price) return;
            const original = price.querySelector('.shipping-original-price');
            const current = price.querySelector('strong');
            const note = price.querySelector('.shipping-discount-note');
            if (original) {
                original.hidden = discount <= 0;
                original.textContent = formatMoney(originalFee);
            }
            if (current) current.textContent = originalFee - discount > 0
                ? formatMoney(originalFee - discount) : 'Miễn phí';
            if (note) {
                note.hidden = discount <= 0;
                note.textContent = `Đã trừ voucher -${formatMoney(discount)}`;
            }
            price?.classList.toggle('has-shipping-discount', discount > 0);
        });
    };

    const findShippingVoucher = id =>
        Array.isArray(config.shippingVouchers)
            ? config.shippingVouchers.find(item => String(item.voucher_id) === String(id))
            : null;

    const applyShippingVoucherSelection = option => {
        const id = option?.dataset.id || '';
        const voucher = findShippingVoucher(id);
        const discount = calculateShippingVoucherDiscountFor(voucher, currentShippingFee);
        if (voucher && discount <= 0) {
            pendingShippingVoucherId = String(voucher.voucher_id);
            if (voucherFeedback) {
                const missing = Math.max(0, Number(voucher.don_hang_toi_thieu || 0) - subtotal);
                voucherFeedback.textContent = missing > 0
                    ? `Voucher phí vận chuyển chưa đủ điều kiện. Cần mua thêm ${formatMoney(missing)}.`
                    : 'Voucher phí vận chuyển chưa thể áp dụng với mức phí hiện tại.';
                voucherFeedback.className = 'checkout-voucher-feedback error';
            }
            renderVoucherSuggestions(voucher);
            return false;
        }

        if (!voucher || String(voucher.voucher_id) === pendingShippingVoucherId) pendingShippingVoucherId = null;
        shippingVoucherOptions.forEach(item => item.classList.toggle('selected', item === option));
        if (selectedShippingVoucherId) selectedShippingVoucherId.value = id;

        currentShippingVoucherDiscount = discount;

        const title = selectedShippingVoucherSummary?.querySelector('[data-shipping-voucher-summary-title]');
        const desc = selectedShippingVoucherSummary?.querySelector('[data-shipping-voucher-summary-desc]');

        if (voucher && currentShippingVoucherDiscount > 0) {
            selectedShippingVoucherSummary?.classList.add('has-voucher');
            if (title) title.textContent = `${voucher.ma_voucher} · ${voucher.ten_voucher}`;
            if (desc) desc.textContent = `Giảm ${formatMoney(currentShippingVoucherDiscount)} phí vận chuyển.`;
        } else {
            selectedShippingVoucherSummary?.classList.remove('has-voucher');
            if (title) title.textContent = voucher ? `${voucher.ma_voucher} · ${voucher.ten_voucher}` : 'Không dùng voucher vận chuyển';
            if (desc) desc.textContent = voucher ? 'Voucher chưa đủ điều kiện cho đơn hàng này.' : 'Phí ship tính theo phương thức vận chuyển.';
        }

        if (shippingVoucherDiscountValue) shippingVoucherDiscountValue.textContent = currentShippingVoucherDiscount > 0
            ? `-${formatMoney(currentShippingVoucherDiscount)}`
            : '0đ';
        shippingVoucherSummaryLine?.classList.toggle('active', currentShippingVoucherDiscount > 0);
        if (shippingVoucherBadge) shippingVoucherBadge.hidden = currentShippingVoucherDiscount <= 0;
        if (voucherFeedback && voucher) {
            voucherFeedback.textContent = `Đã áp dụng ${voucher.ma_voucher}, giảm ${formatMoney(currentShippingVoucherDiscount)} phí vận chuyển.`;
            voucherFeedback.className = 'checkout-voucher-feedback success';
        }
        renderShippingOptionPrices();
        updateMoney();
        return true;
    };

    // Always reevaluate the actual monetary saving when subtotal or shipping fee changes.
    const autoApplyBestShippingVoucher = () => {
        const eligible = shippingVoucherOptions
            .map(option => ({ option, voucher: findShippingVoucher(option.dataset.id || '') }))
            .filter(item => item.voucher)
            .map(item => ({ ...item, discount: calculateShippingVoucherDiscountFor(item.voucher, currentShippingFee) }))
            .filter(item => item.discount > 0)
            .sort((x, y) => y.discount - x.discount);
        const current = eligible.find(item => String(item.option.dataset.id) === String(selectedShippingVoucherId?.value || ''));
        const best = eligible[0];
        // On equal savings, avoid changing the displayed voucher unnecessarily.
        const chosen = current && best && current.discount >= best.discount ? current : best;
        applyShippingVoucherSelection(chosen?.option || shippingVoucherOptions.find(option => !option.dataset.id) || null);
    };

    const applyShippingMethod = method => {
        const allowed = ['STANDARD', 'EXPRESS', 'GHTK_EXPRESS'];
        const normalized = allowed.includes(method) ? method : 'STANDARD';

        if (selectedShippingMethod) selectedShippingMethod.value = normalized;
        currentShippingFee = normalized === 'EXPRESS'
            ? currentExpressShippingFee
            : (normalized === 'GHTK_EXPRESS' ? currentGhtkShippingFee : currentStandardShippingFee);

        shippingMethodOptions.forEach(option => option.classList.toggle('selected', option.dataset.shippingMethod === normalized));

        if (shippingMethodSummary) shippingMethodSummary.textContent = normalized === 'EXPRESS'
            ? 'Hỏa tốc'
            : (normalized === 'GHTK_EXPRESS' ? 'GHTK Express' : 'Giao Hàng Nhanh');

        if (shippingFeeValue) shippingFeeValue.textContent = currentShippingFee > 0 ? formatMoney(currentShippingFee) : 'Miễn phí';

        autoApplyBestShippingVoucher();
    };

    shippingMethodOptions.forEach(option => option.addEventListener('click', () => applyShippingMethod(option.dataset.shippingMethod)));

    shippingVoucherOptions.forEach(option => option.addEventListener('click', () => applyShippingVoucherSelection(option)));

    // ---------------------------
    // Voucher
    // ---------------------------
    const calculateVoucherDiscount = voucher => {
        if (!voucher || subtotal < Number(voucher.don_hang_toi_thieu || 0)) {
            return 0;
        }

        let discount = 0;

        if (voucher.loai_giam === 'phan_tram') {
            discount = subtotal * (Number(voucher.gia_tri_giam || 0) / 100);

            if (voucher.giam_toi_da !== null && voucher.giam_toi_da !== undefined) {
                discount = Math.min(discount, Number(voucher.giam_toi_da || 0));
            }
        } else {
            discount = Number(voucher.gia_tri_giam || 0);
        }

        return Math.max(0, Math.min(discount, subtotal));
    };

    const findVoucher = id =>
        Array.isArray(config.vouchers)
            ? config.vouchers.find(item => String(item.voucher_id) === String(id))
            : null;

    const selectedVoucher = () =>
        findVoucher(selectedVoucherId?.value || '');

    const updateMoney = () => {
        const total = Math.max(0, subtotal + currentShippingFee - currentVoucherDiscount - currentShippingVoucherDiscount);

        if (voucherDiscountValue) {
            voucherDiscountValue.textContent = currentVoucherDiscount > 0
                ? `-${formatMoney(currentVoucherDiscount)}`
                : '0đ';
        }

        voucherSummaryLine?.classList.toggle('active', currentVoucherDiscount > 0);

        if (checkoutTotalValue) checkoutTotalValue.textContent = formatMoney(total);
    };

    const escapeHtml = text => String(text ?? '').replace(/[&<>"']/g, char =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char]);
    const safeProductUrl = value => {
        try {
            const url = new URL(String(value), window.location.origin);
            return ['http:', 'https:'].includes(url.protocol) && url.origin === window.location.origin ? url.href : '#';
        } catch (_) { return '#'; }
    };
    // Ẩn đề xuất là thao tác giao diện; không thay đổi voucher hợp lệ đang được áp dụng.
    // Xóa pending để gợi ý/cảnh báo không tự xuất hiện lại khi cập nhật giỏ hàng.
    voucherSuggestionClose?.addEventListener('click', () => {
        if (voucherSuggestionBox) voucherSuggestionBox.hidden = true;
        if (voucherSuggestionProducts) voucherSuggestionProducts.innerHTML = '';
        pendingVoucherId = null;
        pendingShippingVoucherId = null;
        if (voucherFeedback?.classList.contains('error') &&
            /chưa đủ điều kiện|cần mua thêm/i.test(voucherFeedback.textContent || '')) {
            voucherFeedback.textContent = '';
            voucherFeedback.className = 'checkout-voucher-feedback';
        }
    });

    const renderVoucherSuggestions = voucher => {
        if (!voucherSuggestionBox || !voucherSuggestionProducts) return;

        const minOrder = Number(voucher?.don_hang_toi_thieu || 0);
        const missing = Math.max(0, minOrder - subtotal);

        if (!voucher || missing <= 0) {
            voucherSuggestionBox.hidden = true;
            voucherSuggestionProducts.innerHTML = '';
            return;
        }

        const products = Array.isArray(config.suggestedProducts)
            ? [...config.suggestedProducts]
            : [];

        const relatedCategories = (config.cartCategoryIds || []).map(Number);
        products.sort((a, b) => {
            const aRelated = relatedCategories.includes(Number(a.category_id)) ? 0 : 1;
            const bRelated = relatedCategories.includes(Number(b.category_id)) ? 0 : 1;
            if (aRelated !== bRelated) return aRelated - bRelated;
            const aPrice = Number(a.price || 0);
            const bPrice = Number(b.price || 0);

            const aEnough = aPrice >= missing ? 0 : 1;
            const bEnough = bPrice >= missing ? 0 : 1;

            if (aEnough !== bEnough) return aEnough - bEnough;
            return Math.abs(aPrice - missing) - Math.abs(bPrice - missing);
        });

        const best = products.slice(0, 4);

        voucherSuggestionProducts.innerHTML = best.length
            ? best.map(product => `
                <div class="voucher-suggest-product">
                    <a class="voucher-suggest-image" href="${safeProductUrl(product.url)}" aria-label="Xem ${escapeHtml(product.name)}">
                        ${product.image
                            ? `<img src="${safeProductUrl(product.image)}" alt="">`
                            : '<span>🌿</span>'}
                    </a>
                    <span class="voucher-suggest-info">
                        <strong>${escapeHtml(product.name)}</strong>
                        <small>${formatMoney(product.price)}</small>
                        <span class="voucher-suggest-actions">
                            <a class="voucher-suggest-detail" href="${safeProductUrl(product.url)}">Xem chi tiết →</a>
                            <button type="button" class="voucher-suggest-add" data-suggest-add="${Number(product.plant_id)}">+ Thêm vào đơn hàng</button>
                        </span>
                    </span>
                </div>
            `).join('')
            : '<div class="voucher-no-suggestion">Chưa có sản phẩm phù hợp để đề xuất.</div>';

        voucherSuggestionBox.hidden = false;
    };

    const applyVoucherSelection = option => {
        const id = option?.dataset.id || '';
        const voucher = findVoucher(id);
        if (voucher && subtotal < Number(voucher.don_hang_toi_thieu || 0)) {
            pendingVoucherId = String(voucher.voucher_id);
            renderVoucherSuggestions(voucher);
            if (voucherFeedback) {
                voucherFeedback.textContent = `Mã ${voucher.ma_voucher} chưa đủ điều kiện. Cần mua thêm ${formatMoney(Number(voucher.don_hang_toi_thieu) - subtotal)}.`;
                voucherFeedback.className = 'checkout-voucher-feedback error';
            }
            setExpandableList(voucherListToggle, checkoutVoucherList, false);
            return false;
        }

        if (!voucher || String(voucher.voucher_id) === pendingVoucherId) pendingVoucherId = null;
        voucherOptions.forEach(item =>
            item.classList.toggle('selected', item === option)
        );

        if (selectedVoucherId) selectedVoucherId.value = id;

        const summaryTitle = selectedVoucherSummary?.querySelector('[data-voucher-summary-title]');
        const summaryDesc = selectedVoucherSummary?.querySelector('[data-voucher-summary-desc]');
        const autoBadge = selectedVoucherSummary?.querySelector('.auto-applied-badge');

        if (!voucher) {
            currentVoucherDiscount = 0;
            renderVoucherSuggestions(null);
            selectedVoucherSummary?.classList.remove('has-voucher');
            if (summaryTitle) summaryTitle.textContent = 'Không sử dụng voucher';
            if (summaryDesc) summaryDesc.textContent = 'Thanh toán theo giá hiện tại.';
            if (autoBadge) autoBadge.hidden = true;
            updateMoney();
            setExpandableList(voucherListToggle, checkoutVoucherList, false);
            return true;
        }

        const minOrder = Number(voucher.don_hang_toi_thieu || 0);

        if (subtotal >= minOrder) {
            currentVoucherDiscount = calculateVoucherDiscount(voucher);
            renderVoucherSuggestions(null);
            selectedVoucherSummary?.classList.add('has-voucher');
            if (summaryTitle) summaryTitle.textContent = `${voucher.ma_voucher} · ${voucher.ten_voucher}`;
            if (summaryDesc) summaryDesc.textContent = `Giảm ${formatMoney(currentVoucherDiscount)} cho đơn hàng này.`;
            if (autoBadge) autoBadge.hidden = false;
            setExpandableList(voucherListToggle, checkoutVoucherList, false);
        } else {
            currentVoucherDiscount = 0;
            renderVoucherSuggestions(voucher);
            selectedVoucherSummary?.classList.remove('has-voucher');
            if (summaryTitle) summaryTitle.textContent = `${voucher.ma_voucher} · ${voucher.ten_voucher}`;
            if (summaryDesc) summaryDesc.textContent = `Chưa đủ điều kiện. Cần thêm ${formatMoney(minOrder - subtotal)}.`;
            if (autoBadge) autoBadge.hidden = true;
        }

        updateMoney();
        if (voucherFeedback) {
            voucherFeedback.textContent = currentVoucherDiscount > 0
                ? `Đã áp dụng ${voucher.ma_voucher}, giảm ${formatMoney(currentVoucherDiscount)}.`
                : 'Mã chưa mang lại giảm giá cho đơn hàng này.';
            voucherFeedback.className = `checkout-voucher-feedback ${currentVoucherDiscount > 0 ? 'success' : 'error'}`;
        }
        return currentVoucherDiscount > 0;
    };

    // Reevaluate all eligible order vouchers, not only the previously selected one.
    // Compare real VND savings (percentage discounts include their maximum cap).
    const autoApplyBestOrderVoucher = () => {
        const eligible = voucherOptions
            .map(option => ({ option, voucher: findVoucher(option.dataset.id || '') }))
            .filter(item => item.voucher)
            .map(item => ({ ...item, discount: calculateVoucherDiscount(item.voucher) }))
            .filter(item => item.discount > 0)
            .sort((x, y) => y.discount - x.discount);
        const current = eligible.find(item => String(item.option.dataset.id) === String(selectedVoucherId?.value || ''));
        const best = eligible[0];
        const chosen = current && best && current.discount >= best.discount ? current : best;
        applyVoucherSelection(chosen?.option || voucherOptions.find(option => !option.dataset.id) || null);
    };

    // Event delegation: product rows are replaced after every server-side recalculation.
    document.getElementById('checkoutForm')?.addEventListener('click', async event => {
        const button = event.target.closest('[data-qty-change]');
        if (!button) return;
        const row = button.closest('[data-plant-id]');
        if (!row || row.dataset.busy === '1') return;
        const current = Number(row.querySelector('.checkout-qty-value')?.textContent || 1);
        const next = current + Number(button.dataset.qtyChange);
        if (next < 1 || next > Number(row.dataset.stock || 0)) {
            showCartFeedback(next < 1 ? 'Số lượng tối thiểu là 1.' : 'Số lượng vượt quá tồn kho.', 'error');
            return;
        }
        row.dataset.busy = '1';
        row.querySelectorAll('button').forEach(el => el.disabled = true);
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content || form.querySelector('input[name="_token"]')?.value;
            const response = await fetch(config.quantityUrl, {
                method: 'PATCH', credentials: 'same-origin',
                headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token},
                body: JSON.stringify({plant_id: Number(row.dataset.plantId), quantity: next})
            });
            const payload = await response.json();
            if (!response.ok) throw new Error(payload.message || 'Không thể cập nhật số lượng.');
            // Refresh server-generated markup and all totals, including voucher eligibility.
            await syncCartAfterAdd();
            showCartFeedback('Đã cập nhật số lượng sản phẩm.');
        } catch (error) {
            showCartFeedback(error.message || 'Không thể cập nhật số lượng.', 'error');
            row.dataset.busy = '0';
            row.querySelectorAll('button').forEach(el => el.disabled = false);
        }
    });

    // Thêm cây gợi ý bằng API giỏ hàng có sẵn; sau đó lấy lại chữ ký và toàn bộ giá từ server.
    // Không tự sửa tổng tiền / chữ ký phía trình duyệt (placeOrder kiểm tra cả hai).
    const showCartFeedback = (message, type = 'success') => {
        if (!cartFeedback) return;
        cartFeedback.textContent = message;
        cartFeedback.className = `checkout-cart-feedback ${type}`;
    };
    const syncCartAfterAdd = async () => {
        const response = await fetch(config.refreshCartUrl, {
            headers: { 'Accept': 'application/json' }, credentials: 'same-origin', cache: 'no-store'
        });
        const payload = await response.json();
        if (!response.ok || !payload.products_html || !payload.cart_signature) {
            throw new Error(payload.message || 'Không thể cập nhật thông tin đơn hàng.');
        }

        const markup = document.createElement('div');
        markup.innerHTML = payload.products_html;
        const refreshedProducts = markup.querySelector('#checkoutProductsCard');
        const existingProducts = document.getElementById('checkoutProductsCard');
        if (!refreshedProducts || !existingProducts) throw new Error('Không thể tải lại danh sách sản phẩm.');
        existingProducts.replaceWith(refreshedProducts);

        subtotal = Number(payload.subtotal || 0);
        config.subtotal = subtotal;
        config.suggestedProducts = payload.suggested_products || [];
        config.cartCategoryIds = payload.cart_category_ids || [];
        config.vouchers = payload.vouchers || [];
        config.shippingVouchers = payload.shipping_vouchers || [];
        const signature = document.getElementById('checkoutCartSignature');
        if (signature) signature.value = payload.cart_signature;
        const subtotalLabel = document.getElementById('checkoutSubtotalValue');
        if (subtotalLabel) subtotalLabel.textContent = formatMoney(subtotal);
        const headerCount = document.querySelector('.header-icon-btn.cart-button .cart-count');
        if (headerCount) headerCount.textContent = payload.cart_count > 99 ? '99+' : String(payload.cart_count || 0);

        voucherOptions.forEach(option => {
            const voucher = findVoucher(option.dataset.id || '');
            if (!voucher) return;
            const eligible = calculateVoucherDiscount(voucher) > 0;
            option.classList.toggle('eligible', eligible);
            option.classList.toggle('needs-more', !eligible);
        });
        shippingVoucherOptions.forEach(option => {
            const voucher = findShippingVoucher(option.dataset.id || '');
            if (!voucher) return;
            const eligible = calculateShippingVoucherDiscountFor(voucher, currentShippingFee) > 0;
            option.classList.toggle('eligible', eligible);
            option.classList.toggle('needs-more', !eligible);
        });

        // After any quantity/add-to-order change, automatically choose the highest
        // actual discount, even when a smaller voucher was previously selected.
        pendingVoucherId = null;
        pendingShippingVoucherId = null;
        renderVoucherSuggestions(null);
        autoApplyBestOrderVoucher();
        autoApplyBestShippingVoucher();
        updateMoney();
    };

    voucherSuggestionProducts?.addEventListener('click', async event => {
        const button = event.target.closest('[data-suggest-add]');
        if (!button || addingSuggestedPlant) return;
        const plantId = Number(button.dataset.suggestAdd);
        if (!Number.isInteger(plantId) || plantId <= 0) return;
        addingSuggestedPlant = true;
        button.disabled = true;
        button.textContent = 'Đang thêm...';
        setSubmitState();
        try {
            const csrf = form?.querySelector('input[name="_token"]')?.value;
            if (!csrf || !config.addToCartUrl || !config.refreshCartUrl) {
                throw new Error('Không tìm thấy kết nối với giỏ hàng. Vui lòng tải lại trang.');
            }
            const response = await fetch(config.addToCartUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ plant_id: plantId, so_luong: 1 }),
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Không thể thêm cây vào giỏ hàng.');
            try {
                await syncCartAfterAdd();
                showCartFeedback('Đã thêm cây vào đơn hàng và cập nhật tổng tiền, voucher.');
            } catch (syncError) {
                // Cart đã thay đổi trên server: cần tải lại để tránh gửi chữ ký giỏ hàng cũ.
                showCartFeedback('Đã thêm cây. Đang đồng bộ lại đơn hàng...', 'success');
                window.location.reload();
                return;
            }
        } catch (error) {
            showCartFeedback(error.message || 'Thêm sản phẩm thất bại, vui lòng thử lại.', 'error');
        } finally {
            addingSuggestedPlant = false;
            button.disabled = false;
            button.textContent = '+ Thêm vào đơn hàng';
            setSubmitState();
        }
    });

    const applyVoucherCode = () => {
        const code = (voucherCodeInput?.value || '').trim().toLocaleUpperCase('vi-VN');
        if (!code) {
            if (voucherFeedback) {
                voucherFeedback.textContent = 'Vui lòng nhập mã voucher.';
                voucherFeedback.className = 'checkout-voucher-feedback error';
            }
            return;
        }
        const orderVoucher = (config.vouchers || []).find(v =>
            String(v.ma_voucher || '').trim().toLocaleUpperCase('vi-VN') === code);
        const shippingVoucher = (config.shippingVouchers || []).find(v =>
            String(v.ma_voucher || '').trim().toLocaleUpperCase('vi-VN') === code);
        if (orderVoucher) {
            const option = voucherOptions.find(o => String(o.dataset.id) === String(orderVoucher.voucher_id));
            if (option) applyVoucherSelection(option);
        } else if (shippingVoucher) {
            const option = shippingVoucherOptions.find(o => String(o.dataset.id) === String(shippingVoucher.voucher_id));
            if (option) applyShippingVoucherSelection(option);
        } else if (voucherFeedback) {
            voucherFeedback.textContent = 'Mã không hợp lệ, hết lượt hoặc chưa đến thời gian áp dụng.';
            voucherFeedback.className = 'checkout-voucher-feedback error';
        }
    };
    applyVoucherCodeButton?.addEventListener('click', applyVoucherCode);
    voucherCodeInput?.addEventListener('keydown', event => {
        if (event.key === 'Enter') { event.preventDefault(); applyVoucherCode(); }
    });

    voucherOptions.forEach(option => {
        option.addEventListener('click', () => applyVoucherSelection(option));
    });

    // ---------------------------
    // Chọn phương thức thanh toán
    // ---------------------------
    const paymentOptions = Array.from(document.querySelectorAll('.payment-option input[type="radio"]'));
    const selectedPaymentMethod = () => paymentOptions.find(input => input.checked)?.value || 'COD';

    const refreshPaymentState = () => {
        paymentOptions.forEach(input => {
            input.closest('.payment-option')?.classList.toggle('selected', input.checked);
        });
        const method = selectedPaymentMethod();
        const isPaypal = method === 'PAYPAL';
        const isPayOS = method === 'PAYOS';
        if (placeOrderButtonText) {
            placeOrderButtonText.textContent = isPaypal ? 'Thanh toán qua PayPal' : (isPayOS ? 'Thanh toán qua payOS' : 'Đặt hàng');
        }
    };

    paymentOptions.forEach(input => input.addEventListener('change', refreshPaymentState));
    refreshPaymentState();

    /*
     * Khởi tạo voucher sau khi payment helpers đã được khai báo.
     * Khởi tạo voucher sau khi hàm chọn phương thức thanh toán được khai báo.
     */
    // Always choose the largest *actual* eligible discount on initial load.
    // Keep the current voucher only when its saving ties with the best one.
    autoApplyBestOrderVoucher();

    applyShippingMethod(selectedShippingMethod?.value || config.initialShippingMethod || 'STANDARD');

    // ---------------------------
    // Public Google Maps iframe + external directions, with no Google API key.
    const renderQuote = quote => {
        if (!quote) return;

        const fee = Number(quote.shipping_fee || 0);
        const distance = Number(quote.distance_km || 0);

        currentExpressShippingFee = fee;
        currentStandardShippingFee = distance <= 5
            ? 15000
            : (distance <= 10 ? 20000 : (distance <= 20 ? 30000 : Math.min(50000, 30000 + Math.ceil((distance - 20) / 10) * 5000)));
        currentGhtkShippingFee = distance <= 5
            ? 18000
            : (distance <= 10 ? 25000 : Math.min(60000, 25000 + Math.ceil((distance - 10) / 10) * 7000));

        const feeByMethod = { STANDARD: currentStandardShippingFee, EXPRESS: currentExpressShippingFee, GHTK_EXPRESS: currentGhtkShippingFee };
        shippingMethodOptions.forEach(option => {
            const optionFee = Number(feeByMethod[option.dataset.shippingMethod] || 0);
            option.dataset.shippingFee = String(optionFee);
            const price = option.querySelector('.shipping-method-price strong');
            if (price) price.textContent = optionFee > 0 ? formatMoney(optionFee) : 'Miễn phí';
        });
        applyShippingMethod(selectedShippingMethod?.value || 'STANDARD');
        const formattedDistance = `${distance.toLocaleString('vi-VN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} km`;
        if (shippingDistance) shippingDistance.textContent = formattedDistance;
        if (summaryDistanceValue) summaryDistanceValue.textContent = formattedDistance;
        if (shippingSummaryNotice) {
            const strong = shippingSummaryNotice.querySelector('strong');
            const span = shippingSummaryNotice.querySelector('span');
            if (strong) strong.textContent = `${formattedDistance} · ${shippingMethodSummary?.textContent || 'Vận chuyển'} → ${currentShippingFee > 0 ? formatMoney(currentShippingFee) : 'Miễn phí'}`;
            if (span) span.textContent = 'Phí được tính theo phương thức vận chuyển đang chọn và tự áp dụng voucher phí vận chuyển tốt nhất nếu đủ điều kiện.';
        }


        if (shippingMapStatus) {
            shippingMapStatus.classList.remove('error');
            shippingMapStatus.textContent = `Đã tính phí vận chuyển cho địa chỉ đã chọn: ${formattedDistance}.`;
        }
        if (shippingRouteSource) {
            shippingRouteSource.textContent = quote.route?.source === 'osrm'
                ? 'Khoảng cách theo tuyến đường (OSRM)'
                : 'Khoảng cách ước tính (đường chim bay; dịch vụ tuyến đường đang tạm gián đoạn)';
        }

        const destination = googleQuery(quote.customer?.address, quote.customer?.lat, quote.customer?.lng);
        const origin = googleQuery(quote.store?.address, quote.store?.lat, quote.store?.lng);
        renderGoogleMap(shippingMapElement, destination, origin);
    };

    const readInitialQuote = () => {
        const payload = document.getElementById('initialShippingQuote');
        if (!payload?.textContent) return null;
        try {
            return JSON.parse(payload.textContent);
        } catch (_) {
            return null;
        }
    };

    const initialQuote = config.initialShippingQuote || readInitialQuote();
    if (initialQuote) renderQuote(initialQuote);

    const refreshShippingQuote = async addressId => {

        if (!config.shippingQuoteUrl || !addressId) {
            shippingReady = false;
            setSubmitState();
            return;
        }

        shippingReady = false;
        if (retryShippingQuote) retryShippingQuote.hidden = true;
        setSubmitState();
        if (shippingMapStatus) {
            shippingMapStatus.classList.remove('error');
            shippingMapStatus.textContent = 'Đang tính khoảng cách và phí vận chuyển...';
        }
        if (shippingDistance) shippingDistance.textContent = '-- km';

        try {
            const url = new URL(config.shippingQuoteUrl, window.location.origin);
            url.searchParams.set('address_id', addressId);
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            const payload = await response.json();

            if (!response.ok || !payload.ok) {
                throw new Error(payload.message || 'Không thể tính phí vận chuyển.');
            }

            renderQuote(payload.data);
            shippingReady = true;
            if (retryShippingQuote) retryShippingQuote.hidden = true;
        } catch (error) {
            shippingReady = false;
            if (retryShippingQuote) retryShippingQuote.hidden = false;
            if (shippingMapStatus) {
                shippingMapStatus.classList.add('error');
                shippingMapStatus.textContent = error.message || 'Không thể tính phí vận chuyển.';
            }
            if (shippingRouteSource) shippingRouteSource.textContent = '';
            if (shippingFeeValue) shippingFeeValue.textContent = '--';
            if (summaryDistanceValue) summaryDistanceValue.textContent = '-- km';
            if (shippingSummaryNotice) {
                const strong = shippingSummaryNotice.querySelector('strong');
                const span = shippingSummaryNotice.querySelector('span');
                if (strong) strong.textContent = 'Không tính được khoảng cách';
                if (span) span.textContent = error.message || 'Vui lòng kiểm tra lại địa chỉ giao hàng.';
            }
            currentShippingFee = 0;
            updateMoney();
        } finally {
            setSubmitState();
        }
    };

    retryShippingQuote?.addEventListener('click', () => refreshShippingQuote(hiddenId?.value));
    // ---------------------------
    // Chọn địa chỉ trực tiếp từ danh sách
    // ---------------------------
    const addressOptions = Array.from(document.querySelectorAll('[data-address-option]'));

    const selectedAddressEditButton = document.getElementById('selectedAddressEditButton');

    const syncSummaryEditButton = option => {
        if (!selectedAddressEditButton || !option) return;

        selectedAddressEditButton.dataset.addressId = option.dataset.id || '';
        selectedAddressEditButton.dataset.updateUrl = option.dataset.updateUrl || '';
        selectedAddressEditButton.dataset.recipient = option.dataset.name || '';
        selectedAddressEditButton.dataset.phone = option.dataset.phone || '';
        selectedAddressEditButton.dataset.province = option.dataset.province || '';
        selectedAddressEditButton.dataset.ward = option.dataset.ward || '';
        selectedAddressEditButton.dataset.detail = option.dataset.detail || '';
        selectedAddressEditButton.dataset.latitude = option.dataset.latitude || '';
        selectedAddressEditButton.dataset.longitude = option.dataset.longitude || '';
        selectedAddressEditButton.dataset.default = option.dataset.default || '0';
    };

    addressOptions.forEach(option => {
        option.addEventListener('click', async () => {
            addressOptions.forEach(item => {
                item.classList.remove('selected');
                item.closest('.checkout-address-list-row')?.classList.remove('selected');
            });

            option.classList.add('selected');
            option.closest('.checkout-address-list-row')?.classList.add('selected');

            if (!hiddenId) return;

            hiddenId.value = option.dataset.id || '';

            const name = document.querySelector('[data-address-name]');
            const phone = document.querySelector('[data-address-phone]');
            const full = document.querySelector('[data-address-full]');
            const defaultBadge = document.querySelector('[data-address-default]');

            if (name) name.textContent = option.dataset.name || '';
            if (phone) phone.textContent = option.dataset.phone || '';
            if (full) full.textContent = option.dataset.full || '';

            if (defaultBadge) {
                defaultBadge.hidden = option.dataset.default !== '1';
            } else if (option.dataset.default === '1') {
                const strong = document.querySelector('[data-address-name]');
                if (strong?.parentElement) {
                    const badge = document.createElement('em');
                    badge.dataset.addressDefault = '';
                    badge.textContent = 'Mặc định';
                    strong.insertAdjacentElement('afterend', badge);
                }
            }

            syncSummaryEditButton(option);

            // Dòng đang được chọn được ẩn khỏi danh sách để địa chỉ
            // chỉ xuất hiện đúng một lần ở phần tóm tắt phía trên.
            setExpandableList(addressListToggle, checkoutAddressList, false);

            await refreshShippingQuote(hiddenId.value);
        });
    });

    // ---------------------------
    // Chặn double submit
    // ---------------------------
    form?.addEventListener('submit', event => {

        if (!hiddenId?.value || !shippingReady) {
            event.preventDefault();
            if (shippingMapStatus) {
                shippingMapStatus.classList.add('error');
                shippingMapStatus.textContent = 'Chưa tính được phí vận chuyển. Vui lòng kiểm tra lại vị trí giao hàng.';
            }
            return;
        }

        if (!submitButton) return;
        isSubmitting = true;
        submitButton.disabled = true;
        const label = submitButton.querySelector('span');
        if (label) label.textContent = 'Đang xử lý...';
    });
});
