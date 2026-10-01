document.addEventListener('DOMContentLoaded', () => {
    const page = document.getElementById('aiCarePage');
    const form = document.getElementById('plantChatForm');
    const input = document.getElementById('plantChatInput');
    const messages = document.getElementById('plantChatMessages');
    const submit = document.getElementById('plantChatSubmit');
    const conversationInput = document.getElementById('conversationId');
    const selectedPlant = document.getElementById('selectedPlant');
    const imageInput = document.getElementById('plantImageInput');
    const attachButton = document.getElementById('attachImageBtn');
    const previewBox = document.getElementById('imagePreviewBox');
    const previewImage = document.getElementById('imagePreview');
    const fileName = document.getElementById('imageFileName');
    const removeImage = document.getElementById('removeImageBtn');
    const chatUrl = page?.dataset.chatUrl;
    const baseUrl = page?.dataset.baseUrl;
    const cartUrl = page?.dataset.cartUrl;
    const newConversationLink = document.getElementById('newAiConversationLink');
    const isAuthenticated = page?.dataset.authenticated === '1';
    const guestStorageKey = 'greenshop_ai_guest_chat_v1';

    const readGuestHistory = () => {
        if (isAuthenticated) return [];
        try {
            const value = JSON.parse(localStorage.getItem(guestStorageKey) || '[]');
            return Array.isArray(value) ? value : [];
        } catch (_) {
            return [];
        }
    };

    const writeGuestHistory = (items) => {
        if (isAuthenticated) return;
        try {
            // Giới hạn để localStorage không phình vô hạn.
            localStorage.setItem(guestStorageKey, JSON.stringify(items.slice(-80)));
        } catch (error) {
            console.warn('Không thể lưu lịch sử AI tạm trên trình duyệt.', error);
        }
    };

    let guestHistory = readGuestHistory();

    if (!form || !input || !messages || !submit || !conversationInput || !imageInput || !chatUrl || !baseUrl) return;

    // Chat mới là một màn hình trắng thật sự: bỏ conversation cũ khỏi URL.
    // Conversation mới chỉ được tạo khi người dùng gửi tin nhắn đầu tiên.
    if (newConversationLink) {
        newConversationLink.addEventListener('click', () => {
            messages.replaceChildren();
            conversationInput.value = '';
            input.value = '';
            if (selectedPlant) selectedPlant.value = '';
            document.querySelectorAll('.history-item.active').forEach(item => item.classList.remove('active'));
            if (!isAuthenticated) {
                guestHistory = [];
                localStorage.removeItem(guestStorageKey);
            }
        });
    }

    const aiAvatarSvg = `
        <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 21V10"></path>
            <path d="M12 10C9 10 6 8 6 5c3 0 6 2 6 5Z"></path>
            <path d="M12 14c3 0 6-2 6-5-3 0-6 2-6 5Z"></path>
        </svg>`;

    const scrollBottom = () => { messages.scrollTop = messages.scrollHeight; };

    // Render basic Markdown safely (without injecting model-generated HTML).
    function renderReply(container, raw) {
        const text = String(raw || '').replace(/\r\n/g, '\n').trim()
            .replace(/\n[ \t]*\n(?:[ \t]*\n)+/g, '\n\n');
        const lines = text.split('\n');
        let list = null;
        const inline = (node, value) => {
            const parts = value.split(/(\*\*[^*]+\*\*)/g);
            parts.forEach(part => {
                if (part.startsWith('**') && part.endsWith('**')) {
                    const strong = document.createElement('strong');
                    strong.textContent = part.slice(2, -2);
                    node.appendChild(strong);
                } else node.appendChild(document.createTextNode(part));
            });
        };
        lines.forEach(line => {
            const trimmed = line.trim();
            if (!trimmed) { list = null; return; }
            const heading = trimmed.match(/^#{1,4}\s+(.+)$/);
            const item = trimmed.match(/^(?:[-*]\s+|\d+[.)]\s+)(.+)$/);
            if (heading) {
                list = null;
                const h = document.createElement('h4');
                inline(h, heading[1]);
                container.appendChild(h);
            } else if (item) {
                if (!list) { list = document.createElement('ul'); container.appendChild(list); }
                const li = document.createElement('li'); inline(li, item[1]); list.appendChild(li);
            } else {
                list = null;
                const p = document.createElement('p'); inline(p, trimmed); container.appendChild(p);
            }
        });
    }
    document.querySelectorAll('[data-ai-reply]').forEach(element => {
        const text = element.textContent;
        element.textContent = '';
        renderReply(element, text);
    });

    function renderProductCards(container, products) {
        if (!Array.isArray(products) || products.length === 0) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'ai-product-recommendations';

        const header = document.createElement('div');
        header.className = 'ai-product-recommendations-header';

        const title = document.createElement('div');
        title.className = 'ai-product-recommendations-title';
        title.textContent = 'Cây GreenShop gợi ý cho bạn';

        const navigation = document.createElement('div');
        navigation.className = 'ai-product-navigation';

        const previousButton = document.createElement('button');
        previousButton.type = 'button';
        previousButton.className = 'ai-product-nav-btn ai-product-nav-prev';
        previousButton.setAttribute('aria-label', 'Xem sản phẩm trước');
        previousButton.textContent = '‹';

        const nextButton = document.createElement('button');
        nextButton.type = 'button';
        nextButton.className = 'ai-product-nav-btn ai-product-nav-next';
        nextButton.setAttribute('aria-label', 'Xem sản phẩm tiếp theo');
        nextButton.textContent = '›';

        navigation.append(previousButton, nextButton);
        header.append(title, navigation);
        wrapper.appendChild(header);

        const viewport = document.createElement('div');
        viewport.className = 'ai-product-viewport';

        const track = document.createElement('div');
        track.className = 'ai-product-track';

        products.forEach(product => {
            const card = document.createElement('article');
            card.className = 'ai-product-card';

            const imageLink = document.createElement('a');
            imageLink.href = product.detail_url || '#';
            imageLink.className = 'ai-product-image';

            if (product.image_url) {
                const img = document.createElement('img');
                img.src = product.image_url;
                img.alt = product.name || 'Cây GreenShop';
                img.loading = 'lazy';
                imageLink.appendChild(img);
            } else {
                const placeholder = document.createElement('span');
                placeholder.textContent = '🌱';
                imageLink.appendChild(placeholder);
            }

            const body = document.createElement('div');
            body.className = 'ai-product-body';

            const category = document.createElement('small');
            category.textContent = product.category || 'Cây cảnh';

            const name = document.createElement('a');
            name.href = product.detail_url || '#';
            name.className = 'ai-product-name';
            name.textContent = product.name || 'Cây cảnh';

            const price = document.createElement('strong');
            price.className = 'ai-product-price';
            price.textContent = product.price_formatted || '';

            const stock = document.createElement('div');
            const inStock = Number(product.stock || 0) > 0;
            stock.className = `ai-product-stock ${inStock ? 'in-stock' : 'out-stock'}`;
            stock.textContent = inStock ? 'Còn hàng' : 'Hết hàng';

            const actions = document.createElement('div');
            actions.className = 'ai-product-actions';

            const detail = document.createElement('a');
            detail.href = product.detail_url || '#';
            detail.className = 'ai-product-detail-btn';
            detail.textContent = 'Xem chi tiết';

            const add = document.createElement('button');
            add.type = 'button';
            add.className = 'ai-product-cart-btn';
            add.textContent = 'Thêm giỏ hàng';
            add.dataset.plantId = String(product.plant_id || '');
            add.dataset.cartUrl = product.add_cart_url || cartUrl || '';
            if (!inStock) {
                add.disabled = true;
                add.textContent = 'Hết hàng';
            }

            add.addEventListener('click', async () => {
                if (!add.dataset.plantId || !add.dataset.cartUrl) return;

                const originalText = add.textContent;
                add.disabled = true;
                add.textContent = 'Đang thêm...';

                try {
                    const token = form.querySelector('input[name="_token"]')?.value || '';
                    const formData = new FormData();
                    formData.append('plant_id', add.dataset.plantId);
                    formData.append('so_luong', '1');

                    const response = await fetch(add.dataset.cartUrl, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: formData,
                    });

                    const data = await response.json();
                    if (!response.ok || data.success === false) {
                        throw new Error(data.message || 'Không thể thêm vào giỏ hàng.');
                    }

                    add.textContent = 'Đã thêm ✓';
                    add.classList.add('is-added');

                    window.setTimeout(() => {
                        add.disabled = false;
                        add.textContent = originalText;
                        add.classList.remove('is-added');
                    }, 1800);
                } catch (error) {
                    add.disabled = false;
                    add.textContent = originalText;
                    window.alert(error.message || 'Không thể thêm vào giỏ hàng.');
                }
            });

            actions.append(detail, add);
            body.append(category, name, price, stock, actions);
            card.append(imageLink, body);
            track.appendChild(card);
        });

        viewport.appendChild(track);
        wrapper.appendChild(viewport);
        container.appendChild(wrapper);

        const getScrollStep = () => {
            const firstCard = track.querySelector('.ai-product-card');
            if (!firstCard) return Math.max(viewport.clientWidth * 0.8, 240);

            const trackStyle = window.getComputedStyle(track);
            const gap = Number.parseFloat(trackStyle.columnGap || trackStyle.gap || '0') || 0;
            return firstCard.getBoundingClientRect().width + gap;
        };

        const updateNavigation = () => {
            const maxScrollLeft = Math.max(0, viewport.scrollWidth - viewport.clientWidth);
            previousButton.disabled = viewport.scrollLeft <= 2;
            nextButton.disabled = viewport.scrollLeft >= maxScrollLeft - 2;

            navigation.hidden = maxScrollLeft <= 2;
        };

        previousButton.addEventListener('click', () => {
            viewport.scrollBy({
                left: -getScrollStep(),
                behavior: 'smooth',
            });
        });

        nextButton.addEventListener('click', () => {
            viewport.scrollBy({
                left: getScrollStep(),
                behavior: 'smooth',
            });
        });

        viewport.addEventListener('scroll', updateNavigation, { passive: true });
        window.addEventListener('resize', updateNavigation);

        requestAnimationFrame(updateNavigation);
    }

    // Khôi phục card đã lưu khi mở/reload một cuộc trò chuyện cũ.
    document.querySelectorAll('.ai-history-products').forEach(metadata => {
        try {
            const products = JSON.parse(metadata.textContent || '[]');
            const bubble = metadata.closest('.ai-bubble');
            if (bubble && Array.isArray(products) && products.length > 0) {
                renderProductCards(bubble, products);
            }
        } catch (error) {
            console.warn('Không thể khôi phục card sản phẩm AI.', error);
        } finally {
            metadata.remove();
        }
    });

    function addMessage(text, type, imageUrl = null, products = []) {
        const row = document.createElement('div');
        row.className = `chat-row ${type === 'user' ? 'chat-row-user' : 'chat-row-ai'}`;

        if (type === 'ai') {
            const avatar = document.createElement('div');
            avatar.className = 'message-ai-avatar';
            avatar.innerHTML = aiAvatarSvg;
            row.appendChild(avatar);
        }

        const bubble = document.createElement('div');
        bubble.className = `chat-bubble ${type === 'user' ? 'user-bubble' : 'ai-bubble'}`;
        if (imageUrl) {
            const img = document.createElement('img');
            img.src = imageUrl;
            img.className = 'chat-uploaded-image';
            img.alt = 'Ảnh cây';
            bubble.appendChild(img);
        }
        if (type === 'ai') {
            const content = document.createElement('div');
            content.className = 'ai-reply-content';
            renderReply(content, text);
            bubble.appendChild(content);
            renderProductCards(bubble, products);
        } else {
            const paragraph = document.createElement('p');
            paragraph.textContent = text;
            bubble.appendChild(paragraph);
        }

        const time = document.createElement('div');
        time.className = type === 'user' ? 'message-meta' : 'message-footer';
        time.textContent = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        bubble.appendChild(time);
        row.appendChild(bubble);
        messages.appendChild(row);
        scrollBottom();
    }

    // Guest: khôi phục hội thoại từ localStorage sau F5/đóng mở trình duyệt.
    if (!isAuthenticated && guestHistory.length > 0) {
        messages.replaceChildren();
        guestHistory.forEach(item => {
            if (!item || !['user', 'ai'].includes(item.type)) return;
            addMessage(item.text || '', item.type, null, item.products || []);
        });
    }

    if (attachButton) attachButton.addEventListener('click', () => imageInput.click());

    imageInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowed.includes(file.type)) { alert('Chỉ hỗ trợ JPG, PNG hoặc WEBP.'); this.value = ''; return; }
        if (file.size > 5 * 1024 * 1024) { alert('Ảnh không được vượt quá 5MB.'); this.value = ''; return; }
        if (!previewImage || !fileName || !previewBox) return;
        const reader = new FileReader();
        reader.onload = (event) => {
            previewImage.src = event.target.result;
            fileName.textContent = file.name;
            previewBox.hidden = false;
        };
        reader.readAsDataURL(file);
    });

    if (removeImage) removeImage.addEventListener('click', () => {
        imageInput.value = '';
        if (previewImage) previewImage.src = '';
        if (previewBox) previewBox.hidden = true;
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const message = input.value.trim();
        if (!message) return;
        const selectedFile = imageInput.files[0] || null;
        const localImageUrl = selectedFile ? URL.createObjectURL(selectedFile) : null;
        addMessage(message, 'user', localImageUrl);
        if (!isAuthenticated) {
            guestHistory.push({ type: 'user', text: message, products: [] });
            writeGuestHistory(guestHistory);
        }
        submit.disabled = true;

        const formData = new FormData();
        formData.append('message', message);
        if (conversationInput.value) formData.append('cuoc_tro_chuyen_id', conversationInput.value);
        if (selectedPlant?.value) formData.append('plant_id', selectedPlant.value);
        if (selectedFile) formData.append('anh', selectedFile);

        try {
            const token = form.querySelector('input[name="_token"]')?.value || '';
            const response = await fetch(chatUrl, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token },
                body: formData,
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Không thể gửi câu hỏi.');

            if (isAuthenticated && data.cuoc_tro_chuyen_id) {
                conversationInput.value = data.cuoc_tro_chuyen_id;
            }
            addMessage(data.reply, 'ai', null, data.recommendations || []);
            if (!isAuthenticated) {
                guestHistory.push({
                    type: 'ai',
                    text: data.reply || '',
                    products: data.recommendations || [],
                });
                writeGuestHistory(guestHistory);
            }
            input.value = '';
            imageInput.value = '';
            if (previewImage) previewImage.src = '';
            if (previewBox) previewBox.hidden = true;

            const currentConversation = new URLSearchParams(window.location.search).get('conversation');
            if (isAuthenticated && !currentConversation && data.cuoc_tro_chuyen_id) {
                const url = new URL(window.location.href);
                url.searchParams.set('conversation', data.cuoc_tro_chuyen_id);
                window.history.replaceState({}, '', url);
            }
        } catch (error) {
            console.error(error);
            addMessage(error.message || 'Đã xảy ra lỗi. Vui lòng thử lại.', 'ai');
        } finally {
            submit.disabled = false;
            input.focus();
            if (localImageUrl) URL.revokeObjectURL(localImageUrl);
        }
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            form.requestSubmit();
        }
    });

    scrollBottom();
});
