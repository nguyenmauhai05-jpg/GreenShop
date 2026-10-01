<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách đơn hàng - GreenShop</title>
    <style>
        *{box-sizing:border-box}body{font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;color:#1c2b20;margin:28px}
        h1{margin:0 0 6px;color:#167f36}.meta{color:#6d786f;margin-bottom:22px}
        table{width:100%;border-collapse:collapse;font-size:12px}
        th,td{border:1px solid #dce6de;padding:8px;text-align:left;vertical-align:top}
        th{background:#eef7f0}.money{font-weight:700;color:#16863a}
        .actions{margin:0 0 20px}.actions button{padding:9px 14px;border:1px solid #16863a;background:#16863a;color:#fff;border-radius:6px}
        @media print{.actions{display:none}body{margin:0}}
    </style>
</head>
<body>
<div class="actions"><button onclick="window.print()">In / Lưu thành PDF</button></div>
<h1>GreenShop - Danh sách đơn hàng</h1>
<div class="meta">Xuất lúc {{ now()->format('d/m/Y H:i') }}</div>
<table>
    <thead>
    <tr>
        <th>Mã đơn</th><th>Khách hàng</th><th>SĐT</th><th>Ngày đặt</th>
        <th>Phương thức</th><th>Tổng tiền</th><th>Thanh toán</th><th>Đơn hàng</th>
    </tr>
    </thead>
    <tbody>
    @forelse($orders as $order)
        @php
            $method = $order->thanhToan?->methodLabel() ?? 'Chưa xác định';
            $payRaw = \Illuminate\Support\Str::of((string) optional($order->thanhToan)->trang_thai)->trim()->lower()->ascii()->replace([' ','-'],'_')->value();
            $pay = in_array($payRaw,['paid','da_thanh_toan'],true) ? 'Đã thanh toán' : (in_array($payRaw,['cancelled','canceled','da_huy'],true) ? 'Đã hủy' : 'Chờ xác nhận thanh toán');
        @endphp
        <tr>
            <td>{{ $order->orderCode() }}</td>
            <td>{{ $order->nguoiDung?->ho_ten ?? '—' }}</td>
            <td>{{ $order->nguoiDung?->so_dien_thoai ?? '—' }}</td>
            <td>{{ $order->ngay_dat?->format('d/m/Y H:i') ?? '—' }}</td>
            <td>{{ $method }}</td>
            <td class="money">{{ number_format((float)$order->tong_tien,0,',','.') }}đ</td>
            <td>{{ $pay }}</td>
            <td>{{ $order->statusLabel() }}</td>
        </tr>
    @empty
        <tr><td colspan="8">Không có dữ liệu đơn hàng.</td></tr>
    @endforelse
    </tbody>
</table>
<script>window.addEventListener('load',()=>setTimeout(()=>window.print(),250));</script>
</body>
</html>
