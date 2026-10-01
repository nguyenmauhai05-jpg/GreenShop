@vite([
    'resources/css/app.css',
    'resources/css/customer/site-shell.css',
    'resources/css/chi-tiet-cay.css',
])

@include('trang_chu.components.header')
@if(session('success'))

    <div
        class="gs-toast gs-toast-success"
        id="gsToast"
    >
        <div class="gs-toast-icon">
            ✓
        </div>

        <div class="gs-toast-content">

            <strong>
                Thành công
            </strong>

            <span>
                {{ session('success') }}
            </span>

        </div>

        <button
            type="button"
            class="gs-toast-close"
            onclick="closeGsToast()"
        >
            ×
        </button>
    </div>

@endif


@if(session('error'))

    <div
        class="gs-toast gs-toast-error"
        id="gsToast"
    >
        <div class="gs-toast-icon">
            !
        </div>

        <div class="gs-toast-content">

            <strong>
                Không thể thực hiện
            </strong>

            <span>
                {{ session('error') }}
            </span>

        </div>

        <button
            type="button"
            class="gs-toast-close"
            onclick="closeGsToast()"
        >
            ×
        </button>
    </div>

@endif


<main class="product-detail-page">

    <div class="product-detail-container">

        {{-- BREADCRUMB --}}
        <nav class="breadcrumb">

            <a href="{{ route('trang-chu') }}">
                Trang chủ
            </a>

            <span>›</span>

            <a
                href="{{ route('cua-hang', [
                    'category' => $cay->category_id
                ]) }}"
            >
                {{ $cay->danhMuc->ten_danh_muc ?? 'Cửa hàng' }}
            </a>

            <span>›</span>

            <span class="breadcrumb-current">
                {{ $cay->ten_cay }}
            </span>

        </nav>


        {{-- ============================================
             ẢNH TRÁI + THÔNG TIN PHẢI
        ============================================= --}}
        <section class="product-top">

            <div class="product-gallery-area">

                @include(
                    'chi_tiet_cay.components.gallery',
                    ['cay' => $cay]
                )

            </div>


            <div class="product-info-area">

                @include(
                    'chi_tiet_cay.components.info',
                    ['cay' => $cay]
                )

            </div>

        </section>


        {{-- TAB --}}
        <section class="product-detail-section">

            @include(
                'chi_tiet_cay.components.detail-tabs',
                ['cay' => $cay]
            )

        </section>


        {{-- CÂY CÙNG LOẠI --}}
        <section class="product-related-section">

            @include(
                'chi_tiet_cay.components.related-products',
                ['cayLienQuan' => $cayLienQuan]
            )

        </section>

    </div>

</main>


@include('trang_chu.components.footer')
    @vite('resources/js/customer/chi-tiet-cay.js')

