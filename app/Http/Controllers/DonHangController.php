<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Services\Orders\CustomerOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DonHangController extends Controller
{
    public function index(Request $request, CustomerOrderService $orders)
    {
        return $this->render($request, $orders);
    }

    public function show(Request $request, DonHang $donHang, CustomerOrderService $orders)
    {
        $orders->ensureOwner($donHang, (int) Auth::id());
        return $this->render($request, $orders, $donHang);
    }

    public function cancel(Request $request, DonHang $donHang, CustomerOrderService $orders)
    {
        $orders->ensureOwner($donHang, (int) Auth::id());
        $request->validate(['expected_status' => ['nullable', 'string', 'max:50']]);
        try {
            $message = $orders->cancel($request, $donHang, (int) Auth::id());
            $success = str_contains($message, 'thành công') || str_contains($message, 'đã được ghi nhận');
            return redirect()->route('don-hang.show', $donHang->order_id)
                ->with($success ? 'success' : 'warning', $message);
        } catch (\Throwable $e) {
            Log::error('Huy don hang GreenShop that bai', ['order_id' => $donHang->order_id, 'user_id' => Auth::id(), 'message' => $e->getMessage()]);
            return redirect()->route('don-hang.show', $donHang->order_id)
                ->with('error', 'Không thể hủy đơn hàng. Vui lòng thử lại sau.');
        }
    }

    public function confirmReceipt(Request $request, DonHang $donHang, CustomerOrderService $orders)
    {
        $orders->ensureOwner($donHang, (int) Auth::id());
        $validated = $request->validate(['receipt_status' => ['required', 'in:received,not_received']]);
        try {
            $result = $orders->confirmReceipt($donHang, (int) Auth::id(), $validated['receipt_status']);
            return redirect()->route('don-hang.show', $donHang->order_id)->with($result['type'], $result['message']);
        } catch (\Throwable $e) {
            Log::error('Xac nhan nhan hang GreenShop that bai', ['order_id' => $donHang->order_id, 'user_id' => Auth::id(), 'message' => $e->getMessage()]);
            return redirect()->route('don-hang.show', $donHang->order_id)
                ->with('error', 'Không thể cập nhật trạng thái nhận hàng. Vui lòng thử lại sau.');
        }
    }

    private function render(Request $request, CustomerOrderService $orders, ?DonHang $selected = null)
    {
        $user = Auth::user();
        try {
            return view('don_hang.index', $orders->pageData($request, $user, $selected));
        } catch (\Throwable $e) {
            Log::error('Tai danh sach don hang GreenShop that bai', ['user_id' => $user->user_id, 'message' => $e->getMessage()]);
            return view('don_hang.index', [
                'orders' => collect(), 'selectedOrder' => null,
                'search' => mb_substr(trim((string) $request->query('q', '')), 0, 100),
                'filter' => trim((string) $request->query('status', 'all')),
                'counts' => [], 'retryTransaction' => null,
                'loadError' => 'Không thể tải danh sách đơn hàng. Vui lòng thử lại sau.',
            ]);
        }
    }
}
