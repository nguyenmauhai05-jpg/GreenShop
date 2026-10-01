        

        <div class="filter-group">

            <button
                type="button"
                class="filter-group-title filter-toggle"
                aria-expanded="true"
            >

                <span>
                    Khoảng giá
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

                <div class="price-range-wrap">

                    <input
                        type="range"
                        id="priceRange"

                        min="0"

                        max="<?php echo e(max(
                                (int) $giaCaoNhat,
                                1
                            )); ?>"

                        step="10000"

                        value="<?php echo e(request(
                                'max_price',
                                (int) $giaCaoNhat
                            )); ?>"
                    >


                    <div class="price-range-labels">

                        <span>
                            0đ
                        </span>

                        <span>

                            <?php echo e(number_format(
                                    $giaCaoNhat,
                                    0,
                                    ',',
                                    '.'
                                )); ?>đ+

                        </span>

                    </div>

                </div>


                <div class="price-input-row">


                    
                    <div class="price-box">

                        <input
                            type="number"
                            name="min_price"
                            id="minPriceInput"

                            min="0"

                            value="<?php echo e(request(
                                    'min_price',
                                    0
                                )); ?>"

                            placeholder="Giá từ"
                        >

                    </div>


                    
                    <div class="price-box">

                        <input
                            type="number"
                            name="max_price"
                            id="maxPriceInput"

                            min="0"

                            value="<?php echo e(request(
                                    'max_price',
                                    (int) $giaCaoNhat
                                )); ?>"

                            placeholder="Giá đến"
                        >

                    </div>

                </div>


                

                <?php if(
                    isset($filterErrors['price']) ||
                    isset($filterErrors['min_price']) ||
                    isset($filterErrors['max_price'])
                ): ?>

                    <div class="filter-field-error">

                        <?php echo e($filterErrors['price']
                            ?? $filterErrors['min_price']
                            ?? $filterErrors['max_price']); ?>


                    </div>

                <?php endif; ?>

            </div>

        </div>


<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/cua_hang/components/filter/price.blade.php ENDPATH**/ ?>