<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh giá sản phẩm - GreenShop</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/customer/site-shell.css']); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/customer/shared-account-sidebar.css'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/customer/danh-gia.css'); ?>


</head>
<body>
<?php echo $__env->make('trang_chu.components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="review-layout">
    <div class="shared-sidebar-backdrop" id="sidebarBackdrop"></div>
    <?php echo $__env->make('tai_khoan.components.account-sidebar', ['activeSidebar' => 'reviews', 'sidebarId' => 'accountSidebar'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="review-main">
        <div class="review-heading">
            <button type="button" class="shared-sidebar-toggle" id="mobileMenuButton" aria-label="Mở menu tài khoản">☰</button>
            <div>
                <p class="review-breadcrumb">Tài khoản / Đánh giá sản phẩm</p>
                <h1>Đánh giá sản phẩm</h1>
                <p>Xem và quản lý các đánh giá sản phẩm của bạn</p>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="review-alert success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('warning')): ?>
            <div class="review-alert warning"><?php echo e(session('warning')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="review-alert error"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <div class="review-tabs">
            <a href="<?php echo e(route('danh-gia.index', ['tab' => 'pending'])); ?>" class="review-tab <?php echo e($tab === 'pending' ? 'active' : ''); ?>">
                Chờ đánh giá <span><?php echo e($eligibleCount); ?></span>
            </a>
            <a href="<?php echo e(route('danh-gia.index', ['tab' => 'reviewed'])); ?>" class="review-tab <?php echo e($tab === 'reviewed' ? 'active' : ''); ?>">
                Đã đánh giá <span><?php echo e($reviewedCount); ?></span>
            </a>
        </div>

        <?php if($tab === 'pending'): ?>
            <div class="review-info-box">Hãy chia sẻ cảm nhận của bạn để giúp những khách hàng khác lựa chọn sản phẩm phù hợp.</div>

            <?php $__empty_1 = true; $__currentLoopData = $eligibleDetails->groupBy('order_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderId => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $order = $details->first()->donHang; ?>
                <section class="review-order-card">
                    <div class="review-order-head">
                        <div>
                            <strong>Đơn hàng <?php echo e($order?->orderCode()); ?></strong>
                            <span>Đặt ngày <?php echo e(optional($order?->ngay_dat)->format('d/m/Y')); ?></span>
                        </div>
                        <span class="delivered-badge">✓ Đã giao hàng</span>
                    </div>

                    <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="review-product-row">
                            <div class="review-product-info">
                                <div class="review-product-image">
                                    <?php if($detail->cayCanh?->anh_dai_dien): ?>
                                        <img src="<?php echo e(asset($detail->cayCanh->anh_dai_dien)); ?>" alt="<?php echo e($detail->cayCanh->ten_cay); ?>">
                                    <?php else: ?>
                                        <span>🌿</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <strong><?php echo e($detail->cayCanh?->ten_cay ?? 'Sản phẩm'); ?></strong>
                                    <p>Phân loại: <?php echo e($detail->cayCanh?->danhMuc?->ten_danh_muc ?? 'Cây cảnh'); ?></p>
                                    <b><?php echo e(number_format((float) $detail->don_gia, 0, ',', '.')); ?>đ</b>
                                </div>
                            </div>
                            <div class="review-product-action">
                                <div class="mini-stars selectable-stars" aria-label="Chọn số sao đánh giá">
                                    <?php for($star = 1; $star <= 5; $star++): ?>
                                        <a href="<?php echo e(route('danh-gia.create', ['orderDetail' => $detail->order_detail_id, 'star' => $star])); ?>"
                                           title="<?php echo e($star); ?> sao"
                                           aria-label="Chọn <?php echo e($star); ?> sao">☆</a>
                                    <?php endfor; ?>
                                </div>
                                <small>(Chọn số sao)</small>
                                <a href="<?php echo e(route('danh-gia.create', $detail->order_detail_id)); ?>" class="review-button">Viết đánh giá</a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </section>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="review-empty">
                    <div>☆</div>
                    <h3>Bạn chưa có sản phẩm nào cần đánh giá.</h3>
                    <p>Sản phẩm sẽ xuất hiện tại đây sau khi đơn hàng được giao thành công.</p>
                    <a href="<?php echo e(route('cua-hang')); ?>" class="review-button">Tiếp tục mua sắm</a>
                </div>
            <?php endif; ?>

            <?php if(method_exists($eligibleDetails, 'total')): ?>
                <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $eligibleDetails]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($eligibleDetails)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $attributes = $__attributesOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $component = $__componentOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__componentOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <?php $__empty_1 = true; $__currentLoopData = $reviewed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <section class="reviewed-card">
                    <div class="review-product-info">
                        <div class="review-product-image">
                            <?php if($review->cayCanh?->anh_dai_dien): ?>
                                <img src="<?php echo e(asset($review->cayCanh->anh_dai_dien)); ?>" alt="<?php echo e($review->cayCanh->ten_cay); ?>">
                            <?php else: ?>
                                <span>🌿</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <strong><?php echo e($review->cayCanh?->ten_cay ?? 'Sản phẩm'); ?></strong>
                            <p>Đơn hàng <?php echo e($review->chiTietDonHang?->donHang?->orderCode() ?? '—'); ?></p>
                            <div class="stars-readonly"><?php echo e(str_repeat('★', (int) $review->so_sao)); ?><?php echo e(str_repeat('☆', 5 - (int) $review->so_sao)); ?></div>
                        </div>
                    </div>
                    <span class="status-badge status-<?php echo e($review->trang_thai); ?>"><?php echo e($review->statusLabel()); ?></span>
                    <div class="reviewed-content"><?php echo e($review->noi_dung); ?></div>
                    <?php
                        $reviewImages = $review->hinh_anh ?? [];
                        if (is_string($reviewImages)) {
                            $decodedImages = json_decode($reviewImages, true);
                            $reviewImages = is_array($decodedImages) ? $decodedImages : array_filter([$reviewImages]);
                        }
                        $reviewImages = is_array($reviewImages) ? array_values(array_filter($reviewImages)) : [];
                    ?>
                    <?php if(count($reviewImages)): ?>
                        <div class="reviewed-images" aria-label="Ảnh đính kèm đánh giá">
                            <?php $__currentLoopData = $reviewImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $imageUrl = str_starts_with((string) $image, 'http://') || str_starts_with((string) $image, 'https://')
                                        ? $image
                                        : asset(ltrim((string) $image, '/'));
                                ?>
                                <button type="button" class="review-image-thumb" data-review-image="<?php echo e($imageUrl); ?>" aria-label="Xem ảnh đánh giá lớn">
                                    <img src="<?php echo e($imageUrl); ?>" alt="Ảnh đánh giá" loading="lazy">
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                    <small>Gửi lúc <?php echo e(optional($review->ngay_danh_gia)->format('H:i d/m/Y')); ?></small>
                </section>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="review-empty">
                    <div>☆</div>
                    <h3>Bạn chưa gửi đánh giá nào.</h3>
                </div>
            <?php endif; ?>

            <?php if(method_exists($reviewed, 'total')): ?>
                <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $reviewed]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reviewed)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $attributes = $__attributesOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $component = $__componentOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__componentOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</div>

<div class="review-lightbox" id="reviewLightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Xem ảnh đánh giá">
    <button type="button" class="review-lightbox-close" id="reviewLightboxClose" aria-label="Đóng ảnh">×</button>
    <img id="reviewLightboxImage" src="" alt="Ảnh đánh giá phóng lớn">
</div>
<script>
(() => {
 const toggle = document.getElementById('mobileMenuButton');
 const sidebar = document.getElementById('accountSidebar');
 const backdrop = document.getElementById('sidebarBackdrop');
 if (!toggle || !sidebar || !backdrop) return;
 const close = () => {sidebar.classList.remove('open'); backdrop.classList.remove('open');};
 toggle.addEventListener('click', () => {sidebar.classList.toggle('open'); backdrop.classList.toggle('open');});
 backdrop.addEventListener('click', close);
})();

(() => {
    const lightbox = document.getElementById('reviewLightbox');
    const lightboxImage = document.getElementById('reviewLightboxImage');
    const closeButton = document.getElementById('reviewLightboxClose');
    if (!lightbox || !lightboxImage || !closeButton) return;

    const openLightbox = (src) => {
        lightboxImage.src = src;
        lightbox.classList.add('open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('review-lightbox-open');
    };
    const closeLightbox = () => {
        lightbox.classList.remove('open');
        lightbox.setAttribute('aria-hidden', 'true');
        lightboxImage.src = '';
        document.body.classList.remove('review-lightbox-open');
    };

    document.querySelectorAll('[data-review-image]').forEach((button) => {
        button.addEventListener('click', () => openLightbox(button.dataset.reviewImage));
    });
    closeButton.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && lightbox.classList.contains('open')) closeLightbox();
    });
})();
</script>
</body>
</html>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/danh_gia/index.blade.php ENDPATH**/ ?>