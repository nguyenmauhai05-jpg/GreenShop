<div class="address-modal <?php echo e(($checkoutAddressModal ?? false) ? (old('address_edit_context') === 'checkout' && $errors->any() ? 'open' : '') : ($errors->any() ? 'open' : '')); ?>" id="addressModal" aria-hidden="<?php echo e((($checkoutAddressModal ?? false) ? (old('address_edit_context') === 'checkout' && $errors->any()) : $errors->any()) ? 'false' : 'true'); ?>">
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
            action="<?php echo e(($checkoutAddressModal ?? false) ? '' : route('tai-khoan.so-dia-chi.them')); ?>"
            id="addressForm"
            class="address-form"
        >
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_method" id="addressMethod" value="<?php echo e(($checkoutAddressModal ?? false) ? 'PUT' : ''); ?>">
            <?php if($checkoutAddressModal ?? false): ?>
                <input type="hidden" name="return_to" value="checkout">
                <input type="hidden" name="address_edit_context" value="checkout">
            <?php endif; ?>
            <input type="hidden" name="address_id" id="address_id" value="<?php echo e(old('address_id')); ?>">
            <input type="hidden" name="address_mode" id="address_mode" value="map">
            <input type="hidden" name="latitude" id="address_latitude" value="<?php echo e(old('latitude')); ?>">
            <input type="hidden" name="longitude" id="address_longitude" value="<?php echo e(old('longitude')); ?>">

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
                            class="form-control <?php $__errorArgs = ['nguoi_nhan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            type="text"
                            id="address_recipient"
                            name="nguoi_nhan"
                            value="<?php echo e(old('nguoi_nhan')); ?>"
                            maxlength="100"
                            placeholder="Nhập tên người nhận"
                        >
                        <?php $__errorArgs = ['nguoi_nhan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label for="address_phone">Số điện thoại <span>*</span></label>
                        <input
                            class="form-control <?php $__errorArgs = ['so_dien_thoai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            type="text"
                            id="address_phone"
                            name="so_dien_thoai"
                            value="<?php echo e(old('so_dien_thoai')); ?>"
                            maxlength="20"
                            inputmode="tel"
                            placeholder="Nhập số điện thoại"
                        >
                        <?php $__errorArgs = ['so_dien_thoai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                            class="form-control <?php $__errorArgs = ['tinh_thanh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="address_province"
                            name="tinh_thanh"
                            data-current="<?php echo e(old('tinh_thanh')); ?>"
                        >
                            <option value="">Chọn tỉnh/thành phố</option>
                        </select>
                        <?php $__errorArgs = ['tinh_thanh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label for="address_ward">Phường/Xã <span>*</span></label>
                        <select
                            class="form-control <?php $__errorArgs = ['phuong_xa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="address_ward"
                            name="phuong_xa"
                            data-current="<?php echo e(old('phuong_xa')); ?>"
                        >
                            <option value="">Chọn phường/xã</option>
                        </select>
                        <?php $__errorArgs = ['phuong_xa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group form-group-full">
                        <label for="address_detail">Địa chỉ chi tiết <span>*</span></label>
                        <textarea
                            class="form-control textarea-control <?php $__errorArgs = ['dia_chi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="address_detail"
                            name="dia_chi"
                            rows="3"
                            maxlength="255"
                            placeholder="Số nhà, tên đường, tòa nhà..."
                        ><?php echo e(old('dia_chi')); ?></textarea>
                        <?php $__errorArgs = ['dia_chi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <label class="checkbox-label address-default-check">
                    <input type="hidden" name="mac_dinh" value="0">
                    <input type="checkbox" name="mac_dinh" id="address_default" value="1" <?php echo e(old('mac_dinh') ? 'checked' : ''); ?>>
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
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/tai_khoan/components/address-modal.blade.php ENDPATH**/ ?>