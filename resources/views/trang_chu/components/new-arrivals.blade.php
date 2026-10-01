{{-- =========================================================
    NEW ARRIVALS - SẢN PHẨM MỚI
========================================================= --}}

<section class="new-arrivals-section">

    <div class="new-arrivals-container">

        {{-- HEADER --}}
        <div class="new-arrivals-header">

            <div>
                <div class="new-arrivals-label">
                    <span></span>
                    SẢN PHẨM MỚI
                </div>

                <h2>Sản phẩm mới</h2>

                <p>
                    Những sản phẩm mới nhất vừa được thêm vào GreenShop.
                </p>
            </div>

            <a href="{{ route('cua-hang') }}" class="new-arrivals-view-all">
                Xem tất cả
                <span>→</span>
            </a>

        </div>


        {{-- PRODUCTS --}}
        @if($sanPhamMoi->isEmpty())

            <div class="new-arrivals-empty">
                Hiện chưa có sản phẩm mới.
            </div>

        @else

            <div class="new-arrivals-grid">

                @foreach($sanPhamMoi as $sanPham)

    <div
        class="new-product-card"
        onclick="window.location='{{ route('chi-tiet-cay', $sanPham->plant_id) }}'"
        style="cursor: pointer;"
    >

        {{-- ẢNH --}}
        <div class="new-product-image">

            <span class="new-badge">
                Mới
            </span>

            <img
                src="{{ asset($sanPham->anh_dai_dien) }}"
                alt="{{ $sanPham->ten_cay }}"
                loading="lazy"
                decoding="async"
            >

        </div>


        {{-- THÔNG TIN --}}
        <div class="new-product-content">

            <div class="new-product-category">
                CÂY CẢNH
            </div>

            <h3>
                {{ $sanPham->ten_cay }}
            </h3>

            <div class="new-product-rating">
                ★★★★★
                <span>(5.0)</span>
            </div>


            <div class="new-product-bottom">

                <div class="new-product-price">
                    {{ number_format($sanPham->gia, 0, ',', '.') }}₫
                </div>


                {{-- HÀNH ĐỘNG SẢN PHẨM --}}
                @if($sanPham->so_luong > 0)
                    <div class="new-product-actions" onclick="event.stopPropagation();">
                        <form
                            method="POST"
                            action="{{ route('gio-hang.them') }}"
                            class="js-add-cart-form"
                            data-ajax-cart="true"
                            data-login-url="{{ route('dang-nhap') }}"
                        >
                            @csrf
                            <input type="hidden" name="plant_id" value="{{ $sanPham->plant_id }}">
                            <input type="hidden" name="so_luong" value="1">
                            <button type="submit" class="new-product-cart new-cart-icon" title="Thêm vào giỏ hàng" aria-label="Thêm vào giỏ hàng">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.1 9.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H7M10 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/></svg>
                            </button>
                        </form>
                        @auth
<form method="POST" action="{{ route('gio-hang.mua-ngay') }}">
                            @csrf
                            <input type="hidden" name="plant_id" value="{{ $sanPham->plant_id }}">
                            <input type="hidden" name="so_luong" value="1">
                            <button type="submit" class="new-buy-now">Mua ngay</button>
                        </form>
@else
<a href="{{ route('dang-nhap') }}" class="new-buy-now" style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none">Mua ngay</a>
@endauth
                    </div>
                @else
                    <button type="button" class="new-product-cart" disabled>Hết hàng</button>
                @endif

            </div>

        </div>

    </div>

@endforeach

            </div>

        @endif

    </div>

</section>
