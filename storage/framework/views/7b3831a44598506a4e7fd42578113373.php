        

        <div class="filter-group">

            <button
                type="button"
                class="filter-group-title filter-toggle"
                aria-expanded="true"
            >

                <span>
                    Danh mục
                </span>

                <span class="filter-chevron">

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="m6 15 6-6 6 6"/>
                    </svg>

                </span>

            </button>


            <div class="filter-group-content">

                <label class="filter-checkbox-row">

                    <input
                        type="radio"
                        name="category"
                        value=""
                        <?php echo e(!request('category') ? 'checked' : ''); ?>

                    >

                    <span class="custom-check"></span>

                    <span class="filter-text">
                        Tất cả danh mục
                    </span>

                </label>


                <?php $__currentLoopData = $danhMucs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $danhMuc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <?php if(
                        mb_strtolower(
                            trim($danhMuc->ten_danh_muc)
                        )
                        !== 'chăm sóc cây'
                    ): ?>

                        <label class="filter-checkbox-row">

                            <input
                                type="radio"
                                name="category"
                                value="<?php echo e($danhMuc->category_id); ?>"
                                <?php echo e(request('category')
                                    == $danhMuc->category_id
                                    ? 'checked'
                                    : ''); ?>

                            >

                            <span class="custom-check"></span>


                            <span class="filter-text">

                                <?php echo e($danhMuc->ten_danh_muc); ?>


                                <small>
                                    (<?php echo e($danhMuc->tong_so_cay); ?>)
                                </small>

                            </span>

                        </label>

                    <?php endif; ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>

        </div>

<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/cua_hang/components/filter/category.blade.php ENDPATH**/ ?>