<div
    id="createCategoryModal"
    class="category-modal"
>

    <div
        class="category-modal-overlay"
        onclick="closeCreateCategoryModal()"
    ></div>


    <div class="category-modal-dialog">

        {{-- HEADER --}}
        <div class="category-modal-header">

            <div>

                <h2>
                    Thêm danh mục mới
                </h2>

                <p>
                    Nhập thông tin danh mục cây mới.
                </p>

            </div>


            <button
                type="button"
                class="category-modal-close"
                onclick="closeCreateCategoryModal()"
            >
                ×
            </button>

        </div>


        <form
            id="createCategoryForm"
            method="POST"
            action="{{ route('admin.danh-muc.store') }}"
            novalidate
        >
            @csrf


            {{-- BODY --}}
            <div class="category-modal-body">

                {{-- TÊN DANH MỤC --}}
                <div class="category-form-group">

                    <label for="create_ten_danh_muc">
                        Tên danh mục
                        <span>*</span>
                    </label>


                    <input
                        type="text"
                        id="create_ten_danh_muc"
                        name="ten_danh_muc"
                        value="{{ old('ten_danh_muc') }}"
                        placeholder="Nhập tên danh mục"
                        autocomplete="off"
                    >


                    <small>
                        Tối đa 100 ký tự.
                    </small>


                    <div
                        id="createNameError"
                        class="category-field-error {{ $errors->createCategory->has('ten_danh_muc') ? 'show' : '' }}"
                    >{{ $errors->createCategory->first('ten_danh_muc') }}</div>

                </div>


                {{-- MÔ TẢ --}}
                <div class="category-form-group">

                    <label for="create_mo_ta">
                        Mô tả
                    </label>


                    <textarea
                        id="create_mo_ta"
                        name="mo_ta"
                        placeholder="Nhập mô tả danh mục..."
                    >{{ old('mo_ta') }}</textarea>


                    <small>
                        Tối đa 255 ký tự.
                    </small>


                    <div
                        id="createDescriptionError"
                        class="category-field-error {{ $errors->createCategory->has('mo_ta') ? 'show' : '' }}"
                    >{{ $errors->createCategory->first('mo_ta') }}</div>

                </div>


                {{-- TRẠNG THÁI --}}
                <div class="category-form-group">

                    <label>
                        Trạng thái
                        <span>*</span>
                    </label>


                    <div class="category-radio-group">

                        {{-- HIỂN THỊ --}}
                        <label class="category-radio-option">

                            <input
                                type="radio"
                                name="trang_thai"
                                value="Hiển thị"
                                {{
                                    old(
                                        'trang_thai',
                                        'Hiển thị'
                                    ) === 'Hiển thị'
                                        ? 'checked'
                                        : ''
                                }}
                            >


                            <span>

                                <i>
                                    ●
                                </i>


                                <strong>
                                    Hiển thị
                                </strong>


                                <small>
                                    Danh mục được hiển thị trên hệ thống.
                                </small>

                            </span>

                        </label>


                        {{-- ẨN --}}
                        <label class="category-radio-option">

                            <input
                                type="radio"
                                name="trang_thai"
                                value="Ẩn"
                                {{
                                    old('trang_thai') === 'Ẩn'
                                        ? 'checked'
                                        : ''
                                }}
                            >


                            <span>

                                <i>
                                    ●
                                </i>


                                <strong>
                                    Ẩn
                                </strong>


                                <small>
                                    Tạm thời ẩn danh mục khỏi hệ thống.
                                </small>

                            </span>

                        </label>

                    </div>


                    @error('trang_thai', 'createCategory')
                        <div class="category-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- FOOTER --}}
            <div class="category-modal-footer">

                <button
                    type="button"
                    class="btn-modal-cancel"
                    onclick="closeCreateCategoryModal()"
                >
                    Hủy bỏ
                </button>


                <button
                    type="submit"
                    class="btn-modal-save"
                >
                    Lưu danh mục
                </button>

            </div>

        </form>

    </div>

</div>