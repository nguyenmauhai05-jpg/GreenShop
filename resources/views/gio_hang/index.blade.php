<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Giỏ hàng - GreenShop</title>

    @vite([
        'resources/css/customer/site-shell.css',
        'resources/css/gio-hang.css'
    ])
</head>

<body>

    {{-- HEADER CỦA GREENSHOP --}}
    @include('trang_chu.components.header')


    <main class="cart-page">

        <div class="cart-container">

            {{-- =============================
                 TIÊU ĐỀ
            ============================== --}}
            <div class="cart-heading">

                <div>
                    <h1>
                        Giỏ hàng của bạn

                        @if(($tongLoaiSanPham ?? 0) > 0)
                            <span>
                                ({{ $tongLoaiSanPham }} sản phẩm)
                            </span>
                        @endif
                    </h1>
                </div>

            </div>


            {{-- THÔNG BÁO --}}
            @include('gio_hang.components.toast')


            @if(isset($items) && $items->count() > 0)



                <div class="cart-layout">

                    {{-- DANH SÁCH --}}
                    @include('gio_hang.components.cart-list')


                    {{-- TỔNG TIỀN --}}
                    @include('gio_hang.components.summary')

                </div>

            @else

                {{-- GIỎ TRỐNG --}}
                @include('gio_hang.components.empty-state')

            @endif

        </div>

    </main>


    {{-- MODAL XÓA --}}
    @include('gio_hang.components.delete-modal')


    {{-- FOOTER CỦA GREENSHOP --}}
    @include('trang_chu.components.footer')


@vite('resources/js/customer/gio-hang.js')

</body>
</html>