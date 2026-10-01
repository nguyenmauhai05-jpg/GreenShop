{{-- =====================================================
        FILTER
    ====================================================== --}}

    <form
        method="GET"
        action="{{ route('admin.cay-canh.index') }}"
        class="plant-filter-box"
    >

        {{-- SEARCH --}}
        <div class="plant-search-box">

            <span>
                ⌕
            </span>

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Tìm theo tên cây, mã cây..."
            >

        </div>


        {{-- CATEGORY --}}
        <div class="plant-filter-item">

            <label>
                Danh mục
            </label>

            <select name="category">

                <option value="">
                    Tất cả
                </option>

                @foreach($danhMucs as $danhMuc)

                    <option
                        value="{{ $danhMuc->category_id }}"
                        {{
                            (string) request('category')
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


        {{-- STATUS --}}
        <div class="plant-filter-item">

            <label>
                Trạng thái
            </label>

            <select name="status">

                <option value="">
                    Tất cả
                </option>


                <option
                    value="Đang bán"
                    {{
                        request('status') === 'Đang bán'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đang bán
                </option>


                <option
                    value="Ẩn"
                    {{
                        request('status') === 'Ẩn'
                            ? 'selected'
                            : ''
                    }}
                >
                    Ẩn
                </option>

            </select>

        </div>


        {{-- STOCK --}}
        <div class="plant-filter-item">

            <label>
                Kho hàng
            </label>

            <select name="stock">

                <option value="">
                    Tất cả
                </option>


                <option
                    value="con-hang"
                    {{
                        request('stock') === 'con-hang'
                            ? 'selected'
                            : ''
                    }}
                >
                    Còn hàng
                </option>


                <option
                    value="sap-het"
                    {{
                        request('stock') === 'sap-het'
                            ? 'selected'
                            : ''
                    }}
                >
                    Sắp hết hàng
                </option>


                <option
                    value="het-hang"
                    {{
                        request('stock') === 'het-hang'
                            ? 'selected'
                            : ''
                    }}
                >
                    Hết hàng
                </option>

            </select>

        </div>


        <button
            type="submit"
            class="plant-filter-submit"
        >
            Lọc
        </button>


        <a
            href="{{ route('admin.cay-canh.index') }}"
            class="plant-filter-reset"
        >
            ↻ Xóa bộ lọc
        </a>

    </form>
