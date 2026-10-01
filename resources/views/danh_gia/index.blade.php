<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh giá sản phẩm - GreenShop</title>
    @vite(['resources/css/customer/site-shell.css'])
    @vite('resources/css/customer/shared-account-sidebar.css')
    @vite('resources/css/customer/danh-gia.css')


</head>
<body>
@include('trang_chu.components.header')

<div class="review-layout">
    <div class="shared-sidebar-backdrop" id="sidebarBackdrop"></div>
    @include('tai_khoan.components.account-sidebar', ['activeSidebar' => 'reviews', 'sidebarId' => 'accountSidebar'])

    <main class="review-main">
        <div class="review-heading">
            <button type="button" class="shared-sidebar-toggle" id="mobileMenuButton" aria-label="Mở menu tài khoản">☰</button>
            <div>
                <p class="review-breadcrumb">Tài khoản / Đánh giá sản phẩm</p>
                <h1>Đánh giá sản phẩm</h1>
                <p>Xem và quản lý các đánh giá sản phẩm của bạn</p>
            </div>
        </div>

        @if(session('success'))
            <div class="review-alert success">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="review-alert warning">{{ session('warning') }}</div>
        @endif
        @if(session('error'))
            <div class="review-alert error">{{ session('error') }}</div>
        @endif

        <div class="review-tabs">
            <a href="{{ route('danh-gia.index', ['tab' => 'pending']) }}" class="review-tab {{ $tab === 'pending' ? 'active' : '' }}">
                Chờ đánh giá <span>{{ $eligibleCount }}</span>
            </a>
            <a href="{{ route('danh-gia.index', ['tab' => 'reviewed']) }}" class="review-tab {{ $tab === 'reviewed' ? 'active' : '' }}">
                Đã đánh giá <span>{{ $reviewedCount }}</span>
            </a>
        </div>

        @if($tab === 'pending')
            <div class="review-info-box">Hãy chia sẻ cảm nhận của bạn để giúp những khách hàng khác lựa chọn sản phẩm phù hợp.</div>

            @forelse($eligibleDetails->groupBy('order_id') as $orderId => $details)
                @php $order = $details->first()->donHang; @endphp
                <section class="review-order-card">
                    <div class="review-order-head">
                        <div>
                            <strong>Đơn hàng {{ $order?->orderCode() }}</strong>
                            <span>Đặt ngày {{ optional($order?->ngay_dat)->format('d/m/Y') }}</span>
                        </div>
                        <span class="delivered-badge">✓ Đã giao hàng</span>
                    </div>

                    @foreach($details as $detail)
                        <div class="review-product-row">
                            <div class="review-product-info">
                                <div class="review-product-image">
                                    @if($detail->cayCanh?->anh_dai_dien)
                                        <img src="{{ asset($detail->cayCanh->anh_dai_dien) }}" alt="{{ $detail->cayCanh->ten_cay }}">
                                    @else
                                        <span>🌿</span>
                                    @endif
                                </div>
                                <div>
                                    <strong>{{ $detail->cayCanh?->ten_cay ?? 'Sản phẩm' }}</strong>
                                    <p>Phân loại: {{ $detail->cayCanh?->danhMuc?->ten_danh_muc ?? 'Cây cảnh' }}</p>
                                    <b>{{ number_format((float) $detail->don_gia, 0, ',', '.') }}đ</b>
                                </div>
                            </div>
                            <div class="review-product-action">
                                <div class="mini-stars selectable-stars" aria-label="Chọn số sao đánh giá">
                                    @for($star = 1; $star <= 5; $star++)
                                        <a href="{{ route('danh-gia.create', ['orderDetail' => $detail->order_detail_id, 'star' => $star]) }}"
                                           title="{{ $star }} sao"
                                           aria-label="Chọn {{ $star }} sao">☆</a>
                                    @endfor
                                </div>
                                <small>(Chọn số sao)</small>
                                <a href="{{ route('danh-gia.create', $detail->order_detail_id) }}" class="review-button">Viết đánh giá</a>
                            </div>
                        </div>
                    @endforeach
                </section>
            @empty
                <div class="review-empty">
                    <div>☆</div>
                    <h3>Bạn chưa có sản phẩm nào cần đánh giá.</h3>
                    <p>Sản phẩm sẽ xuất hiện tại đây sau khi đơn hàng được giao thành công.</p>
                    <a href="{{ route('cua-hang') }}" class="review-button">Tiếp tục mua sắm</a>
                </div>
            @endforelse

            @if(method_exists($eligibleDetails, 'total'))
                <x-pagination :paginator="$eligibleDetails" />
            @endif
        @else
            @forelse($reviewed as $review)
                <section class="reviewed-card">
                    <div class="review-product-info">
                        <div class="review-product-image">
                            @if($review->cayCanh?->anh_dai_dien)
                                <img src="{{ asset($review->cayCanh->anh_dai_dien) }}" alt="{{ $review->cayCanh->ten_cay }}">
                            @else
                                <span>🌿</span>
                            @endif
                        </div>
                        <div>
                            <strong>{{ $review->cayCanh?->ten_cay ?? 'Sản phẩm' }}</strong>
                            <p>Đơn hàng {{ $review->chiTietDonHang?->donHang?->orderCode() ?? '—' }}</p>
                            <div class="stars-readonly">{{ str_repeat('★', (int) $review->so_sao) }}{{ str_repeat('☆', 5 - (int) $review->so_sao) }}</div>
                        </div>
                    </div>
                    <span class="status-badge status-{{ $review->trang_thai }}">{{ $review->statusLabel() }}</span>
                    <div class="reviewed-content">{{ $review->noi_dung }}</div>
                    @php
                        $reviewImages = $review->hinh_anh ?? [];
                        if (is_string($reviewImages)) {
                            $decodedImages = json_decode($reviewImages, true);
                            $reviewImages = is_array($decodedImages) ? $decodedImages : array_filter([$reviewImages]);
                        }
                        $reviewImages = is_array($reviewImages) ? array_values(array_filter($reviewImages)) : [];
                    @endphp
                    @if(count($reviewImages))
                        <div class="reviewed-images" aria-label="Ảnh đính kèm đánh giá">
                            @foreach($reviewImages as $image)
                                @php
                                    $imageUrl = str_starts_with((string) $image, 'http://') || str_starts_with((string) $image, 'https://')
                                        ? $image
                                        : asset(ltrim((string) $image, '/'));
                                @endphp
                                <button type="button" class="review-image-thumb" data-review-image="{{ $imageUrl }}" aria-label="Xem ảnh đánh giá lớn">
                                    <img src="{{ $imageUrl }}" alt="Ảnh đánh giá" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif
                    <small>Gửi lúc {{ optional($review->ngay_danh_gia)->format('H:i d/m/Y') }}</small>
                </section>
            @empty
                <div class="review-empty">
                    <div>☆</div>
                    <h3>Bạn chưa gửi đánh giá nào.</h3>
                </div>
            @endforelse

            @if(method_exists($reviewed, 'total'))
                <x-pagination :paginator="$reviewed" />
            @endif
        @endif
    </main>
