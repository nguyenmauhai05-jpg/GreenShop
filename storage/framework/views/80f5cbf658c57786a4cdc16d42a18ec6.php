<?php
    $authLogo = \App\Support\GreenShopSettings::get('logo', 'images/logo.png');
    $authBrandName = \App\Support\GreenShopSettings::get('store_name', 'GreenShop');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - GreenShop</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/customer/auth-login.css', 'resources/js/customer/auth-password.js']); ?>
</head>

<body>

<?php echo $__env->make('components.system-toast', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="auth-container">

    <div class="auth-left">

        <a href="<?php echo e(route('trang-chu')); ?>" class="logo" aria-label="GreenShop">
            <span class="auth-logo-image">
                <img
                    src="<?php echo e(asset($authLogo)); ?>"
                    alt="<?php echo e($authBrandName); ?>"
                >
            </span>
            <span class="auth-brand-name"><?php echo e($authBrandName); ?></span>
        </a>

        <h1 class="title">Chào mừng trở lại</h1>

        <p class="description">
            Đăng nhập để tiếp tục mua sắm và chăm sóc
            những cây xanh yêu thích của bạn.
        </p>

        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-error">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <form
            action="<?php echo e(route('dang-nhap.xu-ly')); ?>"
            method="POST"
            data-auth-form="login"
            novalidate
        >
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo e(old('email')); ?>"
                    placeholder="Nhập email"
                    required
                    autocomplete="email"
                    maxlength="255"
                    class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                >

                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="error-message">
                        <?php echo e($message); ?>

                    </span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <label for="mat_khau">Mật khẩu</label>

                <div class="input-wrapper">
                    <input
                        type="password"
                        id="mat_khau"
                        name="mat_khau"
                        placeholder="Nhập mật khẩu"
                        required
                        autocomplete="current-password"
                        maxlength="72"
                        class="form-control password-input <?php $__errorArgs = ['mat_khau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword('mat_khau', this)"
                    >
                        👁
                    </button>
                </div>

                <?php $__errorArgs = ['mat_khau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="error-message">
                        <?php echo e($message); ?>

                    </span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                <div class="forgot-password">
                    <a href="<?php echo e(route('password.request')); ?>">Quên mật khẩu?</a>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Đăng nhập
            </button>
        </form>

        <div class="register-link">
            Chưa có tài khoản?
            <a href="<?php echo e(route('dang-ky')); ?>">
                Đăng ký
            </a>
        </div>

    </div>

    <div class="auth-right" style="--auth-background-image: url('<?php echo e(asset("images/login-bg.png")); ?>')">

        <div class="right-badge">
            ● Hơn 10.000 cây xanh đã được giao
        </div>

        <div class="right-quote">
            <h2>
                “Cây xanh là nghệ thuật sống — chúng lớn lên,
                thay đổi và mang hơi thở vào từng góc nhà.”
            </h2>

            <p>GreenShop Collection</p>
        </div>

    </div>

</div>
</body>
</html>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/auth/dang_nhap.blade.php ENDPATH**/ ?>