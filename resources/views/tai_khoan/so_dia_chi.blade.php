<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sổ địa chỉ - GreenShop</title>
    @vite(['resources/css/customer/site-shell.css'])
    @vite('resources/css/customer/ho-so.css')
    @vite('resources/css/customer/so-dia-chi.css')
</head>
<body>
@include('trang_chu.components.header')

<div class="account-layout">
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    @include('tai_khoan.components.account-sidebar', ['activeSidebar' => 'addresses', 'sidebarId' => 'accountSidebar'])

    <main class="account-main address-book-main">
        <div class="page-heading address-book-heading">
            <button class="profile-mobile-menu-button" type="button" id="mobileMenuButton" aria-label="Mở menu tài khoản">
                <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>

            <div>
                <h1>Sổ địa chỉ</h1>
                <p>Quản lý các địa chỉ nhận hàng của bạn</p>
            </div>

            <button type="button" class="btn btn-primary address-add-button" id="openAddAddressButton">
                <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Thêm địa chỉ mới
            </button>
        </div>

        @if(session('success'))
            <div class="toast toast-success" id="pageToast" role="status">
                <div class="toast-icon">
                    <svg viewBox="0 0 24 24"><path d="m5 12 4 4 10-10"/></svg>
                </div>
                <div>
                    <strong>Thành công</strong>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="toast-close" aria-label="Đóng">×</button>
            </div>
        @elseif(session('error'))
            <div class="toast toast-error" id="pageToast" role="alert">
                <div class="toast-icon">!</div>
                <div>
                    <strong>Có lỗi xảy ra</strong>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="toast-close" aria-label="Đóng">×</button>
            </div>
        @endif

        @if($diaChis->isEmpty())
            <section class="address-empty-card">
                <div class="address-empty-icon">
                    <svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                </div>
                <h2>Chưa có địa chỉ nhận hàng</h2>
                <p>Thêm địa chỉ bằng cách nhập thông tin hoặc chọn trực tiếp vị trí trên bản đồ.</p>
                <button type="button" class="btn btn-primary" data-open-add-address>
                    <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Thêm địa chỉ đầu tiên
                </button>
            </section>
        @else
            <section class="address-list" aria-label="Danh sách địa chỉ nhận hàng">
                @foreach($diaChis as $diaChi)
                    @php
                        $fullAddress = collect([
                            $diaChi->dia_chi,
                            $diaChi->phuong_xa,
                            $diaChi->tinh_thanh,
                        ])->filter()->join(', ');
                    @endphp

                    <article class="address-card {{ $diaChi->mac_dinh ? 'is-default' : '' }}">
                        <div class="address-card-top">
                            <div class="address-card-title">
                                <span class="address-pin-icon">
                                    <svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                                </span>
                                <div>
                                    <strong>{{ $diaChi->nguoi_nhan }}</strong>
                                    <span>{{ $diaChi->so_dien_thoai }}</span>
                                </div>
                            </div>

                            @if($diaChi->mac_dinh)
                                <span class="address-default-badge">Mặc định</span>
                            @endif
                        </div>

                        <p class="address-full-text">{{ $fullAddress }}</p>

                        @if($diaChi->latitude !== null && $diaChi->longitude !== null)
                            <div class="address-map-state">
                                <svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                                Đã lưu vị trí bản đồ
                            </div>
                        @endif

                        <div class="address-card-actions">
                            <button
                                type="button"
                                class="address-action-button edit"
                                data-edit-address
                                data-address-id="{{ $diaChi->address_id }}"
                                data-update-url="{{ route('tai-khoan.so-dia-chi.cap-nhat', $diaChi) }}"
                                data-recipient="{{ $diaChi->nguoi_nhan }}"
                                data-phone="{{ $diaChi->so_dien_thoai }}"
                                data-province="{{ $diaChi->tinh_thanh }}"
                                data-ward="{{ $diaChi->phuong_xa }}"
                                data-detail="{{ $diaChi->dia_chi }}"
                                data-latitude="{{ $diaChi->latitude }}"
                                data-longitude="{{ $diaChi->longitude }}"
                                data-default="{{ $diaChi->mac_dinh ? '1' : '0' }}"
                            >
                                <svg viewBox="0 0 24 24"><path d="m4 16-.8 4 4-.8L18 8.4 15.6 6 4 16Z"/><path d="m14 7.5 2.5 2.5"/></svg>
                                Chỉnh sửa
                            </button>

                            @unless($diaChi->mac_dinh)
                                <form method="POST" action="{{ route('tai-khoan.so-dia-chi.mac-dinh', $diaChi) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="address-action-button default">
                                        <svg viewBox="0 0 24 24"><path d="M12 3 5 6v5c0 4.7 2.9 8 7 10 4.1-2 7-5.3 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/></svg>
                                        Đặt mặc định
                                    </button>
                                </form>
                            @endunless

                            <form
                                method="POST"
                                action="{{ route('tai-khoan.so-dia-chi.xoa', $diaChi) }}"
                                data-delete-address-form
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="address-action-button delete">
                                    <svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/></svg>
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif
    </main>
</div>

@include('tai_khoan.components.address-modal')

@php
    $oldAddressPayload = [
        'address_id' => old('address_id'),
        'address_mode' => old('address_mode', 'manual'),
        'nguoi_nhan' => old('nguoi_nhan'),
        'so_dien_thoai' => old('so_dien_thoai'),
        'tinh_thanh' => old('tinh_thanh'),
        'phuong_xa' => old('phuong_xa'),
        'dia_chi' => old('dia_chi'),
        'latitude' => old('latitude'),
        'longitude' => old('longitude'),
        'mac_dinh' => old('mac_dinh'),
    ];
@endphp

<script>
window.GreenShopAddressBook = {
    oldHasErrors: {{ $errors->any() ? 'true' : 'false' }},
    createUrl: {{ Illuminate\Support\Js::from(route('tai-khoan.so-dia-chi.them')) }},
    pageUrl: {{ Illuminate\Support\Js::from(route('tai-khoan.so-dia-chi')) }},
    defaultRecipient: {{ Illuminate\Support\Js::from($nguoiDung->ho_ten ?? '') }},
    defaultPhone: {{ Illuminate\Support\Js::from($nguoiDung->so_dien_thoai ?? '') }},
    administrativeApiBase: 'https://provinces.open-api.vn/api/v2',
    old: {{ Illuminate\Support\Js::from($oldAddressPayload) }},
};
</script>
@vite('resources/js/customer/so-dia-chi.js')
</body>
</html>
