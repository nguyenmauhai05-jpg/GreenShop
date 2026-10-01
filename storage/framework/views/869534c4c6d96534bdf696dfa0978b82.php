<article class="checkout-card voucher-checkout-card">
                    <div class="card-heading-row">
                        <div><span class="step-number">3</span><h2>Voucher</h2></div>
                        <?php if($vouchers->isNotEmpty() || $shippingVouchers->isNotEmpty()): ?>
                            <button type="button" class="selector-toggle" id="voucherListToggle" aria-expanded="false" aria-controls="checkoutVoucherList">›</button>
                        <?php endif; ?>
                    </div>

                    <div class="voucher-type-summaries">
                        <div class="selected-voucher-summary <?php echo e($voucherDiscount > 0 ? 'has-voucher' : ''); ?>" id="selectedVoucherSummary">
                            <span class="selected-voucher-icon">%</span>
                            <span class="selected-voucher-main">
                                <small class="voucher-type-label">Mã giảm giá</small>
                                <strong data-voucher-summary-title><?php echo e($selectedVoucher ? $selectedVoucher->ma_voucher.' · '.$selectedVoucher->ten_voucher : 'Chưa có mã phù hợp'); ?></strong>
                                <small data-voucher-summary-desc><?php echo e($voucherDiscount > 0 ? 'Giảm '.number_format($voucherDiscount,0,',','.').'đ cho đơn hàng này.' : 'Chưa áp dụng mã giảm giá.'); ?></small>
                            </span>
                            <span class="auto-applied-badge" <?php echo e($voucherDiscount > 0 ? '' : 'hidden'); ?>>Đã áp dụng</span>
                        </div>

                        <div class="selected-voucher-summary shipping-voucher-summary <?php echo e($shippingVoucherDiscount > 0 ? 'has-voucher' : ''); ?>" id="selectedShippingVoucherSummary">
                            <span class="selected-voucher-icon">🚚</span>
                            <span class="selected-voucher-main">
                                <small class="voucher-type-label">Voucher phí vận chuyển</small>
                                <?php $selectedShippingVoucher = $shippingVouchers->firstWhere('voucher_id', $shippingVoucherId); ?>
                                <strong data-shipping-voucher-summary-title><?php echo e($selectedShippingVoucher ? $selectedShippingVoucher->ma_voucher.' · '.$selectedShippingVoucher->ten_voucher : 'Chưa có mã phù hợp'); ?></strong>
                                <small data-shipping-voucher-summary-desc><?php echo e($shippingVoucherDiscount > 0 ? 'Giảm '.number_format($shippingVoucherDiscount,0,',','.').'đ phí vận chuyển.' : 'Chưa có voucher vận chuyển đủ điều kiện.'); ?></small>
                            </span>
                            <span class="auto-applied-badge" data-shipping-voucher-badge <?php echo e($shippingVoucherDiscount > 0 ? '' : 'hidden'); ?>>Đã áp dụng</span>
                        </div>
                    </div>

                    <div class="checkout-voucher-entry">
                        <label for="checkoutVoucherCode">Nhập mã voucher</label>
                        <div class="checkout-voucher-entry-row">
                            <input type="text" id="checkoutVoucherCode" maxlength="60" autocomplete="off" placeholder="Nhập mã giảm giá hoặc mã phí vận chuyển">
                            <button type="button" id="checkoutApplyVoucherCode">Áp dụng</button>
                        </div>
                        <p id="checkoutVoucherFeedback" class="checkout-voucher-feedback" role="status" aria-live="polite"></p>
                    </div>
                    <p class="checkout-cart-feedback" id="checkoutCartFeedback" role="status" aria-live="polite"></p>
                    <div class="voucher-suggestion-box" id="voucherSuggestionBox" hidden>
                        <div class="voucher-suggestion-header">
                            <h4 class="voucher-suggestion-title">Các loại cây bạn có thể quan tâm</h4>
                            <button type="button" class="voucher-suggestion-close" id="voucherSuggestionClose"
                                    aria-label="Ẩn gợi ý sản phẩm và thông báo voucher" title="Ẩn gợi ý">&times;</button>
                        </div>
                        <div class="voucher-suggestion-products" id="voucherSuggestionProducts"></div>
                        <a href="<?php echo e(route('cua-hang')); ?>" class="voucher-continue-shopping">Tiếp tục mua sắm →</a>
                    </div>
                    <div class="checkout-voucher-list collapsed-selector-list voucher-two-types" id="checkoutVoucherList" hidden>
                        <section class="voucher-list-section">
                            <h3>Mã giảm giá</h3>
                            <button type="button" class="checkout-voucher-option <?php echo e(!$selectedVoucher ? 'selected' : ''); ?>" data-voucher-option data-id="">
                                <span class="voucher-radio"></span><span class="voucher-main"><strong>Không dùng mã giảm giá</strong></span>
                            </button>
                            <?php $__currentLoopData = $vouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voucher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $eligible = $subtotal >= (float)$voucher->don_hang_toi_thieu; ?>
                                <button type="button"
                                    class="checkout-voucher-option <?php echo e((int)$selectedVoucher?->voucher_id === (int)$voucher->voucher_id ? 'selected' : ''); ?> <?php echo e($eligible ? 'eligible' : 'needs-more'); ?>"
                                    data-voucher-option data-id="<?php echo e($voucher->voucher_id); ?>" data-code="<?php echo e($voucher->ma_voucher); ?>"
                                    data-name="<?php echo e($voucher->ten_voucher); ?>" data-min="<?php echo e((float)$voucher->don_hang_toi_thieu); ?>"
                                    data-type="<?php echo e($voucher->loai_giam); ?>" data-value="<?php echo e((float)$voucher->gia_tri_giam); ?>"
                                    data-max="<?php echo e($voucher->giam_toi_da !== null ? (float)$voucher->giam_toi_da : ''); ?>">
                                    <span class="voucher-radio"></span>
                                    <span class="voucher-main"><strong><?php echo e($voucher->ma_voucher); ?> · <?php echo e($voucher->ten_voucher); ?></strong>
                                    <small>Đơn từ <?php echo e(number_format($voucher->don_hang_toi_thieu,0,',','.')); ?>đ</small></span>
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </section>

                        <section class="voucher-list-section">
                            <h3>Voucher phí vận chuyển</h3>
                            <button type="button" class="checkout-voucher-option shipping-voucher-option <?php echo e(!$shippingVoucherId ? 'selected' : ''); ?>" data-shipping-voucher-option data-id="">
                                <span class="voucher-radio"></span><span class="voucher-main"><strong>Không dùng voucher vận chuyển</strong></span>
                            </button>
                            <?php $__currentLoopData = $shippingVouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voucher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $eligible = $subtotal >= (float)$voucher->don_hang_toi_thieu; ?>
                                <button type="button"
                                    class="checkout-voucher-option shipping-voucher-option <?php echo e((int)$shippingVoucherId === (int)$voucher->voucher_id ? 'selected' : ''); ?> <?php echo e($eligible ? 'eligible' : 'needs-more'); ?>"
                                    data-shipping-voucher-option data-id="<?php echo e($voucher->voucher_id); ?>" data-code="<?php echo e($voucher->ma_voucher); ?>"
                                    data-name="<?php echo e($voucher->ten_voucher); ?>" data-min="<?php echo e((float)$voucher->don_hang_toi_thieu); ?>"
                                    data-type="<?php echo e($voucher->loai_giam); ?>" data-value="<?php echo e((float)$voucher->gia_tri_giam); ?>"
                                    data-max="<?php echo e($voucher->giam_toi_da !== null ? (float)$voucher->giam_toi_da : ''); ?>">
                                    <span class="voucher-radio"></span>
                                    <span class="voucher-main"><strong><?php echo e($voucher->ma_voucher); ?> · <?php echo e($voucher->ten_voucher); ?></strong>
                                    <small>Đơn từ <?php echo e(number_format($voucher->don_hang_toi_thieu,0,',','.')); ?>đ</small></span>
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </section>
                    </div>
                </article>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/thanh_toan/components/vouchers.blade.php ENDPATH**/ ?>