<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Services\Orders\AdminOrderExportService;
use App\Services\Orders\AdminOrderQueryService;
use App\Services\Orders\AdminOrderWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DonHangController extends Controller
{
    public function index(Request $request, AdminOrderQueryService $orders)
    {
        try {
            return view('admin.don_hang.index', $orders->pageData($request));
        } catch (\Throwable $e) {
            Log::error('Admin tải quản lý đơn hàng thất bại', ['message' => $e->getMessage(), 'admin_id' => Auth::id()]);
            return view('admin.don_hang.index', [
                'orders' => collect(), 'counts' => $orders->emptyCounts(), 'selectedOrder' => null,
                'filters' => $orders->filters($request), 'loadError' => 'Không thể tải danh sách đơn hàng. Vui lòng thử lại.',
            ]);
        }
    }

    public function confirmCod(Request $request, DonHang $donHang, AdminOrderWorkflowService $workflow) { return $this->run($donHang, fn () => $workflow->confirmCod($request, $donHang)); }
    public function markPreparing(Request $request, DonHang $donHang, AdminOrderWorkflowService $workflow) { return $this->run($donHang, fn () => $workflow->markPreparing($request, $donHang)); }
    public function markShipping(Request $request, DonHang $donHang, AdminOrderWorkflowService $workflow) { return $this->run($donHang, fn () => $workflow->markShipping($request, $donHang)); }
    public function markDelivered(Request $request, DonHang $donHang, AdminOrderWorkflowService $workflow) { return $this->run($donHang, fn () => $workflow->markDelivered($request, $donHang)); }

    public function exportExcel(Request $request, AdminOrderExportService $export): StreamedResponse
    {
        return $export->csv($request);
    }

    public function exportPdf(Request $request, AdminOrderQueryService $orders)
    {
        [$filters, $list] = $orders->exportOrders($request);
        return view('admin.don_hang.print', ['orders' => $list, 'filters' => $filters]);
    }

    private function run(DonHang $order, callable $operation)
    {
        try {
            return back()->with('success', $operation());
        } catch (\RuntimeException $e) {
            return back()->with('warning', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Admin cập nhật đơn hàng thất bại', ['order_id' => $order->order_id, 'admin_id' => Auth::id(), 'message' => $e->getMessage()]);
            return back()->with('error', 'Không thể cập nhật đơn hàng. Vui lòng thử lại.');
        }
    }
}
