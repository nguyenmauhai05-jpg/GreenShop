<div class="report-summary-grid">

    {{-- DOANH THU --}}
    <div class="report-summary-card">

        <div class="report-summary-icon">
            ₫
        </div>

        <div>

            <span>
                Tổng doanh thu
            </span>

            <strong>
                {{
                    number_format(
                        $tongDoanhThu ?? 0,
                        0,
                        ',',
                        '.'
                    )
                }}₫
            </strong>

        </div>

    </div>


    {{-- ĐƠN HÀNG --}}
    <div class="report-summary-card">

        <div class="report-summary-icon">
            🧾
        </div>

        <div>

            <span>
                Tổng đơn hàng
            </span>

            <strong>
                {{ number_format($tongDonHang ?? 0) }}
            </strong>

        </div>

    </div>


    {{-- SẢN PHẨM --}}
    <div class="report-summary-card">

        <div class="report-summary-icon">
            🌱
        </div>

        <div>

            <span>
                Sản phẩm đã bán
            </span>

            <strong>
                {{ number_format($tongSanPhamDaBan ?? 0) }}
            </strong>

        </div>

    </div>


    {{-- KHÁCH HÀNG --}}
    <div class="report-summary-card">

        <div class="report-summary-icon">
            👤
        </div>

        <div>

            <span>
                Khách hàng mới
            </span>

            <strong>
                {{ number_format($tongKhachHangMoi ?? 0) }}
            </strong>

        </div>

    </div>

</div>