<div class="report-card report-product-card">

    <div class="report-card-header">

        <div>
            <h2>
                Báo cáo theo sản phẩm
            </h2>

            <p>
                Thống kê sản phẩm đã bán và doanh thu tương ứng.
            </p>
        </div>

    </div>


    <div class="report-table-responsive">

        <table class="report-table">

            <thead>

                <tr>
                    <th>STT</th>
                    <th>Sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Đã bán</th>
                    <th>Doanh thu</th>
                    <th>Tỷ lệ</th>
                </tr>

            </thead>


            <tbody>

            <?php $__empty_1 = true; $__currentLoopData = $sanPhamBaoCao; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sanPham): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                <tr>

                    <td>
                        <?php echo e(method_exists(
                                $sanPhamBaoCao,
                                'firstItem'
                            )
                                ? $sanPhamBaoCao->firstItem() + $index
                                : $index + 1); ?>

                    </td>


                    <td>

                        <div class="report-product-info">

                            <div class="report-product-image">

                                <?php if(!empty($sanPham->anh_dai_dien)): ?>

                                    <img
                                        src="<?php echo e(asset($sanPham->anh_dai_dien)); ?>"
                                        alt="<?php echo e($sanPham->ten_cay); ?>"
                                    >

                                <?php else: ?>

                                    🌱

                                <?php endif; ?>

                            </div>


                            <div>

                                <strong>
                                    <?php echo e($sanPham->ten_cay); ?>

                                </strong>

                                <span>
                                    Mã: CC<?php echo e(str_pad(
                                        $sanPham->plant_id,
                                        3,
                                        '0',
                                        STR_PAD_LEFT
                                    )); ?>

                                </span>

                            </div>

                        </div>

                    </td>


                    <td>
                        <?php echo e($sanPham->ten_danh_muc ?? 'Chưa phân loại'); ?>

                    </td>


                    <td>
                        <?php echo e(number_format($sanPham->so_luong_da_ban ?? 0)); ?>

                    </td>


                    <td>

                        <strong class="report-money">

                            <?php echo e(number_format(
                                    $sanPham->doanh_thu ?? 0,
                                    0,
                                    ',',
                                    '.'
                                )); ?>₫

                        </strong>

                    </td>


                    <td>

                        <span class="report-percent">
                            <?php echo e($sanPham->ty_le ?? 0); ?>%
                        </span>

                    </td>

                </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                <tr>

                    <td
                        colspan="6"
                        class="report-table-empty"
                    >
                        Không có dữ liệu sản phẩm phù hợp.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>


    <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $sanPhamBaoCao]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sanPhamBaoCao)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $attributes = $__attributesOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $component = $__componentOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__componentOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>

</div><?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/bao_cao/components/product-report.blade.php ENDPATH**/ ?>