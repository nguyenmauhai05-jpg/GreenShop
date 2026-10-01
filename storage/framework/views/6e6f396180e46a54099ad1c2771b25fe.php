<section class="best-sellers-section">

    <div class="best-sellers-container">

        <div class="best-sellers-header">

            <div>
                <div class="best-sellers-kicker">
                    <span></span>
                    SẢN PHẨM NỔI BẬT
                </div>

                <h2>Sản phẩm bán chạy</h2>

                <p>
                    Những sản phẩm được khách hàng GreenShop yêu thích nhất.
                </p>
            </div>

            <a href="<?php echo e(route('cua-hang')); ?>" class="best-sellers-view-all">
                Xem tất cả
                <span>→</span>
            </a>

        </div>


        <?php if($sanPhamBanChay->isEmpty()): ?>

            <div class="best-sellers-empty">

                <div class="empty-icon">
                    🌿
                </div>

                <h3>
                    Chưa có sản phẩm bán chạy
                </h3>

                <p>
                    Hiện tại chưa có dữ liệu đơn hàng hoàn thành
                    để xác định sản phẩm bán chạy.
                </p>

            </div>

        <?php else: ?>

            <div class="best-sellers-grid">

                <?php $__currentLoopData = $sanPhamBanChay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sanPham): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <div class="product-card">

                        <div class="product-image-wrap">

                            <?php if($sanPham->anh_dai_dien): ?>

                                <a
                                    href="<?php echo e(route('chi-tiet-cay', $sanPham->plant_id)); ?>"
                                    class="product-card-image"
                                >
                                    <img
                                        src="<?php echo e(asset($sanPham->anh_dai_dien)); ?>"
                                        alt="<?php echo e($sanPham->ten_cay); ?>"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </a>

                            <?php else: ?>

                                <div class="product-no-image">
                                    🌱
                                </div>

                            <?php endif; ?>


                            <div class="product-badge">
                                Bán chạy
                            </div>


                            <button
                                type="button"
                                class="product-favorite"
                                aria-label="Yêu thích"
                            >
                                ♡
                            </button>

                        </div>


                        <div class="product-info">

                            <p class="product-category">
                                CÂY XANH
                            </p>

                            <h3 class="product-name">
                                <?php echo e($sanPham->ten_cay); ?>

                            </h3>


                            <div class="product-rating">

                                <span class="stars">
                                    ★★★★★
                                </span>

                                <span class="sold-count">
                                    Đã bán <?php echo e($sanPham->tong_da_ban); ?>

                                </span>

                            </div>


                            <div class="product-bottom">

                                <strong class="product-price">
                                    <?php echo e(number_format($sanPham->gia, 0, ',', '.')); ?>₫
                                </strong>


                                <?php if($sanPham->so_luong > 0): ?>

                                    <div class="best-seller-actions">
                                        <form
                                            method="POST"
                                            action="<?php echo e(route('gio-hang.them')); ?>"
                                            class="js-add-cart-form best-seller-cart-form"
                                            data-ajax-cart="true"
                                            data-login-url="<?php echo e(route('dang-nhap')); ?>"
                                        >
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="plant_id" value="<?php echo e($sanPham->plant_id); ?>">
                                            <input type="hidden" name="so_luong" value="1">
                                            <button type="submit" class="add-cart-btn cart-icon-btn" title="Thêm vào giỏ hàng" aria-label="Thêm vào giỏ hàng">
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.1 9.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H7M10 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/></svg>
                                            </button>
                                        </form>
                                        <?php if(auth()->guard()->check()): ?>
<form method="POST" action="<?php echo e(route('gio-hang.mua-ngay')); ?>" class="best-seller-buy-form">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="plant_id" value="<?php echo e($sanPham->plant_id); ?>">
                                            <input type="hidden" name="so_luong" value="1">
                                            <button type="submit" class="buy-now-btn">Mua ngay</button>
                                        </form>
<?php else: ?>
<a href="<?php echo e(route('dang-nhap')); ?>" class="buy-now-btn" style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none">Mua ngay</a>
<?php endif; ?>
                                    </div>

                                <?php else: ?>

                                    <button
                                        type="button"
                                        class="add-cart-btn disabled"
                                        disabled
                                    >
                                        Hết hàng
                                    </button>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>

        <?php endif; ?>

    </div>

</section>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/trang_chu/components/best-sellers.blade.php ENDPATH**/ ?>