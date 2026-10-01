<?php $__env->startSection('title', 'Báo cáo thống kê - GreenShop Admin'); ?>


<?php $__env->startSection('page-title', 'Báo cáo thống kê'); ?>


<?php $__env->startSection('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/admin/bao-cao.css'); ?>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>

<div class="report-page">

    
    <?php echo $__env->make('admin.bao_cao.components.heading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    
    <?php if(session('success')): ?>

        <div class="report-alert report-alert-success">
            <?php echo e(session('success')); ?>

        </div>

    <?php endif; ?>


    <?php if(session('error')): ?>

        <div class="report-alert report-alert-error">
            <?php echo e(session('error')); ?>

        </div>

    <?php endif; ?>


    <?php if(!empty($loiHeThong)): ?>

        <div class="report-alert report-alert-error">
            Không thể tải dữ liệu báo cáo.
            Vui lòng thử lại sau.
        </div>

    <?php endif; ?>


    
    <?php echo $__env->make('admin.bao_cao.components.filters', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <?php if(!empty($khongCoDuLieu)): ?>

        <?php echo $__env->make('admin.bao_cao.components.empty-state', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php else: ?>

        
        <?php echo $__env->make('admin.bao_cao.components.summary-cards', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


        
        <div class="report-grid report-grid-top">

            <?php echo $__env->make('admin.bao_cao.components.revenue-chart', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->make('admin.bao_cao.components.category-revenue', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </div>


        
        <div class="report-grid report-grid-bottom">

            <?php echo $__env->make('admin.bao_cao.components.product-report', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->make('admin.bao_cao.components.order-status', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </div>

    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>

<script>
    window.reportChartData = <?php echo json_encode($duLieuBieuDo ?? [], 15, 512) ?>;
    window.reportCategoryData = <?php echo json_encode($doanhThuDanhMuc ?? [], 15, 512) ?>;
    window.reportGroupBy = <?php echo json_encode($nhomTheo ?? 'ngay', 15, 512) ?>;
</script>

<?php echo app('Illuminate\Foundation\Vite')('resources/js/admin/bao-cao.js'); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/bao_cao/index.blade.php ENDPATH**/ ?>