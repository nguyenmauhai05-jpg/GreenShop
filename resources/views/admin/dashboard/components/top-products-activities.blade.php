    {{-- =====================================================
        HÀNG 3
        TOP 5 + HOẠT ĐỘNG
    ====================================================== --}}

    <div class="dashboard-row dashboard-row-bottom">


        {{-- TOP 5 --}}
        <section class="dashboard-panel">

            <div class="dashboard-panel-header">

                <h3>
                    Top 5 cây bán chạy
                </h3>

                <a href="#">
                    Xem tất cả
                </a>

            </div>


            <div class="dashboard-table-wrapper">

                <table class="dashboard-table">

                    <thead>

                    <tr>
                        <th>#</th>
                        <th>Cây</th>
                        <th>Đã bán</th>
                        <th>Doanh thu</th>
                    </tr>

                    </thead>


                    <tbody>

                    @forelse($topCayBanChay ?? [] as $index => $cay)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

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
                                {{ $cay->da_ban ?? 0 }}
                            </td>


                            <td>

                                {{ number_format(
                                    $cay->doanh_thu ?? 0,
                                    0,
                                    ',',
                                    '.'
                                ) }}₫

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="dashboard-empty-table"
                            >
                                Chưa có dữ liệu bán hàng.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </section>



        {{-- HOẠT ĐỘNG GẦN ĐÂY --}}
        <section class="dashboard-panel">

            <div class="dashboard-panel-header">

                <h3>
                    Hoạt động gần đây
                </h3>

            </div>


            <div class="dashboard-activities">

                @forelse($hoatDongGanDay ?? [] as $activity)

                    <div class="dashboard-activity-item">

                        <div class="dashboard-activity-icon">
                            {{ $activity->icon ?? '✓' }}
                        </div>

                        <div class="dashboard-activity-content">

                            <p>
                                {{ $activity->noi_dung_hien_thi ?? '' }}
                            </p>

                            <span>
                                {{ $activity->thoi_gian_hien_thi ?? '' }}
                            </span>

                        </div>

                    </div>

                @empty

                    <div class="dashboard-empty">
                        Hiện chưa có đánh giá thấp (1–2 sao).
                    </div>

                @endforelse

            </div>

        </section>

    </div>
