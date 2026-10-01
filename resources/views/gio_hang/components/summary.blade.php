<aside class="cart-summary">

    <div class="summary-card">

        <h2>
            Tổng cộng
        </h2>


        <div class="summary-row">

            <span>
                Tổng số lượng
            </span>

            <strong>
                {{ $tongSoLuong ?? 0 }}
            </strong>

        </div>


        <div class="summary-row">

            <span>
                Tạm tính
            </span>

            <strong class="summary-subtotal">

                {{
                    number_format(
                        $tamTinh ?? 0,
                        0,
                        ',',
                        '.'
                    )
                }}₫

            </strong>

        </div>


        <p class="summary-note">
            Phí vận chuyển và thuế sẽ được tính
            tại bước thanh toán.
        </p>


        @if($gioHopLe ?? false)

            <a
                href="{{ url('/thanh-toan') }}"
                class="checkout-button"
            >
                Tiến hành thanh toán
            </a>

        @else

            <button
                type="button"
                class="checkout-button disabled"
                disabled
            >
                Tiến hành thanh toán
            </button>

            <p class="checkout-warning">
                Vui lòng cập nhật giỏ hàng trước khi thanh toán.
            </p>

        @endif


        <div class="secure-payment">

            <span>🛡</span>

            <span>
                Thanh toán bảo mật 100%
            </span>

        </div>

    </div>

</aside>