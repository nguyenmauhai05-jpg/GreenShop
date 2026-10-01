<div
    id="editCategoryModal"
    class="category-modal"
>

    <div
        class="category-modal-overlay"
        onclick="closeEditCategoryModal()"
    ></div>


    <div class="category-modal-dialog">

        {{-- HEADER --}}
        <div class="category-modal-header">

            <div>

                <h2>
                    Sửa danh mục
                </h2>

                <p>
                    Cập nhật thông tin danh mục.
                </p>

            </div>


            <button
                type="button"
                class="category-modal-close"
                onclick="closeEditCategoryModal()"
            >
                ×
            </button>

        </div>


        <form
            id="editCategoryForm"
            method="POST"
            action=""
            novalidate
        >
            @csrf
            @method('PUT')

            <input
                type="hidden"
                id="edit_category_id"
                name="category_id"
                value="{{ old('category_id') }}"
            >


            {{-- BODY --}}
            <div class="category-modal-body">

                {{-- TÊN --}}
                <div class="category-form-group">

                    <label for="edit_ten_danh_muc">
                        Tên danh mục
                        <span>*</span>
                    </label>


                    <input
                        type="text"
                        id="edit_ten_danh_muc"
                        name="ten_danh_muc"
                        placeholder="Nhập tên danh mục"
                        autocomplete="off"
                        value="{{ old('ten_danh_muc') }}"
                    >


                    <small>
                        Tối đa 100 ký tự.
                    </small>

                    <div
                        id="editNameError"
                        class="category-field-error {{ $errors->editCategory->has('ten_danh_muc') ? 'show' : '' }}"
                    >{{ $errors->editCategory->first('ten_danh_muc') }}</div>

                </div>


                {{-- MÔ TẢ --}}
                <div class="category-form-group">

                    <label for="edit_mo_ta">
                        Mô tả
                    </label>


                    <textarea
                        id="edit_mo_ta"
                        name="mo_ta"
                        placeholder="Nhập mô tả danh mục..."
                    >{{ old('mo_ta') }}</textarea>


                    <small>
                        Tối đa 255 ký tự.
                    </small>

                    <div
                        id="editDescriptionError"
                        class="category-field-error {{ $errors->editCategory->has('mo_ta') ? 'show' : '' }}"
                    >{{ $errors->editCategory->first('mo_ta') }}</div>

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
                                id="edit_status_show"
                                name="trang_thai"
                                value="Hiển thị"
                                {{ old('trang_thai') === 'Hiển thị' ? 'checked' : '' }}
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
                                id="edit_status_hide"
                                name="trang_thai"
                                value="Ẩn"
                                {{ old('trang_thai') === 'Ẩn' ? 'checked' : '' }}
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

                    @error('trang_thai', 'editCategory')
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
                    onclick="closeEditCategoryModal()"
                >
                    Hủy bỏ
                </button>


                <button
                    type="submit"
                    class="btn-modal-save"
                >
                    Cập nhật
                </button>

            </div>

        </form>

    </div>

</div>