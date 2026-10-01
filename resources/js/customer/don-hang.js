document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('ordersSidebar');
    const backdrop = document.getElementById('ordersSidebarBackdrop');
    const toggle = document.getElementById('ordersSidebarToggle');

    const closeSidebar = () => {
        sidebar?.classList.remove('open');
        backdrop?.classList.remove('open');
    };

    toggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('open');
        backdrop?.classList.toggle('open');
    });
    backdrop?.addEventListener('click', closeSidebar);

    document.querySelectorAll('[data-close-alert]').forEach((button) => {
        button.addEventListener('click', () => button.closest('.orders-alert')?.remove());
    });


    // Thông báo tạo đơn/thanh toán chỉ hiển thị tạm thời.
    // Sau 3 giây tự mất để không nằm mãi trên trang Đơn hàng của tôi.
    document.querySelectorAll('.auto-dismiss-order-alert').forEach((alert) => {
        window.setTimeout(() => {
            alert.style.transition = 'opacity .25s ease, transform .25s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-6px)';

            window.setTimeout(() => alert.remove(), 260);
        }, 3000);
    });

    const modal = document.getElementById('cancelOrderModal');
    const openButton = document.querySelector('[data-open-cancel]');
    const closeButtons = document.querySelectorAll('[data-close-cancel]');
    const cancelForm = document.getElementById('cancelOrderForm');
    const confirmCancelButton = document.getElementById('confirmCancelButton');

    const openModal = () => {
        if (!modal) return;
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    };
    const closeModal = () => {
        if (!modal) return;
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };

    openButton?.addEventListener('click', openModal);
    closeButtons.forEach((button) => button.addEventListener('click', closeModal));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeModal();
            closeSidebar();
        }
    });

    cancelForm?.addEventListener('submit', () => {
        if (!confirmCancelButton) return;
        confirmCancelButton.disabled = true;
        confirmCancelButton.textContent = 'Đang hủy...';
    });
});
