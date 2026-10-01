<?php
    $headerLogo = \App\Support\GreenShopSettings::get(
        'logo',
        'images/logo.png'
    );

    $headerBrandName = \App\Support\GreenShopSettings::get(
        'store_name',
        'GreenShop'
    );
?>

<?php echo $__env->make('components.system-toast', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<header class="site-header">

    <div class="header-container">


        
        <a
            href="<?php echo e(route('trang-chu')); ?>"
            class="header-logo"
            aria-label="GreenShop"
        >

            <span class="header-logo-image">

                <img
                    src="<?php echo e(asset($headerLogo)); ?>"
                    alt="<?php echo e($headerBrandName); ?>"
                >

            </span>


            <span class="header-brand-name">
                <?php echo e($headerBrandName); ?>

            </span>

        </a>



        
        <nav class="main-nav">

            <a
                href="<?php echo e(route('trang-chu')); ?>"
                class="nav-link <?php echo e(request()->routeIs('trang-chu') ? 'active' : ''); ?>"
            >
                Trang chủ
            </a>


            <a
                href="<?php echo e(route('cua-hang')); ?>"
                class="nav-link <?php echo e(request()->routeIs('cua-hang') ? 'active' : ''); ?>"
            >
                Sản phẩm
            </a>


            <a
                href="<?php echo e(route('cham-soc-cay')); ?>"
                class="nav-link <?php echo e(request()->routeIs('cham-soc-cay*') ? 'active' : ''); ?>"
            >
                Chăm sóc cây và tư vấn 
            </a>


            <a
                href="<?php echo e(route('gioi-thieu')); ?>"
                class="nav-link <?php echo e(request()->routeIs('gioi-thieu') ? 'active' : ''); ?>"
            >
                Giới thiệu
            </a>

        </nav>



        
        <div class="header-actions">


            

            
            <div
                class="header-search <?php echo e(request()->filled('search') ? 'active' : ''); ?>"
            >


                
                <form
                    action="<?php echo e(route('cua-hang')); ?>"
                    method="GET"
                    class="header-search-form"
                    id="headerSearchForm"
                >

                    <input
                        type="text"
                        name="search"
                        id="headerSearchInput"
                        value="<?php echo e(request('search')); ?>"
                        placeholder="Tìm kiếm tên cây..."
                        maxlength="50"
                        autocomplete="off"
                    >

                </form>


                
                <button
                    type="button"
                    class="header-icon-btn header-search-toggle"
                    id="headerSearchToggle"
                    aria-label="Tìm kiếm"
                    aria-expanded="<?php echo e(request()->filled('search') ? 'true' : 'false'); ?>"
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



            
            <?php if(auth()->guard()->check()): ?>

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
                                <?php echo e(Auth::user()->ho_ten); ?>

                            </strong>

                            <span>
                                <?php echo e(Auth::user()->email); ?>

                            </span>

                        </div>


                        <a href="<?php echo e(route('tai-khoan.ho-so')); ?>">
                            Tài khoản của tôi
                        </a>


                        <a href="<?php echo e(route('don-hang.index')); ?>">
                            Đơn hàng của tôi
                        </a>


                        <form
                            action="<?php echo e(route('dang-xuat')); ?>"
                            method="POST"
                        >

                            <?php echo csrf_field(); ?>

                            <button type="submit">
                                Đăng xuất
                            </button>

                        </form>


                    </div>

                </div>


            <?php else: ?>


                <a
                    href="<?php echo e(route('dang-nhap')); ?>"
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


            <?php endif; ?>



            
            <?php

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

            ?>



            
            <a
                href="<?php echo e(route('gio-hang')); ?>"
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


                <?php if($cartCount > 0): ?>

                    <span class="cart-count">

                        <?php echo e($cartCount > 99
                                ? '99+'
                                : $cartCount); ?>


                    </span>

                <?php endif; ?>

            </a>


        </div>

    </div>

</header>



<?php echo app('Illuminate\Foundation\Vite')('resources/js/customer/header.js'); ?>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/trang_chu/components/header.blade.php ENDPATH**/ ?>