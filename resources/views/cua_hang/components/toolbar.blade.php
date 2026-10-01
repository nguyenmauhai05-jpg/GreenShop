<div class="shop-toolbar">

    <div class="shop-toolbar-actions">

        {{-- SỐ SẢN PHẨM / TRANG --}}
        <form
            method="GET"
            action="{{ route('cua-hang') }}"
            class="toolbar-form"
        >

            @foreach(
                request()->except('page', 'per_page')
                as $key => $value
            )

                @if(is_array($value))

                    @foreach($value as $subValue)

                        <input
                            type="hidden"
                            name="{{ $key }}[]"
                            value="{{ $subValue }}"
                        >

                    @endforeach

                @else

                    <input
                        type="hidden"
                        name="{{ $key }}"
                        value="{{ $value }}"
                    >

                @endif

            @endforeach


            
        </form>


        {{-- SẮP XẾP --}}
        <form
            method="GET"
            action="{{ route('cua-hang') }}"
            class="toolbar-form"
        >

            @foreach(
                request()->except('page', 'sort')
                as $key => $value
            )

                @if(is_array($value))

                    @foreach($value as $subValue)

                        <input
                            type="hidden"
                            name="{{ $key }}[]"
                            value="{{ $subValue }}"
                        >

                    @endforeach

                @else

                    <input
                        type="hidden"
                        name="{{ $key }}"
                        value="{{ $value }}"
                    >

                @endif

            @endforeach


            <label>Sắp xếp</label>

            <select
                name="sort"
                onchange="this.form.submit()"
            >

                <option
                    value="newest"
                    {{ request('sort', 'newest') === 'newest'
                        ? 'selected'
                        : '' }}
                >
                    Mới nhất
                </option>


                <option
                    value="best-selling"
                    {{ request('sort') === 'best-selling'
                        ? 'selected'
                        : '' }}
                >
                    Bán chạy nhất
                </option>


                <option
                    value="price-asc"
                    {{ request('sort') === 'price-asc'
                        ? 'selected'
                        : '' }}
                >
                    Giá thấp đến cao
                </option>


                <option
                    value="price-desc"
                    {{ request('sort') === 'price-desc'
                        ? 'selected'
                        : '' }}
                >
                    Giá cao đến thấp
                </option>

            </select>

        </form>

    </div>

</div>