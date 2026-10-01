<div class="report-card">

    <div class="report-card-header">

        <div>
            <h2>
                Đơn hàng theo trạng thái
            </h2>

            <p>
                Phân bố số lượng đơn và doanh thu theo từng trạng thái.
            </p>
        </div>

    </div>


    <div class="report-status-list">

        @forelse($thongKeTrangThai as $item)

            <div class="report-status-item">

                <div class="report-status-main">

                    <div>

                        <strong>
                            {{ $item->ten_trang_thai }}
                        </strong>

                        <span>
                            {{ number_format($item->so_don ?? 0) }} đơn
                        </span>

                    </div>


                    <span class="report-status-percent">
                        {{ $item->ty_le ?? 0 }}%
                    </span>

                </div>


                <div class="report-status-revenue">

                    Doanh thu:

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


                <div class="report-status-progress">

                    <span
                        style="width: {{ min(100, max(0, $item->ty_le ?? 0)) }}%;"
                    ></span>

                </div>

            </div>

        @empty

            <div class="report-card-empty">
                Chưa có dữ liệu trạng thái đơn hàng.
            </div>

        @endforelse

    </div>

</div>