document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
       TOAST TỰ MẤT SAU 3 GIÂY
    ===================================================== */

    document.querySelectorAll('.cart-toast')
        .forEach(function (toast) {

            window.setTimeout(function () {

                toast.classList.add('cart-toast-hide');

                window.setTimeout(function () {
                    toast.remove();
                }, 300);

            }, 3000);

        });


    /* =====================================================
       CẬP NHẬT SỐ LƯỢNG CHÍNH XÁC
    ===================================================== */

    document.querySelectorAll('.quantity-form')
        .forEach(function (form) {

            const input = form.querySelector('.quantity-input');
            const actionButtons = form.querySelectorAll('.quantity-action-button');

            if (!input) {
                return;
            }

            /*
             * Submit số lượng user nhập trực tiếp.
             * Không gửi action increase/decrease.
             */
            function submitDirectQuantity() {

                if (input.disabled) {
                    return;
                }

                let value = parseInt(input.value, 10);
                const min = parseInt(input.min || '1', 10);
                const max = parseInt(input.max || '999999', 10);

                if (Number.isNaN(value)) {
                    value = parseInt(input.dataset.originalValue || '1', 10);
                }

                if (value < min) {
                    value = min;
                }

                if (value > max) {
                    value = max;
                }

                input.value = value;

                const oldHiddenAction = form.querySelector(
                    'input[name="action"][data-cart-action]'
                );

                if (oldHiddenAction) {
                    oldHiddenAction.remove();
                }

                form.submit();
            }


            /*
             * Nút + / - gửi đúng action.
             */
            actionButtons.forEach(function (button) {

                button.addEventListener('click', function () {

                    if (button.disabled) {
                        return;
                    }

                    const hiddenAction = document.createElement('input');

                    hiddenAction.type = 'hidden';
                    hiddenAction.name = 'action';
                    hiddenAction.value = button.dataset.action;
                    hiddenAction.dataset.cartAction = '1';

                    form.appendChild(hiddenAction);

                    form.submit();

                });

            });


            /*
             * Enter trong ô số lượng phải cập nhật đúng số user nhập,
             * không được tự coi Enter là nút "−".
             */
            input.addEventListener('keydown', function (event) {

                if (event.key === 'Enter') {

                    event.preventDefault();

                    submitDirectQuantity();
                }

            });


            /*
             * Khi user đổi số rồi click ra ngoài -> lưu đúng số đó.
             */
            input.addEventListener('change', function () {

                const current = parseInt(input.value, 10);
                const original = parseInt(input.dataset.originalValue || '0', 10);

                if (!Number.isNaN(current) && current !== original) {
                    submitDirectQuantity();
                }

            });

        });


    /* =====================================================
       MODAL XÓA
    ===================================================== */

    const modal = document.getElementById('deleteCartModal');
    const deleteForm = document.getElementById('deleteCartForm');

    document.querySelectorAll('.cart-delete-button')
        .forEach(button => {

            button.addEventListener('click', function () {

                const action = this.dataset.action;

                if (modal && deleteForm && action) {

                    deleteForm.action = action;

                    modal.classList.add('show');

                    document.body.classList.add('modal-open');
                }

            });

        });


    document.querySelectorAll('[data-close-delete-modal]')
        .forEach(button => {

            button.addEventListener('click', function () {

                if (modal) {
                    modal.classList.remove('show');
                }

                document.body.classList.remove('modal-open');

            });

        });


    /* click nền để đóng */

    if (modal) {

        modal.addEventListener('click', function (event) {

            if (event.target === modal) {

                modal.classList.remove('show');

                document.body.classList.remove('modal-open');
            }

        });

    }

});
