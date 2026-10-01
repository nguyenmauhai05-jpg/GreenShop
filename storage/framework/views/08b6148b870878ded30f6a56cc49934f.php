<div
    id="editCategoryModal"
    class="category-modal"
>

    <div
        class="category-modal-overlay"
        onclick="closeEditCategoryModal()"
    ></div>


    <div class="category-modal-dialog">

        
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
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <input
                type="hidden"
                id="edit_category_id"
                name="category_id"
                value="<?php echo e(old('category_id')); ?>"
            >


            
            <div class="category-modal-body">

                
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
                        value="<?php echo e(old('ten_danh_muc')); ?>"
                    >


                    <small>
                        Tối đa 100 ký tự.
                    </small>

                    <div
                        id="editNameError"
                        class="category-field-error <?php echo e($errors->editCategory->has('ten_danh_muc') ? 'show' : ''); ?>"
                    ><?php echo e($errors->editCategory->first('ten_danh_muc')); ?></div>

                </div>


                
                <div class="category-form-group">

                    <label for="edit_mo_ta">
                        Mô tả
                    </label>


                    <textarea
                        id="edit_mo_ta"
                        name="mo_ta"
                        placeholder="Nhập mô tả danh mục..."
                    ><?php echo e(old('mo_ta')); ?></textarea>


                    <small>
                        Tối đa 255 ký tự.
                    </small>

                    <div
                        id="editDescriptionError"
                        class="category-field-error <?php echo e($errors->editCategory->has('mo_ta') ? 'show' : ''); ?>"
                    ><?php echo e($errors->editCategory->first('mo_ta')); ?></div>

                </div>


                
                <div class="category-form-group">

                    <label>
                        Trạng thái
                        <span>*</span>
                    </label>


                    <div class="category-radio-group">

                        
                        <label class="category-radio-option">

                            <input
                                type="radio"
                                id="edit_status_show"
                                name="trang_thai"
                                value="Hiển thị"
                                <?php echo e(old('trang_thai') === 'Hiển thị' ? 'checked' : ''); ?>

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


                        
                        <label class="category-radio-option">

                            <input
                                type="radio"
                                id="edit_status_hide"
                                name="trang_thai"
                                value="Ẩn"
                                <?php echo e(old('trang_thai') === 'Ẩn' ? 'checked' : ''); ?>

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

                    <?php $__errorArgs = ['trang_thai', 'editCategory'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="category-field-error">
                            <?php echo e($message); ?>

                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                </div>

            </div>


            
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

</div><?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/danh_muc/components/modal-edit.blade.php ENDPATH**/ ?>