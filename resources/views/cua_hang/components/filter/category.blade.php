        {{-- =====================================================
             DANH MỤC
        ====================================================== --}}

        <div class="filter-group">

            <button
                type="button"
                class="filter-group-title filter-toggle"
                aria-expanded="true"
            >

                <span>
                    Danh mục
                </span>

                <span class="filter-chevron">

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="m6 15 6-6 6 6"/>
                    </svg>

                </span>

            </button>


            <div class="filter-group-content">

                <label class="filter-checkbox-row">

                    <input
                        type="radio"
                        name="category"
                        value=""
                        {{ !request('category') ? 'checked' : '' }}
                    >

                    <span class="custom-check"></span>

                    <span class="filter-text">
                        Tất cả danh mục
                    </span>

                </label>


                @foreach($danhMucs as $danhMuc)

                    @if(
                        mb_strtolower(
                            trim($danhMuc->ten_danh_muc)
                        )
                        !== 'chăm sóc cây'
                    )

                        <label class="filter-checkbox-row">

                            <input
                                type="radio"
                                name="category"
                                value="{{ $danhMuc->category_id }}"
                                {{
                                    request('category')
                                    == $danhMuc->category_id
                                    ? 'checked'
                                    : ''
                                }}
                            >

                            <span class="custom-check"></span>


                            <span class="filter-text">

                                {{ $danhMuc->ten_danh_muc }}

                                <small>
                                    ({{ $danhMuc->tong_so_cay }})
                                </small>

                            </span>

                        </label>

                    @endif

                @endforeach

            </div>

        </div>

