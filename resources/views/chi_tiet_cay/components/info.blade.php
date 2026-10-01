@php
    $soDanhGia = $cay->danhGias->count();

    $diemDanhGia = $soDanhGia > 0
        ? $cay->danhGias->avg('so_sao')
        : 0;
@endphp


<div class="product-info">

    <h1 class="product-name">
        {{ $cay->ten_cay }}
    </h1>


    <div class="product-rating">

        <div class="rating-stars">

            @for($i = 1; $i <= 5; $i++)

                <span class="{{ $i <= round($diemDanhGia) ? 'active' : '' }}">
                    ★
                </span>

            @endfor

        </div>


        @if($soDanhGia > 0)

            <strong class="rating-score">
                {{ number_format($diemDanhGia, 1) }}
            </strong>

            <span class="rating-count">
                ({{ $soDanhGia }} đánh giá)
            </span>

        @else

            <span class="rating-count">
                Chưa có đánh giá
            </span>

        @endif

    </div>


    <div class="product-price">
        {{ number_format($cay->gia, 0, ',', '.') }}₫
    </div>


    <div class="product-meta">

        <div class="meta-row">

            <strong>Danh mục:</strong>

            <span>
                {{ $cay->danhMuc->ten_danh_muc ?? 'Chưa phân loại' }}
            </span>

        </div>


        <div class="meta-row">

            <strong>Tình trạng:</strong>

            @if($cay->so_luong > 0)

                <span class="product-stock available">
                    Còn hàng
                </span>

            @else

                <span class="product-stock unavailable">
                    Hết hàng
                </span>

            @endif

        </div>

    </div>


    <p class="product-description">
        {{ $cay->mo_ta ?? 'Chưa có mô tả cho cây này.' }}
    </p>


    <form
    method="POST"
    action="{{ route('gio-hang.them') }}"
    class="product-cart-form js-add-cart-form"
    data-ajax-cart="true"
    data-login-url="{{ route('dang-nhap') }}"
>

    @csrf


    <input
        type="hidden"
        name="plant_id"
        value="{{ $cay->plant_id }}"
    >


    <div class="product-buy-row">

        {{-- SỐ LƯỢNG --}}
        <div class="quantity-control">

            <button
                type="button"
                onclick="changeQuantity(-1)"
            >
                −
            </button>


            <input
                id="productQuantity"
                type="number"
                name="so_luong"
                value="1"
                min="1"
                max="{{ max((int) $cay->so_luong, 1) }}"
                readonly
            >


            <button
                type="button"
                onclick="changeQuantity(1)"
            >
                +
            </button>

        </div>


        {{-- THÊM GIỎ --}}
        <button
            type="submit"
            class="add-cart-btn"
            {{ $cay->so_luong <= 0
                ? 'disabled'
                : '' }}
        >

            🛒

            {{ $cay->so_luong > 0
                ? 'Thêm vào giỏ hàng'
                : 'Hết hàng' }}

        </button>

    </div>

</form>


    <button
        type="button"
        class="buy-now-btn"
        {{ $cay->so_luong <= 0 ? 'disabled' : '' }}
    >
        Mua ngay
    </button>


    <div class="product-benefits">

        <div class="benefit-card">
            <span class="benefit-icon">
                <svg
                    viewBox="0 0 24 24"
                    width="18"
                    height="18"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M3 6h11v10H3z"/>
                    <path d="M14 10h4l3 3v3h-7z"/>
                    <circle cx="7" cy="18" r="2"/>
                    <circle cx="18" cy="18" r="2"/>
                </svg>
            </span>

            <div>
                <strong>Miễn phí giao hàng</strong>
                <small>Đơn từ 300k</small>
            </div>
        </div>


        <div class="benefit-card">
            <span class="benefit-icon">
                <svg
                    viewBox="0 0 24 24"
                    width="18"
                    height="18"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M20 7v5h-5"/>
                    <path d="M4 17v-5h5"/>
                    <path d="M6.1 9a7 7 0 0 1 11.8-2L20 12"/>
                    <path d="M17.9 15a7 7 0 0 1-11.8 2L4 12"/>
                </svg>
            </span>

            <div>
                <strong>Đổi trả trong 7 ngày</strong>
                <small>Nếu có lỗi</small>
            </div>
        </div>


        <div class="benefit-card">
            <span class="benefit-icon">
                <svg
                    viewBox="0 0 24 24"
                    width="18"
                    height="18"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M12 21V11"/>
                    <path d="M12 11C8 11 5 8 5 4c4 0 7 3 7 7z"/>
                    <path d="M12 14c4 0 7-3 7-7-4 0-7 3-7 7z"/>
                </svg>
            </span>

            <div>
                <strong>Tư vấn chăm sóc</strong>
                <small>24/7</small>
            </div>
        </div>


        <div class="benefit-card">
            <span class="benefit-icon">
                <svg
                    viewBox="0 0 24 24"
                    width="18"
                    height="18"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6z"/>
                    <path d="M9 12l2 2 4-4"/>
                </svg>
            </span>

            <div>
                <strong>Thanh toán an toàn</strong>
                <small>100% bảo mật</small>
            </div>
        </div>

    </div>

</div>



