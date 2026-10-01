{{-- =====================================================
    CHÀO MỪNG
====================================================== --}}

<div class="dashboard-topbar">

    <div class="dashboard-welcome">

        <h2>
            Chào mừng trở lại,
            {{ Auth::user()->ho_ten ?? 'Quản trị viên' }}! 👋
        </h2>

        <p>
            Tổng quan tình hình hoạt động của GreenShop.
        </p>

    </div>

</div>
