        

        <div class="filter-group">

            <button
                type="button"
                class="filter-group-title filter-toggle"
                aria-expanded="true"
            >

                <span>
                    Tình trạng
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

                        name="status[]"

                        value="con-hang"

                        <?php echo e(in_array(
                                'con-hang',
                                (array) request('status', [])
                            )
                            ? 'checked'
                            : ''); ?>

                    >

                    <span class="custom-check"></span>

                    <span class="filter-text">
                        Còn hàng
                    </span>

                </label>


                

                <label class="filter-checkbox-row">

                    <input
                        type="checkbox"

                        name="status[]"

                        value="het-hang"

                        <?php echo e(in_array(
                                'het-hang',
                                (array) request('status', [])
                            )
                            ? 'checked'
                            : ''); ?>

                    >

                    <span class="custom-check"></span>

                    <span class="filter-text">
                        Hết hàng
                    </span>

                </label>


                <small class="filter-help">
                    Không chọn hoặc chọn cả hai = Tất cả.
                </small>

            </div>

        </div>


<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/cua_hang/components/filter/status.blade.php ENDPATH**/ ?>