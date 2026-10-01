{{-- =====================================================
        TABLE
    ====================================================== --}}

    <div class="category-table-card">

        {{-- TABLE HEADER --}}
        <div class="category-table-header">

            <h2>
                Danh sách danh mục
            </h2>

            <p>
                Danh sách toàn bộ danh mục cây trong GreenShop
            </p>

        </div>


        <div class="category-table-responsive">

            <table class="category-table">

                <thead>

                    <tr>

                        <th>
                            STT
                        </th>

                        <th>
                            Tên danh mục
                        </th>

                        <th>
                            Mô tả
                        </th>

                        <th>
                            Số cây
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Ngày tạo
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse($danhMucs as $index => $danhMuc)

                    <tr>

                        {{-- STT --}}
                        <td>

                            {{
                                method_exists(
                                    $danhMucs,
                                    'firstItem'
                                )
                                    ? $danhMucs->firstItem() + $index
                                    : $index + 1
                            }}

                        </td>


                        {{-- TÊN DANH MỤC --}}
                        <td>

                            <div class="category-name">

                                


                                <div>

                                    <strong>
                                        {{ $danhMuc->ten_danh_muc }}
                                    </strong>

                                </div>

                            </div>

                        </td>


                        {{-- MÔ TẢ --}}
                        <td>

                            <div class="category-description">

                                {{
                                    $danhMuc->mo_ta
                                    ?: 'Chưa có mô tả'
                                }}

                            </div>

                        </td>


                        {{-- SỐ CÂY --}}
                        <td>

                            <span class="category-count">

                                {{ $danhMuc->so_cay ?? 0 }}

                                cây

                            </span>

                        </td>


                        {{-- TRẠNG THÁI --}}
                        <td>

                            @if(
                                $danhMuc->trang_thai
                                ===
                                'Hiển thị'
                            )

                                <span
                                    class="
                                        category-status
                                        status-visible
                                    "
                                >

                                    <i>
                                        ●
                                    </i>

                                    Hiển thị

                                </span>

                            @else

                                <span
                                    class="
                                        category-status
                                        status-hidden
                                    "
                                >

                                    <i>
                                        ●
                                    </i>

                                    Ẩn

                                </span>

                            @endif

                        </td>


                        {{-- NGÀY TẠO --}}
                        <td>

                            @if(
                                !empty(
                                    $danhMuc->created_at
                                )
                            )

                                {{
                                    \Carbon\Carbon::parse(
                                        $danhMuc->created_at
                                    )->format(
                                        'd/m/Y'
                                    )
                                }}

                            @else

                                --

                            @endif

                        </td>


                        {{-- THAO TÁC --}}
                        <td>

                            <div class="category-actions">


                                {{-- SỬA --}}
                                <button
                                    type="button"
                                    class="
                                        category-action-btn
                                        edit
                                    "
                                    title="Sửa danh mục"
                                    onclick="openEditCategoryModal(
                                        {{ $danhMuc->category_id }},
                                        @js($danhMuc->ten_danh_muc),
                                        @js($danhMuc->mo_ta ?? ''),
                                        @js($danhMuc->trang_thai)
                                    )"
                                >
                                    ✎
                                </button>


                                {{-- XÓA --}}
                                <button
                                    type="button"
                                    class="
                                        category-action-btn
                                        delete
                                    "
                                    title="Xóa danh mục"
                                    onclick="openDeleteCategoryModal(
                                        {{ $danhMuc->category_id }},
                                        @js($danhMuc->ten_danh_muc),
                                        {{ $danhMuc->so_cay ?? 0 }}
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
                            colspan="7"
                            class="category-empty"
                        >

                            <i>
                                ▣
                            </i>

                            <h3>
                                Không có danh mục phù hợp
                            </h3>

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

        <x-pagination :paginator="$danhMucs" />

    </div>
