<aside class="checkout-summary">
                <div class="summary-sticky">
                    <h2>Tóm tắt đơn hàng</h2>
                    <div class="summary-line"><span>Tạm tính</span><strong id="checkoutSubtotalValue"><?php echo e(number_format($subtotal, 0, ',', '.')); ?>đ</strong></div>
                    <div class="summary-line"><span>Khoảng cách giao hàng</span><strong id="summaryDistanceValue"><?php echo e($shippingQuote ? number_format((float)$shippingQuote['distance_km'], 2, ',', '.') . ' km' : '-- km'); ?></strong></div>
                    <div class="summary-line"><span>Vận chuyển</span><strong id="shippingMethodSummary"><?php echo e($selectedShippingMethod === 'EXPRESS' ? 'Hỏa tốc' : ($selectedShippingMethod === 'GHTK_EXPRESS' ? 'GHTK Express' : 'Giao Hàng Nhanh')); ?></strong></div>
                    <div class="summary-line"><span>Phí vận chuyển</span><strong id="shippingFeeValue"><?php echo e($shippingFee > 0 ? number_format($shippingFee, 0, ',', '.') . 'đ' : 'Miễn phí'); ?></strong></div>
                    <div class="summary-line shipping-voucher-summary-line <?php echo e($shippingVoucherDiscount > 0 ? 'active' : ''); ?>" id="shippingVoucherSummaryLine">
                        <span>Voucher vận chuyển</span>
                        <strong id="shippingVoucherDiscountValue"><?php echo e($shippingVoucherDiscount > 0 ? '-' . number_format($shippingVoucherDiscount, 0, ',', '.') . 'đ' : '0đ'); ?></strong>
                    </div>
                    <div class="summary-line voucher-summary-line" id="voucherSummaryLine">
                        <span>Mã giảm giá</span>
                        <strong id="voucherDiscountValue"><?php echo e($voucherDiscount > 0 ? '-' . number_format($voucherDiscount, 0, ',', '.') . 'đ' : '0đ'); ?></strong>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-line grand"><span>Tổng thanh toán</span><strong id="checkoutTotalValue"><?php echo e(number_format($total, 0, ',', '.')); ?>đ</strong></div>
                    <button type="submit" class="place-order-button" id="placeOrderButton" <?php echo e((!$selectedAddress || $shippingError) ? 'disabled' : ''); ?>>
                        <span id="placeOrderButtonText"><?php echo e($selectedPaymentMethod === 'PAYPAL' ? 'Thanh toán qua PayPal' : ($selectedPaymentMethod === 'PAYOS' ? 'Thanh toán qua payOS' : 'Đặt hàng')); ?></span><small>→</small>
                    </button>
                    <p class="order-note">Bằng việc đặt hàng, bạn xác nhận thông tin trên là chính xác và đồng ý với chính sách GreenShop.</p>
                </div>
            </aside>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/thanh_toan/components/summary.blade.php ENDPATH**/ ?>