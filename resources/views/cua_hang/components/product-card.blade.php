<article class="shop-product-card">

    {{-- IMAGE --}}
    <a
        href="{{ route('chi-tiet-cay', $cay->plant_id) }}"
        class="shop-product-image"
    >
        @if($cay->anh_dai_dien)
            <img
                src="{{ asset($cay->anh_dai_dien) }}"
                alt="{{ $cay->ten_cay }}"
                loading="lazy"
                decoding="async"
            >
        @else
            <div class="no-product-image">
                🌱
            </div>
        @endif
    </a>

    <div class="shop-product-info">

        {{-- DANH MỤC --}}
        <p class="shop-product-category">
            {{ $cay->ten_danh_muc ?? 'Cây cảnh' }}
        </p>

        {{-- TÊN --}}
        <a
            href="{{ route('chi-tiet-cay', $cay->plant_id) }}"
            class="shop-product-name"
        >
            {{ $cay->ten_cay }}
        </a>

        {{-- GIÁ --}}
        <strong class="shop-product-price">
            {{ number_format($cay->gia, 0, ',', '.') }}₫
        </strong>

        {{-- TÌNH TRẠNG --}}
        <div
            class="stock-status {{ $cay->so_luong > 0 ? 'in-stock' : 'out-stock' }}"
        >
            {{ $cay->so_luong > 0 ? 'Còn hàng' : 'Hết hàng' }}
        </div>

        {{-- HÀNH ĐỘNG SẢN PHẨM --}}
        @if($cay->so_luong > 0)
            <div class="shop-product-actions">
                <form
                    action="{{ route('gio-hang.them') }}"
                    method="POST"
                    class="shop-add-cart-form js-add-cart-form"
                    data-ajax-cart="true"
                    data-login-url="{{ route('dang-nhap') }}"
                >
                    @csrf
                    <input type="hidden" name="plant_id" value="{{ $cay->plant_id }}">
                    <input type="hidden" name="so_luong" value="1">
                    <button type="submit" class="shop-add-cart shop-cart-icon" title="Thêm vào giỏ hàng" aria-label="Thêm vào giỏ hàng">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.1 9.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H7M10 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/></svg>
                    </button>
                </form>

                <form action="{{ route('gio-hang.mua-ngay') }}" method="POST" class="shop-buy-now-form">
                    @csrf
                    <input type="hidden" name="plant_id" value="{{ $cay->plant_id }}">
                    <input type="hidden" name="so_luong" value="1">
                    <button type="submit" class="shop-buy-now">Mua ngay</button>
                </form>
            </div>
        @else
            <button type="button" class="shop-add-cart disabled" disabled>Hết hàng</button>
        @endif

    </div>

</article>
