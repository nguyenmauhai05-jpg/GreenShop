<div class="report-card report-chart-card">

    <div class="report-card-header">
        <div>
            <h2>Biểu đồ doanh thu</h2>
            <p>Theo dõi biến động doanh thu theo khoảng thời gian đã chọn.</p>
        </div>

        @if(isset($duLieuBieuDo) && $duLieuBieuDo->count() > 0)
            <div class="report-chart-legend">
                <span class="report-chart-legend-dot"></span>
                <span>Doanh thu</span>
            </div>
        @endif
    </div>

    <div class="report-chart-wrap">

        @if(isset($duLieuBieuDo) && $duLieuBieuDo->count() > 0)

            <div class="report-chart-canvas-box">
                <canvas
                    id="revenueChart"
                    aria-label="Biểu đồ doanh thu GreenShop"
                ></canvas>

                <div
                    class="report-chart-tooltip"
                    id="revenueChartTooltip"
                    hidden
                ></div>
            </div>

        @else

            <div class="report-card-empty">
                Không có dữ liệu doanh thu để hiển thị biểu đồ.
            </div>

        @endif

    </div>

</div>
