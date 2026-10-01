@extends('admin.layouts.app')


@section('title', 'Sửa cây - GreenShop Admin')


@section('page-title', 'Sửa cây')


@section('styles')
    @vite('resources/css/admin/cay-canh.css')
@endsection

@section('content')

<div class="plant-form-page">

    @if(session('success'))

        <div class="plant-alert plant-alert-success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="plant-alert plant-alert-error">
            {{ session('error') }}
        </div>

    @endif


    <form
        method="POST"
        action="{{ route(
            'admin.cay-canh.update',
            $cay->plant_id
        ) }}"
        enctype="multipart/form-data"
        class="plant-form-card"
    >
        @csrf
        @method('PUT')


        <div class="plant-form-grid">


            {{-- =================================================
                LEFT
            ================================================== --}}

            <div class="plant-form-main">


                {{-- TÊN --}}
                <div class="plant-form-group">

                    <label for="ten_cay">
                        Tên cây
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="ten_cay"
                        name="ten_cay"
                        value="{{ old(
                            'ten_cay',
                            $cay->ten_cay
                        ) }}"
                        class="{{ $errors->has('ten_cay') ? 'is-invalid' : '' }}"
                    >

                    @error('ten_cay')
                        <div class="plant-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- DANH MỤC --}}
                <div class="plant-form-group">

                    <label for="category_id">
                        Danh mục
                        <span>*</span>
                    </label>

                    <select
                        id="category_id"
                        name="category_id"
                        class="{{ $errors->has('category_id') ? 'is-invalid' : '' }}"
                    >

                        <option value="">
                            -- Chọn danh mục --
                        </option>

                        @foreach($danhMucs as $danhMuc)

                            <option
                                value="{{ $danhMuc->category_id }}"
                                {{
                                    (string) old(
                                        'category_id',
                                        $cay->category_id
                                    )
                                    ===
                                    (string) $danhMuc->category_id
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $danhMuc->ten_danh_muc }}
                            </option>

                        @endforeach

                    </select>

                    @error('category_id')
                        <div class="plant-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- GIÁ + SỐ LƯỢNG --}}
                <div class="plant-form-row">

                    <div class="plant-form-group">

                        <label for="gia">
                            Giá bán
                            <span>*</span>
                        </label>

                        <input
                            type="number"
                            id="gia"
                            name="gia"
                            value="{{ old(
                                'gia',
                                $cay->gia
                            ) }}"
                            min="0"
                            step="1000"
                            class="{{ $errors->has('gia') ? 'is-invalid' : '' }}"
                        >

                        @error('gia')
                            <div class="plant-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="plant-form-group">

                        <label for="so_luong">
                            Số lượng tồn kho
                            <span>*</span>
                        </label>

                        <input
                            type="number"
                            id="so_luong"
                            name="so_luong"
                            value="{{ old(
                                'so_luong',
                                $cay->so_luong
                            ) }}"
                            min="0"
                            step="1"
                            class="{{ $errors->has('so_luong') ? 'is-invalid' : '' }}"
                        >

                        @error('so_luong')
                            <div class="plant-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- CHIỀU CAO + TRẠNG THÁI --}}
                <div class="plant-form-row">

                    <div class="plant-form-group">

                        <label for="chieu_cao">
                            Chiều cao
                        </label>

                        <input
                            type="text"
                            id="chieu_cao"
                            name="chieu_cao"
                            value="{{ old(
                                'chieu_cao',
                                $cay->chieu_cao
                            ) }}"
                            maxlength="50"
                            placeholder="Ví dụ: 40 hoặc 30-50 cm"
                            class="{{ $errors->has('chieu_cao') ? 'is-invalid' : '' }}"
                        >

                        @error('chieu_cao')
                            <div class="plant-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="plant-form-group">

                        <label for="trang_thai">
                            Trạng thái
                            <span>*</span>
                        </label>

                        <select
                            id="trang_thai"
                            name="trang_thai"
                            class="{{ $errors->has('trang_thai') ? 'is-invalid' : '' }}"
                        >

                            <option
                                value="Đang bán"
                                {{
                                    old(
                                        'trang_thai',
                                        $cay->trang_thai
                                    )
                                    ===
                                    'Đang bán'
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                Đang bán
                            </option>

                            <option
                                value="Ẩn"
                                {{
                                    old(
                                        'trang_thai',
                                        $cay->trang_thai
                                    )
                                    ===
                                    'Ẩn'
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                Ẩn
                            </option>

                        </select>

                        @error('trang_thai')
                            <div class="plant-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- MÔ TẢ --}}
                <div class="plant-form-group">

                    <label for="mo_ta">
                        Mô tả
                    </label>

                    <textarea
                        id="mo_ta"
                        name="mo_ta"
                        rows="5"
                    >{{ old(
                        'mo_ta',
                        $cay->mo_ta
                    ) }}</textarea>

                    @error('mo_ta')
                        <div class="plant-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- CHĂM SÓC --}}
                <div class="plant-form-group">

                    <label for="cach_cham_soc">
                        Cách chăm sóc
                    </label>

                    <textarea
                        id="cach_cham_soc"
                        name="cach_cham_soc"
                        rows="6"
                    >{{ old(
                        'cach_cham_soc',
                        $cay->cach_cham_soc
                    ) }}</textarea>

                    @error('cach_cham_soc')
                        <div class="plant-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- =================================================
                IMAGE
            ================================================== --}}

            <aside class="plant-form-side">

                <div class="plant-upload-card">

                    <label>
                        Ảnh đại diện
                    </label>


                    <div
                        class="plant-upload-preview"
                        id="plantImagePreview"
                    >

                        @if(!empty($cay->anh_dai_dien))

                            <img
                                id="plantPreviewImage"
                                src="{{ asset(
                                    $cay->anh_dai_dien
                                ) }}"
                                alt="{{ $cay->ten_cay }}"
                            >

                            <div
                                class="plant-upload-placeholder"
                                id="plantUploadPlaceholder"
                                style="display: none;"
                            >
                            </div>

                        @else

                            <div
                                class="plant-upload-placeholder"
                                id="plantUploadPlaceholder"
                            >

                                <div>
                                    🖼
                                </div>

                                <strong>
                                    Chưa có ảnh
                                </strong>

                            </div>


                            <img
                                id="plantPreviewImage"
                                src=""
                                alt="Xem trước ảnh cây"
                                style="display: none;"
                            >

                        @endif

                    </div>


                    <label
                        for="anh_dai_dien"
                        class="plant-upload-btn"
                    >
                        Thay ảnh
                    </label>


                    <input
                        type="file"
                        id="anh_dai_dien"
                        name="anh_dai_dien"
                        accept=".jpg,.jpeg,.png,.webp"
                        hidden
                    >


                    <p class="plant-upload-note">
                        Để trống nếu muốn giữ ảnh hiện tại.
                        JPG, JPEG, PNG, WEBP. Tối đa 5MB.
                    </p>


                    @error('anh_dai_dien')
                        <div class="plant-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </aside>

        </div>


        {{-- ACTION --}}
        <div class="plant-form-actions">

            <a
                href="{{ route('admin.cay-canh.index') }}"
                class="plant-cancel-btn"
            >
                Hủy bỏ
            </a>


            <button
                type="submit"
                class="plant-save-btn"
            >
                Lưu thay đổi
            </button>

        </div>

    </form>

</div>

@endsection


@section('scripts')

@vite('resources/js/admin/plant-form.js')

@endsection