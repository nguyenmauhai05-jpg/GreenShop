

    <div class="plant-table-card">

        <div class="plant-table-header">
            <h2>Danh sách cây</h2>
            <p>Danh sách toàn bộ cây cảnh trong GreenShop</p>
        </div>

        <div class="plant-table-scroll">


            <table class="plant-table">


                <thead>

                    <tr>

                        <th>
                            STT
                        </th>


                        <th>
                            Ảnh
                        </th>


                        <th>
                            Tên cây
                        </th>


                        <th>
                            Danh mục
                        </th>


                        <th>
                            Giá bán
                        </th>


                        <th>
                            Số lượng
                        </th>


                        <th>
                            Tình trạng
                        </th>


                        <th>
                            Hiển thị
                        </th>


                        <th class="action-column">
                            Thao tác
                        </th>

                    </tr>

                </thead>



                <tbody>


                <?php $__empty_1 = true; $__currentLoopData = $cayCanhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>


                    <tr>


                        
                        <td>

                            <?php echo e(method_exists(
                                    $cayCanhs,
                                    'firstItem'
                                )
                                    ? $cayCanhs->firstItem() + $index
                                    : $index + 1); ?>


                        </td>



                        
                        <td>

                            <div class="plant-table-image">


                                <?php if(!empty($cay->anh_dai_dien)): ?>

                                    <img
                                        src="<?php echo e(asset($cay->anh_dai_dien)); ?>"
                                        alt="<?php echo e($cay->ten_cay); ?>"
                                    loading="lazy" decoding="async"
>

                                <?php else: ?>

                                    <span>
                                        🌱
                                    </span>

                                <?php endif; ?>


                            </div>

                        </td>



                        
                        <td>

                            <div class="plant-name-cell">

                                <strong>
                                    <?php echo e($cay->ten_cay); ?>

                                </strong>


                                <span>

                                    Mã: CC<?php echo e(str_pad(
                                        $cay->plant_id,
                                        3,
                                        '0',
                                        STR_PAD_LEFT
                                    )); ?>


                                </span>

                            </div>

                        </td>



                        
                        <td>

                            <?php echo e($cay->ten_danh_muc
                                ?? 'Chưa phân loại'); ?>


                        </td>



                        
                        <td>

                            <strong class="plant-price">

                                <?php echo e(number_format(
                                        $cay->gia,
                                        0,
                                        ',',
                                        '.'
                                    )); ?>₫

                            </strong>

                        </td>



                        
                        <td>

                            <strong>
                                <?php echo e($cay->so_luong); ?>

                            </strong>

                        </td>



                        
                        <td>


                            <?php if($cay->so_luong <= 0): ?>

                                <span class="plant-stock-badge out">
                                    ● Hết hàng
                                </span>


                            <?php elseif($cay->so_luong <= 10): ?>

                                <span class="plant-stock-badge low">
                                    ● Sắp hết hàng
                                </span>


                            <?php else: ?>

                                <span class="plant-stock-badge available">
                                    ● Còn hàng
                                </span>

                            <?php endif; ?>


                        </td>



                        
                        <td>

                            <span
                                class="
                                    plant-display-badge
                                    <?php echo e($cay->trang_thai === 'Đang bán'
                                            ? 'selling'
                                            : 'hidden'); ?>

                                "
                            >

                                <?php echo e($cay->trang_thai); ?>


                            </span>

                        </td>



                        

                        <td>


                            <div class="plant-actions">


                                
                                <a
                                    href="<?php echo e(route(
                                        'admin.cay-canh.show',
                                        $cay->plant_id
                                    )); ?>"
                                    class="plant-action view"
                                    title="Xem chi tiết"
                                >
                                    ◉
                                </a>



                                
                                <a
                                    href="<?php echo e(route(
                                        'admin.cay-canh.edit',
                                        $cay->plant_id
                                    )); ?>"
                                    class="plant-action edit"
                                    title="Sửa cây"
                                >
                                    ✎
                                </a>



                                
                                <button
                                    type="button"
                                    class="plant-action delete"
                                    title="Xóa cây"
                                    onclick="openDeletePlantModal(
                                        <?php echo e($cay->plant_id); ?>,
                                        <?php echo \Illuminate\Support\Js::from($cay->ten_cay)->toHtml() ?>
                                    )"
                                >
                                    🗑
                                </button>


                            </div>


                        </td>


                    </tr>


                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>


                    <tr>

                        <td
                            colspan="9"
                            class="plant-table-empty"
                        >

                            <div>
                                🌱
                            </div>


                            <strong>
                                Không có cây phù hợp
                            </strong>


                            <p>
                                Hãy thử thay đổi từ khóa
                                hoặc bộ lọc.
                            </p>

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>


            </table>


        </div>



        

        <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $cayCanhs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($cayCanhs)]); ?>
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



    </div>


</div>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/cay_canh/components/table.blade.php ENDPATH**/ ?>