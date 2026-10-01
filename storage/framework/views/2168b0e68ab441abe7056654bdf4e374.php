<div class="products-grid">

    <?php $__currentLoopData = $cayCanhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php echo $__env->make(
            'cua_hang.components.product-card',
            ['cay' => $cay]
        , array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div><?php /**PATH D:\LaravelProjects\GreenShop\resources\views/cua_hang/components/product-grid.blade.php ENDPATH**/ ?>