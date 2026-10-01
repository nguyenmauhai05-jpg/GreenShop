    

    <div class="dashboard-row dashboard-row-bottom">


        
        <section class="dashboard-panel">

            <div class="dashboard-panel-header">

                <h3>
                    Top 5 cây bán chạy
                </h3>

                <a href="#">
                    Xem tất cả
                </a>

            </div>


            <div class="dashboard-table-wrapper">

                <table class="dashboard-table">

                    <thead>

                    <tr>
                        <th>#</th>
                        <th>Cây</th>
                        <th>Đã bán</th>
                        <th>Doanh thu</th>
                    </tr>

                    </thead>


                    <tbody>

                    <?php $__empty_1 = true; $__currentLoopData = $topCayBanChay ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <tr>

                            <td>
                                <?php echo e($index + 1); ?>

                            </td>

                            <td>

                                <div class="dashboard-product">

                                    <?php if(!empty($cay->anh_dai_dien)): ?>

                                        <img
                                            src="<?php echo e(asset($cay->anh_dai_dien)); ?>"
                                            alt="<?php echo e($cay->ten_cay); ?>"
                                        loading="lazy" decoding="async"
>

                                    <?php else: ?>

                                        <div class="dashboard-product-image">
                                            🌱
                                        </div>

                                    <?php endif; ?>


                                    <div>

                                        <strong>
                                            <?php echo e($cay->ten_cay); ?>

                                        </strong>

                                        <span>
                                            Mã: CC<?php echo e(str_pad($cay->plant_id, 3, '0', STR_PAD_LEFT)); ?>

                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>
                                <?php echo e($cay->da_ban ?? 0); ?>

                            </td>


                            <td>

                                <?php echo e(number_format(
                                    $cay->doanh_thu ?? 0,
                                    0,
                                    ',',
                                    '.'
                                )); ?>₫

                            </td>

                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <tr>

                            <td
                                colspan="4"
                                class="dashboard-empty-table"
                            >
                                Chưa có dữ liệu bán hàng.
                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>



        
        <section class="dashboard-panel">

            <div class="dashboard-panel-header">

                <h3>
                    Hoạt động gần đây
                </h3>

            </div>


            <div class="dashboard-activities">

                <?php $__empty_1 = true; $__currentLoopData = $hoatDongGanDay ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                    <div class="dashboard-activity-item">

                        <div class="dashboard-activity-icon">
                            <?php echo e($activity->icon ?? '✓'); ?>

                        </div>

                        <div class="dashboard-activity-content">

                            <p>
                                <?php echo e($activity->noi_dung_hien_thi ?? ''); ?>

                            </p>

                            <span>
                                <?php echo e($activity->thoi_gian_hien_thi ?? ''); ?>

                            </span>

                        </div>

                    </div>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                    <div class="dashboard-empty">
                        Hiện chưa có đánh giá thấp (1–2 sao).
                    </div>

                <?php endif; ?>

            </div>

        </section>

    </div>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/dashboard/components/top-products-activities.blade.php ENDPATH**/ ?>