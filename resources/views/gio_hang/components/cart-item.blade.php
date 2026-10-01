<article class="cart-item">

    {{-- ===============================================
         SẢN PHẨM
    ================================================ --}}
    <div class="cart-product">

        <div class="cart-product-image">

            @if(!empty($item->anh_dai_dien))

                <img
                    src="{{ asset($item->anh_dai_dien) }}"
                    alt="{{ $item->ten_cay }}"
                loading="lazy" decoding="async"
>

            @else

                <div class="cart-no-image">
                    🌱
                </div>

            @endif

        </div>


        <div class="cart-product-info">

            <h3>
                {{ $item->ten_cay }}
            </h3>


            @if(!empty($item->ten_danh_muc))

                <p>
                    {{ $item->ten_danh_muc }}
                </p>

            @endif


            @if(!empty($item->chieu_cao))

                <p>
                    Kích thước:
                    {{ $item->chieu_cao }}
                </p>

            @endif


            @if(!$item->san_pham_hop_le)

                <span class="cart-status unavailable">
                    Sản phẩm này hiện không khả dụng
                </span>

            @elseif($item->so_luong_ton <= 0)

                <span class="cart-status unavailable">
                    Hết hàng
                </span>

            @elseif($item->so_luong_ton < $item->so_luong)

                <span class="cart-status warning">
                    Chỉ còn {{ $item->so_luong_ton }} sản phẩm
                </span>

            @else

                <span class="cart-status available">
                    ✓ Còn hàng
                </span>

            @endif

        </div>

    </div>


    {{-- ===============================================
         GIÁ
    ================================================ --}}
    <div class="cart-unit-price">

        {{
            number_format(
                $item->gia_hien_tai,
                0,
                ',',
                '.'
            )
        }}₫

    </div>


    {{-- ===============================================
         SỐ LƯỢNG
    ================================================ --}}
    <div class="cart-quantity">

        <form
            method="POST"
            action="{{ $item->update_url }}"
            class="quantity-form"
        >

            @csrf
            @method('PATCH')


            <button
                type="button"
                class="quantity-button quantity-action-button"
                data-action="decrease"
                {{ !$item->san_pham_hop_le ? 'disabled' : '' }}
            >
                −
            </button>


            <input
                type="number"
                name="so_luong"
                value="{{ $item->so_luong }}"
                min="1"
                max="{{ $item->so_luong_ton }}"
                class="quantity-input"
                data-original-value="{{ $item->so_luong }}"
                {{ !$item->san_pham_hop_le ? 'disabled' : '' }}
            >


            <button
                type="button"
                class="quantity-button quantity-action-button"
                data-action="increase"
                {{ (
                    !$item->san_pham_hop_le
                    ||
                    $item->so_luong >= $item->so_luong_ton
                ) ? 'disabled' : '' }}
            >
                +
            </button>

        </form>


        @if($item->san_pham_hop_le && $item->so_luong_ton > 0)

            <small>
                Tối đa {{ $item->so_luong_ton }}
            </small>

        @endif

    </div>


    {{-- ===============================================
         THÀNH TIỀN
    ================================================ --}}
    <div class="cart-line-total">

        {{
            number_format(
                $item->thanh_tien,
                0,
                ',',
                '.'
            )
        }}₫

    </div>


    {{-- ===============================================
         XÓA
    ================================================ --}}
    <div class="cart-actions">

        <button
            type="button"
            class="cart-delete-button"
            data-action="{{ $item->delete_url }}"
            title="Xóa sản phẩm"
        >
            🗑
        </button>

    </div>

</article>