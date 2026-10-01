{{-- =====================================================
        THỐNG KÊ
    ====================================================== --}}

    <div class="category-stats">


        {{-- TỔNG --}}
        <div class="category-stat-card">

            <div class="stat-icon">
                ▣
            </div>

            <div>

                <span>
                    Tổng danh mục
                </span>

                <strong>
                    {{ number_format($tongDanhMuc ?? 0) }}
                </strong>

            </div>

        </div>


        {{-- HIỂN THỊ --}}
        <div class="category-stat-card">

            <div class="stat-icon">
                ✓
            </div>

            <div>

                <span>
                    Đang hiển thị
                </span>

                <strong>
                    {{ number_format($tongHienThi ?? 0) }}
                </strong>

            </div>

        </div>


        {{-- ẨN --}}
        <div class="category-stat-card">

            <div class="stat-icon">
                ◌
            </div>

            <div>

                <span>
                    Đang ẩn
                </span>

                <strong>
                    {{ number_format($tongAn ?? 0) }}
                </strong>

            </div>

        </div>

    </div>
