<?php

namespace App\Http\Controllers;

use App\Services\Cart\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GioHangController extends Controller
{
    /** Xem giỏ hàng. */
    public function index(CartService $cartService)
    {
        try {
            $data = $cartService->viewData((int) Auth::id());

            return view('gio_hang.index', $data);
        } catch (\Throwable $e) {
            Log::error('Không thể tải giỏ hàng.', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error', 'Không thể tải giỏ hàng. Vui lòng thử lại sau.');
        }
    }

    /** Cập nhật số lượng một dòng giỏ hàng. */
    public function capNhat(Request $request, $cartDetailId, CartService $cartService)
    {
        try {
            $userId = (int) Auth::id();
            $line = $cartService->ownedLineForUpdate($userId, $cartDetailId);

            if (!$line) {
                return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
            }

            if ($line->trang_thai !== 'Đang bán') {
                return back()->with(
                    'error',
                    'Sản phẩm này hiện không khả dụng. Vui lòng xóa sản phẩm khỏi giỏ hàng.'
                );
            }

            if ((int) $line->so_luong_ton <= 0) {
                return back()->with(
                    'error',
                    'Sản phẩm hiện đã hết hàng. Vui lòng xóa sản phẩm khỏi giỏ hàng.'
                );
            }

            $action = $request->input('action');
            if ($action === 'increase') {
                $newQuantity = (int) $line->so_luong + 1;
            } elseif ($action === 'decrease') {
                $newQuantity = (int) $line->so_luong - 1;
            } else {
                $request->validate([
                    'so_luong' => ['required', 'integer'],
                ], [
                    'so_luong.required' => 'Vui lòng nhập số lượng.',
                    'so_luong.integer' => 'Số lượng phải là số nguyên.',
                ]);

                $newQuantity = (int) $request->so_luong;
            }

            if ($newQuantity < 0) {
                return back()->with('error', 'Số lượng phải lớn hơn 0.');
            }

            if ($newQuantity === 0) {
                return back()->with(
                    'warning',
                    'Số lượng phải lớn hơn 0. Bạn có muốn xóa sản phẩm khỏi giỏ hàng?'
                );
            }

            if ($newQuantity > (int) $line->so_luong_ton) {
                return back()->with('error', 'Số lượng vượt quá tồn kho hiện có.');
            }

            $cartService->updateQuantity($cartDetailId, $newQuantity);

            return back()->with('success', 'Đã cập nhật số lượng sản phẩm trong giỏ hàng.');
        } catch (\Throwable $e) {
            Log::error('Cập nhật giỏ hàng thất bại.', [
                'user_id' => Auth::id(),
                'cart_detail_id' => $cartDetailId,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Không thể cập nhật giỏ hàng. Vui lòng thử lại sau.');
        }
    }

    /** Xóa một dòng khỏi giỏ hàng. */
    public function xoa($cartDetailId, CartService $cartService)
    {
        try {
            $line = $cartService->ownedLineForDelete((int) Auth::id(), $cartDetailId);

            if (!$line) {
                return back()->with('error', 'Sản phẩm không còn tồn tại trong giỏ hàng.');
            }

            $cartService->deleteLine($cartDetailId);

            return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        } catch (\Throwable $e) {
            Log::error('Xóa sản phẩm khỏi giỏ hàng thất bại.', [
                'user_id' => Auth::id(),
                'cart_detail_id' => $cartDetailId,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Không thể xóa sản phẩm khỏi giỏ hàng. Vui lòng thử lại sau.');
        }
    }

    /** Thêm sản phẩm vào giỏ hàng. */
    public function them(Request $request, CartService $cartService)
    {
        try {
            $request->validate([
                'plant_id' => ['required', 'integer'],
                'so_luong' => ['required', 'integer', 'min:1'],
            ], [
                'plant_id.required' => 'Không xác định được sản phẩm.',
                'so_luong.required' => 'Vui lòng nhập số lượng.',
                'so_luong.integer' => 'Số lượng phải là số nguyên.',
                'so_luong.min' => 'Số lượng phải lớn hơn 0.',
            ]);

            $plant = $cartService->plant((int) $request->plant_id);

            if (!$plant) {
                return $this->addCartResponse(
                    $request,
                    false,
                    'Sản phẩm không tồn tại hoặc đã bị xóa.',
                    404
                );
            }

            if ($plant->trang_thai !== 'Đang bán') {
                return $this->addCartResponse(
                    $request,
                    false,
                    'Sản phẩm này hiện không khả dụng.',
                    409
                );
            }

            if ((int) $plant->so_luong <= 0) {
                return $this->addCartResponse(
                    $request,
                    false,
                    'Sản phẩm hiện đã hết hàng và không thể thêm vào giỏ.',
                    409
                );
            }

            $quantity = (int) $request->so_luong;
            if ($quantity > (int) $plant->so_luong) {
                return $this->addCartResponse(
                    $request,
                    false,
                    'Số lượng vượt quá tồn kho hiện có.',
                    422
                );
            }

            $userId = (int) Auth::id();
            $cartService->add($userId, $plant, $quantity);

            return $this->addCartResponse(
                $request,
                true,
                'Đã thêm sản phẩm vào giỏ hàng.',
                200,
                $cartService->countForUser($userId)
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Để Laravel tự trả lỗi validation dạng JSON cho AJAX,
            // hoặc redirect kèm errors cho form truyền thống.
            throw $e;
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'SO_LUONG_VUOT_TON') {
                return $this->addCartResponse(
                    $request,
                    false,
                    'Số lượng vượt quá tồn kho hiện có.',
                    422
                );
            }

            throw $e;
        } catch (\Throwable $e) {
            Log::error('Thêm sản phẩm vào giỏ hàng thất bại.', [
                'user_id' => Auth::id(),
                'plant_id' => $request->plant_id ?? null,
                'message' => $e->getMessage(),
            ]);

            return $this->addCartResponse(
                $request,
                false,
                'Đã xảy ra lỗi. Vui lòng thử lại sau.',
                500
            );
        }
    }

    /** Mua ngay một sản phẩm: thêm 1 sản phẩm vào giỏ và chuyển thẳng tới checkout. */
    public function muaNgay(Request $request, CartService $cartService)
    {
        $request->validate([
            'plant_id' => ['required', 'integer'],
            'so_luong' => ['required', 'integer', 'min:1'],
        ]);

        $plant = $cartService->plant((int) $request->plant_id);

        if (!$plant || $plant->trang_thai !== 'Đang bán') {
            return back()->with('error', 'Sản phẩm này hiện không khả dụng.');
        }

        $quantity = (int) $request->so_luong;
        if ((int) $plant->so_luong <= 0 || $quantity > (int) $plant->so_luong) {
            return back()->with('error', 'Sản phẩm hiện đã hết hàng hoặc số lượng vượt quá tồn kho.');
        }

        try {
            session(['greenshop_buy_now_' . Auth::id() => ['plant_id' => (int) $plant->plant_id, 'quantity' => $quantity]]);
            session()->forget('greenshop_checkout_order_code_' . Auth::id());
            return redirect()->route('thanh-toan.index', ['buy_now' => 1]);
        } catch (\Throwable $e) {
            Log::error('Mua ngay thất bại.', [
                'user_id' => Auth::id(),
                'plant_id' => $request->plant_id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Không thể mua ngay sản phẩm. Vui lòng thử lại sau.');
        }
    }

    /**
     * Trả JSON cho thao tác AJAX; giữ redirect cũ nếu submit form bình thường.
     */
    private function addCartResponse(
        Request $request,
        bool $success,
        string $message,
        int $status = 200,
        ?int $cartCount = null
    ) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
                'cart_count' => $cartCount,
            ], $status);
        }

        return back()->with($success ? 'success' : 'error', $message);
    }

}
