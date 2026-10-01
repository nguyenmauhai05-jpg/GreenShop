<?php echo app('Illuminate\Foundation\Vite')([
    'resources/css/app.css',
    'resources/css/customer/site-shell.css',
    'resources/css/cham-soc-cay.css',
    'resources/js/customer/cham-soc-cay.js',
]); ?>

<?php echo $__env->make('trang_chu.components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main
    class="ai-care-page"
    id="aiCarePage"
    data-chat-url="<?php echo e(route('cham-soc-cay.chat')); ?>"
    data-base-url="<?php echo e(route('cham-soc-cay')); ?>"
    data-cart-url="<?php echo e(route('gio-hang.them')); ?>"
    data-authenticated="<?php echo e(auth()->check() ? '1' : '0'); ?>"
>
    <div class="ai-care-layout">
        <?php echo $__env->make('cham_soc_cay.components.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('cham_soc_cay.components.chat-panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</main>

<?php echo $__env->make('trang_chu.components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/cham_soc_cay/index.blade.php ENDPATH**/ ?>