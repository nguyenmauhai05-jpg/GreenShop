    

    <div class="dashboard-row dashboard-row-main">


        
        <section class="dashboard-panel dashboard-revenue">

            <div class="dashboard-panel-header">

                <h3>
                    Doanh thu 7 ngày qua
                </h3>

                <span>
                    Doanh thu
                </span>

            </div>


            <div class="dashboard-chart">

                <?php if(isset($doanhThu7Ngay) && $doanhThu7Ngay->isNotEmpty()): ?>

                    <div class="dashboard-chart-placeholder">

                        <?php $__currentLoopData = $doanhThu7Ngay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <div class="chart-column">

                                <div class="chart-value">
                                    <?php echo e(number_format($item->doanh_thu ?? 0, 0, ',', '.')); ?>₫
                                </div>

                                <div
                                    class="chart-bar"
                                    title="<?php echo e($item->ngay_day_du ?? $item->ngay); ?>: <?php echo e(number_format($item->doanh_thu ?? 0, 0, ',', '.')); ?>₫"
                                    style="height:
                                    <?php echo e(($item->doanh_thu ?? 0) > 0
                                            ? min(150, max(10, (($item->doanh_thu ?? 0) / max($doanhThuMax ?? 1, 1)) * 150))
                                            : 4); ?>px"
                                ></div>

                                <span>
                                    <?php echo e($item->ngay ?? ''); ?>

                                </span>

                            </div>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </div>

                <?php else: ?>

                    <div class="dashboard-empty">
                        Chưa có dữ liệu doanh thu.
                    </div>

                <?php endif; ?>

            </div>

        </section>



        
        <section class="dashboard-panel dashboard-low-stock">

            <div class="dashboard-panel-header">

                <h3>
                    Cây sắp hết hàng
                </h3>



            </div>


            <div class="dashboard-table-wrapper">

                <table class="dashboard-table">

                    <thead>

                    <tr>
                        <th>Cây</th>
                        <th>Số lượng</th>
                        <th>Trạng thái</th>
                    </tr>

                    </thead>


                    <tbody>

                    <?php $__empty_1 = true; $__currentLoopData = $caySapHetHang ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <tr>

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

                                <strong>
                                    <?php echo e($cay->so_luong); ?>

                                </strong>

                            </td>


                            <td>

                                <?php if($cay->so_luong <= 5): ?>

                                    <span class="status-danger">
                                        Sắp hết
                                    </span>

                                <?php else: ?>

                                    <span class="status-warning">
                                        Còn ít
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <tr>

                            <td
                                colspan="3"
                                class="dashboard-empty-table"
                            >
                                Không có cây sắp hết hàng.
                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </div>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/dashboard/components/revenue-stock.blade.php ENDPATH**/ ?>