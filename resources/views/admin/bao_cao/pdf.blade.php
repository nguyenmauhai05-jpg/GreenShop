<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">

    <title>Báo cáo thống kê GreenShop</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #1f2937;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 22px;
            color: #15803d;
        }

        .header p {
            margin: 0;
            color: #6b7280;
        }

        .summary {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }

        .summary td {
            width: 33.33%;
            padding: 15px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
        }

        .summary-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 17px;
            font-weight: bold;
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
        }

        table.report th {
            background: #15803d;
            color: white;
            padding: 9px 7px;
            border: 1px solid #d1d5db;
            text-align: left;
        }

        table.report td {
            padding: 8px 7px;
            border: 1px solid #d1d5db;
        }

        table.report tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #6b7280;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>

<body>

    <div class="header">

        <h1>BÁO CÁO THỐNG KÊ GREENSHOP</h1>

        <p>
            Từ ngày
            <strong>{{ $tuNgay->format('d/m/Y') }}</strong>
            đến
            <strong>{{ $denNgay->format('d/m/Y') }}</strong>
        </p>

    </div>


    <table class="summary">
        <tr>

            <td>
                <div class="summary-label">
                    Tổng doanh thu
                </div>

                <div class="summary-value">
                    {{ number_format($tongDoanhThu, 0, ',', '.') }} đ
                </div>
            </td>

            <td>
                <div class="summary-label">
                    Tổng đơn hàng
                </div>

                <div class="summary-value">
                    {{ number_format($tongDonHang) }}
                </div>
            </td>

            <td>
                <div class="summary-label">
                    Sản phẩm đã bán
                </div>

                <div class="summary-value">
                    {{ number_format($tongSanPham) }}
                </div>
            </td>

        </tr>
    </table>


    <div class="section-title">
        Báo cáo chi tiết theo sản phẩm
    </div>


    <table class="report">

        <thead>
            <tr>
                <th style="width: 5%">STT</th>
                <th style="width: 10%">Mã cây</th>
                <th style="width: 25%">Tên cây</th>
                <th style="width: 20%">Danh mục</th>
                <th style="width: 10%">Đã bán</th>
                <th style="width: 15%">Doanh thu</th>
                <th style="width: 15%">Tỷ lệ</th>
            </tr>
        </thead>

        <tbody>

            @forelse($sanPhams as $index => $sanPham)

                @php
                    $tyLe = $tongDoanhThu > 0
                        ? round(
                            ((float) $sanPham->doanh_thu / $tongDoanhThu) * 100,
                            1
                        )
                        : 0;
                @endphp

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        CC{{ str_pad(
                            $sanPham->plant_id,
                            3,
                            '0',
                            STR_PAD_LEFT
                        ) }}
                    </td>

                    <td>
                        {{ $sanPham->ten_cay }}
                    </td>

                    <td>
                        {{ $sanPham->ten_danh_muc ?? 'Chưa phân loại' }}
                    </td>

                    <td class="center">
                        {{ number_format($sanPham->so_luong_da_ban) }}
                    </td>

                    <td class="right">
                        {{ number_format(
                            $sanPham->doanh_thu,
                            0,
                            ',',
                            '.'
                        ) }} đ
                    </td>

                    <td class="center">
                        {{ $tyLe }}%
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" class="empty">
                        Không có dữ liệu báo cáo phù hợp.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>


    <div class="footer">
        GreenShop - Xuất báo cáo lúc
        {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>