@extends('layouts.password-reset')

@section('title', 'Đặt lại thành công')

@section('content')
<div class="reset-card">
    <div class="icon-circle success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" aria-hidden="true"><path d="m6 12 4 4 8-8"/></svg>
    </div>

    <h1 class="card-title">Thành công!</h1>
    <p class="card-description">Mật khẩu của bạn đã được đặt lại thành công. Bạn có thể đăng nhập bằng mật khẩu mới.</p>

    <a class="btn-primary" href="{{ route('dang-nhap') }}">Đăng nhập ngay</a>
    <div class="card-back"><a href="{{ route('trang-chu') }}">← Quay lại trang chủ</a></div>
</div>
@endsection