</div>

<div class="review-lightbox" id="reviewLightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Xem ảnh đánh giá">
    <button type="button" class="review-lightbox-close" id="reviewLightboxClose" aria-label="Đóng ảnh">×</button>
    <img id="reviewLightboxImage" src="" alt="Ảnh đánh giá phóng lớn">
</div>
<script>
(() => {
 const toggle = document.getElementById('mobileMenuButton');
 const sidebar = document.getElementById('accountSidebar');
 const backdrop = document.getElementById('sidebarBackdrop');
 if (!toggle || !sidebar || !backdrop) return;
 const close = () => {sidebar.classList.remove('open'); backdrop.classList.remove('open');};
 toggle.addEventListener('click', () => {sidebar.classList.toggle('open'); backdrop.classList.toggle('open');});
 backdrop.addEventListener('click', close);
})();

(() => {
    const lightbox = document.getElementById('reviewLightbox');
    const lightboxImage = document.getElementById('reviewLightboxImage');
    const closeButton = document.getElementById('reviewLightboxClose');
    if (!lightbox || !lightboxImage || !closeButton) return;

    const openLightbox = (src) => {
        lightboxImage.src = src;
        lightbox.classList.add('open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('review-lightbox-open');
    };
    const closeLightbox = () => {
        lightbox.classList.remove('open');
        lightbox.setAttribute('aria-hidden', 'true');
        lightboxImage.src = '';
        document.body.classList.remove('review-lightbox-open');
    };

    document.querySelectorAll('[data-review-image]').forEach((button) => {
        button.addEventListener('click', () => openLightbox(button.dataset.reviewImage));
    });
    closeButton.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && lightbox.classList.contains('open')) closeLightbox();
    });
})();
</script>
</body>
</html>
