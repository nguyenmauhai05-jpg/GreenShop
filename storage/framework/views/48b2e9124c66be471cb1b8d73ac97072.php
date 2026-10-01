

    <div class="category-stats">


        
        <div class="category-stat-card">

            <div class="stat-icon">
                ▣
            </div>

            <div>

                <span>
                    Tổng danh mục
                </span>

                <strong>
                    <?php echo e(number_format($tongDanhMuc ?? 0)); ?>

                </strong>

            </div>

        </div>


        
        <div class="category-stat-card">

            <div class="stat-icon">
                ✓
            </div>

            <div>

                <span>
                    Đang hiển thị
                </span>

                <strong>
                    <?php echo e(number_format($tongHienThi ?? 0)); ?>

                </strong>

            </div>

        </div>


        
        <div class="category-stat-card">

            <div class="stat-icon">
                ◌
            </div>

            <div>

                <span>
                    Đang ẩn
                </span>

                <strong>
                    <?php echo e(number_format($tongAn ?? 0)); ?>

                </strong>

            </div>

        </div>

    </div>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/danh_muc/components/stats.blade.php ENDPATH**/ ?>