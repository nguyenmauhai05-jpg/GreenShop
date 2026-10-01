<?php $__env->startSection('title', 'Quản lý voucher - GreenShop Admin'); ?>
<?php $__env->startSection('page-title', 'Quản lý voucher'); ?>

<?php $__env->startSection('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/admin/voucher.css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="voucher-page">

    <div class="voucher-heading voucher-heading-actions-only">
        <button type="button" class="voucher-create-btn" onclick="openCreateVoucherModal()">
            <span>+</span> Thêm voucher mới
        </button>
    </div>

    <?php if(session('success')): ?>
        <div class="voucher-alert success">✓ <?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="voucher-alert error">! <?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="voucher-alert error">
            <strong>Vui lòng kiểm tra lại dữ liệu:</strong>
            <span><?php echo e($errors->first()); ?></span>
        </div>
    <?php endif; ?>

    <div class="voucher-stats">
        <div class="voucher-stat-card">
            <span class="voucher-stat-icon">
                <svg viewBox="0 0 24 24"><path d="M4 7h16v10H4z"/><path d="M9 7a3 3 0 0 0 6 0M9 17a3 3 0 0 1 6 0"/></svg>
            </span>
            <div><span>Tổng voucher</span><strong><?php echo e(number_format($tongVoucher ?? 0)); ?></strong></div>
        </div>
        <div class="voucher-stat-card">
            <span class="voucher-stat-icon green">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>
            </span>
            <div><span>Đang hoạt động</span><strong><?php echo e(number_format($dangHoatDong ?? 0)); ?></strong></div>
        </div>
        <div class="voucher-stat-card">
            <span class="voucher-stat-icon orange">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            </span>
            <div><span>Sắp diễn ra</span><strong><?php echo e(number_format($sapDienRa ?? 0)); ?></strong></div>
        </div>
        <div class="voucher-stat-card">
            <span class="voucher-stat-icon red">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6M15 9l-6 6"/></svg>
            </span>
            <div><span>Hết hạn / hết lượt</span><strong><?php echo e(number_format($hetHan ?? 0)); ?></strong></div>
        </div>
    </div>

    <form method="GET" action="<?php echo e(route('admin.voucher.index')); ?>" class="voucher-filter">
        <div class="voucher-search">
            <span>⌕</span>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm theo mã hoặc tên voucher...">
        </div>

        <select name="scope">
            <option value="">Tất cả nhóm voucher</option>
            <option value="don_hang" <?php if(request('scope') === 'don_hang'): echo 'selected'; endif; ?>>Mã giảm giá</option>
            <option value="van_chuyen" <?php if(request('scope') === 'van_chuyen'): echo 'selected'; endif; ?>>Voucher phí vận chuyển</option>
        </select>

        <select name="type">
            <option value="">Tất cả loại giảm</option>
            <option value="phan_tram" <?php if(request('type') === 'phan_tram'): echo 'selected'; endif; ?>>Giảm theo %</option>
            <option value="so_tien" <?php if(request('type') === 'so_tien'): echo 'selected'; endif; ?>>Giảm số tiền</option>
        </select>

        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Đang chạy</option>
            <option value="upcoming" <?php if(request('status') === 'upcoming'): echo 'selected'; endif; ?>>Sắp diễn ra</option>
            <option value="paused" <?php if(request('status') === 'paused'): echo 'selected'; endif; ?>>Tạm dừng</option>
            <option value="expired" <?php if(request('status') === 'expired'): echo 'selected'; endif; ?>>Hết hạn / hết lượt</option>
        </select>

        <button type="submit" class="voucher-filter-btn">Lọc</button>
        <a href="<?php echo e(route('admin.voucher.index')); ?>" class="voucher-reset-btn">↻ Xóa bộ lọc</a>
    </form>

    <section class="voucher-table-card">
        <div class="voucher-table-head">
            <div>
                <h3>Danh sách voucher</h3>
                <p>Quản lý mã, mức giảm, điều kiện và thời gian sử dụng.</p>
            </div>
        </div>

        <div class="voucher-table-scroll">
            <table class="voucher-table">
                <thead>
                    <tr>
                        <th>Mã voucher</th>
                        <th>Chương trình</th>
                        <th>Mức giảm</th>
                        <th>Nhóm</th>
                        <th>Điều kiện</th>
                        <th>Lượt dùng</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $vouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voucher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $startLocal = $voucher->ngay_bat_dau?->format('Y-m-d H:i:s');
                        $endLocal = $voucher->ngay_ket_thuc?->format('Y-m-d H:i:s');
                        $currentLocal = $nowLocal ?? now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');

                        if (!(bool) $voucher->trang_thai) {
                            $effectiveStatus = 'Tạm dừng';
                            $effectiveClass = 'paused';
                        } elseif ((int) $voucher->da_su_dung >= (int) $voucher->so_luong) {
                            $effectiveStatus = 'Hết lượt';
                            $effectiveClass = 'expired';
                        } elseif ($endLocal && $endLocal < $currentLocal) {
                            $effectiveStatus = 'Hết hạn';
                            $effectiveClass = 'expired';
                        } elseif ($startLocal && $startLocal > $currentLocal) {
                            $effectiveStatus = 'Sắp diễn ra';
                            $effectiveClass = 'upcoming';
                        } else {
                            $effectiveStatus = 'Đang chạy';
                            $effectiveClass = 'active';
                        }
                    ?>
                    <tr>
                        <td>
                            <div class="voucher-code-cell">
                                <span class="voucher-ticket-icon">%</span>
                                <strong><?php echo e($voucher->ma_voucher); ?></strong>
                            </div>
                        </td>
                        <td>
                            <div class="voucher-name-cell">
                                <strong><?php echo e($voucher->ten_voucher); ?></strong>
                                <span><?php echo e($voucher->mo_ta ?: 'Không có mô tả'); ?></span>
                            </div>
                        </td>
                        <td>
                            <?php if($voucher->loai_giam === 'phan_tram'): ?>
                                <strong class="voucher-discount"><?php echo e(number_format($voucher->gia_tri_giam, 0)); ?>%</strong>
                                <?php if($voucher->giam_toi_da): ?>
                                    <small>Tối đa <?php echo e(number_format($voucher->giam_toi_da, 0, ',', '.')); ?>₫</small>
                                <?php endif; ?>
                            <?php else: ?>
                                <strong class="voucher-discount"><?php echo e(number_format($voucher->gia_tri_giam, 0, ',', '.')); ?>₫</strong>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="voucher-condition"><?php echo e(($voucher->pham_vi ?? 'don_hang') === 'van_chuyen' ? 'Phí vận chuyển' : 'Mã giảm giá'); ?></span>
                        </td>
                        <td>
                            <span class="voucher-condition">Đơn từ <?php echo e(number_format($voucher->don_hang_toi_thieu, 0, ',', '.')); ?>₫</span>
                        </td>
                        <td>
                            <strong><?php echo e(number_format($voucher->da_su_dung)); ?>/<?php echo e(number_format($voucher->so_luong)); ?></strong>
                            <div class="voucher-progress"><span style="width: <?php echo e(min(100, $voucher->so_luong > 0 ? ($voucher->da_su_dung / $voucher->so_luong) * 100 : 0)); ?>%"></span></div>
                        </td>
                        <td class="voucher-time">
                            <span><?php echo e($voucher->ngay_bat_dau->format('d/m/Y H:i')); ?></span>
                            <small>→ <?php echo e($voucher->ngay_ket_thuc->format('d/m/Y H:i')); ?></small>
                        </td>
                        <td><span class="voucher-status <?php echo e($effectiveClass); ?>"><?php echo e($effectiveStatus); ?></span></td>
                        <td>
                            <div class="voucher-actions">
                                <button
                                    type="button"
                                    class="voucher-action edit"
                                    title="Chỉnh sửa"
                                    onclick="openEditVoucherModal(this)"
                                    data-id="<?php echo e($voucher->voucher_id); ?>"
                                    data-update-url="<?php echo e(route('admin.voucher.update', $voucher)); ?>"
                                    data-code="<?php echo e($voucher->ma_voucher); ?>"
                                    data-name="<?php echo e($voucher->ten_voucher); ?>"
                                    data-description="<?php echo e($voucher->mo_ta); ?>"
                                    data-scope="<?php echo e($voucher->pham_vi ?? 'don_hang'); ?>"
                                    data-type="<?php echo e($voucher->loai_giam); ?>"
                                    data-value="<?php echo e($voucher->gia_tri_giam); ?>"
                                    data-max="<?php echo e($voucher->giam_toi_da); ?>"
                                    data-min-order="<?php echo e($voucher->don_hang_toi_thieu); ?>"
                                    data-quantity="<?php echo e($voucher->so_luong); ?>"
                                    data-start="<?php echo e($voucher->ngay_bat_dau->format('Y-m-d\\TH:i')); ?>"
                                    data-end="<?php echo e($voucher->ngay_ket_thuc->format('Y-m-d\\TH:i')); ?>"
                                    data-status="<?php echo e($voucher->trang_thai ? 1 : 0); ?>"
                                >✎</button>

                                <form method="POST" action="<?php echo e(route('admin.voucher.toggle', $voucher)); ?>">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="voucher-action toggle" title="<?php echo e($voucher->trang_thai ? 'Tạm dừng' : 'Bật lại'); ?>">
                                        <?php echo e($voucher->trang_thai ? 'Ⅱ' : '▶'); ?>

                                    </button>
                                </form>

                                <form method="POST" action="<?php echo e(route('admin.voucher.destroy', $voucher)); ?>" onsubmit="return confirm('Bạn có chắc muốn xóa voucher này?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="voucher-action delete" title="Xóa">♲</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="9" class="voucher-empty">Chưa có voucher nào. Hãy tạo voucher đầu tiên.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $vouchers]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vouchers)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $attributes = $__attributesOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $component = $__componentOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__componentOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
    </section>
</div>

<div class="voucher-modal" id="voucherModal">
    <div class="voucher-modal-backdrop" onclick="closeVoucherModal()"></div>
    <div class="voucher-modal-dialog">
        <div class="voucher-modal-header">
            <div>
                <h3 id="voucherModalTitle">Thêm voucher mới</h3>
                <p>Thiết lập mã giảm giá và điều kiện áp dụng.</p>
            </div>
            <button type="button" onclick="closeVoucherModal()">×</button>
        </div>

        <form method="POST" action="<?php echo e(route('admin.voucher.store')); ?>" id="voucherForm">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_method" id="voucherMethod" value="POST">

            <div class="voucher-modal-body">
                <div class="voucher-form-grid">
                    <div class="voucher-form-group">
                        <label>Mã voucher <span>*</span></label>
                        <input type="text" name="ma_voucher" id="voucherCode" maxlength="50" placeholder="VD: GREEN20" required>
                        <small>Không dùng khoảng trắng. Mã sẽ được lưu chữ in hoa.</small>
                    </div>

                    <div class="voucher-form-group">
                        <label>Tên chương trình <span>*</span></label>
                        <input type="text" name="ten_voucher" id="voucherName" maxlength="150" placeholder="VD: Giảm 20% cuối tuần" required>
                    </div>

                    <div class="voucher-form-group full">
                        <label>Mô tả</label>
                        <textarea name="mo_ta" id="voucherDescription" rows="2" maxlength="500" placeholder="Mô tả ngắn về voucher..."></textarea>
                    </div>

                    <div class="voucher-form-group">
                        <label>Nhóm voucher <span>*</span></label>
                        <select name="pham_vi" id="voucherScope" required>
                            <option value="don_hang">Mã giảm giá</option>
                            <option value="van_chuyen">Voucher phí vận chuyển</option>
                        </select>
                    </div>

                    <div class="voucher-form-group">
                        <label>Loại giảm <span>*</span></label>
                        <select name="loai_giam" id="voucherType" required onchange="syncVoucherType()">
                            <option value="phan_tram">Giảm theo phần trăm (%)</option>
                            <option value="so_tien">Giảm số tiền cố định</option>
                        </select>
                    </div>

                    <div class="voucher-form-group">
                        <label>Giá trị giảm <span>*</span></label>
                        <input type="number" min="0" step="0.01" name="gia_tri_giam" id="voucherValue" required>
                    </div>

                    <div class="voucher-form-group" id="maxDiscountGroup">
                        <label>Giảm tối đa</label>
                        <input type="number" min="0" step="1000" name="giam_toi_da" id="voucherMax" placeholder="VD: 100000">
                    </div>

                    <div class="voucher-form-group">
                        <label>Đơn hàng tối thiểu <span>*</span></label>
                        <input type="number" min="0" step="1000" name="don_hang_toi_thieu" id="voucherMinOrder" value="0" required>
                    </div>

                    <div class="voucher-form-group">
                        <label>Số lượng voucher <span>*</span></label>
                        <input type="number" min="1" name="so_luong" id="voucherQuantity" value="100" required>
                    </div>

                    <div class="voucher-form-group">
                        <label>Trạng thái <span>*</span></label>
                        <select name="trang_thai" id="voucherStatus" required>
                            <option value="1">Hoạt động</option>
                            <option value="0">Tạm dừng</option>
                        </select>
                    </div>

                    <div class="voucher-form-group">
                        <label>Bắt đầu <span>*</span></label>
                        <input type="datetime-local" name="ngay_bat_dau" id="voucherStart" required>
                    </div>

                    <div class="voucher-form-group">
                        <label>Kết thúc <span>*</span></label>
                        <input type="datetime-local" name="ngay_ket_thuc" id="voucherEnd" required>
                    </div>
                </div>
            </div>

            <div class="voucher-modal-footer">
                <button type="button" class="voucher-cancel-btn" onclick="closeVoucherModal()">Hủy</button>
                <button type="submit" class="voucher-save-btn">Lưu voucher</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php
    $voucherOldValues = [
        'ma_voucher' => old('ma_voucher', ''),
        'ten_voucher' => old('ten_voucher', ''),
        'mo_ta' => old('mo_ta', ''),
        'pham_vi' => old('pham_vi', 'don_hang'),
        'loai_giam' => old('loai_giam', 'phan_tram'),
        'gia_tri_giam' => old('gia_tri_giam', ''),
        'giam_toi_da' => old('giam_toi_da', ''),
        'don_hang_toi_thieu' => old('don_hang_toi_thieu', '0'),
        'so_luong' => old('so_luong', '100'),
        'trang_thai' => (string) old('trang_thai', '1'),
        'ngay_bat_dau' => old('ngay_bat_dau', ''),
        'ngay_ket_thuc' => old('ngay_ket_thuc', ''),
    ];
?>
<script>
window.GreenShopVoucherAdmin = {
    createUrl: <?php echo json_encode(route('admin.voucher.store'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>,
    hasErrors: <?php echo e($errors->any() ? 'true' : 'false'); ?>,
    old: <?php echo json_encode($voucherOldValues, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>,
};
</script>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/admin/voucher.js'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/voucher/index.blade.php ENDPATH**/ ?>