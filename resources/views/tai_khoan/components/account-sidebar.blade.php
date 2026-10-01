    <aside class="account-sidebar shared-account-sidebar" id="{{ $sidebarId ?? 'accountSidebar' }}">
        <nav class="sidebar-nav">
            <a href="{{ route('trang-chu') }}" class="sidebar-link">
                <svg viewBox="0 0 24 24"><path d="m3 10 9-7 9 7v10H5V10"/><path d="M9 20v-6h6v6"/></svg>
                <span>Trang chủ</span>
            </a>
            <a href="{{ route('cua-hang') }}" class="sidebar-link">
                <svg viewBox="0 0 24 24"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg>
                <span>Cây cảnh</span>
            </a>
            <a href="{{ route('gio-hang') }}" class="sidebar-link">
                <svg viewBox="0 0 24 24"><path d="M3 4h2l2.2 11h11.3L21 7H6"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                <span>Giỏ hàng</span>
            </a>
        </nav>

        <div class="sidebar-section-title">TÀI KHOẢN</div>

        <nav class="sidebar-nav">
            <a href="{{ route('tai-khoan.ho-so') }}" class="sidebar-link {{ ($activeSidebar ?? '') === 'profile' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.6 3.6-7 8-7s8 2.4 8 7"/></svg>
                <span>Thông tin tài khoản</span>
            </a>
            <a href="{{ route('tai-khoan.so-dia-chi') }}" class="sidebar-link {{ ($activeSidebar ?? '') === 'addresses' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                <span>Sổ địa chỉ</span>
            </a>
            <a href="{{ route('don-hang.index') }}" class="sidebar-link {{ ($activeSidebar ?? '') === 'orders' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M6 3h12l2 4v14H4V7l2-4Z"/><path d="M4 7h16M9 11a3 3 0 0 0 6 0"/></svg>
                <span>Đơn hàng của tôi</span>
            </a>
            <a href="{{ route('danh-gia.index') }}" class="sidebar-link {{ ($activeSidebar ?? '') === 'reviews' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.2l-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/></svg>
                <span>Đánh giá sản phẩm</span>
            </a>
           
        </nav>

        <form action="{{ route('dang-xuat') }}" method="POST" class="sidebar-logout-form">
            @csrf
            <button class="sidebar-link logout" type="submit">
                <svg viewBox="0 0 24 24"><path d="M9 5H5v14h4M14 8l4 4-4 4M18 12H9"/></svg>
                <span>Đăng xuất</span>
            </button>
        </form>
    </aside>
