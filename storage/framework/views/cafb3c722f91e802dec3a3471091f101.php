<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ cá nhân - GreenShop</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/customer/site-shell.css']); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/customer/ho-so.css'); ?>
</head>
<body>
<?php
    $avatar = $nguoiDung->anh_dai_dien
        ? (str_starts_with($nguoiDung->anh_dai_dien, 'http://') || str_starts_with($nguoiDung->anh_dai_dien, 'https://')
            ? $nguoiDung->anh_dai_dien
            : asset($nguoiDung->anh_dai_dien))
        : null;

    $initial = mb_strtoupper(mb_substr(trim($nguoiDung->ho_ten ?: 'G'), 0, 1));
?>


<?php echo $__env->make('trang_chu.components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="account-layout">
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <?php echo $__env->make('tai_khoan.components.account-sidebar', ['activeSidebar' => 'profile', 'sidebarId' => 'accountSidebar'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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

        <?php if(session('success')): ?>
            <div class="toast toast-success" id="pageToast" role="status">
                <div class="toast-icon">
                    <svg viewBox="0 0 24 24"><path d="m5 12 4 4 10-10"/></svg>
                </div>
                <div>
                    <strong>Cập nhật hồ sơ thành công!</strong>
                    <span><?php echo e(session('success')); ?></span>
                </div>
                <button type="button" class="toast-close" aria-label="Đóng">×</button>
            </div>
        <?php elseif(session('error')): ?>
            <div class="toast toast-error" id="pageToast" role="alert">
                <div class="toast-icon">!</div>
                <div>
                    <strong>Có lỗi xảy ra</strong>
                    <span><?php echo e(session('error')); ?></span>
                </div>
                <button type="button" class="toast-close" aria-label="Đóng">×</button>
            </div>
        <?php endif; ?>

        <form
            action="<?php echo e(route('tai-khoan.ho-so.cap-nhat')); ?>"
            method="POST"
            enctype="multipart/form-data"
            class="profile-card"
            id="profileForm"
            data-editing="<?php echo e($errors->any() ? '1' : '0'); ?>"
        >
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <input type="hidden" name="address_id" value="<?php echo e(old('address_id', $diaChi?->address_id)); ?>">
            
            <input type="hidden" name="quan_huyen" value="">
            <input type="hidden" name="latitude" id="latitude" value="<?php echo e(old('latitude', $diaChi?->latitude)); ?>">
            <input type="hidden" name="longitude" id="longitude" value="<?php echo e(old('longitude', $diaChi?->longitude)); ?>">

            <section class="avatar-column">
                <h2>Ảnh đại diện</h2>
                <p class="section-help">Ảnh hồ sơ của bạn</p>

                <div class="avatar-preview" id="avatarPreview">
                    <?php if($avatar): ?>
                        <img src="<?php echo e($avatar); ?>" alt="Ảnh đại diện hiện tại" id="avatarImage">
                        <span class="avatar-fallback" id="avatarFallback"><?php echo e($initial); ?></span>
                    <?php else: ?>
                        <img src="" alt="Ảnh đại diện" id="avatarImage" hidden>
                        <span class="avatar-fallback" id="avatarFallback"><?php echo e($initial); ?></span>
                    <?php endif; ?>
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
                <?php $__errorArgs = ['anh_dai_dien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="field-error avatar-error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                            class="form-control <?php $__errorArgs = ['ho_ten'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            type="text"
                            id="ho_ten"
                            name="ho_ten"
                            value="<?php echo e(old('ho_ten', $nguoiDung->ho_ten)); ?>"
                            placeholder="Nhập họ và tên"
                            data-edit-control
                            <?php if(!$errors->any()): echo 'disabled'; endif; ?>
                        >
                        <?php $__errorArgs = ['ho_ten'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group form-group-full">
                        <label for="email">Email</label>
                        <input class="form-control readonly-control" type="email" id="email" value="<?php echo e($nguoiDung->email); ?>" readonly>
                        <small class="field-note">Email được liên kết với tài khoản và không thể chỉnh sửa tại đây.</small>
                    </div>

                    <div class="form-group form-group-full">
                        <label for="so_dien_thoai">Số điện thoại <span>*</span></label>
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
                            id="so_dien_thoai"
                            name="so_dien_thoai"
                            value="<?php echo e(old('so_dien_thoai', $nguoiDung->so_dien_thoai)); ?>"
                            placeholder="Nhập số điện thoại"
                            maxlength="20"
                            inputmode="tel"
                            data-edit-control
                            <?php if(!$errors->any()): echo 'disabled'; endif; ?>
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

                    <div class="form-group form-group-full">
                        <label for="tinh_thanh">Tỉnh/Thành phố <span>*</span></label>
                        <select
                            class="form-control <?php $__errorArgs = ['tinh_thanh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="tinh_thanh"
                            name="tinh_thanh"
                            data-current="<?php echo e(old('tinh_thanh', $diaChi?->tinh_thanh)); ?>"
                            data-edit-control
                            <?php if(!$errors->any()): echo 'disabled'; endif; ?>
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

                    <div class="form-group form-group-full">
                        <label for="phuong_xa">Phường/Xã <span>*</span></label>
                        <select
                            class="form-control <?php $__errorArgs = ['phuong_xa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="phuong_xa"
                            name="phuong_xa"
                            data-current="<?php echo e(old('phuong_xa', $diaChi?->phuong_xa)); ?>"
                            data-edit-control
                            <?php if(!$errors->any()): echo 'disabled'; endif; ?>
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
                        <label for="dia_chi">Địa chỉ chi tiết <span>*</span></label>
                        <textarea
                            class="form-control textarea-control <?php $__errorArgs = ['dia_chi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="dia_chi"
                            name="dia_chi"
                            placeholder="Nhập địa chỉ chi tiết"
                            rows="3"
                            data-edit-control
                            <?php if(!$errors->any()): echo 'disabled'; endif; ?>
                        ><?php echo e(old('dia_chi', $diaChi?->dia_chi)); ?></textarea>
                        <?php $__errorArgs = ['dia_chi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                <?php if($diaChi?->latitude && $diaChi?->longitude): ?>
                                    Vị trí đã lưu: <?php echo e(number_format((float) $diaChi->latitude, 6)); ?>, <?php echo e(number_format((float) $diaChi->longitude, 6)); ?>

                                <?php else: ?>
                                    Nhập địa chỉ thủ công để xem trước trên Google Maps (không cần API key).
                                <?php endif; ?>
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
                                <?php echo e(old('mac_dinh', (bool) $diaChi?->mac_dinh) ? 'checked' : ''); ?>

                                data-edit-control
                                <?php if(!$errors->any()): echo 'disabled'; endif; ?>
                            >
                            <span class="custom-checkbox">
                                <svg viewBox="0 0 24 24"><path d="m5 12 4 4 10-10"/></svg>
                            </span>
                            <span>Sử dụng địa chỉ này làm mặc định</span>
                        </label>
                    </div>
                </div>

                <div class="form-actions" id="formActions" <?php if(!$errors->any()): ?> hidden <?php endif; ?>>
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
        oldHasErrors: <?php echo e($errors->any() ? 'true' : 'false'); ?>,
        administrativeApiBase: 'https://provinces.open-api.vn/api/v2',
        profileUrl: <?php echo json_encode(route('tai-khoan.ho-so'), 15, 512) ?>,
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
<?php echo app('Illuminate\Foundation\Vite')('resources/js/customer/ho-so.js'); ?>
</body>
</html>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/tai_khoan/ho_so.blade.php ENDPATH**/ ?>