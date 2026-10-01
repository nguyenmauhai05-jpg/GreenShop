@php
    $anh = $cay->anh_dai_dien;

    if ($anh) {
        $anh = ltrim($anh, '/');

        if (!str_starts_with($anh, 'images/')) {
            $anh = 'images/trang-chu/products/' . $anh;
        }

        $anhUrl = asset($anh);
    } else {
        $anhUrl = null;
    }
@endphp

<div class="gallery">
    <div class="main-gallery">
        @if($anhUrl)
            <img
                src="{{ $anhUrl }}"
                alt="{{ $cay->ten_cay }}"
            >
        @else
            <div class="product-no-image">
                🌱
            </div>
        @endif
    </div>
</div>
