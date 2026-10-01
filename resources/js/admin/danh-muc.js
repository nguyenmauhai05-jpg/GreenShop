const config = document.getElementById('categoryPageConfig');
const baseUrl = config?.dataset.baseUrl || '/admin/danh-muc';

function setModalVisible(modalId, visible) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.toggle('show', visible);
    document.body.classList.toggle('category-modal-open', visible);
}

window.openCreateCategoryModal = function openCreateCategoryModal() {
    setModalVisible('createCategoryModal', true);
    const input = document.getElementById('create_ten_danh_muc');
    if (input) window.setTimeout(() => input.focus(), 100);
};

window.closeCreateCategoryModal = function closeCreateCategoryModal() {
    setModalVisible('createCategoryModal', false);
};

window.openEditCategoryModal = function openEditCategoryModal(id, name, description, status) {
    const modal = document.getElementById('editCategoryModal');
    const form = document.getElementById('editCategoryForm');
    const editIdInput = document.getElementById('edit_category_id');
    const nameInput = document.getElementById('edit_ten_danh_muc');
    const descriptionInput = document.getElementById('edit_mo_ta');
    const statusShow = document.getElementById('edit_status_show');
    const statusHide = document.getElementById('edit_status_hide');

    if (!modal || !form || !editIdInput || !nameInput || !descriptionInput || !statusShow || !statusHide) return;

    form.action = `${baseUrl}/${id}`;
    editIdInput.value = id ?? '';
    nameInput.value = name ?? '';
    descriptionInput.value = description ?? '';
    statusShow.checked = status === 'Hiển thị';
    statusHide.checked = status === 'Ẩn';
    modal.classList.add('show');
    document.body.classList.add('category-modal-open');
    window.setTimeout(() => nameInput.focus(), 100);
};

window.closeEditCategoryModal = function closeEditCategoryModal() {
    setModalVisible('editCategoryModal', false);
};

window.openDeleteCategoryModal = function openDeleteCategoryModal(id, name, plantCount) {
    const modal = document.getElementById('deleteCategoryModal');
    const form = document.getElementById('deleteCategoryForm');
    const nameElement = document.getElementById('deleteCategoryName');
    const warning = document.getElementById('deleteCategoryWarning');
    const deleteButton = document.getElementById('deleteCategoryButton');

    if (!modal || !form || !nameElement) return;

    nameElement.textContent = `"${name}"`;
    form.action = `${baseUrl}/${id}`;

    if (warning && deleteButton) {
        if (Number(plantCount) > 0) {
            warning.classList.add('show');
            warning.innerHTML = `Danh mục này hiện đang chứa <strong>${plantCount} cây</strong>. Bạn cần chuyển các cây sang danh mục khác trước khi xóa.`;
            deleteButton.disabled = true;
        } else {
            warning.classList.remove('show');
            warning.innerHTML = '';
            deleteButton.disabled = false;
        }
    }

    modal.classList.add('show');
    document.body.classList.add('category-modal-open');
};

window.closeDeleteCategoryModal = function closeDeleteCategoryModal() {
    setModalVisible('deleteCategoryModal', false);
};

function validateCategoryTextForm(formId, nameInputId, descriptionInputId, nameErrorId, descriptionErrorId) {
    const form = document.getElementById(formId);
    const nameInput = document.getElementById(nameInputId);
    const descriptionInput = document.getElementById(descriptionInputId);
    const nameError = document.getElementById(nameErrorId);
    const descriptionError = document.getElementById(descriptionErrorId);
    if (!form || !nameInput || !descriptionInput || !nameError || !descriptionError) return;

    const setError = (input, errorElement, message) => {
        input.classList.add('is-invalid');
        errorElement.textContent = message;
        errorElement.classList.add('show');
    };
    const clearError = (input, errorElement) => {
        input.classList.remove('is-invalid');
        errorElement.textContent = '';
        errorElement.classList.remove('show');
    };
    const validateName = () => {
        const value = nameInput.value.trim();
        clearError(nameInput, nameError);
        if (value === '') { setError(nameInput, nameError, 'Tên danh mục không được để trống.'); return false; }
        if (value.length > 100) { setError(nameInput, nameError, 'Tên danh mục không được vượt quá 100 ký tự.'); return false; }
        return true;
    };
    const validateDescription = () => {
        const value = descriptionInput.value.trim();
        clearError(descriptionInput, descriptionError);
        if (value.length > 255) { setError(descriptionInput, descriptionError, 'Mô tả không được vượt quá 255 ký tự.'); return false; }
        return true;
    };

    nameInput.addEventListener('input', validateName);
    nameInput.addEventListener('blur', validateName);
    descriptionInput.addEventListener('input', validateDescription);
    descriptionInput.addEventListener('blur', validateDescription);
    form.addEventListener('submit', (event) => {
        const validName = validateName();
        const validDescription = validateDescription();
        if (!validName || !validDescription) {
            event.preventDefault();
            (validName ? descriptionInput : nameInput).focus();
        }
    });
}

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    window.closeCreateCategoryModal();
    window.closeEditCategoryModal();
    window.closeDeleteCategoryModal();
});

document.addEventListener('DOMContentLoaded', () => {
    validateCategoryTextForm('createCategoryForm', 'create_ten_danh_muc', 'create_mo_ta', 'createNameError', 'createDescriptionError');
    validateCategoryTextForm('editCategoryForm', 'edit_ten_danh_muc', 'edit_mo_ta', 'editNameError', 'editDescriptionError');

    if (config?.dataset.openCreate === '1') {
        window.openCreateCategoryModal();
    }
    if (config?.dataset.openEdit === '1') {
        window.openEditCategoryModal(
            Number(config.dataset.editId || 0),
            config.dataset.editName || '',
            config.dataset.editDescription || '',
            config.dataset.editStatus || 'Hiển thị'
        );
    }
});
