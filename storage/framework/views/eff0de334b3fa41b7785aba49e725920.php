<?php $__env->startSection('title', 'Quản lý danh mục - GreenShop Admin'); ?>


<?php $__env->startSection('page-title', 'Quản lý danh mục'); ?>


<?php $__env->startSection('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/admin/danh-muc.css'); ?>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>

<div class="category-page">

    

    <div class="category-heading">


        <div class="category-heading-actions">

            
            <a
                href="<?php echo e(route(
                    'admin.danh-muc.export-excel',
                    request()->only([
                        'search',
                        'status'
                    ])
                )); ?>"
                class="btn-export"
            >
                ↓ Xuất Excel
            </a>


            
            <button
                type="button"
                class="btn-add-category"
                onclick="openCreateCategoryModal()"
            >
                + Thêm danh mục mới
            </button>

        </div>

    </div>


    

    <?php if(session('success')): ?>

        <div class="category-alert category-alert-success">

            <span>
                ✓
            </span>

            <div>
                <?php echo e(session('success')); ?>

            </div>

        </div>

    <?php endif; ?>


    <?php if(session('error')): ?>

        <div class="category-alert category-alert-error">

            <span>
                !
            </span>

            <div>
                <?php echo e(session('error')); ?>

            </div>

        </div>

    <?php endif; ?>


    <?php if(!empty($loiHeThong)): ?>

        <div class="category-alert category-alert-error">

            <span>
                !
            </span>

            <div>
                Không thể tải danh sách danh mục.
                Vui lòng thử lại sau.
            </div>

        </div>

    <?php endif; ?>

    <?php echo $__env->make('admin.danh_muc.components.stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.danh_muc.components.filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.danh_muc.components.table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.danh_muc.components.modal-create', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.danh_muc.components.modal-edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.danh_muc.components.modal-delete', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div
        id="categoryPageConfig"
        hidden
        data-base-url="<?php echo e(url('/admin/danh-muc')); ?>"
        data-open-create="<?php echo e($errors->getBag('createCategory')->any() ? '1' : '0'); ?>"
        data-open-edit="<?php echo e($errors->getBag('editCategory')->any() && old('category_id') ? '1' : '0'); ?>"
        data-edit-id="<?php echo e((int) old('category_id', 0)); ?>"
        data-edit-name="<?php echo e(old('ten_danh_muc', '')); ?>"
        data-edit-description="<?php echo e(old('mo_ta', '')); ?>"
        data-edit-status="<?php echo e(old('trang_thai', 'Hiển thị')); ?>"
    ></div>

</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/admin/danh-muc.js'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/danh_muc/index.blade.php ENDPATH**/ ?>