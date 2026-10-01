<div
    id="createCategoryModal"
    class="category-modal"
>

    <div
        class="category-modal-overlay"
        onclick="closeCreateCategoryModal()"
    ></div>


    <div class="category-modal-dialog">

        
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
            action="<?php echo e(route('admin.danh-muc.store')); ?>"
            novalidate
        >
            <?php echo csrf_field(); ?>


            
            <div class="category-modal-body">

                
                <div class="category-form-group">

                    <label for="create_ten_danh_muc">
                        Tên danh mục
                        <span>*</span>
                    </label>


                    <input
                        type="text"
                        id="create_ten_danh_muc"
                        name="ten_danh_muc"
                        value="<?php echo e(old('ten_danh_muc')); ?>"
                        placeholder="Nhập tên danh mục"
                        autocomplete="off"
                    >


                    <small>
                        Tối đa 100 ký tự.
                    </small>


                    <div
                        id="createNameError"
                        class="category-field-error <?php echo e($errors->createCategory->has('ten_danh_muc') ? 'show' : ''); ?>"
                    ><?php echo e($errors->createCategory->first('ten_danh_muc')); ?></div>

                </div>


                
                <div class="category-form-group">

                    <label for="create_mo_ta">
                        Mô tả
                    </label>


                    <textarea
                        id="create_mo_ta"
                        name="mo_ta"
                        placeholder="Nhập mô tả danh mục..."
                    ><?php echo e(old('mo_ta')); ?></textarea>


                    <small>
                        Tối đa 255 ký tự.
                    </small>


                    <div
                        id="createDescriptionError"
                        class="category-field-error <?php echo e($errors->createCategory->has('mo_ta') ? 'show' : ''); ?>"
                    ><?php echo e($errors->createCategory->first('mo_ta')); ?></div>

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
                                name="trang_thai"
                                value="Hiển thị"
                                <?php echo e(old(
                                        'trang_thai',
                                        'Hiển thị'
                                    ) === 'Hiển thị'
                                        ? 'checked'
                                        : ''); ?>

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
                                name="trang_thai"
                                value="Ẩn"
                                <?php echo e(old('trang_thai') === 'Ẩn'
                                        ? 'checked'
                                        : ''); ?>

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


                    <?php $__errorArgs = ['trang_thai', 'createCategory'];
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

</div><?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/danh_muc/components/modal-create.blade.php ENDPATH**/ ?>