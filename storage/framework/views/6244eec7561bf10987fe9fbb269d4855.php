<?php $__env->startSection('title', 'Quản lý cây - GreenShop Admin'); ?>


<?php $__env->startSection('page-title', 'Quản lý cây'); ?>


<?php $__env->startSection('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/admin/cay-canh.css'); ?>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>

<div class="plant-management">


    

    <div class="plant-page-heading">


        <div class="plant-heading-actions">

            <a
    href="<?php echo e(route(
        'admin.cay-canh.export-excel',
        request()->only([
            'search',
            'category',
            'status',
            'stock'
        ])
    )); ?>"
    class="plant-export-btn"
>
    ↓ Xuất Excel
</a>


            <a
                href="<?php echo e(route('admin.cay-canh.create')); ?>"
                class="plant-create-btn"
            >
                + Thêm cây mới
            </a>

        </div>

    </div>


    

    <?php if(session('success')): ?>

        <div class="plant-alert plant-alert-success" id="plantSuccessAlert">
            ✓ <?php echo e(session('success')); ?>

        </div>

    <?php endif; ?>


    <?php if(session('error')): ?>

        <div class="plant-alert plant-alert-error">
            <?php echo e(session('error')); ?>

        </div>

    <?php endif; ?>


    <?php if(!empty($loiHeThong)): ?>

        <div class="plant-alert plant-alert-error">
            Không thể tải danh sách cây.
            Vui lòng thử lại sau.
        </div>

    <?php endif; ?>

    <?php echo $__env->make('admin.cay_canh.components.stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.cay_canh.components.filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.cay_canh.components.table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</div>

<?php echo $__env->make('admin.cay_canh.components.delete-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div id="plantPageConfig" hidden data-base-url="<?php echo e(url('/admin/cay-canh')); ?>"></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/admin/cay-canh.js'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/cay_canh/index.blade.php ENDPATH**/ ?>