document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('ratingInput');
    const picker = document.querySelector('[data-star-picker]');
    const label = document.getElementById('starLabel');
    const labels = ['', 'Rất không hài lòng', 'Không hài lòng', 'Bình thường', 'Hài lòng', 'Rất hài lòng'];

    if (picker && input) {
        const buttons = [...picker.querySelectorAll('[data-star]')];
        const paint = (value) => {
            buttons.forEach((button) => {
                const active = Number(button.dataset.star) <= value;
                button.textContent = active ? '★' : '☆';
                button.classList.toggle('active', active);
            });
            if (label) label.textContent = value ? labels[value] : 'Chọn điểm đánh giá';
        };
        buttons.forEach((button) => button.addEventListener('click', () => {
            input.value = button.dataset.star;
            paint(Number(button.dataset.star));
        }));
        paint(Number(input.value || 0));
    }

    const textarea = document.getElementById('noi_dung');
    const charCount = document.getElementById('reviewCharCount');
    if (textarea && charCount) {
        textarea.addEventListener('input', () => charCount.textContent = textarea.value.length);
    }

    const imageInput = document.getElementById('reviewImages');
    const preview = document.getElementById('imagePreviewList');
    const counter = document.getElementById('imageUploadCounter');

    if (imageInput && preview) {
        const MAX_IMAGES = 5;
        const MAX_SIZE = 5 * 1024 * 1024;
        const ALLOWED_TYPES = ['image/jpeg', 'image/png'];
        let selectedFiles = [];

        const fileKey = (file) => `${file.name}-${file.size}-${file.lastModified}`;

        const syncInputFiles = () => {
            const transfer = new DataTransfer();
            selectedFiles.forEach(file => transfer.items.add(file));
            imageInput.files = transfer.files;
        };

        const renderImages = () => {
            preview.innerHTML = '';
            if (counter) counter.textContent = `Đã chọn ${selectedFiles.length}/5 ảnh`;

            selectedFiles.forEach((file, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'review-image-preview-item';

                const image = document.createElement('img');
                image.alt = `Ảnh đánh giá ${index + 1}`;
                const objectUrl = URL.createObjectURL(file);
                image.src = objectUrl;
                image.onload = () => URL.revokeObjectURL(objectUrl);

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'review-image-remove';
                removeButton.setAttribute('aria-label', `Xóa ảnh ${index + 1}`);
                removeButton.textContent = '×';
                removeButton.addEventListener('click', () => {
                    selectedFiles.splice(index, 1);
                    syncInputFiles();
                    renderImages();
                });

                wrapper.append(image, removeButton);
                preview.appendChild(wrapper);
            });
        };

        imageInput.addEventListener('change', () => {
            const incomingFiles = [...imageInput.files];
            if (!incomingFiles.length) return;

            const invalidType = incomingFiles.find(file => !ALLOWED_TYPES.includes(file.type));
            if (invalidType) {
                alert('Chỉ chấp nhận ảnh JPG, JPEG hoặc PNG.');
                syncInputFiles();
                return;
            }

            const oversized = incomingFiles.find(file => file.size > MAX_SIZE);
            if (oversized) {
                alert('Mỗi ảnh không được vượt quá 5MB.');
                syncInputFiles();
                return;
            }

            const existingKeys = new Set(selectedFiles.map(fileKey));
            const uniqueIncoming = incomingFiles.filter(file => !existingKeys.has(fileKey(file)));

            if (selectedFiles.length + uniqueIncoming.length > MAX_IMAGES) {
                alert(`Bạn chỉ được tải lên tối đa ${MAX_IMAGES} ảnh. Hiện đã chọn ${selectedFiles.length} ảnh.`);
                syncInputFiles();
                return;
            }

            selectedFiles.push(...uniqueIncoming);
            syncInputFiles();
            renderImages();
        });

        renderImages();
    }

});
