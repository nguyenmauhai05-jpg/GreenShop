<?php

namespace App\Services\Reports;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportFilterService
{
    /**
     * @return array{tuNgay:Carbon,denNgay:Carbon,nhomTheo:string,loaiBaoCao:string,categoryId:?int,orderStatus:?string}
     */
    public function getFilters(Request $request): array
    {
        $tuNgay = $request->filled('tu_ngay')
            ? $this->parseVietnameseDate((string) $request->tu_ngay)
            : now()->subMonth()->startOfDay();

        $denNgay = $request->filled('den_ngay')
            ? $this->parseVietnameseDate((string) $request->den_ngay, true)
            : now()->endOfDay();

        $today = now()->endOfDay();

        if ($tuNgay->gt($today)) {
            throw ValidationException::withMessages([
                'tu_ngay' => 'Từ ngày không được lớn hơn ngày hiện tại.',
            ]);
        }

        if ($denNgay->gt($today)) {
            throw ValidationException::withMessages([
                'den_ngay' => 'Đến ngày không được lớn hơn ngày hiện tại.',
            ]);
        }

        if ($tuNgay->gt($denNgay)) {
            throw ValidationException::withMessages([
                'tu_ngay' => 'Từ ngày không được lớn hơn Đến ngày.',
                'den_ngay' => 'Đến ngày phải bằng hoặc sau Từ ngày.',
            ]);
        }

        $nhomTheo = (string) $request->input('nhom_theo', 'ngay');
        if (!in_array($nhomTheo, ['ngay', 'thang', 'nam'], true)) {
            $nhomTheo = 'ngay';
        }

        return [
            'tuNgay' => $tuNgay,
            'denNgay' => $denNgay,
            'nhomTheo' => $nhomTheo,
            // UI hiện không còn bộ lọc loại báo cáo.
            'loaiBaoCao' => 'doanh-thu',
            'categoryId' => $request->filled('category_id')
                ? (int) $request->category_id
                : null,
            'orderStatus' => $request->filled('trang_thai_don')
                ? (string) $request->trang_thai_don
                : null,
        ];
    }

    private function parseVietnameseDate(string $value, bool $endOfDay = false): Carbon
    {
        $value = trim($value);

        foreach (['d/m/Y', 'Y-m-d'] as $format) {
            try {
                $date = Carbon::createFromFormat('!' . $format, $value);

                if ($date && $date->format($format) === $value) {
                    return $endOfDay
                        ? $date->endOfDay()
                        : $date->startOfDay();
                }
            } catch (\Throwable) {
                // Thử định dạng tiếp theo.
            }
        }

        throw ValidationException::withMessages([
            $endOfDay ? 'den_ngay' : 'tu_ngay'
                => 'Ngày không hợp lệ. Vui lòng nhập theo định dạng DD/MM/YYYY.',
        ]);
    }
}
