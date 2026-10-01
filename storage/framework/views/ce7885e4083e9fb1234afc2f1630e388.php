<aside class="shop-sidebar">
    <form
        method="GET"
        action="<?php echo e(route('cua-hang')); ?>"
        class="shop-filter-form"
        id="shopFilterForm"
    >
        <div class="filter-heading">BỘ LỌC</div>

        
        <?php if(request('search')): ?>
            <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
        <?php endif; ?>

        <?php if(!empty($filterErrors)): ?>
            <div class="filter-error-box">
                <strong>Bộ lọc chưa hợp lệ</strong>
                <?php $__currentLoopData = $filterErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><?php echo e($message); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <?php echo $__env->make('cua_hang.components.filter.category', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('cua_hang.components.filter.price', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('cua_hang.components.filter.status', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('cua_hang.components.filter.size', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php if(request('sort')): ?>
            <input type="hidden" name="sort" value="<?php echo e(request('sort')); ?>">
        <?php endif; ?>

        <button type="submit" class="filter-submit-btn">
            Áp dụng bộ lọc
        </button>

        <a
            href="<?php echo e(route('cua-hang', request('search') ? ['search' => request('search')] : [])); ?>"
            class="filter-reset-btn"
        >
            ↻ Xóa bộ lọc
        </a>
    </form>
</aside>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/cua_hang/components/filter.blade.php ENDPATH**/ ?>