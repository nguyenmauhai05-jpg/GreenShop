<div class="report-card">

    <div class="report-card-header">

        <div>
            <h2>
                Doanh thu theo danh mục
            </h2>

            <p>
                Tỷ lệ đóng góp doanh thu của từng danh mục.
            </p>
        </div>

    </div>


    <div class="report-category-list">

        @forelse($doanhThuDanhMuc as $item)

            <div class="report-category-item">

                <div class="report-category-top">

                    <div>
                        <strong>
                            {{ $item->ten_danh_muc ?? 'Chưa phân loại' }}
                        </strong>

                        <span>
                            {{ $item->ty_le ?? 0 }}%
                        </span>
                    </div>


                    <strong>
                        {{
                            number_format(
                                $item->doanh_thu ?? 0,
                                0,
                                ',',
                                '.'
                            )
                        }}₫
                    </strong>

                </div>


                <div class="report-category-progress">

                    <span
                        style="width: {{ min(100, max(0, $item->ty_le ?? 0)) }}%;"
                    ></span>

                </div>

            </div>

        @empty

            <div class="report-card-empty">
                Chưa có dữ liệu doanh thu theo danh mục.
            </div>

        @endforelse

    </div>

</div>