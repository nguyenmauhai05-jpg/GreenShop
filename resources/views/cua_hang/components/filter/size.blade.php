        {{-- =====================================================
             KÍCH THƯỚC
        ====================================================== --}}

        <div class="filter-group">

            <button
                type="button"
                class="filter-group-title filter-toggle"
                aria-expanded="true"
            >

                <span>
                    Kích thước
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


                {{-- NHỎ --}}

                <label class="filter-checkbox-row">

                    <input
                        type="checkbox"

                        name="size[]"

                        value="small"

                        {{
                            in_array(
                                'small',
                                (array) request('size', [])
                            )
                            ? 'checked'
                            : ''
                        }}
                    >

                    <span class="custom-check"></span>

                    <span class="filter-text">
                        Nhỏ (&lt; 30cm)
                    </span>

                </label>


                {{-- TRUNG BÌNH --}}

                <label class="filter-checkbox-row">

                    <input
                        type="checkbox"

                        name="size[]"

                        value="medium"

                        {{
                            in_array(
                                'medium',
                                (array) request('size', [])
                            )
                            ? 'checked'
                            : ''
                        }}
                    >

                    <span class="custom-check"></span>

                    <span class="filter-text">
                        Trung bình (30 - 80cm)
                    </span>

                </label>


                {{-- LỚN --}}

                <label class="filter-checkbox-row">

                    <input
                        type="checkbox"

                        name="size[]"

                        value="large"

                        {{
                            in_array(
                                'large',
                                (array) request('size', [])
                            )
                            ? 'checked'
                            : ''
                        }}
                    >

                    <span class="custom-check"></span>

                    <span class="filter-text">
                        Lớn (&gt; 80cm)
                    </span>

                </label>

            </div>

        </div>

