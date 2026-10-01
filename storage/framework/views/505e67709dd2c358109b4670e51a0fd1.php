

    <div class="plant-summary-grid">

        <div class="plant-summary-card">

            <span>
                Tổng số cây
            </span>

            <strong>
                <?php echo e(number_format($tongCay ?? 0)); ?>

            </strong>

        </div>


        <div class="plant-summary-card">

            <span>
                Đang bán
            </span>

            <strong>
                <?php echo e(number_format($tongDangBan ?? 0)); ?>

            </strong>

        </div>


        <div class="plant-summary-card">

            <span>
                Sắp hết hàng
            </span>

            <strong class="orange">
                <?php echo e(number_format($tongSapHet ?? 0)); ?>

            </strong>

        </div>


        <div class="plant-summary-card">

            <span>
                Hết hàng
            </span>

            <strong class="red">
                <?php echo e(number_format($tongHetHang ?? 0)); ?>

            </strong>

        </div>

    </div>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/cay_canh/components/stats.blade.php ENDPATH**/ ?>