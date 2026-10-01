<article class="checkout-card">
                    <div class="card-heading-row"><div><span class="step-number">5</span><h2>Phương thức thanh toán</h2></div></div>
                    <label class="payment-option {{ $selectedPaymentMethod === 'COD' ? 'selected' : '' }}">
                        <input type="radio" name="payment_method" value="COD" {{ $selectedPaymentMethod === 'COD' ? 'checked' : '' }}>
                        <span class="payment-radio"></span>
                        <span class="payment-icon">₫</span>
                        <span><strong>COD - Thanh toán khi nhận hàng</strong><small>Thanh toán trực tiếp cho nhân viên giao hàng khi nhận cây.</small></span>
                    </label>

                    <label class="payment-option {{ $selectedPaymentMethod === 'PAYPAL' ? 'selected' : '' }}">
                        <input type="radio" name="payment_method" value="PAYPAL" {{ $selectedPaymentMethod === 'PAYPAL' ? 'checked' : '' }}>
                        <span class="payment-radio"></span>
                        <span class="payment-icon">P</span>
                        <span><strong>PayPal</strong><small>Thanh toán an toàn bằng tài khoản PayPal Sandbox.</small></span>
                    </label>

                    <label class="payment-option {{ $selectedPaymentMethod === 'PAYOS' ? 'selected' : '' }}">
                        <input type="radio" name="payment_method" value="PAYOS" {{ $selectedPaymentMethod === 'PAYOS' ? 'checked' : '' }}>
                        <span class="payment-radio"></span>
                        <span class="payment-icon">QR</span>
                        <span><strong>payOS - QR ngân hàng tự động</strong><small>Chuyển khoản bằng QR; chỉ xác nhận đơn khi payOS thông báo thanh toán thành công.</small></span>
                    </label>
                </article>
