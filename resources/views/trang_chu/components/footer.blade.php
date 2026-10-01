@php
    $footerLogo = \App\Support\GreenShopSettings::get('logo', 'images/logo.png');
    $footerBrandName = \App\Support\GreenShopSettings::get('store_name', 'GreenShop');
    $footerContactEmail = \App\Support\GreenShopSettings::get('contact_email', 'contact@greenshop.com');
@endphp

<footer class="site-footer">

    <div class="footer-container">

        {{-- =================================================
             PHẦN TRÊN
        ================================================== --}}
        <div class="footer-top">

            {{-- THƯƠNG HIỆU --}}
            <div class="footer-brand">

                <a href="{{ route('trang-chu') }}" class="footer-logo" aria-label="GreenShop">
                    <span class="footer-logo-image">
                        <img
                            src="{{ asset($footerLogo) }}"
                            alt="{{ $footerBrandName }}"
                        >
                    </span>
                    <span class="footer-brand-name">{{ $footerBrandName }}</span>
                </a>

                <p class="footer-description">
                    Mang cây xanh khỏe mạnh và vẻ đẹp tự nhiên
                    đến từng không gian sống.
                </p>

                <div class="footer-socials">

                    <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        f
                    </a>

                    <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        ◎
                    </a>

                    <a href="https://www.tiktok.com/" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                        ♪
                    </a>

                </div>

            </div>


            {{-- CỬA HÀNG --}}
            <div class="footer-column">

                <h4>
                    CỬA HÀNG
                </h4>

                <a href="{{ route('cua-hang', ['category' => 1]) }}">
                    Cây trong nhà
                </a>

                <a href="{{ route('cua-hang', ['category' => 2]) }}">
                    Cây ngoài trời
                </a>

                <a href="{{ route('cua-hang', ['sort' => 'newest']) }}">
                    Sản phẩm mới
                </a>

                <a href="{{ route('cua-hang', ['sort' => 'best-selling']) }}">
                    Sản phẩm bán chạy
                </a>

            </div>


            {{-- DỊCH VỤ --}}
            <div class="footer-column">

                <h4>
                    DỊCH VỤ
                </h4>

                <a href="{{ route('cham-soc-cay') }}">
                    Chăm sóc cây
                </a>

                <a href="{{ route('blog') }}">
                    Hướng dẫn chăm sóc
                </a>

                <a href="mailto:{{ $footerContactEmail }}">
                    Liên hệ
                </a>

                <a href="{{ route('gioi-thieu') }}">
                    Câu hỏi thường gặp
                </a>

            </div>


            {{-- GREENSHOP --}}
            <div class="footer-column">

                <h4>
                    VỀ GREENSHOP
                </h4>

                <a href="{{ route('gioi-thieu') }}">
                    Giới thiệu
                </a>

                <a href="{{ route('blog') }}">
                    Bài viết
                </a>

                <a href="{{ route('cua-hang') }}">
                    Cửa hàng
                </a>

                <a href="{{ route('gioi-thieu') }}">
                    Chính sách
                </a>

            </div>

        </div>


        {{-- =================================================
             PHẦN DƯỚI
        ================================================== --}}
        <div class="footer-bottom">

            <p>
                © {{ date('Y') }} GreenShop. All rights reserved.
            </p>

            <div class="footer-bottom-links">

                <a href="{{ route('gioi-thieu') }}">
                    Chính sách bảo mật
                </a>

                <a href="{{ route('gioi-thieu') }}">
                    Điều khoản sử dụng
                </a>

            </div>

        </div>

    </div>

</footer>
