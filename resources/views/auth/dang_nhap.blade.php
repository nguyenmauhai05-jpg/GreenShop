@php
    $authLogo = \App\Support\GreenShopSettings::get('logo', 'images/logo.png');
    $authBrandName = \App\Support\GreenShopSettings::get('store_name', 'GreenShop');
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - GreenShop</title>
    @vite(['resources/css/customer/auth-login.css', 'resources/js/customer/auth-password.js'])
</head>

<body>

@include('components.system-toast')

<div class="auth-container">

    <div class="auth-left">

        <a href="{{ route('trang-chu') }}" class="logo" aria-label="GreenShop">
            <span class="auth-logo-image">
                <img
                    src="{{ asset($authLogo) }}"
                    alt="{{ $authBrandName }}"
                >
            </span>
            <span class="auth-brand-name">{{ $authBrandName }}</span>
        </a>

        <h1 class="title">Chào mừng trở lại</h1>

        <p class="description">
            Đăng nhập để tiếp tục mua sắm và chăm sóc
            những cây xanh yêu thích của bạn.
        </p>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <form
            action="{{ route('dang-nhap.xu-ly') }}"
            method="POST"
            data-auth-form="login"
            novalidate
        >
            @csrf

            <div class="form-group">
                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Nhập email"
                    required
                    autocomplete="email"
                    maxlength="255"
                    class="form-control @error('email') is-invalid @enderror"
                >

                @error('email')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror
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
                        class="form-control password-input @error('mat_khau') is-invalid @enderror"
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword('mat_khau', this)"
                    >
                        👁
                    </button>
                </div>

                @error('mat_khau')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror

                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Đăng nhập
            </button>
        </form>

        <div class="register-link">
            Chưa có tài khoản?
            <a href="{{ route('dang-ky') }}">
                Đăng ký
            </a>
        </div>

    </div>

    <div class="auth-right" style="--auth-background-image: url('{{ asset("images/login-bg.png") }}')">

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
