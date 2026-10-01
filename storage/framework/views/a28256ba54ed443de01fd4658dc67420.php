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

        <?php $__empty_1 = true; $__currentLoopData = $thongKeTrangThai; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

            <div class="report-status-item">

                <div class="report-status-main">

                    <div>

                        <strong>
                            <?php echo e($item->ten_trang_thai); ?>

                        </strong>

                        <span>
                            <?php echo e(number_format($item->so_don ?? 0)); ?> đơn
                        </span>

                    </div>


                    <span class="report-status-percent">
                        <?php echo e($item->ty_le ?? 0); ?>%
                    </span>

                </div>


                <div class="report-status-revenue">

                    Doanh thu:

                    <strong>
                        <?php echo e(number_format(
                                $item->doanh_thu ?? 0,
                                0,
                                ',',
                                '.'
                            )); ?>₫
                    </strong>

                </div>


                <div class="report-status-progress">

                    <span
                        style="width: <?php echo e(min(100, max(0, $item->ty_le ?? 0))); ?>%;"
                    ></span>

                </div>

            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

            <div class="report-card-empty">
                Chưa có dữ liệu trạng thái đơn hàng.
            </div>

        <?php endif; ?>

    </div>

</div><?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/bao_cao/components/order-status.blade.php ENDPATH**/ ?>