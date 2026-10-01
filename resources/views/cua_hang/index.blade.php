<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cửa hàng - GreenShop</title>

    @vite([
        'resources/css/customer/site-shell.css',
        'resources/css/cua-hang.css',
        'resources/js/customer/cua-hang.js',
    ])
</head>

<body>

    {{-- HEADER --}}
    @include('trang_chu.components.header')

    {{-- THÔNG BÁO THÊM GIỎ HÀNG / LỖI --}}
    @if(session('success') || session('warning') || session('error'))
        <div class="shop-toast-container" id="shopToast">
            <div class="shop-toast {{ session('error') ? 'error' : (session('warning') ? 'warning' : 'success') }}" role="alert">
                <span class="shop-toast-icon">
                    {{ session('error') ? '×' : (session('warning') ? '!' : '✓') }}
                </span>
                <span class="shop-toast-message">
                    {{ session('error') ?? session('warning') ?? session('success') }}
                </span>
                <button type="button" class="shop-toast-close" aria-label="Đóng thông báo">×</button>
            </div>
        </div>
    @endif


    <main>

        {{-- BANNER --}}
        @include('cua_hang.components.banner')


        <section class="shop-section">

            <div class="shop-container">

                {{-- BỘ LỌC --}}
                @include('cua_hang.components.filter')


                {{-- NỘI DUNG CỬA HÀNG --}}
                <div class="shop-main">

                    @if(isset($loiHeThong))

                        @include('cua_hang.components.error-state')

                    @else

                        {{-- TOOLBAR --}}
                        @include('cua_hang.components.toolbar')


                        @if($cayCanhs->count() === 0)

                            {{-- KHÔNG CÓ SẢN PHẨM --}}
                            @include('cua_hang.components.empty-state')

                        @else

                            {{-- DANH SÁCH SẢN PHẨM --}}
                            @include('cua_hang.components.product-grid')

                            {{-- PHÂN TRANG --}}
                            @include('cua_hang.components.pagination')

                        @endif

                    @endif

                </div>

            </div>

        </section>

    </main>


    {{-- FOOTER --}}
    @include('trang_chu.components.footer')


</body>
</html>