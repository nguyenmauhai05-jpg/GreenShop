{{-- =====================================================
        TABLE
    ====================================================== --}}

    <div class="plant-table-card">

        <div class="plant-table-header">
            <h2>Danh sách cây</h2>
            <p>Danh sách toàn bộ cây cảnh trong GreenShop</p>
        </div>

        <div class="plant-table-scroll">


            <table class="plant-table">


                <thead>

                    <tr>

                        <th>
                            STT
                        </th>


                        <th>
                            Ảnh
                        </th>


                        <th>
                            Tên cây
                        </th>


                        <th>
                            Danh mục
                        </th>


                        <th>
                            Giá bán
                        </th>


                        <th>
                            Số lượng
                        </th>


                        <th>
                            Tình trạng
                        </th>


                        <th>
                            Hiển thị
                        </th>


                        <th class="action-column">
                            Thao tác
                        </th>

                    </tr>

                </thead>



                <tbody>


                @forelse($cayCanhs as $index => $cay)


                    <tr>


                        {{-- STT --}}
                        <td>

                            {{
                                method_exists(
                                    $cayCanhs,
                                    'firstItem'
                                )
                                    ? $cayCanhs->firstItem() + $index
                                    : $index + 1
                            }}

                        </td>



                        {{-- IMAGE --}}
                        <td>

                            <div class="plant-table-image">


                                @if(!empty($cay->anh_dai_dien))

                                    <img
                                        src="{{ asset($cay->anh_dai_dien) }}"
                                        alt="{{ $cay->ten_cay }}"
                                    loading="lazy" decoding="async"
>

                                @else

                                    <span>
                                        🌱
                                    </span>

                                @endif


                            </div>

                        </td>



                        {{-- NAME --}}
                        <td>

                            <div class="plant-name-cell">

                                <strong>
                                    {{ $cay->ten_cay }}
                                </strong>


                                <span>

                                    Mã: CC{{ str_pad(
                                        $cay->plant_id,
                                        3,
                                        '0',
                                        STR_PAD_LEFT
                                    ) }}

                                </span>

                            </div>

                        </td>



                        {{-- CATEGORY --}}
                        <td>

                            {{
                                $cay->ten_danh_muc
                                ?? 'Chưa phân loại'
                            }}

                        </td>



                        {{-- PRICE --}}
                        <td>

                            <strong class="plant-price">

                                {{
                                    number_format(
                                        $cay->gia,
                                        0,
                                        ',',
                                        '.'
                                    )
                                }}₫

                            </strong>

                        </td>



                        {{-- STOCK --}}
                        <td>

                            <strong>
                                {{ $cay->so_luong }}
                            </strong>

                        </td>



                        {{-- STOCK STATUS --}}
                        <td>


                            @if($cay->so_luong <= 0)

                                <span class="plant-stock-badge out">
                                    ● Hết hàng
                                </span>


                            @elseif($cay->so_luong <= 10)

                                <span class="plant-stock-badge low">
                                    ● Sắp hết hàng
                                </span>


                            @else

                                <span class="plant-stock-badge available">
                                    ● Còn hàng
                                </span>

                            @endif


                        </td>



                        {{-- DISPLAY STATUS --}}
                        <td>

                            <span
                                class="
                                    plant-display-badge
                                    {{
                                        $cay->trang_thai === 'Đang bán'
                                            ? 'selling'
                                            : 'hidden'
                                    }}
                                "
                            >

                                {{ $cay->trang_thai }}

                            </span>

                        </td>



                        {{-- =================================================
                            ACTION
                        ================================================== --}}

                        <td>


                            <div class="plant-actions">


                                {{-- XEM CHI TIẾT --}}
                                <a
                                    href="{{ route(
                                        'admin.cay-canh.show',
                                        $cay->plant_id
                                    ) }}"
                                    class="plant-action view"
                                    title="Xem chi tiết"
                                >
                                    ◉
                                </a>



                                {{-- SỬA --}}
                                <a
                                    href="{{ route(
                                        'admin.cay-canh.edit',
                                        $cay->plant_id
                                    ) }}"
                                    class="plant-action edit"
                                    title="Sửa cây"
                                >
                                    ✎
                                </a>



                                {{-- XÓA --}}
                                <button
                                    type="button"
                                    class="plant-action delete"
                                    title="Xóa cây"
                                    onclick="openDeletePlantModal(
                                        {{ $cay->plant_id }},
                                        @js($cay->ten_cay)
                                    )"
                                >
                                    🗑
                                </button>


                            </div>


                        </td>


                    </tr>


                @empty


                    <tr>

                        <td
                            colspan="9"
                            class="plant-table-empty"
                        >

                            <div>
                                🌱
                            </div>


                            <strong>
                                Không có cây phù hợp
                            </strong>


                            <p>
                                Hãy thử thay đổi từ khóa
                                hoặc bộ lọc.
                            </p>

                        </td>

                    </tr>


                @endforelse


                </tbody>


            </table>


        </div>



        {{-- =================================================
            PAGINATION
        ================================================== --}}

        <x-pagination :paginator="$cayCanhs" />



    </div>


</div>
