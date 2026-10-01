<div class="detail-box">

    <div class="tab-menu">

        <button
            class="tab-button active"
            data-tab="detail"
        >
            Thông tin chi tiết
        </button>

        <button
            class="tab-button"
            data-tab="feature"
        >
            Đặc điểm
        </button>

        <button
            class="tab-button"
            data-tab="care"
        >
            Hướng dẫn chăm sóc
        </button>

        <button
            class="tab-button"
            data-tab="review"
        >
            Đánh giá ({{ $cay->danhGias->count() }})
        </button>

    </div>


    <div
        class="tab-panel active"
        id="tab-detail"
    >

        <div class="detail-table">

            <div class="detail-row">
                <strong>Tên cây</strong>
                <span>{{ $cay->ten_cay }}</span>
            </div>

            <div class="detail-row">
                <strong>Danh mục</strong>
                <span>
                    {{ $cay->danhMuc->ten_danh_muc ?? 'Chưa cập nhật' }}
                </span>
            </div>

            <div class="detail-row">
                <strong>Chiều cao</strong>
                <span>
                    {{ $cay->chieu_cao ?? 'Chưa cập nhật' }}
                </span>
            </div>

            <div class="detail-row">
                <strong>Tình trạng</strong>
                <span>
                    {{ $cay->so_luong > 0 ? 'Còn hàng' : 'Hết hàng' }}
                </span>
            </div>

        </div>

    </div>


    <div
        class="tab-panel"
        id="tab-feature"
    >
        <p>
            {{ $cay->mo_ta ?? 'Chưa có thông tin đặc điểm.' }}
        </p>
    </div>


    <div
        class="tab-panel"
        id="tab-care"
    >
        <p>
            {{ $cay->cach_cham_soc ?? 'Chưa có hướng dẫn chăm sóc.' }}
        </p>
    </div>


    <div
        class="tab-panel"
        id="tab-review"
    >

        @include(
            'chi_tiet_cay.components.reviews',
            ['cay' => $cay]
        )

    </div>

</div>


