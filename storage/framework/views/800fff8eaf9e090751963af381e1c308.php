<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cửa hàng - GreenShop</title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/customer/site-shell.css',
        'resources/css/cua-hang.css',
        'resources/js/customer/cua-hang.js',
    ]); ?>
</head>

<body>

    
    <?php echo $__env->make('trang_chu.components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php if(session('success') || session('warning') || session('error')): ?>
        <div class="shop-toast-container" id="shopToast">
            <div class="shop-toast <?php echo e(session('error') ? 'error' : (session('warning') ? 'warning' : 'success')); ?>" role="alert">
                <span class="shop-toast-icon">
                    <?php echo e(session('error') ? '×' : (session('warning') ? '!' : '✓')); ?>

                </span>
                <span class="shop-toast-message">
                    <?php echo e(session('error') ?? session('warning') ?? session('success')); ?>

                </span>
                <button type="button" class="shop-toast-close" aria-label="Đóng thông báo">×</button>
            </div>
        </div>
    <?php endif; ?>


    <main>

        
        <?php echo $__env->make('cua_hang.components.banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


        <section class="shop-section">

            <div class="shop-container">

                
                <?php echo $__env->make('cua_hang.components.filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


                
                <div class="shop-main">

                    <?php if(isset($loiHeThong)): ?>

                        <?php echo $__env->make('cua_hang.components.error-state', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    <?php else: ?>

                        
                        <?php echo $__env->make('cua_hang.components.toolbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


                        <?php if($cayCanhs->count() === 0): ?>

                            
                            <?php echo $__env->make('cua_hang.components.empty-state', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        <?php else: ?>

                            
                            <?php echo $__env->make('cua_hang.components.product-grid', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                            
                            <?php echo $__env->make('cua_hang.components.pagination', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        <?php endif; ?>

                    <?php endif; ?>

                </div>

            </div>

        </section>

    </main>


    
    <?php echo $__env->make('trang_chu.components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


</body>
</html><?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/cua_hang/index.blade.php ENDPATH**/ ?>