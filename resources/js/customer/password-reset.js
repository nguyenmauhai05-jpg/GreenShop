document.querySelectorAll('.toggle-password').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.target);
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    });

    const password = document.getElementById('password');
    const bars = document.getElementById('strength-bars');
    const text = document.getElementById('strength-text');

    password.addEventListener('input', function () {
        const value = password.value;
        let score = 0;
        if (value.length >= 8) score++;
        if (/[a-z]/.test(value) && /[A-Z]/.test(value)) score++;
        if (/\d/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;

        const labels = ['Chưa nhập', 'Yếu', 'Trung bình', 'Khá', 'Mạnh'];
        text.textContent = value ? labels[score] : labels[0];
        bars.classList.toggle('good', score >= 3);
        bars.querySelectorAll('span').forEach(function (bar, index) {
            bar.classList.toggle('active', index < score);
        });
    });
