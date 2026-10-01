@php
    $authLogo = \App\Support\GreenShopSettings::get('logo', 'images/logo.png');
    $authBrandName = \App\Support\GreenShopSettings::get('store_name', 'GreenShop');
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng ký - GreenShop</title>
    @vite(['resources/css/customer/auth-register.css', 'resources/js/customer/auth-password.js'])
</head>

<body>

@include('components.system-toast')

<div class="auth-container">

    <!-- PHẦN FORM -->
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

        <h1 class="title">
            Tạo tài khoản
        </h1>

        <p class="description">
            Tham gia GreenShop và mang thêm nhiều sắc xanh
            vào cuộc sống của bạn.
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
            action="{{ route('dang-ky.xu-ly') }}"
            method="POST"
            data-auth-form="register"
            novalidate
        >
            @csrf

            <!-- HỌ TÊN -->
            <div class="form-group">
                <label for="ho_ten">
                    Họ tên
                </label>

                <input
                    type="text"
                    id="ho_ten"
                    name="ho_ten"
                    value="{{ old('ho_ten') }}"
                    placeholder="Nhập họ và tên"
                    required
                    autocomplete="name"
                    minlength="2"
                    maxlength="150"
                    class="form-control @error('ho_ten') is-invalid @enderror"
                >

                @error('ho_ten')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror
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
                    value="{{ old('email') }}"
                    placeholder="Nhập email"
                    required
                    autocomplete="email"
                    minlength="5"
                    maxlength="255"
                    class="form-control @error('email') is-invalid @enderror"
                >

                @error('email')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror
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
                    value="{{ old('so_dien_thoai') }}"
                    placeholder="Nhập số điện thoại"
                    required
                    inputmode="numeric"
                    autocomplete="tel"
                    pattern="0[0-9]{9}"
                    minlength="10"
                    maxlength="20"
                    class="form-control @error('so_dien_thoai') is-invalid @enderror"
                >

                @error('so_dien_thoai')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror
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
                        class="form-control password-input @error('mat_khau') is-invalid @enderror"
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

                @error('mat_khau')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror
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
                        class="form-control password-input @error('xac_nhan_mat_khau') is-invalid @enderror"
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

                @error('xac_nhan_mat_khau')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror
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

            <a href="{{ route('dang-nhap') }}">
                Đăng nhập
            </a>
        </div>

    </div>


    <!-- PHẦN ẢNH -->
    <div class="auth-right" style="--auth-background-image: url('{{ asset("images/register-bg.png") }}')">

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
