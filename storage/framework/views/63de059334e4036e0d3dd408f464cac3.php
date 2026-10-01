<div class="shop-toolbar">

    <div class="shop-toolbar-actions">

        
        <form
            method="GET"
            action="<?php echo e(route('cua-hang')); ?>"
            class="toolbar-form"
        >

            <?php $__currentLoopData = request()->except('page', 'per_page'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <?php if(is_array($value)): ?>

                    <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <input
                            type="hidden"
                            name="<?php echo e($key); ?>[]"
                            value="<?php echo e($subValue); ?>"
                        >

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php else: ?>

                    <input
                        type="hidden"
                        name="<?php echo e($key); ?>"
                        value="<?php echo e($value); ?>"
                    >

                <?php endif; ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


            
        </form>


        
        <form
            method="GET"
            action="<?php echo e(route('cua-hang')); ?>"
            class="toolbar-form"
        >

            <?php $__currentLoopData = request()->except('page', 'sort'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <?php if(is_array($value)): ?>

                    <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <input
                            type="hidden"
                            name="<?php echo e($key); ?>[]"
                            value="<?php echo e($subValue); ?>"
                        >

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php else: ?>

                    <input
                        type="hidden"
                        name="<?php echo e($key); ?>"
                        value="<?php echo e($value); ?>"
                    >

                <?php endif; ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


            <label>Sắp xếp</label>

            <select
                name="sort"
                onchange="this.form.submit()"
            >

                <option
                    value="newest"
                    <?php echo e(request('sort', 'newest') === 'newest'
                        ? 'selected'
                        : ''); ?>

                >
                    Mới nhất
                </option>


                <option
                    value="best-selling"
                    <?php echo e(request('sort') === 'best-selling'
                        ? 'selected'
                        : ''); ?>

                >
                    Bán chạy nhất
                </option>


                <option
                    value="price-asc"
                    <?php echo e(request('sort') === 'price-asc'
                        ? 'selected'
                        : ''); ?>

                >
                    Giá thấp đến cao
                </option>


                <option
                    value="price-desc"
                    <?php echo e(request('sort') === 'price-desc'
                        ? 'selected'
                        : ''); ?>

                >
                    Giá cao đến thấp
                </option>

            </select>

        </form>

    </div>

</div><?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/cua_hang/components/toolbar.blade.php ENDPATH**/ ?>