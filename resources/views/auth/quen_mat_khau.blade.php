@extends('layouts.password-reset')

@section('title', 'Quên mật khẩu')

@section('content')
<div class="reset-card">
    <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="3" y="5" width="18" height="14" rx="2"/>
            <path d="m3 7 9 6 9-6"/>
        </svg>
    </div>

    <h1 class="card-title">Quên mật khẩu</h1>
    <p class="card-description">Nhập email của bạn để nhận liên kết đặt lại mật khẩu.</p>

    @if(session('error'))
        <div class="info-box" style="border-color:#f0d2d0;background:#fff4f3;color:#a84640;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                <input
                    class="form-control @error('email') is-invalid @enderror"
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Nhập email của bạn"
                    maxlength="255"
                    autocomplete="email"
                    autofocus
                >
            </div>
            @error('email')<span class="error-message">{{ $message }}</span>@enderror
        </div>

        <button type="submit" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
            Gửi liên kết đặt lại mật khẩu
        </button>
    </form>

    <div class="card-back"><a href="{{ route('dang-nhap') }}">← Quay lại đăng nhập</a></div>
</div>
@endsection
