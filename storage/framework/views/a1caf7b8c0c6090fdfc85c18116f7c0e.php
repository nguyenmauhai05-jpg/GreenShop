<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        GreenShop - Cây xanh cho không gian sống
    </title>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/customer/site-shell.css', 'resources/css/customer/home.css']); ?>

    
</head>


<body>

    
    <?php echo $__env->make('trang_chu.components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <main>

        
        <?php echo $__env->make('trang_chu.components.hero', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
    <?php echo $__env->make('trang_chu.components.categories', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('trang_chu.components.best-sellers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
<?php echo $__env->make('trang_chu.components.promotion', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('trang_chu.components.benefits', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('trang_chu.components.new-arrivals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <?php echo $__env->make('trang_chu.components.testimonials', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        

    </main>

     <?php echo $__env->make('trang_chu.components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html><?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/trang_chu/index.blade.php ENDPATH**/ ?>