<?php

namespace App\Services\Orders;

use App\Models\DonHang;
use App\Models\GiaoDichThanhToan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOrderService
{
    public function pageData(Request $request, $user, ?DonHang $forcedSelectedOrder = null): array
    {
        $search = trim((string) $request->query('q', ''));
        $filter = trim((string) $request->query('status', 'all'));
        if (mb_strlen($search) > 100) {
            $search = mb_substr($search, 0, 100);
        }

        $allowed = ['all', 'pending_payment', 'pending_confirmation', 'preparing', 'shipping', 'delivered', 'completed', 'cancelled'];
        if (!in_array($filter, $allowed, true)) {
            $filter = 'all';
        }

        $query = DonHang::query()
            ->where('user_id', $user->user_id)
            ->with(['thanhToan'])
            ->withCount('chiTietDonHangs')
            ->withSum('chiTietDonHangs as total_quantity', 'so_luong')
            ->orderByDesc('ngay_dat')
            ->orderByDesc('order_id');

        if ($filter !== 'all') {
            $query->whereIn('trang_thai', DonHang::databaseStatusAliases($filter));
        }

        if ($search !== '') {
            $digits = preg_replace('/\D+/', '', $search);
            $candidateId = null;
            if (preg_match('/(\d{1,10})\s*$/', $search, $matches)) {
                $candidateId = (int) ltrim($matches[1], '0');
            } elseif ($digits !== '') {
                $candidateId = (int) $digits;
            }

            $query->where(function ($subQuery) use ($search, $candidateId) {
                $normalizedCode = ltrim(strtoupper($search), '#');
                $subQuery->where('order_code', 'like', '%' . $normalizedCode . '%');
                if ($candidateId) {
                    $subQuery->orWhere('order_id', $candidateId);
                }
            });
        }

        $orders = $query->paginate(6)->withQueryString();
        $selectedOrder = $forcedSelectedOrder;

        if (!$selectedOrder && $request->filled('selected')) {
            $selectedOrder = DonHang::where('user_id', $user->user_id)
                ->where('order_id', (int) $request->query('selected'))
                ->first();
        }
        $selectedOrder ??= $orders->first();

        if ($selectedOrder) {
            $this->ensureOwner($selectedOrder, (int) $user->user_id);
            $selectedOrder->load(['diaChi', 'thanhToan', 'chiTietDonHangs.cayCanh']);
        }

        $retryTransaction = null;
        if ($selectedOrder && $selectedOrder->normalizedStatus() === 'pending_payment' && $selectedOrder->thanhToan) {
            $retryTransaction = GiaoDichThanhToan::where('payment_id', $selectedOrder->thanhToan->payment_id)
                ->where('status', 'pending')
                ->orderByDesc('transaction_id')
                ->first();
        }

        return [
            'orders' => $orders,
            'selectedOrder' => $selectedOrder,
            'search' => $search,
            'filter' => $filter,
            'counts' => $this->statusCounts((int) $user->user_id),
            'retryTransaction' => $retryTransaction,
        ];
    }

    public function cancel(Request $request, DonHang $order, int $userId): string
    {
        return DB::transaction(function () use ($request, $order, $userId) {
            $locked = DonHang::with(['chiTietDonHangs', 'thanhToan'])
                ->where('order_id', $order->order_id)->lockForUpdate()->first();
            if (!$locked) {
                abort(404, 'Không tìm thấy đơn hàng.');
            }
            $this->ensureOwner($locked, $userId);

            $current = $locked->normalizedStatus();
            $expected = trim((string) $request->input('expected_status'));
            if ($expected !== '' && $expected !== $current) {
                return 'Trạng thái đơn hàng đã thay đổi. Vui lòng tải lại và kiểm tra.';
            }
            if ($current === 'cancelled') return 'Đơn hàng này đã được hủy trước đó.';
            if ($current === 'shipping') return 'Đơn hàng đang được giao và không thể hủy.';
            if ($current === 'delivered') return 'Đơn hàng đã được giao và không thể hủy.';
            if ($current === 'completed') return 'Đơn hàng đã hoàn thành và không thể hủy.';
            if (!$locked->isCancelable()) return 'Trạng thái đơn hàng đã thay đổi. Vui lòng tải lại và kiểm tra.';

            foreach ($locked->chiTietDonHangs as $detail) {
                if ($detail->plant_id && $detail->so_luong > 0) {
                    DB::table('cay_canh')->where('plant_id', $detail->plant_id)
                        ->increment('so_luong', (int) $detail->so_luong);
                }
            }

            $locked->trang_thai = 'cancelled';
            $locked->save();
            $payment = $locked->thanhToan;
            if ($payment) {
                $status = strtolower(trim((string) $payment->trang_thai));
                if (in_array($status, ['paid', 'da_thanh_toan', 'đã thanh toán'], true)) {
                    $payment->trang_thai = 'refund_pending';
                    $payment->save();
                    return 'Yêu cầu hủy đơn đã được ghi nhận. Khoản thanh toán sẽ được xử lý theo chính sách hoàn tiền.';
                }
                if (!in_array($status, ['refunded', 'hoan_tien', 'đã hoàn tiền'], true)) {
                    $payment->trang_thai = 'cancelled';
                    $payment->save();
                }

                // Không để giao dịch online pending tiếp tục được dùng sau khi đơn đã hủy.
                GiaoDichThanhToan::where('payment_id', $payment->payment_id)
                    ->where('status', 'pending')
                    ->update(['status' => 'cancelled']);
            }
            return 'Đơn hàng đã được hủy thành công.';
        }, 3);
    }

    public function confirmReceipt(DonHang $order, int $userId, string $receiptStatus): array
    {
        return DB::transaction(function () use ($order, $userId, $receiptStatus) {
            $locked = DonHang::where('order_id', $order->order_id)->lockForUpdate()->first();
            if (!$locked) abort(404, 'Không tìm thấy đơn hàng.');
            $this->ensureOwner($locked, $userId);
            $current = $locked->normalizedStatus();
            if ($current === 'completed') {
                return ['type' => 'success', 'message' => 'Đơn hàng này đã được xác nhận nhận hàng và hoàn thành trước đó.'];
            }
            if ($current !== 'delivered') {
                return ['type' => 'warning', 'message' => 'Bạn chỉ có thể xác nhận nhận hàng khi đơn đang ở trạng thái Đã giao.'];
            }
            if ($receiptStatus === 'received') {
                $locked->trang_thai = 'completed';
                $locked->save();
                return ['type' => 'success', 'message' => 'Cảm ơn bạn đã xác nhận đã nhận hàng. Đơn hàng đã hoàn thành.'];
            }
            return ['type' => 'warning', 'message' => 'Đã ghi nhận bạn chưa nhận được hàng. Đơn vẫn ở trạng thái Đã giao để GreenShop tiếp tục xử lý.'];
        }, 3);
    }

    public function ensureOwner(DonHang $order, int $userId): void
    {
        if ((int) $order->user_id !== $userId) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }
    }

    private function statusCounts(int $userId): array
    {
        $orders = DonHang::where('user_id', $userId)->get(['trang_thai']);
        $counts = ['all' => $orders->count(), 'pending_payment' => 0, 'pending_confirmation' => 0, 'preparing' => 0, 'shipping' => 0, 'delivered' => 0, 'completed' => 0, 'cancelled' => 0];
        foreach ($orders as $order) {
            $key = $order->normalizedStatus();
            if (array_key_exists($key, $counts)) $counts[$key]++;
        }
        return $counts;
    }
}
