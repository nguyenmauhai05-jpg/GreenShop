{{-- =====================================================
        FILTER
    ====================================================== --}}

    <div class="category-filter-box">

        <form
            method="GET"
            action="{{ route('admin.danh-muc.index') }}"
            class="category-filter-form"
        >

            {{-- SEARCH --}}
            <div class="category-search">

                <i>
                    ⌕
                </i>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Tìm kiếm danh mục theo tên..."
                >

            </div>


            {{-- STATUS --}}
            <div class="category-filter-select">

                <i>
                    ▾
                </i>

                <select name="status">

                    <option value="">
                        Tất cả trạng thái
                    </option>


                    <option
                        value="Hiển thị"
                        {{
                            request('status') === 'Hiển thị'
                                ? 'selected'
                                : ''
                        }}
                    >
                        Hiển thị
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


            {{-- FILTER --}}
            <button
                type="submit"
                class="btn-filter"
            >
                Lọc
            </button>


            {{-- CLEAR --}}
            <a
                href="{{ route('admin.danh-muc.index') }}"
                class="btn-clear-filter"
            >
                ↻ Xóa bộ lọc
            </a>

        </form>

    </div>
