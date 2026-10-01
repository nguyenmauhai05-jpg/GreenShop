@php
    $headerLogo = \App\Support\GreenShopSettings::get(
        'logo',
        'images/logo.png'
    );

    $headerBrandName = \App\Support\GreenShopSettings::get(
        'store_name',
        'GreenShop'
    );
@endphp

@include('components.system-toast')

<header class="site-header">

    <div class="header-container">


        {{-- =========================================================
            LOGO
        ========================================================== --}}
        <a
            href="{{ route('trang-chu') }}"
            class="header-logo"
            aria-label="GreenShop"
        >

            <span class="header-logo-image">

                <img
                    src="{{ asset($headerLogo) }}"
                    alt="{{ $headerBrandName }}"
                >

            </span>


            <span class="header-brand-name">
                {{ $headerBrandName }}
            </span>

        </a>



        {{-- =========================================================
            MENU
        ========================================================== --}}
        <nav class="main-nav">

            <a
                href="{{ route('trang-chu') }}"
                class="nav-link {{ request()->routeIs('trang-chu') ? 'active' : '' }}"
            >
                Trang chủ
            </a>


            <a
                href="{{ route('cua-hang') }}"
                class="nav-link {{ request()->routeIs('cua-hang') ? 'active' : '' }}"
            >
                Sản phẩm
            </a>


            <a
                href="{{ route('cham-soc-cay') }}"
                class="nav-link {{ request()->routeIs('cham-soc-cay*') ? 'active' : '' }}"
            >
                Chăm sóc cây và tư vấn 
            </a>


            <a
                href="{{ route('gioi-thieu') }}"
                class="nav-link {{ request()->routeIs('gioi-thieu') ? 'active' : '' }}"
            >
                Giới thiệu
            </a>

        </nav>



        {{-- =========================================================
            ACTIONS
        ========================================================== --}}
        <div class="header-actions">


            {{-- =====================================================
                SEARCH
            ====================================================== --}}

            {{-- 
                Nếu URL đang có ?search=...
                thì tự động thêm class active.

                Ví dụ:
                /cua-hang?search=kim+tien

                Sau khi reload, ô search vẫn mở.
            --}}
            <div
                class="header-search {{ request()->filled('search') ? 'active' : '' }}"
            >


                {{-- FORM SEARCH --}}
                <form
                    action="{{ route('cua-hang') }}"
                    method="GET"
                    class="header-search-form"
                    id="headerSearchForm"
                >

                    <input
                        type="text"
                        name="search"
                        id="headerSearchInput"
                        value="{{ request('search') }}"
                        placeholder="Tìm kiếm tên cây..."
                        maxlength="50"
                        autocomplete="off"
                    >

                </form>


                {{-- 
                    CHỈ CÓ 1 NÚT KÍNH LÚP

                    - Search đóng:
                      click 1 lần -> mở.

                    - Search mở + có từ khóa:
                      click 1 lần -> tìm kiếm.

                    - Double click:
                      đóng search.
                --}}
                <button
                    type="button"
                    class="header-icon-btn header-search-toggle"
                    id="headerSearchToggle"
                    aria-label="Tìm kiếm"
                    aria-expanded="{{ request()->filled('search') ? 'true' : 'false' }}"
                >

                    <svg viewBox="0 0 24 24">

                        <circle
                            cx="11"
                            cy="11"
                            r="7"
                        ></circle>

                        <path
                            d="M20 20L16.5 16.5"
                        ></path>

                    </svg>

                </button>

            </div>



            {{-- =====================================================
                USER
            ====================================================== --}}
            @auth

                <div class="user-menu">

                    <button
                        type="button"
                        class="header-icon-btn user-button"
                        aria-label="Tài khoản"
                    >

                        <svg viewBox="0 0 24 24">

                            <circle
                                cx="12"
                                cy="8"
                                r="4"
                            ></circle>

                            <path
                                d="
                                    M4 21
                                    C4 16.5 7.5 14 12 14
                                    C16.5 14 20 16.5 20 21
                                "
                            ></path>

                        </svg>

                    </button>


                    <div class="user-dropdown">


                        <div class="user-dropdown-info">

                            <strong>
                                {{ Auth::user()->ho_ten }}
                            </strong>

                            <span>
                                {{ Auth::user()->email }}
                            </span>

                        </div>


                        <a href="{{ route('tai-khoan.ho-so') }}">
                            Tài khoản của tôi
                        </a>


                        <a href="{{ route('don-hang.index') }}">
                            Đơn hàng của tôi
                        </a>


                        <form
                            action="{{ route('dang-xuat') }}"
                            method="POST"
                        >

                            @csrf

                            <button type="submit">
                                Đăng xuất
                            </button>

                        </form>


                    </div>

                </div>


            @else


                <a
                    href="{{ route('dang-nhap') }}"
                    class="header-icon-btn"
                    aria-label="Đăng nhập"
                >

                    <svg viewBox="0 0 24 24">

                        <circle
                            cx="12"
                            cy="8"
                            r="4"
                        ></circle>

                        <path
                            d="
                                M4 21
                                C4 16.5 7.5 14 12 14
                                C16.5 14 20 16.5 20 21
                            "
                        ></path>

                    </svg>

                </a>


            @endauth



            {{-- =====================================================
                CART COUNT
            ====================================================== --}}
            @php

                $cartCount = 0;

                if (auth()->check()) {

                    $cartCount = (int)
                        \Illuminate\Support\Facades\DB::table(
                            'gio_hang as gh'
                        )

                        ->join(
                            'chi_tiet_gio_hang as ct',
                            'ct.cart_id',
                            '=',
                            'gh.cart_id'
                        )

                        ->where(
                            'gh.user_id',
                            auth()->id()
                        )

                        ->sum(
                            'ct.so_luong'
                        );
                }

            @endphp



            {{-- =====================================================
                CART
            ====================================================== --}}
            <a
                href="{{ route('gio-hang') }}"
                class="header-icon-btn cart-button"
                aria-label="Giỏ hàng"
            >

                <svg viewBox="0 0 24 24">

                    <path
                        d="M3 4H5L7 15H18L20 7H6"
                    ></path>

                    <circle
                        cx="9"
                        cy="20"
                        r="1"
                    ></circle>

                    <circle
                        cx="17"
                        cy="20"
                        r="1"
                    ></circle>

                </svg>


                @if($cartCount > 0)

                    <span class="cart-count">

                        {{
                            $cartCount > 99
                                ? '99+'
                                : $cartCount
                        }}

                    </span>

                @endif

            </a>


        </div>

    </div>

</header>



@vite('resources/js/customer/header.js')
