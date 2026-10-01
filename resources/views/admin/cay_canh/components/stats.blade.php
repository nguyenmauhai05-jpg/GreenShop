{{-- =====================================================
        THỐNG KÊ NHANH
    ====================================================== --}}

    <div class="plant-summary-grid">

        <div class="plant-summary-card">

            <span>
                Tổng số cây
            </span>

            <strong>
                {{ number_format($tongCay ?? 0) }}
            </strong>

        </div>


        <div class="plant-summary-card">

            <span>
                Đang bán
            </span>

            <strong>
                {{ number_format($tongDangBan ?? 0) }}
            </strong>

        </div>


        <div class="plant-summary-card">

            <span>
                Sắp hết hàng
            </span>

            <strong class="orange">
                {{ number_format($tongSapHet ?? 0) }}
            </strong>

        </div>


        <div class="plant-summary-card">

            <span>
                Hết hàng
            </span>

            <strong class="red">
                {{ number_format($tongHetHang ?? 0) }}
            </strong>

        </div>

    </div>
