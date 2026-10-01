<div class="report-summary-grid">

    
    <div class="report-summary-card">

        <div class="report-summary-icon">
            ₫
        </div>

        <div>

            <span>
                Tổng doanh thu
            </span>

            <strong>
                <?php echo e(number_format(
                        $tongDoanhThu ?? 0,
                        0,
                        ',',
                        '.'
                    )); ?>₫
            </strong>

        </div>

    </div>


    
    <div class="report-summary-card">

        <div class="report-summary-icon">
            🧾
        </div>

        <div>

            <span>
                Tổng đơn hàng
            </span>

            <strong>
                <?php echo e(number_format($tongDonHang ?? 0)); ?>

            </strong>

        </div>

    </div>


    
    <div class="report-summary-card">

        <div class="report-summary-icon">
            🌱
        </div>

        <div>

            <span>
                Sản phẩm đã bán
            </span>

            <strong>
                <?php echo e(number_format($tongSanPhamDaBan ?? 0)); ?>

            </strong>

        </div>

    </div>


    
    <div class="report-summary-card">

        <div class="report-summary-icon">
            👤
        </div>

        <div>

            <span>
                Khách hàng mới
            </span>

            <strong>
                <?php echo e(number_format($tongKhachHangMoi ?? 0)); ?>

            </strong>

        </div>

    </div>

</div><?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/bao_cao/components/summary-cards.blade.php ENDPATH**/ ?>