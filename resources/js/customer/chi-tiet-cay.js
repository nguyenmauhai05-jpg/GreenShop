function closeGsToast() {
        const toast = document.getElementById('gsToast');

        if (!toast) return;

        toast.style.animation = 'gsToastOut 0.3s ease forwards';

        setTimeout(() => {
            toast.remove();
        }, 300);
    }

    document.addEventListener('DOMContentLoaded', function () {

        const toast = document.getElementById('gsToast');

        if (!toast) return;

        // Tự động biến mất sau 3 giây
        setTimeout(() => {
            closeGsToast();
        }, 3000);
    });

function changeQuantity(amount) {

    const input =
        document.getElementById('productQuantity');

    if (!input) return;

    const min =
        parseInt(input.min || 1);

    const max =
        parseInt(input.max || 1);

    let value =
        parseInt(input.value || 1);

    value += amount;

    if (value < min) {
        value = min;
    }

    if (value > max) {
        value = max;
    }

    input.value = value;
}

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const buttons =
            document.querySelectorAll('.tab-button');

        const panels =
            document.querySelectorAll('.tab-panel');


        buttons.forEach(button => {

            button.addEventListener(
                'click',
                function () {

                    buttons.forEach(item =>
                        item.classList.remove('active')
                    );

                    panels.forEach(item =>
                        item.classList.remove('active')
                    );


                    this.classList.add('active');


                    const panel =
                        document.getElementById(
                            'tab-' + this.dataset.tab
                        );

                    if (panel) {
                        panel.classList.add('active');
                    }

                }
            );

        });

    }
);
