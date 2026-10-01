

    <form
        method="GET"
        action="<?php echo e(route('admin.cay-canh.index')); ?>"
        class="plant-filter-box"
    >

        
        <div class="plant-search-box">

            <span>
                ⌕
            </span>

            <input
                type="text"
                name="search"
                value="<?php echo e(request('search')); ?>"
                placeholder="Tìm theo tên cây, mã cây..."
            >

        </div>


        
        <div class="plant-filter-item">

            <label>
                Danh mục
            </label>

            <select name="category">

                <option value="">
                    Tất cả
                </option>

                <?php $__currentLoopData = $danhMucs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $danhMuc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <option
                        value="<?php echo e($danhMuc->category_id); ?>"
                        <?php echo e((string) request('category')
                            ===
                            (string) $danhMuc->category_id
                                ? 'selected'
                                : ''); ?>

                    >
                        <?php echo e($danhMuc->ten_danh_muc); ?>

                    </option>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </select>

        </div>


        
        <div class="plant-filter-item">

            <label>
                Trạng thái
            </label>

            <select name="status">

                <option value="">
                    Tất cả
                </option>


                <option
                    value="Đang bán"
                    <?php echo e(request('status') === 'Đang bán'
                            ? 'selected'
                            : ''); ?>

                >
                    Đang bán
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


        
        <div class="plant-filter-item">

            <label>
                Kho hàng
            </label>

            <select name="stock">

                <option value="">
                    Tất cả
                </option>


                <option
                    value="con-hang"
                    <?php echo e(request('stock') === 'con-hang'
                            ? 'selected'
                            : ''); ?>

                >
                    Còn hàng
                </option>


                <option
                    value="sap-het"
                    <?php echo e(request('stock') === 'sap-het'
                            ? 'selected'
                            : ''); ?>

                >
                    Sắp hết hàng
                </option>


                <option
                    value="het-hang"
                    <?php echo e(request('stock') === 'het-hang'
                            ? 'selected'
                            : ''); ?>

                >
                    Hết hàng
                </option>

            </select>

        </div>


        <button
            type="submit"
            class="plant-filter-submit"
        >
            Lọc
        </button>


        <a
            href="<?php echo e(route('admin.cay-canh.index')); ?>"
            class="plant-filter-reset"
        >
            ↻ Xóa bộ lọc
        </a>

    </form>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/cay_canh/components/filter.blade.php ENDPATH**/ ?>