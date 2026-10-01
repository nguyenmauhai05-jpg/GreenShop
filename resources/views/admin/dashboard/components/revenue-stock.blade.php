    {{-- =====================================================
        HÀNG 2
        BIỂU ĐỒ + CÂY SẮP HẾT
    ====================================================== --}}

    <div class="dashboard-row dashboard-row-main">


        {{-- BIỂU ĐỒ --}}
        <section class="dashboard-panel dashboard-revenue">

            <div class="dashboard-panel-header">

                <h3>
                    Doanh thu 7 ngày qua
                </h3>

                <span>
                    Doanh thu
                </span>

            </div>


            <div class="dashboard-chart">

                @if(isset($doanhThu7Ngay) && $doanhThu7Ngay->isNotEmpty())

                    <div class="dashboard-chart-placeholder">

                        @foreach($doanhThu7Ngay as $item)

                            <div class="chart-column">

                                <div class="chart-value">
                                    {{ number_format($item->doanh_thu ?? 0, 0, ',', '.') }}₫
                                </div>

                                <div
                                    class="chart-bar"
                                    title="{{ $item->ngay_day_du ?? $item->ngay }}: {{ number_format($item->doanh_thu ?? 0, 0, ',', '.') }}₫"
                                    style="height:
                                    {{
                                        ($item->doanh_thu ?? 0) > 0
                                            ? min(150, max(10, (($item->doanh_thu ?? 0) / max($doanhThuMax ?? 1, 1)) * 150))
                                            : 4
                                    }}px"
                                ></div>

                                <span>
                                    {{ $item->ngay ?? '' }}
                                </span>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="dashboard-empty">
                        Chưa có dữ liệu doanh thu.
                    </div>

                @endif

            </div>

        </section>



        {{-- CÂY SẮP HẾT HÀNG --}}
        <section class="dashboard-panel dashboard-low-stock">

            <div class="dashboard-panel-header">

                <h3>
                    Cây sắp hết hàng
                </h3>



            </div>


            <div class="dashboard-table-wrapper">

                <table class="dashboard-table">

                    <thead>

                    <tr>
                        <th>Cây</th>
                        <th>Số lượng</th>
                        <th>Trạng thái</th>
                    </tr>

                    </thead>


                    <tbody>

                    @forelse($caySapHetHang ?? [] as $cay)

                        <tr>

                            <td>

                                <div class="dashboard-product">

                                    @if(!empty($cay->anh_dai_dien))

                                        <img
                                            src="{{ asset($cay->anh_dai_dien) }}"
                                            alt="{{ $cay->ten_cay }}"
                                        loading="lazy" decoding="async"
>

                                    @else

                                        <div class="dashboard-product-image">
                                            🌱
                                        </div>

                                    @endif


                                    <div>

                                        <strong>
                                            {{ $cay->ten_cay }}
                                        </strong>

                                        <span>
                                            Mã: CC{{ str_pad($cay->plant_id, 3, '0', STR_PAD_LEFT) }}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>

                                <strong>
                                    {{ $cay->so_luong }}
                                </strong>

                            </td>


                            <td>

                                @if($cay->so_luong <= 5)

                                    <span class="status-danger">
                                        Sắp hết
                                    </span>

                                @else

                                    <span class="status-warning">
                                        Còn ít
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="3"
                                class="dashboard-empty-table"
                            >
                                Không có cây sắp hết hàng.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    </div>
