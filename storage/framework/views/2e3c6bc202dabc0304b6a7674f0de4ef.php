<?php $__env->startSection('title', 'Quản lý đơn hàng - GreenShop'); ?>
<?php $__env->startSection('page-title', 'Quản lý đơn hàng'); ?>

<?php $__env->startSection('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/admin/don-hang.css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $selectedPayment = $selectedOrder?->thanhToan;
    $selectedStatus = $selectedOrder?->normalizedStatus();
    $isSelectedCod = $selectedPayment
        ? in_array(strtoupper(trim((string) $selectedPayment->phuong_thuc)), ['COD', 'CASH'], true)
        : false;

    $paymentState = function ($payment) {
        if (!$payment) return 'pending';
        $value = \Illuminate\Support\Str::of((string) $payment->trang_thai)
            ->trim()->lower()->ascii()->replace([' ', '-'], '_')->value();
        if (in_array($value, ['paid', 'da_thanh_toan'], true)) return 'paid';
        if (in_array($value, ['cancelled', 'canceled', 'da_huy'], true)) return 'cancelled';
        return 'pending';
    };

    $paymentLabel = function ($payment) use ($paymentState) {
        $isCod = $payment
            ? in_array(strtoupper(trim((string) $payment->phuong_thuc)), ['COD', 'CASH'], true)
            : false;

        return match ($paymentState($payment)) {
            'paid' => 'Đã thanh toán',
            'cancelled' => 'Đã hủy',
            default => $isCod ? 'Chưa thanh toán' : 'Chờ xác nhận thanh toán',
        };
    };

    $paymentMethodLabel = function ($payment) {
        return $payment?->methodLabel() ?? 'Chưa xác định';
    };

    $orderBadgeClass = fn ($status) => match ($status) {
        'pending_confirmation' => 'badge-orange',
        'preparing' => 'badge-purple',
        'shipping' => 'badge-blue',
        'delivered' => 'badge-green',
        'completed' => 'badge-green',
        'cancelled' => 'badge-red',
        default => 'badge-gray',
    };
?>

<div class="order-admin-page">

    <?php if(session('success')): ?>
        <div class="admin-order-alert success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('warning')): ?>
        <div class="admin-order-alert warning"><?php echo e(session('warning')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="admin-order-alert error"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if(isset($loadError)): ?>
        <div class="admin-order-alert error"><?php echo e($loadError); ?></div>
    <?php endif; ?>

    <div class="order-toolbar">
        <div class="order-export-actions">
            <a href="<?php echo e(route('admin.don-hang.export-excel', request()->query())); ?>" class="btn-export">
                <span>▤</span> Xuất Excel
            </a>
            <a href="<?php echo e(route('admin.don-hang.export-pdf', request()->query())); ?>" class="btn-export" target="_blank">
                <span>▧</span> Xuất PDF
            </a>
        </div>
    </div>

    <div class="order-admin-layout">
        <section class="order-main-column">

            <div class="order-stat-grid">
                <a href="<?php echo e(route('admin.don-hang.index')); ?>" class="order-stat-card <?php echo e($filters['order_status'] === 'all' ? 'active' : ''); ?>">
                    <span class="stat-icon blue">▣</span>
                    <div><small>Tất cả đơn hàng</small><strong><?php echo e(number_format($counts['all'] ?? 0)); ?></strong><span>Đơn hàng</span></div>
                </a>

                <a href="<?php echo e(route('admin.don-hang.index', ['order_status' => 'pending_confirmation'])); ?>" class="order-stat-card <?php echo e($filters['order_status'] === 'pending_confirmation' ? 'active' : ''); ?>">
                    <span class="stat-icon orange">⌑</span>
                    <div><small>Chờ xác nhận</small><strong><?php echo e(number_format($counts['pending_confirmation'] ?? 0)); ?></strong><span>Đơn hàng</span></div>
                </a>

                <a href="<?php echo e(route('admin.don-hang.index', ['order_status' => 'waiting_payment'])); ?>" class="order-stat-card <?php echo e($filters['order_status'] === 'waiting_payment' ? 'active' : ''); ?>">
                    <span class="stat-icon purple">♧</span>
                    <div><small>Chờ xác nhận thanh toán</small><strong><?php echo e(number_format($counts['waiting_payment'] ?? 0)); ?></strong><span>Đơn hàng</span></div>
                </a>

                <a href="<?php echo e(route('admin.don-hang.index', ['order_status' => 'preparing'])); ?>" class="order-stat-card <?php echo e($filters['order_status'] === 'preparing' ? 'active' : ''); ?>">
                    <span class="stat-icon violet">⌁</span>
                    <div><small>Chờ vận chuyển</small><strong><?php echo e(number_format($counts['preparing'] ?? 0)); ?></strong><span>Đơn hàng</span></div>
                </a>

                <a href="<?php echo e(route('admin.don-hang.index', ['order_status' => 'shipping'])); ?>" class="order-stat-card <?php echo e($filters['order_status'] === 'shipping' ? 'active' : ''); ?>">
                    <span class="stat-icon blue">◈</span>
                    <div><small>Đang giao</small><strong><?php echo e(number_format($counts['shipping'] ?? 0)); ?></strong><span>Đơn hàng</span></div>
                </a>

                <a href="<?php echo e(route('admin.don-hang.index', ['order_status' => 'delivered'])); ?>" class="order-stat-card <?php echo e($filters['order_status'] === 'delivered' ? 'active' : ''); ?>">
                    <span class="stat-icon green">✓</span>
                    <div><small>Đã giao</small><strong><?php echo e(number_format($counts['delivered'] ?? 0)); ?></strong><span>Đơn hàng</span></div>
                </a>

                <a href="<?php echo e(route('admin.don-hang.index', ['order_status' => 'completed'])); ?>" class="order-stat-card <?php echo e($filters['order_status'] === 'completed' ? 'active' : ''); ?>">
                    <span class="stat-icon green">✓</span>
                    <div><small>Hoàn thành</small><strong><?php echo e(number_format($counts['completed'] ?? 0)); ?></strong><span>Đơn hàng</span></div>
                </a>

                <a href="<?php echo e(route('admin.don-hang.index', ['order_status' => 'cancelled'])); ?>" class="order-stat-card <?php echo e($filters['order_status'] === 'cancelled' ? 'active' : ''); ?>">
                    <span class="stat-icon red">×</span>
                    <div><small>Đã hủy</small><strong><?php echo e(number_format($counts['cancelled'] ?? 0)); ?></strong><span>Đơn hàng</span></div>
                </a>
            </div>

            <form method="GET" action="<?php echo e(route('admin.don-hang.index')); ?>" class="order-filter-panel">
                <div class="filter-group search-filter">
                    <label>Tìm kiếm</label>
                    <div class="filter-input-wrap">
                        <span>⌕</span>
                        <input type="text" name="q" value="<?php echo e($filters['q']); ?>" placeholder="Mã đơn, tên khách hàng, SĐT...">
                    </div>
                </div>

                <input type="hidden" name="order_status" value="<?php echo e($filters['order_status']); ?>">
                <div class="filter-group">
                    <label>Phương thức thanh toán</label>
                    <select name="payment_method">
                        <option value="all" <?php if($filters['payment_method'] === 'all'): echo 'selected'; endif; ?>>Tất cả</option>
                        <option value="COD" <?php if($filters['payment_method'] === 'COD'): echo 'selected'; endif; ?>>COD</option>
                        <option value="PAYPAL" <?php if($filters['payment_method'] === 'PAYPAL'): echo 'selected'; endif; ?>>PayPal</option>
                        <option value="PAYOS" <?php if($filters['payment_method'] === 'PAYOS'): echo 'selected'; endif; ?>>payOS</option>
                    </select>
                </div>

                <div class="filter-group date-range-group">
                    <label>Khoảng thời gian</label>
                    <div class="date-range">
                        <input type="date" name="date_from" value="<?php echo e($filters['date_from']); ?>">
                        <span>→</span>
                        <input type="date" name="date_to" value="<?php echo e($filters['date_to']); ?>">
                    </div>
                </div>

                <button type="submit" class="filter-submit">Lọc</button>
                <a href="<?php echo e(route('admin.don-hang.index')); ?>" class="filter-reset">↻ Làm mới</a>
            </form>

            <div class="order-table-card">
                <div class="order-table-scroll">
                    <table class="admin-order-table">
                        <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Phương thức TT</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái thanh toán</th>
                            <th>Trạng thái đơn hàng</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $payment = $order->thanhToan;
                                $method = $paymentMethodLabel($payment);
                                $pState = $paymentState($payment);
                                $status = $order->normalizedStatus();
                                $isCod = $method === 'COD';
                            ?>
                            <tr class="<?php echo e($selectedOrder && $selectedOrder->order_id === $order->order_id ? 'selected-row' : ''); ?>">
                                <td><strong><?php echo e($order->orderCode()); ?></strong></td>
                                <td>
                                    <strong><?php echo e($order->nguoiDung?->ho_ten ?? 'Khách hàng'); ?></strong>
                                    <small><?php echo e($order->nguoiDung?->so_dien_thoai ?? '—'); ?></small>
                                </td>
                                <td>
                                    <?php echo e($order->ngay_dat?->format('d/m/Y') ?? '—'); ?>

                                    <small><?php echo e($order->ngay_dat?->format('H:i') ?? ''); ?></small>
                                </td>
                                <td>
                                    <span class="payment-method-icon"><?php echo e($isCod ? '▣' : '▰'); ?></span>
                                    <?php echo e($method); ?>

                                </td>
                                <td class="money"><?php echo e(number_format((float) $order->tong_tien, 0, ',', '.')); ?>đ</td>
                                <td>
                                    <span class="status-badge <?php echo e($pState === 'paid' ? 'badge-green' : ($pState === 'cancelled' ? 'badge-red' : 'badge-orange')); ?>">
                                        <?php echo e($paymentLabel($payment)); ?>

                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo e($orderBadgeClass($status)); ?>">
                                        <?php echo e($order->statusLabel()); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a class="btn-view" href="<?php echo e(route('admin.don-hang.index', array_merge(request()->query(), ['selected' => $order->order_id]))); ?>">⌕ Xem</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="empty-order-table">Không có đơn hàng phù hợp với bộ lọc.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['paginator' => $orders]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orders)]); ?>
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
            </div>
        </section>

        <aside class="order-detail-panel">
            <?php if($selectedOrder): ?>
                <?php
                    $detailPayment = $selectedOrder->thanhToan;
                    $detailPState = $paymentState($detailPayment);
                    $detailMethod = $paymentMethodLabel($detailPayment);
                    $detailStatus = $selectedOrder->normalizedStatus();
                    $detailAddress = $selectedOrder->diaChi;
                    $subtotal = $selectedOrder->chiTietDonHangs->sum(fn($d) => ((float) $d->don_gia) * ((int) $d->so_luong));
                ?>

                <div class="detail-panel-head">
                    <div>
                        <small>Chi tiết đơn hàng</small>
                        <h2><?php echo e($selectedOrder->orderCode()); ?></h2>
                        <p>Đặt lúc <?php echo e($selectedOrder->ngay_dat?->format('d/m/Y H:i')); ?></p>
                    </div>
                    <span class="status-badge <?php echo e($orderBadgeClass($detailStatus)); ?>"><?php echo e($selectedOrder->statusLabel()); ?></span>
                </div>

                <section class="detail-section">
                    <h3>⌂ Thông tin khách hàng</h3>
                    <strong><?php echo e($selectedOrder->nguoiDung?->ho_ten ?? 'Khách hàng'); ?></strong>
                    <p>☎ <?php echo e($selectedOrder->nguoiDung?->so_dien_thoai ?? '—'); ?></p>
                    <p>✉ <?php echo e($selectedOrder->nguoiDung?->email ?? '—'); ?></p>
                    <p>⌖
                        <?php echo e($detailAddress
                            ? trim(($detailAddress->dia_chi ?? '') . ', ' . ($detailAddress->phuong_xa ?? '') . ', ' . ($detailAddress->quan_huyen ?? '') . ', ' . ($detailAddress->tinh_thanh ?? ''), ', ')
                            : 'Chưa có địa chỉ'); ?>

                    </p>
                </section>

                <section class="detail-section">
                    <h3>▣ Thông tin thanh toán</h3>
                    <div class="detail-info-row"><span>Phương thức thanh toán</span><strong><?php echo e($detailMethod); ?></strong></div>
                    <div class="detail-info-row"><span>Trạng thái thanh toán</span><span class="status-badge <?php echo e($detailPState === 'paid' ? 'badge-green' : ($detailPState === 'cancelled' ? 'badge-red' : 'badge-orange')); ?>"><?php echo e($paymentLabel($detailPayment)); ?></span></div>
                    <div class="detail-info-row"><span>Trạng thái đơn hàng</span><span class="status-badge <?php echo e($orderBadgeClass($detailStatus)); ?>"><?php echo e($selectedOrder->statusLabel()); ?></span></div>
                </section>

                <section class="detail-section">
                    <h3>Sản phẩm (<?php echo e($selectedOrder->chiTietDonHangs->count()); ?>)</h3>
                    <div class="detail-products">
                        <?php $__empty_1 = true; $__currentLoopData = $selectedOrder->chiTietDonHangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="detail-product">
                                <div class="detail-product-image">
                                    <?php if($detail->cayCanh?->anh_dai_dien): ?>
                                        <img src="<?php echo e(asset($detail->cayCanh->anh_dai_dien)); ?>" alt="<?php echo e($detail->cayCanh->ten_cay); ?>">
                                    <?php else: ?>
                                        <span>🌱</span>
                                    <?php endif; ?>
                                </div>
                                <div class="detail-product-info">
                                    <strong><?php echo e($detail->cayCanh?->ten_cay ?? 'Sản phẩm'); ?></strong>
                                    <small>SL: <?php echo e($detail->so_luong); ?></small>
                                </div>
                                <strong class="detail-product-price"><?php echo e(number_format((float) $detail->don_gia * (int) $detail->so_luong, 0, ',', '.')); ?>đ</strong>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="muted">Không có dữ liệu sản phẩm.</p>
                        <?php endif; ?>
                    </div>

                    <div class="detail-total">
                        <div><span>Tạm tính</span><strong><?php echo e(number_format($subtotal, 0, ',', '.')); ?>đ</strong></div>
                        <div><span>Phí vận chuyển</span><strong><?php echo e(number_format((float) $selectedOrder->phi_van_chuyen, 0, ',', '.')); ?>đ</strong></div>
                        <div class="grand-total"><span>Tổng tiền</span><strong><?php echo e(number_format((float) $selectedOrder->tong_tien, 0, ',', '.')); ?>đ</strong></div>
                    </div>
                </section>

                <div class="detail-lock-note">
                    🔒 Thông tin đơn hàng ở chế độ chỉ đọc. Admin không thể sửa sản phẩm, số lượng, địa chỉ, giá hoặc phương thức thanh toán.
                </div>

                <div class="detail-actions">
                    <?php if($detailStatus === 'pending_confirmation' && $isSelectedCod): ?>
                        <form method="POST" action="<?php echo e(route('admin.don-hang.confirm-cod', $selectedOrder->order_id)); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="expected_status" value="pending_confirmation">
                            <button class="detail-primary green" type="submit">Xác nhận đơn</button>
                        </form>
                    <?php elseif($detailStatus === 'pending_confirmation' && !$isSelectedCod && $detailPState !== 'paid'): ?>
                        <span class="text-muted"><?php echo e($detailPayment?->normalizedMethod() === 'BANK_TRANSFER' ? 'Đơn chuyển khoản cũ: cần đối soát riêng' : 'Chờ xác nhận từ cổng thanh toán'); ?></span>
                    <?php elseif($detailStatus === 'preparing'): ?>
                        <form method="POST" action="<?php echo e(route('admin.don-hang.shipping', $selectedOrder->order_id)); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="expected_status" value="preparing">
                            <button class="detail-primary blue" type="submit">Chuyển sang Đang giao</button>
                        </form>
                    <?php elseif($detailStatus === 'shipping'): ?>
                        <form method="POST" action="<?php echo e(route('admin.don-hang.delivered', $selectedOrder->order_id)); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="expected_status" value="shipping">
                            <button class="detail-primary blue" type="submit">Xác nhận Đã giao</button>
                        </form>
                    <?php elseif($detailStatus === 'delivered'): ?>
                        <p class="detail-note">Đã giao. Chờ khách hàng xác nhận đã nhận hàng để tự động hoàn thành đơn.</p>
                    <?php endif; ?>

                    <a href="<?php echo e(route('admin.don-hang.index', request()->except('selected'))); ?>" class="detail-close">Đóng</a>
                </div>
            <?php else: ?>
                <div class="empty-detail">
                    <span>▣</span>
                    <h3>Chưa chọn đơn hàng</h3>
                    <p>Chọn “Xem” để hiển thị chi tiết đơn hàng.</p>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/don_hang/index.blade.php ENDPATH**/ ?>