<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ cá nhân - GreenShop</title>
    @vite(['resources/css/customer/site-shell.css'])
    @vite('resources/css/customer/ho-so.css')
</head>
<body>
@php
    $avatar = $nguoiDung->anh_dai_dien
        ? (str_starts_with($nguoiDung->anh_dai_dien, 'http://') || str_starts_with($nguoiDung->anh_dai_dien, 'https://')
            ? $nguoiDung->anh_dai_dien
            : asset($nguoiDung->anh_dai_dien))
        : null;

    $initial = mb_strtoupper(mb_substr(trim($nguoiDung->ho_ten ?: 'G'), 0, 1));
@endphp

{{-- Dùng CHÍNH header dùng chung của trang chủ để mọi trang đồng bộ tuyệt đối --}}
@include('trang_chu.components.header')

<div class="account-layout">
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    @include('tai_khoan.components.account-sidebar', ['activeSidebar' => 'profile', 'sidebarId' => 'accountSidebar'])

    <main class="account-main">
        <div class="page-heading">
            <button class="profile-mobile-menu-button" type="button" id="mobileMenuButton" aria-label="Mở menu tài khoản">
                <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
            <div>
                <h1>Hồ sơ cá nhân</h1>
                <p>Quản lý thông tin hồ sơ và địa chỉ tài khoản</p>
            </div>
            <span class="security-pill">
                <svg viewBox="0 0 24 24"><path d="M12 3 5 6v5c0 4.7 2.9 8 7 10 4.1-2 7-5.3 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/></svg>
                Thông tin được bảo mật
            </span>
        </div>

        @if(session('success'))
            <div class="toast toast-success" id="pageToast" role="status">
                <div class="toast-icon">
                    <svg viewBox="0 0 24 24"><path d="m5 12 4 4 10-10"/></svg>
                </div>
                <div>
                    <strong>Cập nhật hồ sơ thành công!</strong>
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

        <form
            action="{{ route('tai-khoan.ho-so.cap-nhat') }}"
            method="POST"
            enctype="multipart/form-data"
            class="profile-card"
            id="profileForm"
            data-editing="{{ $errors->any() ? '1' : '0' }}"
        >
            @csrf
            @method('PUT')
            <input type="hidden" name="address_id" value="{{ old('address_id', $diaChi?->address_id) }}">
            {{-- GreenShop dùng mô hình địa chỉ 2 cấp trên hồ sơ: Tỉnh/Thành phố + Phường/Xã.
                 Giữ input ẩn rỗng để tương thích CSDL cũ còn cột quan_huyen. --}}
            <input type="hidden" name="quan_huyen" value="">
            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $diaChi?->latitude) }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $diaChi?->longitude) }}">

            <section class="avatar-column">
                <h2>Ảnh đại diện</h2>
                <p class="section-help">Ảnh hồ sơ của bạn</p>

                <div class="avatar-preview" id="avatarPreview">
                    @if($avatar)
                        <img src="{{ $avatar }}" alt="Ảnh đại diện hiện tại" id="avatarImage">
                        <span class="avatar-fallback" id="avatarFallback">{{ $initial }}</span>
                    @else
                        <img src="" alt="Ảnh đại diện" id="avatarImage" hidden>
                        <span class="avatar-fallback" id="avatarFallback">{{ $initial }}</span>
                    @endif
                    <span class="avatar-status">
                        <svg viewBox="0 0 24 24"><path d="m5 12 4 4 10-10"/></svg>
                    </span>
                </div>

                <input
                    type="file"
                    name="anh_dai_dien"
                    id="avatarInput"
                    accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                    class="sr-only"
                >
                <button type="button" class="btn btn-outline avatar-button" id="chooseAvatarButton" data-edit-action disabled>
                    <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Chọn ảnh
                </button>
                <p class="avatar-note">JPG, JPEG hoặc PNG · Tối đa 2 MB</p>
                @error('anh_dai_dien')
                    <div class="field-error avatar-error">{{ $message }}</div>
                @enderror
            </section>

            <section class="profile-fields" id="addressSection">
                <div class="section-heading-row">
                    <div>
                        <h2>Thông tin cá nhân</h2>
                        <p class="section-help">Cập nhật thông tin liên hệ và địa chỉ giao hàng theo Tỉnh/Thành phố và Phường/Xã</p>
                    </div>
                    <button type="button" class="btn btn-edit" id="editButton">
                        <svg viewBox="0 0 24 24"><path d="m4 16-.8 4 4-.8L18 8.4 15.6 6 4 16Z"/><path d="m14 7.5 2.5 2.5"/></svg>
                        Chỉnh sửa
                    </button>
                </div>

                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="ho_ten">Họ và tên <span>*</span></label>
                        <input
                            class="form-control @error('ho_ten') is-invalid @enderror"
                            type="text"
                            id="ho_ten"
                            name="ho_ten"
                            value="{{ old('ho_ten', $nguoiDung->ho_ten) }}"
                            placeholder="Nhập họ và tên"
                            data-edit-control
                            @disabled(!$errors->any())
                        >
                        @error('ho_ten')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group form-group-full">
                        <label for="email">Email</label>
                        <input class="form-control readonly-control" type="email" id="email" value="{{ $nguoiDung->email }}" readonly>
                        <small class="field-note">Email được liên kết với tài khoản và không thể chỉnh sửa tại đây.</small>
                    </div>

                    <div class="form-group form-group-full">
                        <label for="so_dien_thoai">Số điện thoại <span>*</span></label>
                        <input
                            class="form-control @error('so_dien_thoai') is-invalid @enderror"
                            type="text"
                            id="so_dien_thoai"
                            name="so_dien_thoai"
                            value="{{ old('so_dien_thoai', $nguoiDung->so_dien_thoai) }}"
                            placeholder="Nhập số điện thoại"
                            maxlength="20"
                            inputmode="tel"
                            data-edit-control
                            @disabled(!$errors->any())
                        >
                        @error('so_dien_thoai')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group form-group-full">
                        <label for="tinh_thanh">Tỉnh/Thành phố <span>*</span></label>
                        <select
                            class="form-control @error('tinh_thanh') is-invalid @enderror"
                            id="tinh_thanh"
                            name="tinh_thanh"
                            data-current="{{ old('tinh_thanh', $diaChi?->tinh_thanh) }}"
                            data-edit-control
                            @disabled(!$errors->any())
                        >
                            <option value="">Chọn tỉnh/thành phố</option>
                        </select>
                        @error('tinh_thanh')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group form-group-full">
                        <label for="phuong_xa">Phường/Xã <span>*</span></label>
                        <select
                            class="form-control @error('phuong_xa') is-invalid @enderror"
                            id="phuong_xa"
                            name="phuong_xa"
                            data-current="{{ old('phuong_xa', $diaChi?->phuong_xa) }}"
                            data-edit-control
                            @disabled(!$errors->any())
                        >
                            <option value="">Chọn phường/xã</option>
                        </select>
                        @error('phuong_xa')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group form-group-full">
                        <label for="dia_chi">Địa chỉ chi tiết <span>*</span></label>
                        <textarea
                            class="form-control textarea-control @error('dia_chi') is-invalid @enderror"
                            id="dia_chi"
                            name="dia_chi"
                            placeholder="Nhập địa chỉ chi tiết"
                            rows="3"
                            data-edit-control
                            @disabled(!$errors->any())
                        >{{ old('dia_chi', $diaChi?->dia_chi) }}</textarea>
                        @error('dia_chi')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group form-group-full">
                        <div class="map-location-card">
                            <div class="map-location-heading">
                                <div>
                                    <label class="map-location-title">Định vị địa chỉ trên bản đồ</label>
                                    <p class="map-location-help">
                                        Nhập địa chỉ thủ công rồi xem trên Google Maps. Có thể mở Google Maps để kiểm tra hoặc chỉ đường.
                                    </p>
                                </div>
                                <span class="map-location-badge" id="mapLocationBadge">Chưa định vị</span>
                            </div>

                            <div class="map-location-actions">
                                <button type="button" class="btn btn-map-action" id="useCurrentLocationButton" data-map-edit-action disabled>
                                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="8"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>
                                    Dùng vị trí hiện tại
                                </button>
                                <button type="button" class="btn btn-map-action" id="findAddressOnMapButton" data-map-edit-action disabled>
                                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                                    Xem trên Google Maps
                                </button>
                            </div>

                            <div class="profile-map" id="profileMap" aria-label="Bản đồ chọn vị trí giao hàng"></div>
                            <div class="map-location-status" id="mapLocationStatus">
                                @if($diaChi?->latitude && $diaChi?->longitude)
                                    Vị trí đã lưu: {{ number_format((float) $diaChi->latitude, 6) }}, {{ number_format((float) $diaChi->longitude, 6) }}
                                @else
                                    Nhập địa chỉ thủ công để xem trước trên Google Maps (không cần API key).
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group form-group-full checkbox-row">
                        <label class="checkbox-label">
                            <input type="hidden" name="mac_dinh" value="0">
                            <input
                                type="checkbox"
                                name="mac_dinh"
                                value="1"
                                {{ old('mac_dinh', (bool) $diaChi?->mac_dinh) ? 'checked' : '' }}
                                data-edit-control
                                @disabled(!$errors->any())
                            >
                            <span class="custom-checkbox">
                                <svg viewBox="0 0 24 24"><path d="m5 12 4 4 10-10"/></svg>
                            </span>
                            <span>Sử dụng địa chỉ này làm mặc định</span>
                        </label>
                    </div>
                </div>

                <div class="form-actions" id="formActions" @if(!$errors->any()) hidden @endif>
                    <button type="button" class="btn btn-secondary" id="cancelButton">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24"><path d="M5 4h12l2 2v14H5V4Z"/><path d="M8 4v6h8V4M8 20v-6h8v6"/></svg>
                        Lưu thay đổi
                    </button>
                </div>
            </section>
        </form>
    </main>
