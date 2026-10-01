<div class="report-filter-card">

    <form
        method="GET"
        action="<?php echo e(route('admin.bao-cao.index')); ?>"
        class="report-filter-form" id="reportFilterForm"
    >

        
        <div class="report-filter-group">

            <label for="reportFromDate">Từ ngày</label>

            <input
                id="reportFromDate"
                type="text"
                name="tu_ngay"
                inputmode="numeric"
                maxlength="10"
                autocomplete="off"
                placeholder="DD/MM/YYYY"
                value="<?php echo e(old('tu_ngay', request('tu_ngay', $tuNgay->format('d/m/Y')))); ?>"
                data-report-date
            >

            <?php $__errorArgs = ['tu_ngay'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <small class="report-filter-error"><?php echo e($message); ?></small>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>


        
        <div class="report-filter-group">

            <label for="reportToDate">Đến ngày</label>

            <input
                id="reportToDate"
                type="text"
                name="den_ngay"
                inputmode="numeric"
                maxlength="10"
                autocomplete="off"
                placeholder="DD/MM/YYYY"
                value="<?php echo e(old('den_ngay', request('den_ngay', $denNgay->format('d/m/Y')))); ?>"
                data-report-date
            >

            <?php $__errorArgs = ['den_ngay'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <small class="report-filter-error"><?php echo e($message); ?></small>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>


        


        
        <div class="report-filter-group">

            <label>
                Nhóm theo
            </label>

            <select name="nhom_theo">

                <option
                    value="ngay"
                    <?php echo e($nhomTheo === 'ngay'
                            ? 'selected'
                            : ''); ?>

                >
                    Ngày
                </option>

                <option
                    value="thang"
                    <?php echo e($nhomTheo === 'thang'
                            ? 'selected'
                            : ''); ?>

                >
                    Tháng
                </option>

                <option
                    value="nam"
                    <?php echo e($nhomTheo === 'nam'
                            ? 'selected'
                            : ''); ?>

                >
                    Năm
                </option>

            </select>

        </div>


        
        <div class="report-filter-group">

            <label>
                Danh mục
            </label>

            <select name="category_id">

                <option value="">
                    Tất cả danh mục
                </option>

                <?php $__currentLoopData = $danhMucs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $danhMuc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <option
                        value="<?php echo e($danhMuc->category_id); ?>"
                        <?php echo e((string) $categoryId
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


        
        <div class="report-filter-group">

            <label>
                Trạng thái đơn
            </label>

            <select name="trang_thai_don">

                <option value="">
                    Tất cả trạng thái
                </option>

                <option
                    value="pending_confirmation"
                    <?php echo e($orderStatus === 'pending_confirmation'
                            ? 'selected'
                            : ''); ?>

                >
                    Chờ xác nhận
                </option>

                <option
                    value="preparing"
                    <?php echo e($orderStatus === 'preparing'
                            ? 'selected'
                            : ''); ?>

                >
                    Đang chuẩn bị
                </option>

                <option
                    value="shipping"
                    <?php echo e($orderStatus === 'shipping'
                            ? 'selected'
                            : ''); ?>

                >
                    Đang giao
                </option>

                <option
                    value="delivered"
                    <?php echo e($orderStatus === 'delivered'
                            ? 'selected'
                            : ''); ?>

                >
                    Đã giao
                </option>

                <option
                    value="completed"
                    <?php echo e($orderStatus === 'completed'
                            ? 'selected'
                            : ''); ?>

                >
                    Hoàn thành
                </option>

                <option
                    value="cancelled"
                    <?php echo e($orderStatus === 'cancelled'
                            ? 'selected'
                            : ''); ?>

                >
                    Đã hủy
                </option>

            </select>

        </div>


        <div class="report-filter-actions">

            <button
                type="submit"
                class="report-filter-btn"
            >
                Áp dụng
            </button>


            <a
                href="<?php echo e(route('admin.bao-cao.index')); ?>"
                class="report-reset-btn"
            >
                Xóa bộ lọc
            </a>

        </div>

    </form>

</div><?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/bao_cao/components/filters.blade.php ENDPATH**/ ?>