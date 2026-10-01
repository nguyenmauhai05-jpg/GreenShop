@php
    $adminSystemSettings = \App\Support\GreenShopSettings::all();
    $adminStoreName = $adminSystemSettings['store_name'] ?? 'GreenShop';
    $adminStoreLogo = $adminSystemSettings['logo'] ?? 'images/trang-chu/logo/logo.png';
@endphp

<aside class="admin-sidebar">

    {{-- LOGO --}}
    <div class="admin-sidebar-logo">

        <a
            href="{{ route('admin.dashboard') }}"
            class="admin-sidebar-logo-image"
        >
            <img
                src="{{ asset($adminStoreLogo) }}"
                alt="{{ $adminStoreName }}"
            >
        </a>

        <div>
            <strong>{{ $adminStoreName }}</strong>
            <span>Admin Panel</span>
        </div>

    </div>


    {{-- MENU --}}
    <nav class="admin-sidebar-menu">

        <a
            href="{{ route('admin.dashboard') }}"
            class="admin-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
        >
            <span class="admin-menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 11.5 12 4l9 7.5"/>
                    <path d="M5.5 10.5V20h13v-9.5"/>
                    <path d="M9.5 20v-6h5v6"/>
                </svg>
            </span>

            <span>Dashboard</span>
        </a>


        <a
            href="{{ route('admin.cay-canh.index') }}"
            class="admin-menu-item
            {{ request()->routeIs('admin.cay-canh.*') ? 'active' : '' }}"
        >
            <span class="admin-menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 21V11"/>
                    <path d="M12 11C8 11 5 8 5 4c4 0 7 3 7 7z"/>
                    <path d="M12 14c4 0 7-3 7-7-4 0-7 3-7 7z"/>
                    <path d="M8 21h8"/>
                </svg>
            </span>

            <span>Quản lý cây</span>
        </a>


        <a
            href="{{ route('admin.danh-muc.index') }}"
            class="admin-menu-item {{ request()->routeIs('admin.danh-muc.*') ? 'active' : '' }}"
        >
            <span class="admin-menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="4" y="4" width="6" height="6" rx="1"/>
                    <rect x="14" y="4" width="6" height="6" rx="1"/>
                    <rect x="4" y="14" width="6" height="6" rx="1"/>
                    <rect x="14" y="14" width="6" height="6" rx="1"/>
                </svg>
            </span>

            <span>Quản lý danh mục</span>
        </a>


        <a
            href="{{ route('admin.voucher.index') }}"
            class="admin-menu-item {{ request()->routeIs('admin.voucher.*') ? 'active' : '' }}"
        >
            <span class="admin-menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 7h16v10H4z"/>
                    <path d="M9 7a3 3 0 0 0 6 0"/>
                    <path d="M9 17a3 3 0 0 1 6 0"/>
                    <path d="M9 11h.01M15 13h.01"/>
                </svg>
            </span>

            <span>Quản lý voucher</span>
        </a>


        <a
            href="{{ route('admin.don-hang.index') }}"
            class="admin-menu-item {{ request()->routeIs('admin.don-hang.*') ? 'active' : '' }}"
        >
            <span class="admin-menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M7 4h10"/>
                    <path d="M9 3h6v3H9z"/>
                    <rect x="5" y="5" width="14" height="16" rx="2"/>
                    <path d="M8 10h8"/>
                    <path d="M8 14h8"/>
                    <path d="M8 18h5"/>
                </svg>
            </span>

            <span>Quản lý đơn hàng</span>
        </a>


        <a
            href="{{ route('admin.danh-gia.index') }}"
            class="admin-menu-item {{ request()->routeIs('admin.danh-gia.*') ? 'active' : '' }}"
        >
            <span class="admin-menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.8 1-6.1-4.4-4.3 6.1-.9z"/>
                </svg>
            </span>

            <span>Quản lý đánh giá</span>
        </a>


        <a
            href="{{ route('admin.bao-cao.index') }}"
            class="admin-menu-item {{ request()->routeIs('admin.bao-cao.*') ? 'active' : '' }}"
        >
            <span class="admin-menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 20V10"/>
                    <path d="M10 20V4"/>
                    <path d="M16 20v-7"/>
                    <path d="M22 20H2"/>
                </svg>
            </span>

            <span>Báo cáo thống kê</span>
        </a>


        <a
            href="{{ route('admin.cai-dat-he-thong.index') }}"
            class="admin-menu-item {{ request()->routeIs('admin.cai-dat-he-thong.*') ? 'active' : '' }}"
        >
            <span class="admin-menu-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06A1.8 1.8 0 0 0 15.66 19a1.8 1.8 0 0 0-1.08 1.64V21h-3v-.36A1.8 1.8 0 0 0 10.5 19a1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 6.76 15.3 1.8 1.8 0 0 0 5.12 14.2H4.8v-3h.32A1.8 1.8 0 0 0 6.76 10.1a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 10.5 6.36 1.8 1.8 0 0 0 11.58 4.7V4.4h3v.3A1.8 1.8 0 0 0 15.66 6.36a1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 10.1a1.8 1.8 0 0 0 1.64 1.08h.36v3h-.36A1.8 1.8 0 0 0 19.4 15z"/>
                </svg>
            </span>

            <span>Cài đặt hệ thống</span>
        </a>

    </nav>


    {{-- ADMIN INFO --}}
    <div class="admin-sidebar-bottom">

        <div class="admin-sidebar-user">

            <div class="admin-avatar">
                {{ strtoupper(substr(Auth::user()->ho_ten ?? 'A', 0, 1)) }}
            </div>

            <div>
                <strong>{{ Auth::user()->ho_ten ?? 'Quản trị viên' }}</strong>
                <span>Administrator</span>
            </div>

        </div>


        <form
            method="POST"
            action="{{ route('dang-xuat') }}"
        >
            @csrf

            <button
                type="submit"
                class="admin-logout"
            >
                <span class="admin-logout-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M10 17l5-5-5-5"/>
                        <path d="M15 12H3"/>
                        <path d="M14 4h5a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-5"/>
                    </svg>
                </span>

                Đăng xuất
            </button>

        </form>

    </div>

</aside>
