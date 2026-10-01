<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi - GreenShop</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/customer/site-shell.css']); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/customer/shared-account-sidebar.css'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/customer/don-hang.css'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/customer/orders-readable.css'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/customer/receipt-modal.css'); ?>
</head>
<body>
<?php echo $__env->make('trang_chu.components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
    $statusTabs = [
        'all' => 'Tất cả',
        'pending_confirmation' => 'Chờ xác nhận',
        'preparing' => 'Đang chuẩn bị',
        'shipping' => 'Đang giao',
        'delivered' => 'Đã giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];

    $payment = $selectedOrder?->thanhToan;
    $paymentMethodLabel = $payment?->methodLabel() ?? 'Thanh toán khi nhận hàng';
    $paymentFormLabel = $payment?->paymentFormLabel() ?? 'Thanh toán khi nhận hàng';
    $paymentStatus = $payment?->trang_thai;
    $paymentStatusNormalized = strtolower(trim((string) $paymentStatus));
    $isBankTransferPayment = $payment && !in_array(strtoupper(trim((string) $payment->phuong_thuc)), ['COD', 'CASH'], true);
    $paymentLabel = match ($paymentStatusNormalized) {
        'paid', 'da_thanh_toan', 'đã thanh toán' => 'Đã thanh toán',
        'failed', 'that_bai', 'thất bại' => 'Thanh toán thất bại',
        'cancelled', 'canceled', 'da_huy', 'đã hủy' => 'Đã hủy',
        'refund_pending' => 'Đang hoàn tiền',
        'refunded' => 'Đã hoàn tiền',
        default => $isBankTransferPayment ? 'Chờ xác nhận thanh toán' : 'Thanh toán khi nhận hàng',
    };
    $paymentClass = in_array($paymentStatusNormalized, ['paid', 'da_thanh_toan', 'đã thanh toán'], true)
        ? 'paid'
        : (in_array($paymentStatusNormalized, ['failed', 'cancelled', 'canceled'], true) ? 'failed' : 'pending');
?>

<div class="orders-shell">
    <div class="orders-mobile-backdrop" id="ordersSidebarBackdrop"></div>

    <?php echo $__env->make('tai_khoan.components.account-sidebar', ['activeSidebar' => 'orders', 'sidebarId' => 'ordersSidebar'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="orders-main">
        <section class="orders-list-panel">
            <div class="orders-list-heading">
                <button type="button" class="sidebar-toggle" id="ordersSidebarToggle" aria-label="Mở menu">☰</button>
                <div>
                    <h1>Đơn hàng của tôi</h1>
                    <p>Theo dõi và quản lý các đơn hàng của bạn</p>
                </div>
            </div>

            <?php if(session('success') || session('warning') || session('error')): ?>
                <div class="orders-alert <?php echo e(session('error') ? 'error' : (session('warning') ? 'warning' : 'success')); ?> auto-dismiss-order-alert" id="ordersAlert">
                    <span class="alert-dot"><?php echo e(session('error') ? '!' : (session('warning') ? '!' : '✓')); ?></span>
                    <span><?php echo e(session('error') ?? session('warning') ?? session('success')); ?></span>
                    <button type="button" aria-label="Đóng" data-close-alert>×</button>
                </div>
            <?php endif; ?>

            <?php if(session('payment_notice')): ?>
                <div class="orders-alert success auto-dismiss-order-alert">
                    <span class="alert-dot">✓</span><span><?php echo e(session('payment_notice')); ?></span>
                    <button type="button" aria-label="Đóng" data-close-alert>×</button>
                </div>
            <?php endif; ?>

            <?php if(isset($loadError)): ?>
                <div class="orders-alert error"><span class="alert-dot">!</span><span><?php echo e($loadError); ?></span></div>
            <?php endif; ?>

            <form method="GET" action="<?php echo e(route('don-hang.index')); ?>" class="order-search-form">
                <label class="order-search-box">
                    <span>⌕</span>
                    <input type="text" name="q" maxlength="100" value="<?php echo e($search); ?>" placeholder="Tìm theo mã đơn hàng...">
                </label>
                <input type="hidden" name="status" value="<?php echo e($filter); ?>">
                <button type="submit" class="filter-button">Tìm kiếm</button>
            </form>

            <div class="status-tabs" role="tablist" aria-label="Lọc trạng thái đơn hàng">
                <?php $__currentLoopData = $statusTabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a class="status-tab <?php echo e($filter === $key ? 'active' : ''); ?>"
                       href="<?php echo e(route('don-hang.index', array_filter(['status' => $key, 'q' => $search ?: null]))); ?>">
                        <?php echo e($label); ?>

                        <?php if(($counts[$key] ?? 0) > 0): ?>
                            <span><?php echo e($counts[$key]); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="order-list">
                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $selected = $selectedOrder && (int)$selectedOrder->order_id === (int)$order->order_id;
                        $normalized = $order->normalizedStatus();
                        $paymentMethod = $order->thanhToan?->methodLabel() ?? 'Thanh toán khi nhận hàng';
                    ?>
                    <a href="<?php echo e(route('don-hang.show', $order->order_id)); ?>"
                       class="order-card <?php echo e($selected ? 'selected' : ''); ?>">
                        <div class="order-card-icon status-<?php echo e($normalized); ?>">▧</div>
                        <div class="order-card-body">
                            <div class="order-card-top">
                                <strong><?php echo e($order->orderCode()); ?></strong>
                                <span class="order-status status-<?php echo e($normalized); ?>"><?php echo e($order->statusLabel()); ?></span>
                            </div>
                            <div class="order-card-meta">
                                <span><?php echo e(optional($order->ngay_dat)->format('d/m/Y H:i')); ?></span>
                                <span class="order-total"><?php echo e(number_format((float)$order->tong_tien, 0, ',', '.')); ?>đ</span>
                            </div>
                            <div class="order-card-bottom">
                                <span>Thanh toán: <?php echo e($paymentMethod); ?></span>
                                <span><?php echo e((int)($order->total_quantity ?? $order->chi_tiet_don_hangs_count ?? 0)); ?> sản phẩm</span>
                            </div>
                        </div>
                        <span class="order-chevron">›</span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-orders">
                        <div class="empty-orders-icon">▢</div>
                        <?php if($search !== ''): ?>
                            <h3>Không tìm thấy đơn hàng phù hợp.</h3>
                            <p>Hãy kiểm tra lại mã đơn hoặc xóa từ khóa tìm kiếm.</p>
                        <?php elseif($filter !== 'all'): ?>
                            <h3>Không có đơn hàng phù hợp với bộ lọc đã chọn.</h3>
                            <p>Thử chọn trạng thái khác để xem các đơn hàng của bạn.</p>
                        <?php else: ?>
                            <h3>Bạn chưa có đơn hàng nào.</h3>
                            <p>Các đơn sau khi đặt sẽ xuất hiện tại đây.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
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
        </section>

        <section class="order-detail-panel">
            <?php if($selectedOrder): ?>
                <?php
                    $normalized = $selectedOrder->normalizedStatus();
                    $currentStep = $selectedOrder->progressStep();
                    $address = $selectedOrder->diaChi;
                    $payment = $selectedOrder->thanhToan;
                    $subtotal = $selectedOrder->chiTietDonHangs->sum(fn($d) => (float)$d->don_gia * (int)$d->so_luong);
                    $shipping = (float)($selectedOrder->phi_van_chuyen ?? 0);
                    $timeline = [
                        ['label' => 'Chờ xác nhận', 'icon' => '✓'],
                        ['label' => 'Đang chuẩn bị', 'icon' => '□'],
                        ['label' => 'Đang giao', 'icon' => '▣'],
                        ['label' => 'Đã giao', 'icon' => '✓'],
                        ['label' => 'Hoàn thành', 'icon' => '✓'],
                    ];
                ?>

                <div class="detail-heading-row">
                    <div>
                        <span class="eyebrow">CHI TIẾT ĐƠN HÀNG</span>
                        <h2><?php echo e($selectedOrder->orderCode()); ?></h2>
                    </div>
                    <div class="detail-actions">
                        <?php if($selectedOrder->normalizedStatus() === 'delivered'): ?>
                            <form method="POST" action="<?php echo e(route('don-hang.confirm-receipt', $selectedOrder->order_id)); ?>" id="receiptConfirmForm">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="receipt_status" value="received">
                                <button type="submit" class="detail-action receipt-confirm">Đã nhận hàng</button>
                            </form>
                        <?php endif; ?>
                        <button type="button" class="detail-action" onclick="window.print()">▤ In hóa đơn</button>
                        <?php if($selectedOrder->isCancelable()): ?>
                            <button type="button" class="detail-action danger" data-open-cancel>♲ Hủy đơn</button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-item">
                        <span>Mã đơn hàng</span>
                        <strong><?php echo e($selectedOrder->orderCode()); ?></strong>
                    </div>
                    <div class="summary-item">
                        <span>Ngày đặt</span>
                        <strong><?php echo e(optional($selectedOrder->ngay_dat)->format('d/m/Y H:i')); ?></strong>
                    </div>
                    <div class="summary-item">
                        <span>Trạng thái</span>
                        <strong><span class="order-status status-<?php echo e($normalized); ?>"><?php echo e($selectedOrder->statusLabel()); ?></span></strong>
                    </div>
                    <div class="summary-item">
                        <span>Phương thức thanh toán</span>
                        <strong><?php echo e($paymentMethodLabel); ?></strong>
                    </div>
                    <div class="summary-item total-highlight">
                        <span>Tổng tiền</span>
                        <strong><?php echo e(number_format((float)$selectedOrder->tong_tien, 0, ',', '.')); ?>đ</strong>
                    </div>
                    <div class="summary-item">
                        <span>Số lượng sản phẩm</span>
                        <strong><?php echo e($selectedOrder->chiTietDonHangs->sum('so_luong')); ?> sản phẩm</strong>
                    </div>
                </div>

                <div class="timeline-card <?php echo e($normalized === 'cancelled' ? 'cancelled' : ''); ?>">
                    <?php if($normalized === 'cancelled'): ?>
                        <div class="cancelled-timeline-message">Đơn hàng đã bị hủy. Tiến trình giao hàng đã dừng.</div>
                    <?php else: ?>
                        <?php $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="timeline-step <?php echo e($index <= $currentStep ? 'done' : ''); ?> <?php echo e($index === $currentStep ? 'current' : ''); ?>">
                                <span class="timeline-dot"><?php echo e($step['icon']); ?></span>
                                <span class="timeline-label"><?php echo e($step['label']); ?></span>
                                <?php if($index === 0 && $selectedOrder->ngay_dat): ?>
                                    <small><?php echo e($selectedOrder->ngay_dat->format('d/m/Y H:i')); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>

                <div class="info-grid">
                    <article class="info-card">
                        <div class="info-card-title"><span>⌖</span><strong>Thông tin giao hàng</strong></div>
                        <?php if($address): ?>
                            <div class="info-line"><span>♙</span><b><?php echo e($address->nguoi_nhan ?: Auth::user()->ho_ten); ?></b><?php if($address->mac_dinh): ?><em>Mặc định</em><?php endif; ?></div>
                            <div class="info-line"><span>☎</span><span><?php echo e($address->so_dien_thoai ?: Auth::user()->so_dien_thoai); ?></span></div>
                            <div class="info-line align-top"><span>⌖</span><span><?php echo e(collect([$address->dia_chi, $address->phuong_xa, $address->quan_huyen, $address->tinh_thanh])->filter()->join(', ')); ?></span></div>
                        <?php else: ?>
                            <p class="info-error">Không thể tải đầy đủ thông tin địa chỉ của đơn hàng.</p>
                        <?php endif; ?>
                    </article>

                    <article class="info-card">
                        <div class="info-card-title"><span>▣</span><strong>Thông tin thanh toán</strong></div>
                        <div class="info-line"><span>▤</span><span>Hình thức thanh toán</span><b><?php echo e($paymentFormLabel); ?></b></div>
                        <div class="payment-status-row">
                            <span>Trạng thái thanh toán</span>
                            <strong class="payment-badge <?php echo e($paymentClass); ?>"><?php echo e($paymentLabel); ?></strong>
                        </div>
                        <?php if($selectedOrder->normalizedStatus() === 'pending_payment' && $retryTransaction && strtoupper((string)$retryTransaction->provider) !== 'MOMO'): ?>
                            <a class="retry-payment-button" href="<?php echo e(route('thanh-toan.online.retry', $retryTransaction->transaction_id)); ?>">
                                Thanh toán ngay với <?php echo e(strtoupper((string)$retryTransaction->provider) === 'MOMO' ? 'MoMo' : $retryTransaction->provider); ?>

                                <span>→</span>
                            </a>
                            <small class="retry-payment-note">Tiếp tục thanh toán cho chính đơn này, GreenShop không tạo đơn hàng mới.</small>
                        <?php endif; ?>
                    </article>
                </div>

                <article class="products-card">
                    <div class="products-title">Sản phẩm đã đặt <span>(<?php echo e($selectedOrder->chiTietDonHangs->sum('so_luong')); ?> sản phẩm)</span></div>
                    <div class="products-table-head">
                        <span>Sản phẩm</span><span>Đơn giá</span><span>Số lượng</span><span>Thành tiền</span>
                    </div>
                    <?php $__empty_1 = true; $__currentLoopData = $selectedOrder->chiTietDonHangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $plant = $detail->cayCanh;
                            $rawImage = $plant?->anh_dai_dien;
                            $imageUrl = $rawImage
                                ? ((str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://')) ? $rawImage : asset(ltrim($rawImage, '/')))
                                : null;
                            $lineTotal = (float)$detail->don_gia * (int)$detail->so_luong;
                        ?>
                        <div class="product-row">
                            <div class="product-info">
                                <div class="product-thumb">
                                    <?php if($imageUrl): ?><img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($plant?->ten_cay ?? 'Sản phẩm'); ?>"><?php else: ?><span>🌿</span><?php endif; ?>
                                </div>
                                <div>
                                    <strong><?php echo e($plant?->ten_cay ?? 'Sản phẩm không còn tồn tại'); ?></strong>
                                    <span><?php echo e($plant?->chieu_cao ? 'Kích thước: ' . $plant->chieu_cao : 'Cây cảnh GreenShop'); ?></span>
                                </div>
                            </div>
                            <div data-label="Đơn giá"><?php echo e(number_format((float)$detail->don_gia, 0, ',', '.')); ?>đ</div>
                            <div data-label="Số lượng"><?php echo e($detail->so_luong); ?></div>
                            <div data-label="Thành tiền"><strong><?php echo e(number_format($lineTotal, 0, ',', '.')); ?>đ</strong></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="products-load-error">Không thể tải đầy đủ thông tin sản phẩm của đơn hàng.</div>
                    <?php endif; ?>

                    <div class="order-totals">
                        <div><span>Tạm tính</span><strong><?php echo e(number_format($subtotal, 0, ',', '.')); ?>đ</strong></div>
                        <div><span>Phí vận chuyển</span><strong><?php echo e(number_format($shipping, 0, ',', '.')); ?>đ</strong></div>
                        <div class="grand-total"><span>Tổng cộng</span><strong><?php echo e(number_format((float)$selectedOrder->tong_tien, 0, ',', '.')); ?>đ</strong></div>
                    </div>
                </article>
            <?php else: ?>
                <div class="no-selected-order">
                    <div>▧</div>
                    <h2>Chưa có đơn hàng để hiển thị</h2>
                    <p>Chọn một đơn ở danh sách bên trái để xem chi tiết.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<?php if($selectedOrder && $selectedOrder->isCancelable()): ?>
<div class="cancel-modal" id="cancelOrderModal" aria-hidden="true">
    <div class="cancel-modal-backdrop" data-close-cancel></div>
    <div class="cancel-dialog" role="dialog" aria-modal="true" aria-labelledby="cancelOrderTitle">
        <div class="cancel-icon">!</div>
        <h2 id="cancelOrderTitle">Hủy đơn hàng?</h2>
        <p>Bạn có chắc chắn muốn hủy đơn hàng <strong><?php echo e($selectedOrder->orderCode()); ?></strong>?</p>
        <p class="cancel-note">Thao tác sẽ cập nhật trạng thái đơn và hoàn lại tồn kho sản phẩm.</p>
        <div class="cancel-actions">
            <button type="button" class="btn-neutral" data-close-cancel>Không</button>
            <form action="<?php echo e(route('don-hang.cancel', $selectedOrder->order_id)); ?>" method="POST" id="cancelOrderForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="expected_status" value="<?php echo e($selectedOrder->normalizedStatus()); ?>">
                <button type="submit" class="btn-cancel" id="confirmCancelButton">Xác nhận hủy</button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if($selectedOrder && $selectedOrder->normalizedStatus() === 'delivered'): ?>
<div class="cancel-modal receipt-modal" id="receiptConfirmModal" aria-hidden="true">
    <div class="cancel-modal-backdrop" data-close-receipt></div>
    <div class="cancel-dialog" role="dialog" aria-modal="true" aria-labelledby="receiptConfirmTitle" aria-describedby="receiptConfirmDesc">
        <div class="receipt-modal-icon">✓</div>
        <h2 id="receiptConfirmTitle">Xác nhận đã nhận hàng</h2>
        <p id="receiptConfirmDesc">Bạn xác nhận đã nhận được đơn hàng <strong><?php echo e($selectedOrder->orderCode()); ?></strong>?</p>
        <p class="cancel-note">Sau khi xác nhận, đơn hàng sẽ chuyển sang trạng thái Hoàn thành.</p>
        <div class="cancel-actions">
            <button type="button" class="btn-neutral" data-close-receipt>Quay lại</button>
            <button type="button" class="receipt-submit" id="receiptSubmitButton">Xác nhận</button>
        </div>
    </div>
</div>
<script>
(() => {
    const form = document.getElementById('receiptConfirmForm');
    const modal = document.getElementById('receiptConfirmModal');
    if (!form || !modal) return;
    const confirmButton = document.getElementById('receiptSubmitButton');
    const openButton = form.querySelector('button[type="submit"]');
    const close = () => {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        openButton?.focus();
    };
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        confirmButton?.focus();
    });
    modal.querySelectorAll('[data-close-receipt]').forEach(button => button.addEventListener('click', close));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && modal.classList.contains('open')) close();
    });
    confirmButton?.addEventListener('click', () => {
        confirmButton.disabled = true;
        confirmButton.textContent = 'Đang xác nhận...';
        form.submit();
    });
})();
</script>
<?php endif; ?>

<?php echo app('Illuminate\Foundation\Vite')('resources/js/customer/don-hang.js'); ?>
</body>
</html>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/don_hang/index.blade.php ENDPATH**/ ?>