@extends('layouts.password-reset')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<div class="reset-card">
    <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/></svg>
    </div>

    <h1 class="card-title">Đặt lại mật khẩu</h1>
    <p class="card-description">Vui lòng nhập mật khẩu mới cho tài khoản của bạn.</p>

    @if(session('error'))
        <div class="info-box" style="border-color:#f0d2d0;background:#fff4f3;color:#a84640;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ old('email', $email) }}">

        @error('email')<span class="error-message" style="margin-bottom:12px;">{{ $message }}</span>@enderror
        @error('token')<span class="error-message" style="margin-bottom:12px;">{{ $message }}</span>@enderror

        <div class="form-group">
            <label for="password">Mật khẩu mới</label>
            <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" placeholder="Nhập mật khẩu mới" maxlength="72" autocomplete="new-password">
                <button class="toggle-password" type="button" data-target="password" aria-label="Hiện hoặc ẩn mật khẩu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                </button>
            </div>
            @error('password')<span class="error-message">{{ $message }}</span>@enderror
            <div class="strength-row">
                <span>Độ mạnh mật khẩu: <strong id="strength-text">Chưa nhập</strong></span>
                <div class="strength-bars" id="strength-bars"><span></span><span></span><span></span><span></span></div>
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Xác nhận mật khẩu</label>
            <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" id="password_confirmation" name="password_confirmation" placeholder="Nhập lại mật khẩu" maxlength="72" autocomplete="new-password">
                <button class="toggle-password" type="button" data-target="password_confirmation" aria-label="Hiện hoặc ẩn mật khẩu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                </button>
            </div>
            @error('password_confirmation')<span class="error-message">{{ $message }}</span>@enderror
        </div>

        <button type="submit" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
            Đặt lại mật khẩu
        </button>
    </form>

    <div class="card-back"><a href="{{ route('dang-nhap') }}">← Quay lại đăng nhập</a></div>
</div>
@endsection

@push('scripts')
@vite('resources/js/customer/password-reset.js')
@endpush
