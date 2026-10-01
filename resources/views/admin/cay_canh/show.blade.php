@extends('admin.layouts.app')

@section('title', 'Chi tiết cây - GreenShop Admin')
@section('page-title', 'Chi tiết cây')

@section('styles')
    @vite('resources/css/admin/cay-canh.css')
@endsection

@section('content')
<div class="plant-detail-page">
    <div class="plant-detail-heading">
        <div>
            <div class="plant-detail-breadcrumb">Quản lý cây / Chi tiết cây</div>
            <h2>{{ $cay->ten_cay }}</h2>
            <p>Xem đầy đủ thông tin cây trong hệ thống GreenShop.</p>
        </div>

        <div class="plant-detail-actions">
            <a href="{{ route('admin.cay-canh.index') }}" class="plant-detail-btn secondary">
                ← Quay lại danh sách
            </a>
            <a href="{{ route('admin.cay-canh.edit', $cay->plant_id) }}" class="plant-detail-btn primary">
                ✎ Sửa cây
            </a>
        </div>
    </div>

    <div class="plant-detail-layout">
        <section class="plant-detail-card plant-detail-image-card">
            <div class="plant-detail-image-wrap">
                @if(!empty($cay->anh_dai_dien))
                    <img src="{{ asset($cay->anh_dai_dien) }}" alt="{{ $cay->ten_cay }}">
                @else
                    <div class="plant-detail-no-image">Chưa có ảnh cây</div>
                @endif
            </div>

            <div class="plant-detail-image-meta">
                <span class="plant-detail-code">CC{{ str_pad($cay->plant_id, 3, '0', STR_PAD_LEFT) }}</span>
                <span class="plant-detail-status {{ $cay->trang_thai === 'Đang bán' ? 'selling' : 'hidden' }}">
                    {{ $cay->trang_thai }}
                </span>
            </div>
        </section>

        <section class="plant-detail-card plant-detail-info-card">
            <div class="plant-detail-section-title">
                <h3>Thông tin cơ bản</h3>
                <span>Thông tin đang lưu trong hệ thống</span>
            </div>

            <div class="plant-detail-info-grid">
                <div class="plant-detail-field">
                    <span>Mã cây</span>
                    <strong>CC{{ str_pad($cay->plant_id, 3, '0', STR_PAD_LEFT) }}</strong>
                </div>
                <div class="plant-detail-field">
                    <span>Tên cây</span>
                    <strong>{{ $cay->ten_cay }}</strong>
                </div>
                <div class="plant-detail-field">
                    <span>Danh mục</span>
                    <strong>{{ $cay->ten_danh_muc }}</strong>
                </div>
                <div class="plant-detail-field">
                    <span>Giá bán</span>
                    <strong class="plant-detail-price">{{ number_format($cay->gia, 0, ',', '.') }}đ</strong>
                </div>
                <div class="plant-detail-field">
                    <span>Số lượng tồn</span>
                    <strong>{{ number_format($cay->so_luong) }} cây</strong>
                </div>
                <div class="plant-detail-field">
                    <span>Chiều cao</span>
                    <strong>{{ filled($cay->chieu_cao) ? $cay->chieu_cao : 'Chưa cập nhật' }}</strong>
                </div>
            </div>
        </section>
    </div>

    <div class="plant-detail-bottom-grid">
        <section class="plant-detail-card plant-detail-text-card">
            <div class="plant-detail-section-title">
                <h3>Mô tả cây</h3>
                <span>Thông tin giới thiệu sản phẩm</span>
            </div>
            <div class="plant-detail-long-text">
                {!! nl2br(e($cay->mo_ta ?: 'Chưa có mô tả cho cây này.')) !!}
            </div>
        </section>

        <section class="plant-detail-card plant-detail-text-card">
            <div class="plant-detail-section-title">
                <h3>Cách chăm sóc</h3>
                <span>Hướng dẫn chăm sóc cây</span>
            </div>
            <div class="plant-detail-long-text">
                {!! nl2br(e($cay->cach_cham_soc ?: 'Chưa có hướng dẫn chăm sóc cho cây này.')) !!}
            </div>
        </section>
    </div>
</div>
@endsection
