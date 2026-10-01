<?php
    $authLogo = \App\Support\GreenShopSettings::get('logo', 'images/logo.png');
    $authBrandName = \App\Support\GreenShopSettings::get('store_name', 'GreenShop');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng ký - GreenShop</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/customer/auth-register.css', 'resources/js/customer/auth-password.js']); ?>
</head>

<body>

<?php echo $__env->make('components.system-toast', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="auth-container">

    <!-- PHẦN FORM -->
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

        <h1 class="title">
            Tạo tài khoản
        </h1>

        <p class="description">
            Tham gia GreenShop và mang thêm nhiều sắc xanh
            vào cuộc sống của bạn.
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
            action="<?php echo e(route('dang-ky.xu-ly')); ?>"
            method="POST"
            data-auth-form="register"
            novalidate
        >
            <?php echo csrf_field(); ?>

            <!-- HỌ TÊN -->
            <div class="form-group">
                <label for="ho_ten">
                    Họ tên
                </label>

                <input
                    type="text"
                    id="ho_ten"
                    name="ho_ten"
                    value="<?php echo e(old('ho_ten')); ?>"
                    placeholder="Nhập họ và tên"
                    required
                    autocomplete="name"
                    minlength="2"
                    maxlength="150"
                    class="form-control <?php $__errorArgs = ['ho_ten'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                >

                <?php $__errorArgs = ['ho_ten'];
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

            <!-- EMAIL -->
            <div class="form-group">
                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo e(old('email')); ?>"
                    placeholder="Nhập email"
                    required
                    autocomplete="email"
                    minlength="5"
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

            <!-- SỐ ĐIỆN THOẠI -->
            <div class="form-group">
                <label for="so_dien_thoai">
                    Số điện thoại
                </label>

                <input
                    type="text"
                    id="so_dien_thoai"
                    name="so_dien_thoai"
                    value="<?php echo e(old('so_dien_thoai')); ?>"
                    placeholder="Nhập số điện thoại"
                    required
                    inputmode="numeric"
                    autocomplete="tel"
                    pattern="0[0-9]{9}"
                    minlength="10"
                    maxlength="20"
                    class="form-control <?php $__errorArgs = ['so_dien_thoai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                >

                <?php $__errorArgs = ['so_dien_thoai'];
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

            <!-- MẬT KHẨU -->
            <div class="form-group">
                <label for="mat_khau">
                    Mật khẩu
                </label>

                <div class="input-wrapper">

                    <input
                        type="password"
                        id="mat_khau"
                        name="mat_khau"
                        placeholder="Nhập mật khẩu"
                        required
                        autocomplete="new-password"
                        minlength="8"
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
                        aria-label="Hiện hoặc ẩn mật khẩu"
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
            </div>

            <!-- XÁC NHẬN MẬT KHẨU -->
            <div class="form-group">
                <label for="xac_nhan_mat_khau">
                    Xác nhận mật khẩu
                </label>

                <div class="input-wrapper">

                    <input
                        type="password"
                        id="xac_nhan_mat_khau"
                        name="xac_nhan_mat_khau"
                        placeholder="Nhập lại mật khẩu"
                        required
                        autocomplete="new-password"
                        minlength="8"
                        maxlength="72"
                        class="form-control password-input <?php $__errorArgs = ['xac_nhan_mat_khau'];
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
                        onclick="togglePassword('xac_nhan_mat_khau', this)"
                        aria-label="Hiện hoặc ẩn mật khẩu"
                    >
                        👁
                    </button>

                </div>

                <?php $__errorArgs = ['xac_nhan_mat_khau'];
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

            <!-- BUTTON -->
            <button
                type="submit"
                class="btn-submit"
            >
                Đăng ký
            </button>

        </form>

        <div class="login-link">
            Đã có tài khoản?

            <a href="<?php echo e(route('dang-nhap')); ?>">
                Đăng nhập
            </a>
        </div>

    </div>


    <!-- PHẦN ẢNH -->
    <div class="auth-right" style="--auth-background-image: url('<?php echo e(asset("images/register-bg.png")); ?>')">

        <div class="right-badge">
            ● Hơn 10.000 cây xanh đã được giao
        </div>

        <div class="right-quote">

            <h2>
                “Mỗi cây xanh bạn mang về nhà là một
                hành động nhỏ nuôi dưỡng chính mình
                và thế giới.”
            </h2>

            <p>
                GreenShop Community
            </p>

        </div>

    </div>

</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/auth/dang_ky.blade.php ENDPATH**/ ?>