<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi - GreenShop</title>
    @vite(['resources/css/customer/site-shell.css'])
    @vite('resources/css/customer/shared-account-sidebar.css')
    @vite('resources/css/customer/don-hang.css')
    @vite('resources/css/customer/orders-readable.css')
    @vite('resources/css/customer/receipt-modal.css')
</head>
<body>
@include('trang_chu.components.header')

@php
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
@endphp

<div class="orders-shell">
    <div class="orders-mobile-backdrop" id="ordersSidebarBackdrop"></div>

    @include('tai_khoan.components.account-sidebar', ['activeSidebar' => 'orders', 'sidebarId' => 'ordersSidebar'])

    <main class="orders-main">
        <section class="orders-list-panel">
            <div class="orders-list-heading">
                <button type="button" class="sidebar-toggle" id="ordersSidebarToggle" aria-label="Mở menu">☰</button>
                <div>
                    <h1>Đơn hàng của tôi</h1>
                    <p>Theo dõi và quản lý các đơn hàng của bạn</p>
                </div>
            </div>

            @if(session('success') || session('warning') || session('error'))
                <div class="orders-alert {{ session('error') ? 'error' : (session('warning') ? 'warning' : 'success') }} auto-dismiss-order-alert" id="ordersAlert">
                    <span class="alert-dot">{{ session('error') ? '!' : (session('warning') ? '!' : '✓') }}</span>
                    <span>{{ session('error') ?? session('warning') ?? session('success') }}</span>
                    <button type="button" aria-label="Đóng" data-close-alert>×</button>
                </div>
            @endif

            @if(session('payment_notice'))
                <div class="orders-alert success auto-dismiss-order-alert">
                    <span class="alert-dot">✓</span><span>{{ session('payment_notice') }}</span>
                    <button type="button" aria-label="Đóng" data-close-alert>×</button>
                </div>
            @endif

            @isset($loadError)
                <div class="orders-alert error"><span class="alert-dot">!</span><span>{{ $loadError }}</span></div>
            @endisset

            <form method="GET" action="{{ route('don-hang.index') }}" class="order-search-form">
                <label class="order-search-box">
                    <span>⌕</span>
                    <input type="text" name="q" maxlength="100" value="{{ $search }}" placeholder="Tìm theo mã đơn hàng...">
                </label>
                <input type="hidden" name="status" value="{{ $filter }}">
                <button type="submit" class="filter-button">Tìm kiếm</button>
            </form>

            <div class="status-tabs" role="tablist" aria-label="Lọc trạng thái đơn hàng">
                @foreach($statusTabs as $key => $label)
                    <a class="status-tab {{ $filter === $key ? 'active' : '' }}"
                       href="{{ route('don-hang.index', array_filter(['status' => $key, 'q' => $search ?: null])) }}">
                        {{ $label }}
                        @if(($counts[$key] ?? 0) > 0)
                            <span>{{ $counts[$key] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="order-list">
                @forelse($orders as $order)
                    @php
                        $selected = $selectedOrder && (int)$selectedOrder->order_id === (int)$order->order_id;
                        $normalized = $order->normalizedStatus();
                        $paymentMethod = $order->thanhToan?->methodLabel() ?? 'Thanh toán khi nhận hàng';
                    @endphp
                    <a href="{{ route('don-hang.show', $order->order_id) }}"
                       class="order-card {{ $selected ? 'selected' : '' }}">
                        <div class="order-card-icon status-{{ $normalized }}">▧</div>
                        <div class="order-card-body">
                            <div class="order-card-top">
                                <strong>{{ $order->orderCode() }}</strong>
                                <span class="order-status status-{{ $normalized }}">{{ $order->statusLabel() }}</span>
                            </div>
                            <div class="order-card-meta">
                                <span>{{ optional($order->ngay_dat)->format('d/m/Y H:i') }}</span>
                                <span class="order-total">{{ number_format((float)$order->tong_tien, 0, ',', '.') }}đ</span>
                            </div>
                            <div class="order-card-bottom">
                                <span>Thanh toán: {{ $paymentMethod }}</span>
                                <span>{{ (int)($order->total_quantity ?? $order->chi_tiet_don_hangs_count ?? 0) }} sản phẩm</span>
                            </div>
                        </div>
                        <span class="order-chevron">›</span>
                    </a>
                @empty
                    <div class="empty-orders">
                        <div class="empty-orders-icon">▢</div>
                        @if($search !== '')
                            <h3>Không tìm thấy đơn hàng phù hợp.</h3>
                            <p>Hãy kiểm tra lại mã đơn hoặc xóa từ khóa tìm kiếm.</p>
                        @elseif($filter !== 'all')
                            <h3>Không có đơn hàng phù hợp với bộ lọc đã chọn.</h3>
                            <p>Thử chọn trạng thái khác để xem các đơn hàng của bạn.</p>
                        @else
                            <h3>Bạn chưa có đơn hàng nào.</h3>
                            <p>Các đơn sau khi đặt sẽ xuất hiện tại đây.</p>
                        @endif
                    </div>
                @endforelse
            </div>

            <x-pagination :paginator="$orders" />
        </section>

        <section class="order-detail-panel">
            @if($selectedOrder)
                @php
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
                @endphp

                <div class="detail-heading-row">
                    <div>
                        <span class="eyebrow">CHI TIẾT ĐƠN HÀNG</span>
                        <h2>{{ $selectedOrder->orderCode() }}</h2>
                    </div>
                    <div class="detail-actions">
                        @if($selectedOrder->normalizedStatus() === 'delivered')
                            <form method="POST" action="{{ route('don-hang.confirm-receipt', $selectedOrder->order_id) }}" id="receiptConfirmForm">
                                @csrf
                                <input type="hidden" name="receipt_status" value="received">
                                <button type="submit" class="detail-action receipt-confirm">Đã nhận hàng</button>
                            </form>
                        @endif
                        <button type="button" class="detail-action" onclick="window.print()">▤ In hóa đơn</button>
                        @if($selectedOrder->isCancelable())
                            <button type="button" class="detail-action danger" data-open-cancel>♲ Hủy đơn</button>
                        @endif
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-item">
                        <span>Mã đơn hàng</span>
                        <strong>{{ $selectedOrder->orderCode() }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>Ngày đặt</span>
                        <strong>{{ optional($selectedOrder->ngay_dat)->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>Trạng thái</span>
                        <strong><span class="order-status status-{{ $normalized }}">{{ $selectedOrder->statusLabel() }}</span></strong>
                    </div>
                    <div class="summary-item">
                        <span>Phương thức thanh toán</span>
                        <strong>{{ $paymentMethodLabel }}</strong>
                    </div>
                    <div class="summary-item total-highlight">
                        <span>Tổng tiền</span>
                        <strong>{{ number_format((float)$selectedOrder->tong_tien, 0, ',', '.') }}đ</strong>
                    </div>
                    <div class="summary-item">
                        <span>Số lượng sản phẩm</span>
                        <strong>{{ $selectedOrder->chiTietDonHangs->sum('so_luong') }} sản phẩm</strong>
                    </div>
                </div>

                <div class="timeline-card {{ $normalized === 'cancelled' ? 'cancelled' : '' }}">
                    @if($normalized === 'cancelled')
                        <div class="cancelled-timeline-message">Đơn hàng đã bị hủy. Tiến trình giao hàng đã dừng.</div>
                    @else
                        @foreach($timeline as $index => $step)
                            <div class="timeline-step {{ $index <= $currentStep ? 'done' : '' }} {{ $index === $currentStep ? 'current' : '' }}">
                                <span class="timeline-dot">{{ $step['icon'] }}</span>
                                <span class="timeline-label">{{ $step['label'] }}</span>
                                @if($index === 0 && $selectedOrder->ngay_dat)
                                    <small>{{ $selectedOrder->ngay_dat->format('d/m/Y H:i') }}</small>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="info-grid">
                    <article class="info-card">
                        <div class="info-card-title"><span>⌖</span><strong>Thông tin giao hàng</strong></div>
                        @if($address)
                            <div class="info-line"><span>♙</span><b>{{ $address->nguoi_nhan ?: Auth::user()->ho_ten }}</b>@if($address->mac_dinh)<em>Mặc định</em>@endif</div>
                            <div class="info-line"><span>☎</span><span>{{ $address->so_dien_thoai ?: Auth::user()->so_dien_thoai }}</span></div>
                            <div class="info-line align-top"><span>⌖</span><span>{{ collect([$address->dia_chi, $address->phuong_xa, $address->quan_huyen, $address->tinh_thanh])->filter()->join(', ') }}</span></div>
                        @else
                            <p class="info-error">Không thể tải đầy đủ thông tin địa chỉ của đơn hàng.</p>
                        @endif
                    </article>

                    <article class="info-card">
                        <div class="info-card-title"><span>▣</span><strong>Thông tin thanh toán</strong></div>
                        <div class="info-line"><span>▤</span><span>Hình thức thanh toán</span><b>{{ $paymentFormLabel }}</b></div>
                        <div class="payment-status-row">
                            <span>Trạng thái thanh toán</span>
                            <strong class="payment-badge {{ $paymentClass }}">{{ $paymentLabel }}</strong>
                        </div>
                        @if($selectedOrder->normalizedStatus() === 'pending_payment' && $retryTransaction && strtoupper((string)$retryTransaction->provider) !== 'MOMO')
                            <a class="retry-payment-button" href="{{ route('thanh-toan.online.retry', $retryTransaction->transaction_id) }}">
                                Thanh toán ngay với {{ strtoupper((string)$retryTransaction->provider) === 'MOMO' ? 'MoMo' : $retryTransaction->provider }}
                                <span>→</span>
                            </a>
                            <small class="retry-payment-note">Tiếp tục thanh toán cho chính đơn này, GreenShop không tạo đơn hàng mới.</small>
                        @endif
                    </article>
                </div>

                <article class="products-card">
                    <div class="products-title">Sản phẩm đã đặt <span>({{ $selectedOrder->chiTietDonHangs->sum('so_luong') }} sản phẩm)</span></div>
                    <div class="products-table-head">
                        <span>Sản phẩm</span><span>Đơn giá</span><span>Số lượng</span><span>Thành tiền</span>
                    </div>
                    @forelse($selectedOrder->chiTietDonHangs as $detail)
                        @php
                            $plant = $detail->cayCanh;
                            $rawImage = $plant?->anh_dai_dien;
                            $imageUrl = $rawImage
                                ? ((str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://')) ? $rawImage : asset(ltrim($rawImage, '/')))
                                : null;
                            $lineTotal = (float)$detail->don_gia * (int)$detail->so_luong;
                        @endphp
                        <div class="product-row">
                            <div class="product-info">
                                <div class="product-thumb">
                                    @if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $plant?->ten_cay ?? 'Sản phẩm' }}">@else<span>🌿</span>@endif
                                </div>
                                <div>
                                    <strong>{{ $plant?->ten_cay ?? 'Sản phẩm không còn tồn tại' }}</strong>
                                    <span>{{ $plant?->chieu_cao ? 'Kích thước: ' . $plant->chieu_cao : 'Cây cảnh GreenShop' }}</span>
                                </div>
                            </div>
                            <div data-label="Đơn giá">{{ number_format((float)$detail->don_gia, 0, ',', '.') }}đ</div>
                            <div data-label="Số lượng">{{ $detail->so_luong }}</div>
                            <div data-label="Thành tiền"><strong>{{ number_format($lineTotal, 0, ',', '.') }}đ</strong></div>
                        </div>
                    @empty
                        <div class="products-load-error">Không thể tải đầy đủ thông tin sản phẩm của đơn hàng.</div>
                    @endforelse

                    <div class="order-totals">
                        <div><span>Tạm tính</span><strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong></div>
                        <div><span>Phí vận chuyển</span><strong>{{ number_format($shipping, 0, ',', '.') }}đ</strong></div>
                        <div class="grand-total"><span>Tổng cộng</span><strong>{{ number_format((float)$selectedOrder->tong_tien, 0, ',', '.') }}đ</strong></div>
                    </div>
                </article>
            @else
                <div class="no-selected-order">
                    <div>▧</div>
                    <h2>Chưa có đơn hàng để hiển thị</h2>
                    <p>Chọn một đơn ở danh sách bên trái để xem chi tiết.</p>
                </div>
            @endif
        </section>
    </main>
</div>

@if($selectedOrder && $selectedOrder->isCancelable())
<div class="cancel-modal" id="cancelOrderModal" aria-hidden="true">
    <div class="cancel-modal-backdrop" data-close-cancel></div>
    <div class="cancel-dialog" role="dialog" aria-modal="true" aria-labelledby="cancelOrderTitle">
        <div class="cancel-icon">!</div>
        <h2 id="cancelOrderTitle">Hủy đơn hàng?</h2>
        <p>Bạn có chắc chắn muốn hủy đơn hàng <strong>{{ $selectedOrder->orderCode() }}</strong>?</p>
        <p class="cancel-note">Thao tác sẽ cập nhật trạng thái đơn và hoàn lại tồn kho sản phẩm.</p>
        <div class="cancel-actions">
            <button type="button" class="btn-neutral" data-close-cancel>Không</button>
            <form action="{{ route('don-hang.cancel', $selectedOrder->order_id) }}" method="POST" id="cancelOrderForm">
                @csrf
                <input type="hidden" name="expected_status" value="{{ $selectedOrder->normalizedStatus() }}">
                <button type="submit" class="btn-cancel" id="confirmCancelButton">Xác nhận hủy</button>
            </form>
        </div>
    </div>
</div>
@endif

@if($selectedOrder && $selectedOrder->normalizedStatus() === 'delivered')
<div class="cancel-modal receipt-modal" id="receiptConfirmModal" aria-hidden="true">
    <div class="cancel-modal-backdrop" data-close-receipt></div>
    <div class="cancel-dialog" role="dialog" aria-modal="true" aria-labelledby="receiptConfirmTitle" aria-describedby="receiptConfirmDesc">
        <div class="receipt-modal-icon">✓</div>
        <h2 id="receiptConfirmTitle">Xác nhận đã nhận hàng</h2>
        <p id="receiptConfirmDesc">Bạn xác nhận đã nhận được đơn hàng <strong>{{ $selectedOrder->orderCode() }}</strong>?</p>
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
@endif

@vite('resources/js/customer/don-hang.js')
</body>
</html>
