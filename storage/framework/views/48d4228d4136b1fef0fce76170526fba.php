<article class="checkout-card shipping-method-card shipping-card-compact">
                    <div class="card-heading-row shipping-method-heading compact-selector-heading">
                        <div><span class="step-number">4</span><h2>Phương thức vận chuyển</h2></div>
                        <button type="button" class="selector-toggle" id="shippingViewAll" aria-expanded="false" aria-controls="shippingMoreOptions">›</button>
                    </div>
                    <div class="shipping-method-options compact">
                        <button type="button" class="shipping-method-option <?php echo e($selectedShippingMethod === 'STANDARD' ? 'selected' : ''); ?>" data-shipping-method="STANDARD" data-shipping-fee="<?php echo e($standardShippingFee); ?>">
                            <span class="shipping-selected-corner">✓</span>
                            <span class="shipping-method-main">
                                <strong><?php echo e($standardFrom); ?> - <?php echo e($standardTo); ?></strong>
                                <small>Giao Hàng Nhanh · mô phỏng</small>
                            </span>
                            <span class="shipping-method-price" data-shipping-price="STANDARD"><del class="shipping-original-price" hidden></del><strong><?php echo e(number_format($standardShippingFee, 0, ',', '.')); ?>đ</strong><small class="shipping-discount-note" hidden></small></span>
                        </button>
                        <button type="button" class="shipping-method-option <?php echo e($selectedShippingMethod === 'EXPRESS' ? 'selected' : ''); ?>" data-shipping-method="EXPRESS" data-shipping-fee="<?php echo e($expressShippingFee); ?>">
                            <span class="shipping-selected-corner">✓</span>
                            <span class="shipping-method-main">
                                <strong>Giao trong ngày</strong>
                                <small>Hỏa tốc · chỉ Hà Nội · <?php echo e($shippingQuote ? number_format($distanceKm, 2, ',', '.') . ' km' : 'đang tính khoảng cách'); ?></small>
                            </span>
                            <span class="shipping-method-price express" data-shipping-price="EXPRESS"><del class="shipping-original-price" hidden></del><strong><?php echo e($expressShippingFee > 0 ? number_format($expressShippingFee, 0, ',', '.') . 'đ' : 'Miễn phí'); ?></strong><small class="shipping-discount-note" hidden></small></span>
                        </button>
                        <div class="shipping-more-options" id="shippingMoreOptions" hidden>
                            <button type="button" class="shipping-method-option <?php echo e($selectedShippingMethod === 'GHTK_EXPRESS' ? 'selected' : ''); ?>" data-shipping-method="GHTK_EXPRESS" data-shipping-fee="<?php echo e($ghtkShippingFee); ?>">
                                <span class="shipping-selected-corner">✓</span>
                                <span class="shipping-method-main">
                                    <strong><?php echo e(now()->addDay()->format('d/m')); ?> - <?php echo e(now()->addDays(2)->format('d/m')); ?></strong>
                                    <small>Giao Hàng Tiết Kiệm Express · mô phỏng</small>
                                </span>
                                <span class="shipping-method-price" data-shipping-price="GHTK_EXPRESS"><del class="shipping-original-price" hidden></del><strong><?php echo e(number_format($ghtkShippingFee, 0, ',', '.')); ?>đ</strong><small class="shipping-discount-note" hidden></small></span>
                            </button>
                        </div>
                    </div>
</article>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/thanh_toan/components/shipping.blade.php ENDPATH**/ ?>