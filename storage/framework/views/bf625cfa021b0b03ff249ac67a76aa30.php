<article class="shop-product-card">

    
    <a
        href="<?php echo e(route('chi-tiet-cay', $cay->plant_id)); ?>"
        class="shop-product-image"
    >
        <?php if($cay->anh_dai_dien): ?>
            <img
                src="<?php echo e(asset($cay->anh_dai_dien)); ?>"
                alt="<?php echo e($cay->ten_cay); ?>"
                loading="lazy"
                decoding="async"
            >
        <?php else: ?>
            <div class="no-product-image">
                🌱
            </div>
        <?php endif; ?>
    </a>

    <div class="shop-product-info">

        
        <p class="shop-product-category">
            <?php echo e($cay->ten_danh_muc ?? 'Cây cảnh'); ?>

        </p>

        
        <a
            href="<?php echo e(route('chi-tiet-cay', $cay->plant_id)); ?>"
            class="shop-product-name"
        >
            <?php echo e($cay->ten_cay); ?>

        </a>

        
        <strong class="shop-product-price">
            <?php echo e(number_format($cay->gia, 0, ',', '.')); ?>₫
        </strong>

        
        <div
            class="stock-status <?php echo e($cay->so_luong > 0 ? 'in-stock' : 'out-stock'); ?>"
        >
            <?php echo e($cay->so_luong > 0 ? 'Còn hàng' : 'Hết hàng'); ?>

        </div>

        
        <?php if($cay->so_luong > 0): ?>
            <div class="shop-product-actions">
                <form
                    action="<?php echo e(route('gio-hang.them')); ?>"
                    method="POST"
                    class="shop-add-cart-form js-add-cart-form"
                    data-ajax-cart="true"
                    data-login-url="<?php echo e(route('dang-nhap')); ?>"
                >
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="plant_id" value="<?php echo e($cay->plant_id); ?>">
                    <input type="hidden" name="so_luong" value="1">
                    <button type="submit" class="shop-add-cart shop-cart-icon" title="Thêm vào giỏ hàng" aria-label="Thêm vào giỏ hàng">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.1 9.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H7M10 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/></svg>
                    </button>
                </form>

                <form action="<?php echo e(route('gio-hang.mua-ngay')); ?>" method="POST" class="shop-buy-now-form">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="plant_id" value="<?php echo e($cay->plant_id); ?>">
                    <input type="hidden" name="so_luong" value="1">
                    <button type="submit" class="shop-buy-now">Mua ngay</button>
                </form>
            </div>
        <?php else: ?>
            <button type="button" class="shop-add-cart disabled" disabled>Hết hàng</button>
        <?php endif; ?>

    </div>

</article>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/cua_hang/components/product-card.blade.php ENDPATH**/ ?>