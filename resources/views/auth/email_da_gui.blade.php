@extends('layouts.password-reset')

@section('title', 'Email đã gửi')

@section('content')
<div class="reset-card">
    <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
        </svg>
    </div>

    <h1 class="card-title">Đã gửi liên kết!</h1>
    <p class="card-description">
        Hệ thống đã gửi liên kết đặt lại mật khẩu đến <strong>{{ $email ?: 'email của bạn' }}</strong>.
    </p>

    <div class="info-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
        <span>Vui lòng kiểm tra hộp thư đến và thư mục spam nếu bạn không thấy email.</span>
    </div>

    <a class="btn-primary" href="{{ route('dang-nhap') }}">Quay lại đăng nhập</a>
    <div class="card-back"><a href="{{ route('password.request') }}">← Gửi lại bằng email khác</a></div>
</div>
@endsection
