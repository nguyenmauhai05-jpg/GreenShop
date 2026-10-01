<div class="report-filter-card">

    <form
        method="GET"
        action="{{ route('admin.bao-cao.index') }}"
        class="report-filter-form" id="reportFilterForm"
    >

        {{-- TỪ NGÀY --}}
        <div class="report-filter-group">

            <label for="reportFromDate">Từ ngày</label>

            <input
                id="reportFromDate"
                type="text"
                name="tu_ngay"
                inputmode="numeric"
                maxlength="10"
                autocomplete="off"
                placeholder="DD/MM/YYYY"
                value="{{ old('tu_ngay', request('tu_ngay', $tuNgay->format('d/m/Y'))) }}"
                data-report-date
            >

            @error('tu_ngay')
                <small class="report-filter-error">{{ $message }}</small>
            @enderror
        </div>


        {{-- ĐẾN NGÀY --}}
        <div class="report-filter-group">

            <label for="reportToDate">Đến ngày</label>

            <input
                id="reportToDate"
                type="text"
                name="den_ngay"
                inputmode="numeric"
                maxlength="10"
                autocomplete="off"
                placeholder="DD/MM/YYYY"
                value="{{ old('den_ngay', request('den_ngay', $denNgay->format('d/m/Y'))) }}"
                data-report-date
            >

            @error('den_ngay')
                <small class="report-filter-error">{{ $message }}</small>
            @enderror
        </div>


        {{-- Đã bỏ bộ lọc Loại báo cáo --}}


        {{-- NHÓM THEO --}}
        <div class="report-filter-group">

            <label>
                Nhóm theo
            </label>

            <select name="nhom_theo">

                <option
                    value="ngay"
                    {{
                        $nhomTheo === 'ngay'
                            ? 'selected'
                            : ''
                    }}
                >
                    Ngày
                </option>

                <option
                    value="thang"
                    {{
                        $nhomTheo === 'thang'
                            ? 'selected'
                            : ''
                    }}
                >
                    Tháng
                </option>

                <option
                    value="nam"
                    {{
                        $nhomTheo === 'nam'
                            ? 'selected'
                            : ''
                    }}
                >
                    Năm
                </option>

            </select>

        </div>


        {{-- DANH MỤC --}}
        <div class="report-filter-group">

            <label>
                Danh mục
            </label>

            <select name="category_id">

                <option value="">
                    Tất cả danh mục
                </option>

                @foreach($danhMucs as $danhMuc)

                    <option
                        value="{{ $danhMuc->category_id }}"
                        {{
                            (string) $categoryId
                            ===
                            (string) $danhMuc->category_id
                                ? 'selected'
                                : ''
                        }}
                    >
                        {{ $danhMuc->ten_danh_muc }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- TRẠNG THÁI ĐƠN --}}
        <div class="report-filter-group">

            <label>
                Trạng thái đơn
            </label>

            <select name="trang_thai_don">

                <option value="">
                    Tất cả trạng thái
                </option>

                <option
                    value="pending_confirmation"
                    {{
                        $orderStatus === 'pending_confirmation'
                            ? 'selected'
                            : ''
                    }}
                >
                    Chờ xác nhận
                </option>

                <option
                    value="preparing"
                    {{
                        $orderStatus === 'preparing'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đang chuẩn bị
                </option>

                <option
                    value="shipping"
                    {{
                        $orderStatus === 'shipping'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đang giao
                </option>

                <option
                    value="delivered"
                    {{
                        $orderStatus === 'delivered'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đã giao
                </option>

                <option
                    value="completed"
                    {{
                        $orderStatus === 'completed'
                            ? 'selected'
                            : ''
                    }}
                >
                    Hoàn thành
                </option>

                <option
                    value="cancelled"
                    {{
                        $orderStatus === 'cancelled'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đã hủy
                </option>

            </select>

        </div>


        <div class="report-filter-actions">

            <button
                type="submit"
                class="report-filter-btn"
            >
                Áp dụng
            </button>


            <a
                href="{{ route('admin.bao-cao.index') }}"
                class="report-reset-btn"
            >
                Xóa bộ lọc
            </a>

        </div>

    </form>

</div>