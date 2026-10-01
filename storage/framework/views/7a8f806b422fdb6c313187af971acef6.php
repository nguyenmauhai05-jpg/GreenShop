

    <div class="category-filter-box">

        <form
            method="GET"
            action="<?php echo e(route('admin.danh-muc.index')); ?>"
            class="category-filter-form"
        >

            
            <div class="category-search">

                <i>
                    ⌕
                </i>

                <input
                    type="text"
                    name="search"
                    value="<?php echo e(request('search')); ?>"
                    placeholder="Tìm kiếm danh mục theo tên..."
                >

            </div>


            
            <div class="category-filter-select">

                <i>
                    ▾
                </i>

                <select name="status">

                    <option value="">
                        Tất cả trạng thái
                    </option>


                    <option
                        value="Hiển thị"
                        <?php echo e(request('status') === 'Hiển thị'
                                ? 'selected'
                                : ''); ?>

                    >
                        Hiển thị
                    </option>


                    <option
                        value="Ẩn"
                        <?php echo e(request('status') === 'Ẩn'
                                ? 'selected'
                                : ''); ?>

                    >
                        Ẩn
                    </option>

                </select>

            </div>


            
            <button
                type="submit"
                class="btn-filter"
            >
                Lọc
            </button>


            
            <a
                href="<?php echo e(route('admin.danh-muc.index')); ?>"
                class="btn-clear-filter"
            >
                ↻ Xóa bộ lọc
            </a>

        </form>

    </div>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/danh_muc/components/filter.blade.php ENDPATH**/ ?>