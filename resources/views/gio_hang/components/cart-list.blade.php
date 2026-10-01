<section class="cart-products">

    {{-- HEADER TABLE --}}
    <div class="cart-table-header">

        <div>Sản phẩm</div>

        <div>Giá</div>

        <div>Số lượng</div>

        <div>Thành tiền</div>

        <div>Thao tác</div>

    </div>


    {{-- ITEMS --}}
    <div class="cart-items">

        @foreach($items as $item)

            @include(
                'gio_hang.components.cart-item',
                ['item' => $item]
            )

        @endforeach

    </div>


    {{-- FOOT --}}
    <div class="cart-list-footer">

        <a
            href="{{ route('cua-hang') }}"
            class="continue-shopping"
        >
            ← Tiếp tục mua sắm
        </a>

    </div>

</section>