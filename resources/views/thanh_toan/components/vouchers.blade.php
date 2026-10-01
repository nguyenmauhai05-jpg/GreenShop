<article class="checkout-card voucher-checkout-card">
                    <div class="card-heading-row">
                        <div><span class="step-number">3</span><h2>Voucher</h2></div>
                        @if($vouchers->isNotEmpty() || $shippingVouchers->isNotEmpty())
                            <button type="button" class="selector-toggle" id="voucherListToggle" aria-expanded="false" aria-controls="checkoutVoucherList">›</button>
                        @endif
                    </div>

                    <div class="voucher-type-summaries">
                        <div class="selected-voucher-summary {{ $voucherDiscount > 0 ? 'has-voucher' : '' }}" id="selectedVoucherSummary">
                            <span class="selected-voucher-icon">%</span>
                            <span class="selected-voucher-main">
                                <small class="voucher-type-label">Mã giảm giá</small>
                                <strong data-voucher-summary-title>{{ $selectedVoucher ? $selectedVoucher->ma_voucher.' · '.$selectedVoucher->ten_voucher : 'Chưa có mã phù hợp' }}</strong>
                                <small data-voucher-summary-desc>{{ $voucherDiscount > 0 ? 'Giảm '.number_format($voucherDiscount,0,',','.').'đ cho đơn hàng này.' : 'Chưa áp dụng mã giảm giá.' }}</small>
                            </span>
                            <span class="auto-applied-badge" {{ $voucherDiscount > 0 ? '' : 'hidden' }}>Đã áp dụng</span>
                        </div>

                        <div class="selected-voucher-summary shipping-voucher-summary {{ $shippingVoucherDiscount > 0 ? 'has-voucher' : '' }}" id="selectedShippingVoucherSummary">
                            <span class="selected-voucher-icon">🚚</span>
                            <span class="selected-voucher-main">
                                <small class="voucher-type-label">Voucher phí vận chuyển</small>
                                @php $selectedShippingVoucher = $shippingVouchers->firstWhere('voucher_id', $shippingVoucherId); @endphp
                                <strong data-shipping-voucher-summary-title>{{ $selectedShippingVoucher ? $selectedShippingVoucher->ma_voucher.' · '.$selectedShippingVoucher->ten_voucher : 'Chưa có mã phù hợp' }}</strong>
                                <small data-shipping-voucher-summary-desc>{{ $shippingVoucherDiscount > 0 ? 'Giảm '.number_format($shippingVoucherDiscount,0,',','.').'đ phí vận chuyển.' : 'Chưa có voucher vận chuyển đủ điều kiện.' }}</small>
                            </span>
                            <span class="auto-applied-badge" data-shipping-voucher-badge {{ $shippingVoucherDiscount > 0 ? '' : 'hidden' }}>Đã áp dụng</span>
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
                        <a href="{{ route('cua-hang') }}" class="voucher-continue-shopping">Tiếp tục mua sắm →</a>
                    </div>
                    <div class="checkout-voucher-list collapsed-selector-list voucher-two-types" id="checkoutVoucherList" hidden>
                        <section class="voucher-list-section">
                            <h3>Mã giảm giá</h3>
                            <button type="button" class="checkout-voucher-option {{ !$selectedVoucher ? 'selected' : '' }}" data-voucher-option data-id="">
                                <span class="voucher-radio"></span><span class="voucher-main"><strong>Không dùng mã giảm giá</strong></span>
                            </button>
                            @foreach($vouchers as $voucher)
                                @php $eligible = $subtotal >= (float)$voucher->don_hang_toi_thieu; @endphp
                                <button type="button"
                                    class="checkout-voucher-option {{ (int)$selectedVoucher?->voucher_id === (int)$voucher->voucher_id ? 'selected' : '' }} {{ $eligible ? 'eligible' : 'needs-more' }}"
                                    data-voucher-option data-id="{{ $voucher->voucher_id }}" data-code="{{ $voucher->ma_voucher }}"
                                    data-name="{{ $voucher->ten_voucher }}" data-min="{{ (float)$voucher->don_hang_toi_thieu }}"
                                    data-type="{{ $voucher->loai_giam }}" data-value="{{ (float)$voucher->gia_tri_giam }}"
                                    data-max="{{ $voucher->giam_toi_da !== null ? (float)$voucher->giam_toi_da : '' }}">
                                    <span class="voucher-radio"></span>
                                    <span class="voucher-main"><strong>{{ $voucher->ma_voucher }} · {{ $voucher->ten_voucher }}</strong>
                                    <small>Đơn từ {{ number_format($voucher->don_hang_toi_thieu,0,',','.') }}đ</small></span>
                                </button>
                            @endforeach
                        </section>

                        <section class="voucher-list-section">
                            <h3>Voucher phí vận chuyển</h3>
                            <button type="button" class="checkout-voucher-option shipping-voucher-option {{ !$shippingVoucherId ? 'selected' : '' }}" data-shipping-voucher-option data-id="">
                                <span class="voucher-radio"></span><span class="voucher-main"><strong>Không dùng voucher vận chuyển</strong></span>
                            </button>
                            @foreach($shippingVouchers as $voucher)
                                @php $eligible = $subtotal >= (float)$voucher->don_hang_toi_thieu; @endphp
                                <button type="button"
                                    class="checkout-voucher-option shipping-voucher-option {{ (int)$shippingVoucherId === (int)$voucher->voucher_id ? 'selected' : '' }} {{ $eligible ? 'eligible' : 'needs-more' }}"
                                    data-shipping-voucher-option data-id="{{ $voucher->voucher_id }}" data-code="{{ $voucher->ma_voucher }}"
                                    data-name="{{ $voucher->ten_voucher }}" data-min="{{ (float)$voucher->don_hang_toi_thieu }}"
                                    data-type="{{ $voucher->loai_giam }}" data-value="{{ (float)$voucher->gia_tri_giam }}"
                                    data-max="{{ $voucher->giam_toi_da !== null ? (float)$voucher->giam_toi_da : '' }}">
                                    <span class="voucher-radio"></span>
                                    <span class="voucher-main"><strong>{{ $voucher->ma_voucher }} · {{ $voucher->ten_voucher }}</strong>
                                    <small>Đơn từ {{ number_format($voucher->don_hang_toi_thieu,0,',','.') }}đ</small></span>
                                </button>
                            @endforeach
                        </section>
                    </div>
                </article>
