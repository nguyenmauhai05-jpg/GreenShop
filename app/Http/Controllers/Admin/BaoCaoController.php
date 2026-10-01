<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Reports\ReportDataService;
use App\Services\Reports\ReportExportService;
use App\Services\Reports\ReportFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BaoCaoController extends Controller
{
    public function __construct(
        private readonly ReportFilterService $filterService,
        private readonly ReportDataService $reportService,
        private readonly ReportExportService $exportService,
    ) {
    }

    public function index(Request $request)
    {
        try {
            $filters = $this->filterService->getFilters($request);

            return view(
                'admin.bao_cao.index',
                $this->reportService->getIndexData($request, $filters)
            );
        } catch (\Throwable $e) {
            $this->logError('Không thể tải báo cáo thống kê Admin.', $e);

            return view('admin.bao_cao.index', [
                'loiHeThong' => true,
                'khongCoDuLieu' => true,
                'danhMucs' => collect(),
                'tongDoanhThu' => 0,
                'tongDonHang' => 0,
                'tongSanPhamDaBan' => 0,
                'tongKhachHangMoi' => 0,
                'duLieuBieuDo' => collect(),
                'doanhThuDanhMuc' => collect(),
                'sanPhamBaoCao' => collect(),
                'thongKeTrangThai' => collect(),
                'tuNgay' => now()->subMonth()->startOfDay(),
                'denNgay' => now()->endOfDay(),
                'nhomTheo' => 'ngay',
                'loaiBaoCao' => 'doanh-thu',
                'categoryId' => null,
                'orderStatus' => null,
            ]);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $filters = $this->filterService->getFilters($request);
            $summary = $this->reportService->getExportSummary($filters);
            $file = $this->exportService->createExcel($summary);

            return response()
                ->download($file['path'], $file['fileName'])
                ->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            $this->logError('Xuất Excel báo cáo thất bại.', $e);

            return back()->with(
                'error',
                'Không thể xuất Excel. Vui lòng thử lại.'
            );
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $filters = $this->filterService->getFilters($request);
            $summary = $this->reportService->getExportSummary($filters);
            $pdf = $this->exportService->createPdf($summary);

            return $pdf->download(
                'bao-cao-greenshop-'
                . now()->format('d-m-Y-His')
                . '.pdf'
            );
        } catch (\Throwable $e) {
            $this->logError('Xuất PDF báo cáo thất bại.', $e);

            return back()->with(
                'error',
                'Không thể xuất PDF. Vui lòng thử lại.'
            );
        }
    }

    private function logError(string $message, \Throwable $exception): void
    {
        Log::error($message, [
            'user_id' => Auth::id(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }
}
