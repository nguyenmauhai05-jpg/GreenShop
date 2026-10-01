<?php $__env->startSection('title', 'Quản lý đánh giá - GreenShop Admin'); ?>
<?php $__env->startSection('page-title', 'Quản lý đánh giá'); ?>

<?php $__env->startSection('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/admin/danh-gia.css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusLabel = fn ($review) => filled($review?->phan_hoi_admin)
        ? 'Đã trả lời'
        : 'Chưa trả lời';

    $statusClass = fn ($review) => filled($review?->phan_hoi_admin)
        ? 'approved'
        : 'pending';

    $selectedImages = $selectedReview?->hinh_anh ?? [];
?>

<div class="admin-review-page">

    <?php if(session('success')): ?>
        <div class="review-alert success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('warning')): ?>
        <div class="review-alert warning"><?php echo e(session('warning')); ?></div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="review-alert error"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php if(isset($loadError)): ?>
        <div class="review-alert error"><?php echo e($loadError); ?></div>
    <?php endif; ?>

    <form method="GET" action="<?php echo e(route('admin.danh-gia.index')); ?>" class="review-filter-panel">
        <div class="review-filter search">
            <label>Tìm kiếm đánh giá</label>
            <div class="review-input-icon">
                <span>⌕</span>
                <input
                    type="text"
                    name="q"
                    value="<?php echo e($filters['q']); ?>"
                    placeholder="Sản phẩm, người đánh giá, nội dung..."
                >
            </div>
        </div>

        <div class="review-filter">
            <label>Số sao</label>
            <select name="stars">
                <option value="all" <?php if($filters['stars'] === 'all'): echo 'selected'; endif; ?>>Tất cả</option>
                <?php for($i = 5; $i >= 1; $i--): ?>
                    <option value="<?php echo e($i); ?>" <?php if($filters['stars'] === (string)$i): echo 'selected'; endif; ?>>
                        <?php echo e($i); ?> sao
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="review-filter">
            <label>Trạng thái</label>
            <select name="status">
                <option value="all" <?php if($filters['status'] === 'all'): echo 'selected'; endif; ?>>Tất cả</option>
                <option value="not_replied" <?php if($filters['status'] === 'not_replied'): echo 'selected'; endif; ?>>Chưa trả lời</option>
                <option value="replied" <?php if($filters['status'] === 'replied'): echo 'selected'; endif; ?>>Đã trả lời</option>
            </select>
        </div>

        <div class="review-filter date-range">
            <label>Khoảng ngày</label>
            <div class="review-date-row">
                <input type="date" name="date_from" value="<?php echo e($filters['date_from']); ?>">
                <span>→</span>
                <input type="date" name="date_to" value="<?php echo e($filters['date_to']); ?>">
            </div>
        </div>

        <button type="submit" class="review-btn primary">Lọc</button>

        <a href="<?php echo e(route('admin.danh-gia.index')); ?>" class="review-btn secondary review-refresh-btn">
            ↻ Làm mới
        </a>
    </form>

    <div class="review-tabs">
        <a
            href="<?php echo e(route('admin.danh-gia.index', array_merge(request()->except(['page', 'selected']), ['status' => 'all']))); ?>"
            class="<?php echo e($filters['status'] === 'all' ? 'active' : ''); ?>"
        >
            Tất cả <span><?php echo e($counts['all'] ?? 0); ?></span>
        </a>

        <a
            href="<?php echo e(route('admin.danh-gia.index', array_merge(request()->except(['page', 'selected']), ['status' => 'not_replied']))); ?>"
            class="<?php echo e($filters['status'] === 'not_replied' ? 'active pending' : ''); ?>"
        >
            Chưa trả lời <span><?php echo e($counts['not_replied'] ?? 0); ?></span>
        </a>

        <a
            href="<?php echo e(route('admin.danh-gia.index', array_merge(request()->except(['page', 'selected']), ['status' => 'replied']))); ?>"
            class="<?php echo e($filters['status'] === 'replied' ? 'active approved' : ''); ?>"
        >
            Đã trả lời <span><?php echo e($counts['replied'] ?? 0); ?></span>
        </a>
    </div>

    <div class="review-layout">

        <section class="review-table-card">
            <div class="review-table-scroll">
                <table class="admin-review-table">
                    <thead>
                    <tr>
                        <th>STT</th>
                        <th>Sản phẩm</th>
                        <th>Người đánh giá</th>
                        <th>Số sao</th>
                        <th>Nội dung đánh giá</th>
                        <th>Trạng thái</th>
                        <th>Ngày đánh giá</th>
                        <th>Thao tác</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e($selectedReview && $selectedReview->review_id === $review->review_id ? 'selected-row' : ''); ?>">
                            <td>
                                <?php echo e(method_exists($reviews, 'firstItem') ? (($reviews->firstItem() ?? 1) + $loop->index) : $loop->iteration); ?>

                            </td>

                            <td>
                                <div class="review-product-cell">
                                    <div class="review-product-thumb">
                                        <?php if($review->cayCanh?->anh_dai_dien): ?>
                                            <img src="<?php echo e(asset($review->cayCanh->anh_dai_dien)); ?>" alt="<?php echo e($review->cayCanh->ten_cay); ?>">
                                        <?php else: ?>
                                            <span>🌱</span>
                                        <?php endif; ?>
                                    </div>

                                    <div>
                                        <strong><?php echo e($review->cayCanh?->ten_cay ?? 'Sản phẩm'); ?></strong>
                                        <small>
                                            <?php echo e($review->cayCanh?->danhMuc?->ten_danh_muc ?? 'Cây cảnh'); ?>

                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <strong><?php echo e($review->nguoiDung?->ho_ten ?? 'Khách hàng'); ?></strong>
                                <small><?php echo e($review->nguoiDung?->email ?? '—'); ?></small>
                            </td>

                            <td>
                                <div class="review-stars">
                                    <?php for($star = 1; $star <= 5; $star++): ?>
                                        <span class="<?php echo e($star <= (int)$review->so_sao ? 'filled' : ''); ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                            </td>

                            <td class="review-content-cell">
                                <?php echo e(\Illuminate\Support\Str::limit($review->noi_dung ?: 'Không có nội dung', 68)); ?>

                            </td>

                            <td>
                                <span class="review-status <?php echo e($statusClass($review)); ?>">
                                    <?php echo e($statusLabel($review)); ?>

                                </span>
                            </td>

                            <td>
                                <?php echo e($review->ngay_danh_gia?->format('d/m/Y') ?? '—'); ?>

                                <small><?php echo e($review->ngay_danh_gia?->format('H:i') ?? ''); ?></small>
                            </td>

                            <td>
                                <div class="review-row-actions">
                                    <a
                                        class="review-icon-btn"
                                        title="Xem chi tiết"
                                        href="<?php echo e(route('admin.danh-gia.index', array_merge(request()->query(), ['selected' => $review->review_id]))); ?>"
                                    >
                                        ◉
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="review-empty-row">
                                Không có đánh giá phù hợp với bộ lọc.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $reviews]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reviews)]); ?>
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

        <aside class="review-detail-panel">
            <?php if($selectedReview): ?>
                <div class="review-detail-head">
                    <div>
                        <small>Chi tiết đánh giá</small>
                        <h3>#DG<?php echo e(str_pad((string)$selectedReview->review_id, 4, '0', STR_PAD_LEFT)); ?></h3>
                    </div>

                    <span class="review-status <?php echo e($statusClass($selectedReview)); ?>">
                        <?php echo e($statusLabel($selectedReview)); ?>

                    </span>
                </div>

                <section class="review-detail-section">
                    <div class="review-detail-product">
                        <div class="review-detail-product-image">
                            <?php if($selectedReview->cayCanh?->anh_dai_dien): ?>
                                <img src="<?php echo e(asset($selectedReview->cayCanh->anh_dai_dien)); ?>" alt="<?php echo e($selectedReview->cayCanh->ten_cay); ?>">
                            <?php else: ?>
                                <span>🌱</span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <strong><?php echo e($selectedReview->cayCanh?->ten_cay ?? 'Sản phẩm'); ?></strong>
                            <small><?php echo e($selectedReview->cayCanh?->danhMuc?->ten_danh_muc ?? 'Cây cảnh'); ?></small>
                        </div>
                    </div>
                </section>

                <section class="review-detail-section">
                    <h4>Thông tin đánh giá</h4>

                    <div class="review-detail-info">
                        <span>Người đánh giá</span>
                        <strong><?php echo e($selectedReview->nguoiDung?->ho_ten ?? 'Khách hàng'); ?></strong>
                    </div>

                    <div class="review-detail-info">
                        <span>Email</span>
                        <strong><?php echo e($selectedReview->nguoiDung?->email ?? '—'); ?></strong>
                    </div>

                    <div class="review-detail-info">
                        <span>Mã đơn hàng</span>
                        <strong><?php echo e($selectedReview->chiTietDonHang?->donHang?->orderCode() ?? '—'); ?></strong>
                    </div>

                    <div class="review-detail-info">
                        <span>Ngày đánh giá</span>
                        <strong><?php echo e($selectedReview->ngay_danh_gia?->format('d/m/Y H:i') ?? '—'); ?></strong>
                    </div>

                    <div class="review-detail-info">
                        <span>Số sao</span>
                        <div class="review-stars large">
                            <?php for($star = 1; $star <= 5; $star++): ?>
                                <span class="<?php echo e($star <= (int)$selectedReview->so_sao ? 'filled' : ''); ?>">★</span>
                            <?php endfor; ?>
                        </div>
                    </div>
                </section>

                <section class="review-detail-section">
                    <h4>Nội dung đánh giá</h4>
                    <div class="review-detail-content">
                        <?php echo e($selectedReview->noi_dung ?: 'Khách hàng không nhập nội dung.'); ?>

                    </div>
                </section>

                <?php if(!empty($selectedImages)): ?>
                    <section class="review-detail-section">
                        <h4>Hình ảnh đính kèm</h4>
                        <div class="review-detail-images">
                            <?php $__currentLoopData = $selectedImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(asset($image)); ?>" target="_blank" rel="noopener">
                                    <img src="<?php echo e(asset($image)); ?>" alt="Ảnh đánh giá">
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if(filled($selectedReview->phan_hoi_admin)): ?>
                    <section class="review-detail-section">
                        <h4>Phản hồi của Admin</h4>
                        <div class="admin-review-reply-box">
                            <div><?php echo e($selectedReview->phan_hoi_admin); ?></div>
                            <?php if($selectedReview->phan_hoi_luc): ?>
                                <small>Trả lời lúc <?php echo e($selectedReview->phan_hoi_luc->format('d/m/Y H:i')); ?></small>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="review-detail-section">
                    <h4><?php echo e(filled($selectedReview->phan_hoi_admin) ? 'Cập nhật trả lời' : 'Trả lời đánh giá'); ?></h4>

                    <form
                        method="POST"
                        action="<?php echo e(route('admin.danh-gia.reply', $selectedReview->review_id)); ?>"
                        class="admin-review-reply-form"
                    >
                        <?php echo csrf_field(); ?>

                        <textarea
                            name="phan_hoi_admin"
                            rows="5"
                            maxlength="2000"
                            required
                            placeholder="Nhập nội dung trả lời khách hàng..."
                        ><?php echo e(old('phan_hoi_admin', $selectedReview->phan_hoi_admin)); ?></textarea>

                        <?php $__errorArgs = ['phan_hoi_admin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="review-alert error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        <button type="submit" class="review-btn primary full">
                            <?php echo e(filled($selectedReview->phan_hoi_admin) ? 'Cập nhật trả lời' : 'Gửi trả lời'); ?>

                        </button>
                    </form>
                </section>

                <div class="review-detail-note">
                    Đánh giá được hiển thị công khai ngay sau khi khách hàng gửi. Admin chỉ được trả lời đánh giá; không được duyệt, từ chối, xóa hoặc chỉnh sửa số sao, nội dung và hình ảnh đánh giá gốc.
                </div>

                <div class="review-detail-actions">
                    <a
                        href="<?php echo e(route('admin.danh-gia.index', request()->except('selected'))); ?>"
                        class="review-btn secondary full"
                    >
                        Đóng
                    </a>
                </div>
            <?php else: ?>
                <div class="review-empty-detail">
                    <span>☆</span>
                    <h3>Chưa chọn đánh giá</h3>
                    <p>Chọn biểu tượng “Xem” để hiển thị chi tiết đánh giá.</p>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/danh_gia/index.blade.php ENDPATH**/ ?>