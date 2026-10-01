<?php $__env->startSection('title', 'Quên mật khẩu'); ?>

<?php $__env->startSection('content'); ?>
<div class="reset-card">
    <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="3" y="5" width="18" height="14" rx="2"/>
            <path d="m3 7 9 6 9-6"/>
        </svg>
    </div>

    <h1 class="card-title">Quên mật khẩu</h1>
    <p class="card-description">Nhập email của bạn để nhận liên kết đặt lại mật khẩu.</p>

    <?php if(session('error')): ?>
        <div class="info-box" style="border-color:#f0d2d0;background:#fff4f3;color:#a84640;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('password.email')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                <input
                    class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo e(old('email')); ?>"
                    placeholder="Nhập email của bạn"
                    maxlength="255"
                    autocomplete="email"
                    autofocus
                >
            </div>
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="error-message"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <button type="submit" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
            Gửi liên kết đặt lại mật khẩu
        </button>
    </form>

    <div class="card-back"><a href="<?php echo e(route('dang-nhap')); ?>">← Quay lại đăng nhập</a></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.password-reset', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/auth/quen_mat_khau.blade.php ENDPATH**/ ?>