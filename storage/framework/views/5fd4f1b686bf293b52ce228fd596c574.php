        

        <div class="filter-group">

            <button
                type="button"
                class="filter-group-title filter-toggle"
                aria-expanded="true"
            >

                <span>
                    Kích thước
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
                        type="checkbox"

                        name="size[]"

                        value="small"

                        <?php echo e(in_array(
                                'small',
                                (array) request('size', [])
                            )
                            ? 'checked'
                            : ''); ?>

                    >

                    <span class="custom-check"></span>

                    <span class="filter-text">
                        Nhỏ (&lt; 30cm)
                    </span>

                </label>


                

                <label class="filter-checkbox-row">

                    <input
                        type="checkbox"

                        name="size[]"

                        value="medium"

                        <?php echo e(in_array(
                                'medium',
                                (array) request('size', [])
                            )
                            ? 'checked'
                            : ''); ?>

                    >

                    <span class="custom-check"></span>

                    <span class="filter-text">
                        Trung bình (30 - 80cm)
                    </span>

                </label>


                

                <label class="filter-checkbox-row">

                    <input
                        type="checkbox"

                        name="size[]"

                        value="large"

                        <?php echo e(in_array(
                                'large',
                                (array) request('size', [])
                            )
                            ? 'checked'
                            : ''); ?>

                    >

                    <span class="custom-check"></span>

                    <span class="filter-text">
                        Lớn (&gt; 80cm)
                    </span>

                </label>

            </div>

        </div>

<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/cua_hang/components/filter/size.blade.php ENDPATH**/ ?>