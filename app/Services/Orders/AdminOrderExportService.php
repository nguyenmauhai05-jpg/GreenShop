<?php

namespace App\Services\Orders;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminOrderExportService
{
    public function __construct(private AdminOrderQueryService $query) {}

    public function csv(Request $request): StreamedResponse
    {
        [, $orders] = $this->query->exportOrders($request);
        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Mã đơn','Khách hàng','Số điện thoại','Ngày đặt','Phương thức thanh toán','Tổng tiền','Trạng thái thanh toán','Trạng thái đơn hàng']);
            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->orderCode(), optional($order->nguoiDung)->ho_ten, optional($order->nguoiDung)->so_dien_thoai,
                    optional($order->ngay_dat)->format('d/m/Y H:i'),
                    $this->query->paymentMethodLabel(optional($order->thanhToan)->phuong_thuc),
                    (float) $order->tong_tien,
                    $this->query->paymentStatusLabel(optional($order->thanhToan)->trang_thai),
                    $order->statusLabel(),
                ]);
            }
            fclose($out);
        }, 'greenshop-don-hang-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
