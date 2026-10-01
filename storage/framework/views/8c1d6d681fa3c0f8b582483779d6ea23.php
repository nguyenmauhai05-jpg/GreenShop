<div class="report-card report-chart-card">

    <div class="report-card-header">
        <div>
            <h2>Biểu đồ doanh thu</h2>
            <p>Theo dõi biến động doanh thu theo khoảng thời gian đã chọn.</p>
        </div>

        <?php if(isset($duLieuBieuDo) && $duLieuBieuDo->count() > 0): ?>
            <div class="report-chart-legend">
                <span class="report-chart-legend-dot"></span>
                <span>Doanh thu</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="report-chart-wrap">

        <?php if(isset($duLieuBieuDo) && $duLieuBieuDo->count() > 0): ?>

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

        <?php else: ?>

            <div class="report-card-empty">
                Không có dữ liệu doanh thu để hiển thị biểu đồ.
            </div>

        <?php endif; ?>

    </div>

</div>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/bao_cao/components/revenue-chart.blade.php ENDPATH**/ ?>