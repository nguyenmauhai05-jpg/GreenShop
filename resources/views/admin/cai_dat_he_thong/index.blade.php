@extends('admin.layouts.app')

@section('title', 'Cài đặt hệ thống - GreenShop Admin')
@section('page-title', 'Cài đặt hệ thống')

@section('styles')
    @vite('resources/css/admin/cai-dat-he-thong.css')
@endsection

@section('content')
@php
    $value = fn(string $key, mixed $fallback = '') => old($key, $settings[$key] ?? $fallback);
    $maintenanceOn = (bool) old('maintenance_mode', $settings['maintenance_mode'] ?? false);
    $mailAuthOn = (bool) old('mail_auth', $settings['mail_auth'] ?? false);
    $logoPath = $settings['logo'] ?? 'images/trang-chu/logo/logo.png';
@endphp

<div class="system-settings-page">

    @if(session('success'))
        <div class="settings-alert success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="settings-alert error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="settings-alert error">
            Dữ liệu chưa hợp lệ. Vui lòng kiểm tra các trường được đánh dấu bên dưới.
        </div>
    @endif

    <form
        id="systemSettingsForm"
        method="POST"
        action="{{ route('admin.cai-dat-he-thong.update') }}"
        enctype="multipart/form-data"
        novalidate
    >
        @csrf
        @method('PUT')

        {{-- THÔNG TIN CỬA HÀNG --}}
        <section class="settings-card">
            <div class="settings-card-title">
                <span class="settings-section-icon">▣</span>
                <div>
                    <h3>Thông tin cửa hàng</h3>
                    <p>Cập nhật thông tin nhận diện và liên hệ của GreenShop.</p>
                </div>
            </div>

            <div class="store-settings-grid">
                <div class="settings-fields">
                    <div class="settings-form-grid">
                        <div class="settings-field">
                            <label for="store_name">Tên cửa hàng <b>*</b></label>
                            <input id="store_name" name="store_name" type="text" autocomplete="off"
                                   value="{{ $value('store_name') }}"
                                   class="@error('store_name') invalid @enderror">
                            @error('store_name')<small class="field-error">{{ $message }}</small>@enderror<small id="storeNameClientError" class="field-error client-field-error"></small>
                        </div>

                        <div class="settings-field">
                            <label for="contact_email">Email liên hệ <b>*</b></label>
                            <input id="contact_email" name="contact_email" type="email" maxlength="255"
                                   value="{{ $value('contact_email') }}"
                                   class="@error('contact_email') invalid @enderror">
                            @error('contact_email')<small class="field-error">{{ $message }}</small>@enderror<small id="contactEmailClientError" class="field-error client-field-error"></small>
                        </div>

                        <div class="settings-field">
                            <label for="phone">Số điện thoại <b>*</b></label>
                            <input id="phone" name="phone" type="text" maxlength="10" inputmode="numeric" autocomplete="off"
                                   value="{{ $value('phone') }}"
                                   class="@error('phone') invalid @enderror">
                            @error('phone')<small class="field-error">{{ $message }}</small>@enderror<small id="phoneClientError" class="field-error client-field-error"></small>
                        </div>

                        <div class="settings-field full">
                            <label for="address">Địa chỉ <b>*</b></label>
                            <input id="address" name="address" type="text" maxlength="255"
                                   value="{{ $value('address') }}"
                                   class="@error('address') invalid @enderror">
                            @error('address')<small class="field-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="settings-field full store-map-setting">
                            <div class="store-map-heading">
                                <div>
                                    <label>Vị trí cửa hàng trên bản đồ <b>*</b></label>
                                    <small>Nhập địa chỉ cửa hàng thủ công; dịch vụ vận chuyển hiện tại có thể xác định tọa độ từ địa chỉ.</small>
                                </div>
                                <button type="button" id="findStoreOnMap" class="map-locate-button">⌖ Xem Google Maps</button>
                            </div>

                            <input type="hidden" id="store_latitude" name="store_latitude" value="{{ $value('store_latitude') }}">
                            <input type="hidden" id="store_longitude" name="store_longitude" value="{{ $value('store_longitude') }}">

                            <div
                                id="storeLocationMap"
                                class="store-location-map"
                                data-geocode-url="{{ route('map.geocode') }}"
                            ></div>
                            <small id="storeMapStatus" class="map-status">Nhập địa chỉ thủ công rồi bấm “Xem Google Maps”. Bản đồ nhúng không hỗ trợ lấy tọa độ bằng cách bấm.</small>
                            <small class="map-coordinate">Tọa độ: <span id="storeCoordinateText">{{ $value('store_latitude') && $value('store_longitude') ? $value('store_latitude') . ', ' . $value('store_longitude') : 'Chưa chọn' }}</span></small>
                            @error('store_latitude')<small class="field-error">{{ $message }}</small>@enderror
                            @error('store_longitude')<small class="field-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="settings-field full">
                            <label for="description">Mô tả cửa hàng</label>
                            <textarea id="description" name="description"
                                      class="@error('description') invalid @enderror">{{ $value('description') }}</textarea>
                            <div class="field-meta">
                                <span>@error('description')<small class="field-error">{{ $message }}</small>@enderror<small id="descriptionClientError" class="field-error client-field-error"></small></span>
                                <small><span id="descriptionCount">{{ mb_strlen($value('description')) }}</span>/500</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="logo-settings">
                    <label>Logo cửa hàng</label>
                    <div class="logo-preview" id="logoPreview">
                        <img src="{{ asset($logoPath) }}" alt="{{ $value('store_name', 'GreenShop') }}" id="logoPreviewImage">
                    </div>

                    <input type="file" name="logo" id="logoInput" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" hidden>
                    <button type="button" class="change-logo-btn" id="changeLogoButton">⇩ Thay đổi logo</button>
                    <small>JPG, JPEG, PNG hoặc WEBP · Tối đa 2 MB</small>
                    @error('logo')<small class="field-error">{{ $message }}</small>@enderror
                </div>
            </div>
        </section>

        {{-- EMAIL HỆ THỐNG --}}
        <section class="settings-card">
            <div class="settings-card-title">
                <span class="settings-section-icon">✉</span>
                <div>
                    <h3>Email hệ thống</h3>
                    <p>Cấu hình thông tin gửi email của hệ thống. Mật khẩu SMTP tiếp tục lấy từ biến môi trường và không hiển thị tại đây.</p>
                </div>
            </div>

            <div class="settings-form-grid four">
                <div class="settings-field">
                    <label for="mail_driver">Mail Driver <b>*</b></label>
                    <select id="mail_driver" name="mail_driver">
                        @foreach($mailDriverOptions as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}" @selected($value('mail_driver') === $optionValue)>
                                {{ $optionLabel }}
                            </option>
                        @endforeach
                    </select>
                    @error('mail_driver')<small class="field-error">{{ $message }}</small>@enderror
                </div>

                <div class="settings-field">
                    <label for="mail_host">Host <b>*</b></label>
                    <input id="mail_host" name="mail_host" type="text" autocomplete="off"
                           value="{{ $value('mail_host') }}"
                           class="@error('mail_host') invalid @enderror">
                    @error('mail_host')<small class="field-error">{{ $message }}</small>@enderror<small id="mailHostClientError" class="field-error client-field-error"></small>
                </div>

                <div class="settings-field">
                    <label for="mail_port">Port <b>*</b></label>
                    <input id="mail_port" name="mail_port" type="number" min="1" max="65535"
                           value="{{ $value('mail_port') }}"
                           class="@error('mail_port') invalid @enderror">
                    @error('mail_port')<small class="field-error">{{ $message }}</small>@enderror
                </div>

                <div class="settings-field">
                    <label for="mail_from">Email gửi từ <b>*</b></label>
                    <input id="mail_from" name="mail_from" type="email" maxlength="255"
                           value="{{ $value('mail_from') }}"
                           class="@error('mail_from') invalid @enderror">
                    @error('mail_from')<small class="field-error">{{ $message }}</small>@enderror
                </div>
            </div>

            <div class="settings-toggle-row">
                <div>
                    <strong>Sử dụng xác thực</strong>
                    <small>Bật nếu máy chủ SMTP yêu cầu xác thực. Username/password vẫn lấy từ .env và không được hiển thị dạng văn bản rõ.</small>
                </div>

                <label class="switch">
                    <input type="hidden" name="mail_auth" value="0">
                    <input type="checkbox" name="mail_auth" value="1" @checked($mailAuthOn)>
                    <span class="switch-slider"></span>
                </label>
            </div>
        </section>

        {{-- BẢO TRÌ --}}
        <section class="settings-card maintenance-card">
            <div class="settings-card-title">
                <span class="settings-section-icon">🛠</span>
                <div>
                    <h3>Bảo trì hệ thống</h3>
                    <p>Khi bật, khách hàng sẽ tạm thời không truy cập được website. Khu vực Admin vẫn hoạt động để có thể tắt bảo trì.</p>
                </div>
            </div>

            <div class="settings-toggle-row maintenance">
                <div>
                    <strong>Chế độ bảo trì</strong>
                    <small>Chỉ Admin được phép bật hoặc tắt.</small>
                </div>

                <label class="switch">
                    <input type="hidden" name="maintenance_mode" value="0">
                    <input type="checkbox" name="maintenance_mode" id="maintenanceMode" value="1" @checked($maintenanceOn)>
                    <span class="switch-slider"></span>
                </label>
            </div>

            <label class="maintenance-confirm" id="maintenanceConfirmRow" @if(!$maintenanceOn) hidden @endif>
                <input type="checkbox" name="confirm_maintenance" value="1" @checked(old('confirm_maintenance'))>
                <span>Tôi xác nhận muốn bật chế độ bảo trì và tạm dừng truy cập của khách hàng.</span>
            </label>

            @error('confirm_maintenance')
                <small class="field-error maintenance-error">{{ $message }}</small>
            @enderror
        </section>

        <div class="settings-bottom-actions">
            <a href="{{ route('admin.dashboard') }}" class="settings-cancel">Hủy</a>
            <button type="submit" class="settings-save-bottom">▣ Lưu thay đổi</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
@vite('resources/js/admin/cai-dat-he-thong.js')
@endsection
