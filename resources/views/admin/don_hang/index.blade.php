@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng - GreenShop')
@section('page-title', 'Quản lý đơn hàng')

@section('styles')
    @vite('resources/css/admin/don-hang.css')
@endsection

@section('content')
@php
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
@endphp

<div class="order-admin-page">

    @if(session('success'))
        <div class="admin-order-alert success">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="admin-order-alert warning">{{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="admin-order-alert error">{{ session('error') }}</div>
    @endif
    @isset($loadError)
        <div class="admin-order-alert error">{{ $loadError }}</div>
    @endisset

    <div class="order-toolbar">
        <div class="order-export-actions">
            <a href="{{ route('admin.don-hang.export-excel', request()->query()) }}" class="btn-export">
                <span>▤</span> Xuất Excel
            </a>
            <a href="{{ route('admin.don-hang.export-pdf', request()->query()) }}" class="btn-export" target="_blank">
                <span>▧</span> Xuất PDF
            </a>
        </div>
    </div>

    <div class="order-admin-layout">
        <section class="order-main-column">

            <div class="order-stat-grid">
                <a href="{{ route('admin.don-hang.index') }}" class="order-stat-card {{ $filters['order_status'] === 'all' ? 'active' : '' }}">
                    <span class="stat-icon blue">▣</span>
                    <div><small>Tất cả đơn hàng</small><strong>{{ number_format($counts['all'] ?? 0) }}</strong><span>Đơn hàng</span></div>
                </a>

                <a href="{{ route('admin.don-hang.index', ['order_status' => 'pending_confirmation']) }}" class="order-stat-card {{ $filters['order_status'] === 'pending_confirmation' ? 'active' : '' }}">
                    <span class="stat-icon orange">⌑</span>
                    <div><small>Chờ xác nhận</small><strong>{{ number_format($counts['pending_confirmation'] ?? 0) }}</strong><span>Đơn hàng</span></div>
                </a>

                <a href="{{ route('admin.don-hang.index', ['order_status' => 'waiting_payment']) }}" class="order-stat-card {{ $filters['order_status'] === 'waiting_payment' ? 'active' : '' }}">
                    <span class="stat-icon purple">♧</span>
                    <div><small>Chờ xác nhận thanh toán</small><strong>{{ number_format($counts['waiting_payment'] ?? 0) }}</strong><span>Đơn hàng</span></div>
                </a>

                <a href="{{ route('admin.don-hang.index', ['order_status' => 'preparing']) }}" class="order-stat-card {{ $filters['order_status'] === 'preparing' ? 'active' : '' }}">
                    <span class="stat-icon violet">⌁</span>
                    <div><small>Chờ vận chuyển</small><strong>{{ number_format($counts['preparing'] ?? 0) }}</strong><span>Đơn hàng</span></div>
                </a>

                <a href="{{ route('admin.don-hang.index', ['order_status' => 'shipping']) }}" class="order-stat-card {{ $filters['order_status'] === 'shipping' ? 'active' : '' }}">
                    <span class="stat-icon blue">◈</span>
                    <div><small>Đang giao</small><strong>{{ number_format($counts['shipping'] ?? 0) }}</strong><span>Đơn hàng</span></div>
                </a>

                <a href="{{ route('admin.don-hang.index', ['order_status' => 'delivered']) }}" class="order-stat-card {{ $filters['order_status'] === 'delivered' ? 'active' : '' }}">
                    <span class="stat-icon green">✓</span>
                    <div><small>Đã giao</small><strong>{{ number_format($counts['delivered'] ?? 0) }}</strong><span>Đơn hàng</span></div>
                </a>

                <a href="{{ route('admin.don-hang.index', ['order_status' => 'completed']) }}" class="order-stat-card {{ $filters['order_status'] === 'completed' ? 'active' : '' }}">
                    <span class="stat-icon green">✓</span>
                    <div><small>Hoàn thành</small><strong>{{ number_format($counts['completed'] ?? 0) }}</strong><span>Đơn hàng</span></div>
                </a>

                <a href="{{ route('admin.don-hang.index', ['order_status' => 'cancelled']) }}" class="order-stat-card {{ $filters['order_status'] === 'cancelled' ? 'active' : '' }}">
                    <span class="stat-icon red">×</span>
                    <div><small>Đã hủy</small><strong>{{ number_format($counts['cancelled'] ?? 0) }}</strong><span>Đơn hàng</span></div>
                </a>
            </div>

            <form method="GET" action="{{ route('admin.don-hang.index') }}" class="order-filter-panel">
                <div class="filter-group search-filter">
                    <label>Tìm kiếm</label>
                    <div class="filter-input-wrap">
                        <span>⌕</span>
                        <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Mã đơn, tên khách hàng, SĐT...">
                    </div>
                </div>

                <input type="hidden" name="order_status" value="{{ $filters['order_status'] }}">
                <div class="filter-group">
                    <label>Phương thức thanh toán</label>
                    <select name="payment_method">
                        <option value="all" @selected($filters['payment_method'] === 'all')>Tất cả</option>
                        <option value="COD" @selected($filters['payment_method'] === 'COD')>COD</option>
                        <option value="PAYPAL" @selected($filters['payment_method'] === 'PAYPAL')>PayPal</option>
                        <option value="PAYOS" @selected($filters['payment_method'] === 'PAYOS')>payOS</option>
                    </select>
                </div>

                <div class="filter-group date-range-group">
                    <label>Khoảng thời gian</label>
                    <div class="date-range">
                        <input type="date" name="date_from" value="{{ $filters['date_from'] }}">
                        <span>→</span>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] }}">
                    </div>
                </div>

                <button type="submit" class="filter-submit">Lọc</button>
                <a href="{{ route('admin.don-hang.index') }}" class="filter-reset">↻ Làm mới</a>
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
                        @forelse($orders as $order)
                            @php
                                $payment = $order->thanhToan;
                                $method = $paymentMethodLabel($payment);
                                $pState = $paymentState($payment);
                                $status = $order->normalizedStatus();
                                $isCod = $method === 'COD';
                            @endphp
                            <tr class="{{ $selectedOrder && $selectedOrder->order_id === $order->order_id ? 'selected-row' : '' }}">
                                <td><strong>{{ $order->orderCode() }}</strong></td>
                                <td>
                                    <strong>{{ $order->nguoiDung?->ho_ten ?? 'Khách hàng' }}</strong>
                                    <small>{{ $order->nguoiDung?->so_dien_thoai ?? '—' }}</small>
                                </td>
                                <td>
                                    {{ $order->ngay_dat?->format('d/m/Y') ?? '—' }}
                                    <small>{{ $order->ngay_dat?->format('H:i') ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="payment-method-icon">{{ $isCod ? '▣' : '▰' }}</span>
                                    {{ $method }}
                                </td>
                                <td class="money">{{ number_format((float) $order->tong_tien, 0, ',', '.') }}đ</td>
                                <td>
                                    <span class="status-badge {{ $pState === 'paid' ? 'badge-green' : ($pState === 'cancelled' ? 'badge-red' : 'badge-orange') }}">
                                        {{ $paymentLabel($payment) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $orderBadgeClass($status) }}">
                                        {{ $order->statusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a class="btn-view" href="{{ route('admin.don-hang.index', array_merge(request()->query(), ['selected' => $order->order_id])) }}">⌕ Xem</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-order-table">Không có đơn hàng phù hợp với bộ lọc.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$orders" />
            </div>
        </section>

        <aside class="order-detail-panel">
            @if($selectedOrder)
                @php
                    $detailPayment = $selectedOrder->thanhToan;
                    $detailPState = $paymentState($detailPayment);
                    $detailMethod = $paymentMethodLabel($detailPayment);
                    $detailStatus = $selectedOrder->normalizedStatus();
                    $detailAddress = $selectedOrder->diaChi;
                    $subtotal = $selectedOrder->chiTietDonHangs->sum(fn($d) => ((float) $d->don_gia) * ((int) $d->so_luong));
                @endphp

                <div class="detail-panel-head">
                    <div>
                        <small>Chi tiết đơn hàng</small>
                        <h2>{{ $selectedOrder->orderCode() }}</h2>
                        <p>Đặt lúc {{ $selectedOrder->ngay_dat?->format('d/m/Y H:i') }}</p>
                    </div>
                    <span class="status-badge {{ $orderBadgeClass($detailStatus) }}">{{ $selectedOrder->statusLabel() }}</span>
                </div>

                <section class="detail-section">
                    <h3>⌂ Thông tin khách hàng</h3>
                    <strong>{{ $selectedOrder->nguoiDung?->ho_ten ?? 'Khách hàng' }}</strong>
                    <p>☎ {{ $selectedOrder->nguoiDung?->so_dien_thoai ?? '—' }}</p>
                    <p>✉ {{ $selectedOrder->nguoiDung?->email ?? '—' }}</p>
                    <p>⌖
                        {{ $detailAddress
                            ? trim(($detailAddress->dia_chi ?? '') . ', ' . ($detailAddress->phuong_xa ?? '') . ', ' . ($detailAddress->quan_huyen ?? '') . ', ' . ($detailAddress->tinh_thanh ?? ''), ', ')
                            : 'Chưa có địa chỉ'
                        }}
                    </p>
                </section>

                <section class="detail-section">
                    <h3>▣ Thông tin thanh toán</h3>
                    <div class="detail-info-row"><span>Phương thức thanh toán</span><strong>{{ $detailMethod }}</strong></div>
                    <div class="detail-info-row"><span>Trạng thái thanh toán</span><span class="status-badge {{ $detailPState === 'paid' ? 'badge-green' : ($detailPState === 'cancelled' ? 'badge-red' : 'badge-orange') }}">{{ $paymentLabel($detailPayment) }}</span></div>
                    <div class="detail-info-row"><span>Trạng thái đơn hàng</span><span class="status-badge {{ $orderBadgeClass($detailStatus) }}">{{ $selectedOrder->statusLabel() }}</span></div>
                </section>

                <section class="detail-section">
                    <h3>Sản phẩm ({{ $selectedOrder->chiTietDonHangs->count() }})</h3>
                    <div class="detail-products">
                        @forelse($selectedOrder->chiTietDonHangs as $detail)
                            <div class="detail-product">
                                <div class="detail-product-image">
                                    @if($detail->cayCanh?->anh_dai_dien)
                                        <img src="{{ asset($detail->cayCanh->anh_dai_dien) }}" alt="{{ $detail->cayCanh->ten_cay }}">
                                    @else
                                        <span>🌱</span>
                                    @endif
                                </div>
                                <div class="detail-product-info">
                                    <strong>{{ $detail->cayCanh?->ten_cay ?? 'Sản phẩm' }}</strong>
                                    <small>SL: {{ $detail->so_luong }}</small>
                                </div>
                                <strong class="detail-product-price">{{ number_format((float) $detail->don_gia * (int) $detail->so_luong, 0, ',', '.') }}đ</strong>
                            </div>
                        @empty
                            <p class="muted">Không có dữ liệu sản phẩm.</p>
                        @endforelse
                    </div>

                    <div class="detail-total">
                        <div><span>Tạm tính</span><strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong></div>
                        <div><span>Phí vận chuyển</span><strong>{{ number_format((float) $selectedOrder->phi_van_chuyen, 0, ',', '.') }}đ</strong></div>
                        <div class="grand-total"><span>Tổng tiền</span><strong>{{ number_format((float) $selectedOrder->tong_tien, 0, ',', '.') }}đ</strong></div>
                    </div>
                </section>

                <div class="detail-lock-note">
                    🔒 Thông tin đơn hàng ở chế độ chỉ đọc. Admin không thể sửa sản phẩm, số lượng, địa chỉ, giá hoặc phương thức thanh toán.
                </div>

                <div class="detail-actions">
                    @if($detailStatus === 'pending_confirmation' && $isSelectedCod)
                        <form method="POST" action="{{ route('admin.don-hang.confirm-cod', $selectedOrder->order_id) }}">
                            @csrf
                            <input type="hidden" name="expected_status" value="pending_confirmation">
                            <button class="detail-primary green" type="submit">Xác nhận đơn</button>
                        </form>
                    @elseif($detailStatus === 'pending_confirmation' && !$isSelectedCod && $detailPState !== 'paid')
                        <span class="text-muted">{{ $detailPayment?->normalizedMethod() === 'BANK_TRANSFER' ? 'Đơn chuyển khoản cũ: cần đối soát riêng' : 'Chờ xác nhận từ cổng thanh toán' }}</span>
                    @elseif($detailStatus === 'preparing')
                        <form method="POST" action="{{ route('admin.don-hang.shipping', $selectedOrder->order_id) }}">
                            @csrf
                            <input type="hidden" name="expected_status" value="preparing">
                            <button class="detail-primary blue" type="submit">Chuyển sang Đang giao</button>
                        </form>
                    @elseif($detailStatus === 'shipping')
                        <form method="POST" action="{{ route('admin.don-hang.delivered', $selectedOrder->order_id) }}">
                            @csrf
                            <input type="hidden" name="expected_status" value="shipping">
                            <button class="detail-primary blue" type="submit">Xác nhận Đã giao</button>
                        </form>
                    @elseif($detailStatus === 'delivered')
                        <p class="detail-note">Đã giao. Chờ khách hàng xác nhận đã nhận hàng để tự động hoàn thành đơn.</p>
                    @endif

                    <a href="{{ route('admin.don-hang.index', request()->except('selected')) }}" class="detail-close">Đóng</a>
                </div>
            @else
                <div class="empty-detail">
                    <span>▣</span>
                    <h3>Chưa chọn đơn hàng</h3>
                    <p>Chọn “Xem” để hiển thị chi tiết đơn hàng.</p>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection
