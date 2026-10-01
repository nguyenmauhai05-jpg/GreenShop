<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Quên mật khẩu'); ?> - GreenShop</title>
<?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/customer/password-reset.css'); ?>
</head>
<body style="--password-reset-bg: url('<?php echo e(asset('images/forgot-password-bg.png')); ?>')">
    <?php echo $__env->make('components.system-toast', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="page-shell">

    <main class="content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>

<footer class="benefits" aria-label="Cam kết GreenShop">
    <div class="benefit">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
        <div><strong>Cây chất lượng</strong><small>Được chọn lọc kỹ càng</small></div>
    </div>
    <div class="benefit">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h11v10H3z"/><path d="M14 9h4l3 3v4h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></svg>
        <div><strong>Giao hàng toàn quốc</strong><small>Nhanh chóng, an toàn</small></div>
    </div>
    <div class="benefit">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 13a8 8 0 0 1 16 0"/><path d="M4 13v4a2 2 0 0 0 2 2h2v-6H4Zm16 0v4a2 2 0 0 1-2 2h-2v-6h4Z"/></svg>
        <div><strong>Hỗ trợ 24/7</strong><small>Tận tâm, chu đáo</small></div>
    </div>
</footer>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/layouts/password-reset.blade.php ENDPATH**/ ?>