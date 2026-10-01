<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viết đánh giá - GreenShop</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/customer/site-shell.css']); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/customer/danh-gia.css'); ?>
</head>
<body>
<?php echo $__env->make('trang_chu.components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="review-layout">
    <aside class="review-sidebar">
        <nav class="review-side-nav">
            <a href="<?php echo e(route('trang-chu')); ?>" class="review-side-link">
                <svg class="review-side-icon" viewBox="0 0 24 24"><path d="m3 10 9-7 9 7v10H5V10"/><path d="M9 20v-6h6v6"/></svg>
                <span>Trang chủ</span>
            </a>
            <a href="<?php echo e(route('cua-hang')); ?>" class="review-side-link">
                <svg class="review-side-icon" viewBox="0 0 24 24"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg>
                <span>Cây cảnh</span>
            </a>
            <a href="<?php echo e(route('gio-hang')); ?>" class="review-side-link">
                <svg class="review-side-icon" viewBox="0 0 24 24"><path d="M3 4h2l2.2 11h11.3L21 7H6"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                <span>Giỏ hàng</span>
            </a>
        </nav>

        <div class="review-side-title">TÀI KHOẢN</div>

        <nav class="review-side-nav">
            <a href="<?php echo e(route('tai-khoan.ho-so')); ?>" class="review-side-link">
                <svg class="review-side-icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.6 3.6-7 8-7s8 2.4 8 7"/></svg>
                <span>Thông tin tài khoản</span>
            </a>
            <a href="<?php echo e(route('tai-khoan.ho-so')); ?>#addressSection" class="review-side-link">
                <svg class="review-side-icon" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                <span>Sổ địa chỉ</span>
            </a>
            <a href="<?php echo e(route('don-hang.index')); ?>" class="review-side-link">
                <svg class="review-side-icon" viewBox="0 0 24 24"><path d="M6 3h12l2 4v14H4V7l2-4Z"/><path d="M4 7h16M9 11a3 3 0 0 0 6 0"/></svg>
                <span>Đơn hàng của tôi</span>
            </a>
            <a href="<?php echo e(route('danh-gia.index')); ?>" class="review-side-link active">
                <svg class="review-side-icon" viewBox="0 0 24 24"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.2l-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/></svg>
                <span>Đánh giá sản phẩm</span>
            </a>
            <a href="#" class="review-side-link">
                <svg class="review-side-icon" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
                <span>Thông báo</span>
            </a>
        </nav>

        <form action="<?php echo e(route('dang-xuat')); ?>" method="POST" class="review-logout-form">
            <?php echo csrf_field(); ?>
            <button type="submit" class="review-side-link logout">
                <svg class="review-side-icon" viewBox="0 0 24 24"><path d="M9 5H5v14h4M14 8l4 4-4 4M18 12H9"/></svg>
                <span>Đăng xuất</span>
            </button>
        </form>
    </aside>

    <main class="review-main review-form-page">
        <p class="review-breadcrumb">Trang chủ / Đánh giá sản phẩm / Viết đánh giá</p>
        <h1>Đánh giá sản phẩm</h1>
        <p>Chia sẻ trải nghiệm của bạn để giúp những khách hàng khác</p>

        <?php if(session('error')): ?>
            <div class="review-alert error"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <section class="review-form-card">
            <div class="review-target-product">
                <div class="review-product-info">
                    <div class="review-product-image large">
                        <?php if($orderDetail->cayCanh?->anh_dai_dien): ?>
                            <img src="<?php echo e(asset($orderDetail->cayCanh->anh_dai_dien)); ?>" alt="<?php echo e($orderDetail->cayCanh->ten_cay); ?>">
                        <?php else: ?>
                            <span>🌿</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong><?php echo e($orderDetail->cayCanh?->ten_cay ?? 'Sản phẩm'); ?></strong>
                        <p>Phân loại: <?php echo e($orderDetail->cayCanh?->danhMuc?->ten_danh_muc ?? 'Cây cảnh'); ?></p>
                        <b><?php echo e(number_format((float) $orderDetail->don_gia, 0, ',', '.')); ?>đ</b>
                    </div>
                </div>
                <div class="order-meta">
                    <span>Đã mua ngày: <?php echo e(optional($orderDetail->donHang?->ngay_dat)->format('d/m/Y')); ?></span>
                    <span>Mã đơn hàng: <?php echo e($orderDetail->donHang?->orderCode()); ?></span>
                    <a href="<?php echo e(route('don-hang.show', $orderDetail->order_id)); ?>">Xem chi tiết đơn hàng</a>
                </div>
            </div>

            <form action="<?php echo e(route('danh-gia.store', $orderDetail->order_detail_id)); ?>" method="POST" enctype="multipart/form-data" id="reviewForm">
                <?php echo csrf_field(); ?>

                <div class="review-field">
                    <label>1. Chọn số sao <span>*</span></label>
                    <div class="star-picker" data-star-picker>
                        <?php for($star = 1; $star <= 5; $star++): ?>
                            <button type="button" data-star="<?php echo e($star); ?>" aria-label="<?php echo e($star); ?> sao">☆</button>
                        <?php endfor; ?>
                        <span id="starLabel">Chọn điểm đánh giá</span>
                    </div>
                    <input type="hidden" name="so_sao" id="ratingInput" value="<?php echo e(old('so_sao', $selectedStar ?? '')); ?>">
                    <?php $__errorArgs = ['so_sao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="review-field">
                    <label for="noi_dung">2. Chia sẻ cảm nhận của bạn <span>*</span></label>
                    <textarea id="noi_dung" name="noi_dung" rows="6" maxlength="2000" placeholder="Bạn nghĩ gì về sản phẩm này? Hãy chia sẻ ưu điểm, nhược điểm hoặc trải nghiệm của bạn..."><?php echo e(old('noi_dung')); ?></textarea>
                    <div class="char-count"><span id="reviewCharCount"><?php echo e(mb_strlen(old('noi_dung', ''))); ?></span>/2000</div>
                    <?php $__errorArgs = ['noi_dung'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="review-field">
                    <label>3. Thêm hình ảnh <small>(tối đa 5 ảnh)</small></label>
                    <label class="upload-box" for="reviewImages">
                        <strong>▧ Nhấn để tải ảnh lên</strong>
                        <span>JPG, JPEG hoặc PNG · Tối đa 5 ảnh · Mỗi ảnh tối đa 5MB</span>
                    </label>
                    <input type="file" id="reviewImages" name="hinh_anh[]" accept="image/jpeg,image/png,.jpg,.jpeg,.png" multiple hidden>
                    <div class="image-upload-counter" id="imageUploadCounter">Đã chọn 0/5 ảnh</div>
                    <div class="image-preview-list" id="imagePreviewList"></div>
                    <?php $__errorArgs = ['hinh_anh'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['hinh_anh.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="field-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="review-form-actions">
                    <a href="<?php echo e(route('danh-gia.index')); ?>" class="review-cancel">Hủy</a>
                    <button type="submit" class="review-submit">Gửi đánh giá</button>
                </div>
            </form>
        </section>

        <div class="review-note">🛡 Đánh giá của bạn sẽ được hiển thị công khai sau khi được hệ thống/Admin duyệt.</div>
    </main>
</div>

<?php echo app('Illuminate\Foundation\Vite')('resources/js/customer/danh-gia.js'); ?>
</body>
</html>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/danh_gia/form.blade.php ENDPATH**/ ?>