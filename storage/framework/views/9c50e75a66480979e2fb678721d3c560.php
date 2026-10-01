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

        <?php $__empty_1 = true; $__currentLoopData = $doanhThuDanhMuc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

            <div class="report-category-item">

                <div class="report-category-top">

                    <div>
                        <strong>
                            <?php echo e($item->ten_danh_muc ?? 'Chưa phân loại'); ?>

                        </strong>

                        <span>
                            <?php echo e($item->ty_le ?? 0); ?>%
                        </span>
                    </div>


                    <strong>
                        <?php echo e(number_format(
                                $item->doanh_thu ?? 0,
                                0,
                                ',',
                                '.'
                            )); ?>₫
                    </strong>

                </div>


                <div class="report-category-progress">

                    <span
                        style="width: <?php echo e(min(100, max(0, $item->ty_le ?? 0))); ?>%;"
                    ></span>

                </div>

            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

            <div class="report-card-empty">
                Chưa có dữ liệu doanh thu theo danh mục.
            </div>

        <?php endif; ?>

    </div>

</div><?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/bao_cao/components/category-revenue.blade.php ENDPATH**/ ?>