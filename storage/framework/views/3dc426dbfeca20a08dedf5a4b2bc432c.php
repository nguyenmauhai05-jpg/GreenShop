

    <div class="category-table-card">

        
        <div class="category-table-header">

            <h2>
                Danh sách danh mục
            </h2>

            <p>
                Danh sách toàn bộ danh mục cây trong GreenShop
            </p>

        </div>


        <div class="category-table-responsive">

            <table class="category-table">

                <thead>

                    <tr>

                        <th>
                            STT
                        </th>

                        <th>
                            Tên danh mục
                        </th>

                        <th>
                            Mô tả
                        </th>

                        <th>
                            Số cây
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Ngày tạo
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php $__empty_1 = true; $__currentLoopData = $danhMucs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $danhMuc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                    <tr>

                        
                        <td>

                            <?php echo e(method_exists(
                                    $danhMucs,
                                    'firstItem'
                                )
                                    ? $danhMucs->firstItem() + $index
                                    : $index + 1); ?>


                        </td>


                        
                        <td>

                            <div class="category-name">

                                


                                <div>

                                    <strong>
                                        <?php echo e($danhMuc->ten_danh_muc); ?>

                                    </strong>

                                </div>

                            </div>

                        </td>


                        
                        <td>

                            <div class="category-description">

                                <?php echo e($danhMuc->mo_ta
                                    ?: 'Chưa có mô tả'); ?>


                            </div>

                        </td>


                        
                        <td>

                            <span class="category-count">

                                <?php echo e($danhMuc->so_cay ?? 0); ?>


                                cây

                            </span>

                        </td>


                        
                        <td>

                            <?php if(
                                $danhMuc->trang_thai
                                ===
                                'Hiển thị'
                            ): ?>

                                <span
                                    class="
                                        category-status
                                        status-visible
                                    "
                                >

                                    <i>
                                        ●
                                    </i>

                                    Hiển thị

                                </span>

                            <?php else: ?>

                                <span
                                    class="
                                        category-status
                                        status-hidden
                                    "
                                >

                                    <i>
                                        ●
                                    </i>

                                    Ẩn

                                </span>

                            <?php endif; ?>

                        </td>


                        
                        <td>

                            <?php if(
                                !empty(
                                    $danhMuc->created_at
                                )
                            ): ?>

                                <?php echo e(\Carbon\Carbon::parse(
                                        $danhMuc->created_at
                                    )->format(
                                        'd/m/Y'
                                    )); ?>


                            <?php else: ?>

                                --

                            <?php endif; ?>

                        </td>


                        
                        <td>

                            <div class="category-actions">


                                
                                <button
                                    type="button"
                                    class="
                                        category-action-btn
                                        edit
                                    "
                                    title="Sửa danh mục"
                                    onclick="openEditCategoryModal(
                                        <?php echo e($danhMuc->category_id); ?>,
                                        <?php echo \Illuminate\Support\Js::from($danhMuc->ten_danh_muc)->toHtml() ?>,
                                        <?php echo \Illuminate\Support\Js::from($danhMuc->mo_ta ?? '')->toHtml() ?>,
                                        <?php echo \Illuminate\Support\Js::from($danhMuc->trang_thai)->toHtml() ?>
                                    )"
                                >
                                    ✎
                                </button>


                                
                                <button
                                    type="button"
                                    class="
                                        category-action-btn
                                        delete
                                    "
                                    title="Xóa danh mục"
                                    onclick="openDeleteCategoryModal(
                                        <?php echo e($danhMuc->category_id); ?>,
                                        <?php echo \Illuminate\Support\Js::from($danhMuc->ten_danh_muc)->toHtml() ?>,
                                        <?php echo e($danhMuc->so_cay ?? 0); ?>

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
                            colspan="7"
                            class="category-empty"
                        >

                            <i>
                                ▣
                            </i>

                            <h3>
                                Không có danh mục phù hợp
                            </h3>

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $danhMucs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($danhMucs)]); ?>
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
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/danh_muc/components/table.blade.php ENDPATH**/ ?>