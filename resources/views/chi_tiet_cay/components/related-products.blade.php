<section class="related-products">

    <div class="related-heading">

        <h2>
            Sản phẩm liên quan
        </h2>


        <a href="{{ route('cua-hang') }}">
            Xem tất cả →
        </a>

    </div>


    <div class="related-grid">

        @forelse($cayLienQuan as $item)

            @php
                $anhLienQuan = $item->anh_dai_dien;

                if (
                    $anhLienQuan
                    &&
                    !str_starts_with(
                        $anhLienQuan,
                        'images/'
                    )
                ) {
                    $anhLienQuan =
                        'images/trang-chu/products/'
                        . $anhLienQuan;
                }
            @endphp


            <a
                href="{{ route(
                    'chi-tiet-cay',
                    $item->plant_id
                ) }}"
                class="related-card"
            >

                <div class="related-image">

                    @if($item->anh_dai_dien)

                        <img
                            src="{{ asset($anhLienQuan) }}"
                            alt="{{ $item->ten_cay }}"
                        loading="lazy" decoding="async"
>

                    @else

                        <div class="related-no-image">
                            🌱
                        </div>

                    @endif

                </div>


                <div class="related-info">

                    <h3>
                        {{ $item->ten_cay }}
                    </h3>

                    <strong>
                        {{
                            number_format(
                                $item->gia,
                                0,
                                ',',
                                '.'
                            )
                        }}₫
                    </strong>

                </div>

            </a>

        @empty

            <div class="related-empty">
                Hiện chưa có cây cùng loại.
            </div>

        @endforelse

    </div>

</section>