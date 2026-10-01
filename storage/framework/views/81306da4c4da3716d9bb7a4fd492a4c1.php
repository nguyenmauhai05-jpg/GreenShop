<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?php echo $__env->yieldContent('title', 'GreenShop Admin'); ?></title>

    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/admin.css', 'resources/css/admin/sidebar.css']); ?>

    
    <?php echo $__env->yieldContent('styles'); ?>
</head>

<body>

<?php echo $__env->make('components.system-toast', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="admin-layout">

    
    <?php echo $__env->make('admin.layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <div class="admin-wrapper">

        
        <?php echo $__env->make('admin.layouts.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


        
        <main class="admin-main">

            <?php echo $__env->yieldContent('content'); ?>

        </main>

    </div>

</div>


<?php echo $__env->yieldContent('scripts'); ?>

</body>
</html><?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>