<?php

namespace App\Services\Orders;

use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderWorkflowService
{
    public function __construct(private AdminOrderQueryService $query) {}

    public function confirmCod(Request $request, DonHang $order): string
    {
        return $this->transition($request, $order, function (DonHang $locked) {
            $this->assertStatus($locked, 'pending_confirmation');
            $payment = $locked->thanhToan;
            if (!$payment || !$this->query->isCod($payment->phuong_thuc)) throw new \RuntimeException('Đơn hàng không sử dụng phương thức COD.');
            $locked->trang_thai = 'preparing'; $locked->save();
            return 'Đã xác nhận đơn COD. Đơn hàng chuyển sang Chờ vận chuyển.';
        });
    }


    public function markPreparing(Request $r, DonHang $o): string { return $this->statusTransition($r,$o,'pending_confirmation','preparing','Đơn hàng đã chuyển sang Chờ vận chuyển.'); }
    public function markShipping(Request $r, DonHang $o): string { return $this->statusTransition($r,$o,'preparing','shipping','Đơn hàng đã chuyển sang Đang giao.'); }
    public function markDelivered(Request $r, DonHang $o): string { return $this->statusTransition($r,$o,'shipping','delivered','Đơn hàng đã chuyển sang Đã giao.'); }

    private function statusTransition(Request $request, DonHang $order, string $expected, string $next, string $message): string
    {
        return $this->transition($request, $order, function (DonHang $locked) use ($expected,$next,$message) {
            $this->assertStatus($locked, $expected);
            // Never let unpaid online orders bypass the verified gateway callback.
            if ($expected === 'pending_confirmation' && $next === 'preparing'
                && !$this->query->isCod($locked->thanhToan?->phuong_thuc)
                && $this->query->paymentStatus($locked->thanhToan?->trang_thai) !== 'paid') {
                throw new \RuntimeException('Đơn thanh toán trực tuyến chưa được cổng thanh toán xác nhận.');
            }
            $locked->trang_thai = $next;
            $locked->save();
            return $message;
        });
    }

    private function transition(Request $request, DonHang $order, callable $callback): string
    {
        return DB::transaction(function () use ($request,$order,$callback) {
            $locked = DonHang::with('thanhToan')->lockForUpdate()->findOrFail($order->order_id);
            $expected = trim((string) $request->input('expected_status', ''));
            if ($expected !== '' && $locked->normalizedStatus() !== $expected) throw new \RuntimeException('Trạng thái đơn hàng đã thay đổi. Hệ thống đã tải trạng thái mới và không thực hiện xác nhận sai trạng thái.');
            if ($locked->normalizedStatus() === 'cancelled') throw new \RuntimeException('Đơn đã hủy, không thể tiếp tục xử lý.');
            return $callback($locked);
        }, 3);
    }

    private function assertStatus(DonHang $order, string $expected): void
    {
        if ($order->normalizedStatus() !== $expected) throw new \RuntimeException('Trạng thái đơn hàng đã thay đổi. Vui lòng kiểm tra trạng thái mới trước khi thao tác.');
    }
}