</div>

<div class="confirm-modal" id="cancelModal" aria-hidden="true">
    <div class="confirm-modal__backdrop"></div>
    <div class="confirm-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="cancelModalTitle">
        <div class="modal-icon">
            <svg viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01"/><path d="M10.3 4.5 2.9 17.3A2 2 0 0 0 4.6 20h14.8a2 2 0 0 0 1.7-2.7L13.7 4.5a2 2 0 0 0-3.4 0Z"/></svg>
        </div>
        <h2 id="cancelModalTitle">Hủy các thay đổi?</h2>
        <p>Các thay đổi chưa được lưu. Bạn có chắc chắn muốn hủy?</p>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" id="keepEditingButton">Tiếp tục chỉnh sửa</button>
            <button type="button" class="btn btn-danger" id="confirmCancelButton">Hủy thay đổi</button>
        </div>
    </div>
</div>

<script>
    window.GreenShopProfile = {
        oldHasErrors: {{ $errors->any() ? 'true' : 'false' }},
        administrativeApiBase: 'https://provinces.open-api.vn/api/v2',
        profileUrl: @json(route('tai-khoan.ho-so')),
        locationData: {
            "Hà Nội": [
                "Cống Vị", "Điện Biên", "Giảng Võ", "Kim Mã", "Liễu Giai", "Ngọc Hà", "Quán Thánh", "Trúc Bạch",
                "Dịch Vọng", "Dịch Vọng Hậu", "Mai Dịch", "Nghĩa Đô", "Nghĩa Tân", "Quan Hoa", "Trung Hòa", "Yên Hòa",
                "Cát Linh", "Hàng Bột", "Khâm Thiên", "Láng Hạ", "Láng Thượng", "Nam Đồng", "Ô Chợ Dừa", "Thổ Quan", "Trung Liệt",
                "Bạch Đằng", "Bách Khoa", "Bạch Mai", "Đồng Nhân", "Lê Đại Hành", "Minh Khai", "Phố Huế", "Quỳnh Mai", "Thanh Nhàn", "Vĩnh Tuy",
                "Chương Dương", "Cửa Đông", "Cửa Nam", "Đồng Xuân", "Hàng Bạc", "Hàng Bài", "Hàng Bồ", "Hàng Buồm", "Hàng Gai", "Tràng Tiền",
                "Biên Giang", "Dương Nội", "Hà Cầu", "Kiến Hưng", "La Khê", "Mộ Lao", "Nguyễn Trãi", "Phú La", "Văn Quán",
                "Cầu Diễn", "Đại Mỗ", "Mễ Trì", "Mỹ Đình 1", "Mỹ Đình 2", "Phú Đô", "Phương Canh", "Tây Mỗ", "Trung Văn", "Xuân Phương",
                "Hạ Đình", "Khương Đình", "Khương Mai", "Khương Trung", "Nhân Chính", "Phương Liệt", "Thanh Xuân Bắc", "Thanh Xuân Nam", "Thanh Xuân Trung", "Thượng Đình"
            ],
            "TP. Hồ Chí Minh": [
                "Bến Nghé", "Bến Thành", "Cầu Kho", "Cầu Ông Lãnh", "Đa Kao", "Nguyễn Cư Trinh", "Nguyễn Thái Bình", "Tân Định",
                "Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10",
                "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15", "Phường 16", "Phường 17", "Phường 19", "Phường 21", "Phường 22", "Phường 24", "Phường 25", "Phường 26", "Phường 27", "Phường 28",
                "Võ Thị Sáu", "An Khánh", "An Lợi Đông", "An Phú", "Bình Chiểu", "Bình Thọ", "Cát Lái", "Hiệp Bình Chánh", "Hiệp Bình Phước", "Linh Chiểu", "Linh Đông", "Linh Tây", "Linh Trung", "Linh Xuân", "Phước Long A", "Phước Long B", "Thảo Điền", "Thủ Thiêm", "Trường Thọ"
            ],
            "Đà Nẵng": [
                "Bình Hiên", "Bình Thuận", "Hải Châu I", "Hải Châu II", "Hòa Cường Bắc", "Hòa Cường Nam", "Nam Dương", "Phước Ninh", "Thạch Thang", "Thanh Bình",
                "An Hải Bắc", "An Hải Đông", "An Hải Tây", "Mân Thái", "Nại Hiên Đông", "Phước Mỹ", "Thọ Quang", "Hòa Hải", "Hòa Quý", "Khuê Mỹ", "Mỹ An"
            ]
        }
    };
</script>
@vite('resources/js/customer/ho-so.js')
</body>
</html>
