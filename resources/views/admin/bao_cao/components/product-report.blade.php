<div class="report-card report-product-card">

    <div class="report-card-header">

        <div>
            <h2>
                Báo cáo theo sản phẩm
            </h2>

            <p>
                Thống kê sản phẩm đã bán và doanh thu tương ứng.
            </p>
        </div>

    </div>


    <div class="report-table-responsive">

        <table class="report-table">

            <thead>

                <tr>
                    <th>STT</th>
                    <th>Sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Đã bán</th>
                    <th>Doanh thu</th>
                    <th>Tỷ lệ</th>
                </tr>

            </thead>


            <tbody>

            @forelse($sanPhamBaoCao as $index => $sanPham)

                <tr>

                    <td>
                        {{
                            method_exists(
                                $sanPhamBaoCao,
                                'firstItem'
                            )
                                ? $sanPhamBaoCao->firstItem() + $index
                                : $index + 1
                        }}
                    </td>


                    <td>

                        <div class="report-product-info">

                            <div class="report-product-image">

                                @if(!empty($sanPham->anh_dai_dien))

                                    <img
                                        src="{{ asset($sanPham->anh_dai_dien) }}"
                                        alt="{{ $sanPham->ten_cay }}"
                                    >

                                @else

                                    🌱

                                @endif

                            </div>


                            <div>

                                <strong>
                                    {{ $sanPham->ten_cay }}
                                </strong>

                                <span>
                                    Mã: CC{{ str_pad(
                                        $sanPham->plant_id,
                                        3,
                                        '0',
                                        STR_PAD_LEFT
                                    ) }}
                                </span>

                            </div>

                        </div>

                    </td>


                    <td>
                        {{ $sanPham->ten_danh_muc ?? 'Chưa phân loại' }}
                    </td>


                    <td>
                        {{ number_format($sanPham->so_luong_da_ban ?? 0) }}
                    </td>


                    <td>

                        <strong class="report-money">

                            {{
                                number_format(
                                    $sanPham->doanh_thu ?? 0,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}₫

                        </strong>

                    </td>


                    <td>

                        <span class="report-percent">
                            {{ $sanPham->ty_le ?? 0 }}%
                        </span>

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="6"
                        class="report-table-empty"
                    >
                        Không có dữ liệu sản phẩm phù hợp.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    <x-pagination :paginator="$sanPhamBaoCao" />

</div>