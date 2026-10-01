<div class="address-modal {{ ($checkoutAddressModal ?? false) ? (old('address_edit_context') === 'checkout' && $errors->any() ? 'open' : '') : ($errors->any() ? 'open' : '') }}" id="addressModal" aria-hidden="{{ (($checkoutAddressModal ?? false) ? (old('address_edit_context') === 'checkout' && $errors->any()) : $errors->any()) ? 'false' : 'true' }}">
    <div class="address-modal-backdrop" data-close-address-modal></div>

    <div class="address-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="addressModalTitle">
        <div class="address-modal-header">
            <div>
                <h2 id="addressModalTitle">Thêm địa chỉ mới</h2>
                <p>Nhập thông tin địa chỉ và xem vị trí trực tiếp trên Google Maps.</p>
            </div>
            <button type="button" class="address-modal-close" data-close-address-modal aria-label="Đóng">×</button>
        </div>

        <form
            method="POST"
            action="{{ ($checkoutAddressModal ?? false) ? '' : route('tai-khoan.so-dia-chi.them') }}"
            id="addressForm"
            class="address-form"
        >
            @csrf
            <input type="hidden" name="_method" id="addressMethod" value="{{ ($checkoutAddressModal ?? false) ? 'PUT' : '' }}">
            @if($checkoutAddressModal ?? false)
                <input type="hidden" name="return_to" value="checkout">
                <input type="hidden" name="address_edit_context" value="checkout">
            @endif
            <input type="hidden" name="address_id" id="address_id" value="{{ old('address_id') }}">
            <input type="hidden" name="address_mode" id="address_mode" value="map">
            <input type="hidden" name="latitude" id="address_latitude" value="{{ old('latitude') }}">
            <input type="hidden" name="longitude" id="address_longitude" value="{{ old('longitude') }}">

            <div class="address-mode-switch" role="tablist" aria-label="Google Maps">
                <button type="button" class="address-mode-button active" data-address-mode="map" aria-selected="true">
                    <svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    Xem Google Maps
                </button>
            </div>

            <div class="address-form-body">
                <div class="address-form-grid">
                    <div class="form-group">
                        <label for="address_recipient">Họ và tên người nhận <span>*</span></label>
                        <input
                            class="form-control @error('nguoi_nhan') is-invalid @enderror"
                            type="text"
                            id="address_recipient"
                            name="nguoi_nhan"
                            value="{{ old('nguoi_nhan') }}"
                            maxlength="100"
                            placeholder="Nhập tên người nhận"
                        >
                        @error('nguoi_nhan')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="address_phone">Số điện thoại <span>*</span></label>
                        <input
                            class="form-control @error('so_dien_thoai') is-invalid @enderror"
                            type="text"
                            id="address_phone"
                            name="so_dien_thoai"
                            value="{{ old('so_dien_thoai') }}"
                            maxlength="20"
                            inputmode="tel"
                            placeholder="Nhập số điện thoại"
                        >
                        @error('so_dien_thoai')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <section class="address-map-panel" id="addressMapPanel">
                    <div class="address-map-toolbar">
                        <div>
                            <strong>Xem vị trí giao hàng trên Google Maps</strong>
                            <span>Nhập địa chỉ thủ công ở dưới, sau đó xem trên Google Maps. Không thể đặt ghim trực tiếp trong bản đồ nhúng.</span>
                        </div>
                        <div class="address-map-actions">
                            <button type="button" class="btn btn-outline" id="addressUseCurrentLocation">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="8"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>
                                Vị trí hiện tại
                            </button>
                            <button type="button" class="btn btn-outline" id="addressFindOnMap">
                                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                                Xem trên Google Maps
                            </button>
                        </div>
                    </div>

                    <div class="address-map" id="addressBookMap"></div>
                    <div class="address-map-status" id="addressMapStatus">Nhập địa chỉ thủ công để xem Google Maps.</div>
                </section>

                <div class="address-form-grid address-location-fields">
                    <div class="form-group">
                        <label for="address_province">Tỉnh/Thành phố <span>*</span></label>
                        <select
                            class="form-control @error('tinh_thanh') is-invalid @enderror"
                            id="address_province"
                            name="tinh_thanh"
                            data-current="{{ old('tinh_thanh') }}"
                        >
                            <option value="">Chọn tỉnh/thành phố</option>
                        </select>
                        @error('tinh_thanh')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="address_ward">Phường/Xã <span>*</span></label>
                        <select
                            class="form-control @error('phuong_xa') is-invalid @enderror"
                            id="address_ward"
                            name="phuong_xa"
                            data-current="{{ old('phuong_xa') }}"
                        >
                            <option value="">Chọn phường/xã</option>
                        </select>
                        @error('phuong_xa')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group form-group-full">
                        <label for="address_detail">Địa chỉ chi tiết <span>*</span></label>
                        <textarea
                            class="form-control textarea-control @error('dia_chi') is-invalid @enderror"
                            id="address_detail"
                            name="dia_chi"
                            rows="3"
                            maxlength="255"
                            placeholder="Số nhà, tên đường, tòa nhà..."
                        >{{ old('dia_chi') }}</textarea>
                        @error('dia_chi')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <label class="checkbox-label address-default-check">
                    <input type="hidden" name="mac_dinh" value="0">
                    <input type="checkbox" name="mac_dinh" id="address_default" value="1" {{ old('mac_dinh') ? 'checked' : '' }}>
                    <span class="custom-checkbox">
                        <svg viewBox="0 0 24 24"><path d="m5 12 4 4 10-10"/></svg>
                    </span>
                    <span>Đặt làm địa chỉ mặc định</span>
                </label>
            </div>

            <div class="address-modal-footer">
                <button type="button" class="btn btn-secondary" data-close-address-modal>Hủy</button>
                <button type="submit" class="btn btn-primary" id="saveAddressButton">
                    <svg viewBox="0 0 24 24"><path d="M5 4h12l2 2v14H5V4Z"/><path d="M8 4v6h8V4M8 20v-6h8v6"/></svg>
                    Lưu địa chỉ
                </button>
            </div>
        </form>
    </div>
</div>
