<?php

namespace App\Http\Controllers;

use App\Models\DiaChi;
use App\Models\DonHang;
use App\Services\MapShippingService;
use App\Services\Checkout\CheckoutIntegrityService;
use App\Services\Checkout\CheckoutPageService;
use App\Services\Checkout\OrderCreationService;
use App\Services\Checkout\ShippingPricingService;
use App\Services\Payments\OnlinePaymentService;
use App\Services\Payments\PaymentLifecycleService;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ThanhToanController extends Controller
{
    /** UC16/17 - Màn hình checkout. */
    public function index(CheckoutPageService $checkoutPage)
    {
        try {
            return view('thanh_toan.index', $checkoutPage->dataFor(Auth::user()));
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'EMPTY_CART') {
                return redirect()->route('don-hang.index')
                    ->with('warning', 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi đặt hàng.');
            }
            throw $e;
        }
    }

    /** Dữ liệu giỏ hàng sau khi khách thêm cây ngay tại trang thanh toán. */
    public function refreshCart(CheckoutPageService $checkoutPage)
    {
        try {
            $data = $checkoutPage->dataFor(Auth::user());
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'EMPTY_CART') {
                return response()->json(['message' => 'Giỏ hàng đang trống.'], 409);
            }
            throw $e;
        }

        $toPayload = static function ($product) {
            $photo = $product->anh_dai_dien;
            $url = $photo ? ((str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://'))
                ? $photo : asset(ltrim($photo, '/'))) : null;
            return [
                'plant_id' => (int) $product->plant_id,
                'category_id' => (int) $product->category_id,
                'name' => $product->ten_cay,
                'price' => (float) $product->gia,
                'image' => $url,
                'url' => route('chi-tiet-cay', $product->plant_id),
            ];
        };

        return response()->json([
            'products_html' => view('thanh_toan.components.products', $data)->render(),
            'subtotal' => (float) $data['subtotal'],
            'cart_signature' => $data['cartSignature'],
            'cart_count' => (int) $data['cart']->chiTietGioHangs->sum('so_luong'),
            'cart_category_ids' => $data['cartCategoryIds'],
            'suggested_products' => $data['suggestedProducts']->map($toPayload)->values(),
            'vouchers' => $data['vouchers']->map(static fn ($v) => [
                'voucher_id' => (int) $v->voucher_id,
                'ma_voucher' => $v->ma_voucher,
                'ten_voucher' => $v->ten_voucher,
                'loai_giam' => $v->loai_giam,
                'gia_tri_giam' => (float) $v->gia_tri_giam,
                'giam_toi_da' => $v->giam_toi_da !== null ? (float) $v->giam_toi_da : null,
                'don_hang_toi_thieu' => (float) $v->don_hang_toi_thieu,
            ])->values(),
            'shipping_vouchers' => $data['shippingVouchers']->map(static fn ($v) => [
                'voucher_id' => (int) $v->voucher_id,
                'ma_voucher' => $v->ma_voucher,
                'ten_voucher' => $v->ten_voucher,
                'loai_giam' => $v->loai_giam,
                'gia_tri_giam' => (float) $v->gia_tri_giam,
                'giam_toi_da' => $v->giam_toi_da !== null ? (float) $v->giam_toi_da : null,
                'don_hang_toi_thieu' => (float) $v->don_hang_toi_thieu,
            ])->values(),
        ]);
    }

    /** Chỉnh số lượng ngay trên trang thanh toán; backend trả lại giá và chữ ký mới. */
    public function updateCheckoutQuantity(Request $request, CheckoutPageService $checkoutPage)
    {
        $data = $request->validate(['plant_id' => ['required', 'integer'], 'quantity' => ['required', 'integer', 'min:1']]);
        $userId = (int) Auth::id();
        $plant = \App\Models\CayCanh::find($data['plant_id']);
        if (!$plant || $plant->trang_thai !== 'Đang bán' || (int) $plant->so_luong < $data['quantity']) {
            return response()->json(['message' => 'Sản phẩm không khả dụng hoặc vượt quá tồn kho.'], 422);
        }
        $buyNow = session('greenshop_buy_now_' . $userId);
        if (is_array($buyNow)) {
            if ((int) $buyNow['plant_id'] !== (int) $data['plant_id']) return response()->json(['message' => 'Sản phẩm không thuộc đơn mua ngay.'], 403);
            $buyNow['quantity'] = (int) $data['quantity'];
            session(['greenshop_buy_now_' . $userId => $buyNow]);
        } else {
            $cart = \App\Models\GioHang::where('user_id', $userId)->first();
            if (!$cart) return response()->json(['message' => 'Không tìm thấy giỏ hàng.'], 404);
            $updated = \Illuminate\Support\Facades\DB::table('chi_tiet_gio_hang')
                ->where('cart_id', $cart->cart_id)->where('plant_id', $data['plant_id'])
                ->update(['so_luong' => (int) $data['quantity']]);
            if (!$updated) return response()->json(['message' => 'Sản phẩm không nằm trong giỏ hàng.'], 404);
        }
        return $this->refreshCart($checkoutPage);
    }

    /** UC16/17 - Validate + transaction tạo đơn. */
    public function placeOrder(
        Request $request,
        MapShippingService $map,
        OnlinePaymentService $onlinePayments,
        PaymentLifecycleService $paymentLifecycle,
        ShippingPricingService $shippingPricing,
        CheckoutIntegrityService $checkoutIntegrity,
        OrderCreationService $orderCreation,
    )
    {

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'address_id' => ['bail', 'required', 'integer'],
            'shipping_method' => ['bail', 'required', 'string', 'in:STANDARD,EXPRESS,GHTK_EXPRESS'],
            'payment_method' => ['bail', 'required', 'string', 'in:COD,PAYPAL,PAYOS'],
            'checkout_order_code' => ['bail', 'required', 'string', 'regex:/^GS\d{6}-\d{4}$/'],
            'cart_signature' => ['bail', 'required', 'string', 'size:64'],
            'voucher_id' => ['nullable', 'integer', 'exists:vouchers,voucher_id'],
            'shipping_voucher_id' => ['nullable', 'integer', 'exists:vouchers,voucher_id'],
        ], [
            'address_id.required' => 'Vui lòng chọn địa chỉ giao hàng.',
            'address_id.integer' => 'Địa chỉ giao hàng không hợp lệ. Vui lòng chọn địa chỉ khác.',
            'shipping_method.required' => 'Vui lòng chọn phương thức vận chuyển.',
            'shipping_method.in' => 'Phương thức vận chuyển không hợp lệ.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán này hiện không khả dụng. Vui lòng chọn phương thức khác.',
            'checkout_order_code.required' => 'Mã đơn hàng thanh toán không hợp lệ. Vui lòng tải lại trang thanh toán.',
            'checkout_order_code.regex' => 'Mã đơn hàng thanh toán không hợp lệ. Vui lòng tải lại trang thanh toán.',
            'cart_signature.required' => 'Giỏ hàng đã có thay đổi. Vui lòng kiểm tra lại trước khi đặt hàng.',
            'cart_signature.size' => 'Giỏ hàng đã có thay đổi. Vui lòng kiểm tra lại trước khi đặt hàng.',
            'voucher_id.exists' => 'Voucher đã chọn không còn tồn tại.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $sessionKey = 'greenshop_checkout_order_code_' . $user->user_id;
        $checkoutOrderCode = (string) $request->input('checkout_order_code');
        if (!hash_equals((string) session($sessionKey, ''), $checkoutOrderCode)) {
            return back()->withInput()->with('error', 'Phiên thanh toán đã thay đổi. Vui lòng tải lại trang thanh toán.');
        }

        $address = DiaChi::find($request->integer('address_id'));
        if (!$address) {
            return back()->withInput()->with('error', 'Địa chỉ giao hàng không còn tồn tại. Vui lòng chọn địa chỉ khác.');
        }
        if ((int) $address->user_id !== (int) $user->user_id) {
            abort(403, 'Bạn không có quyền sử dụng địa chỉ giao hàng này.');
        }

        // Phí ship luôn được tính lại phía server, không lấy số tiền từ JavaScript/client.
        try {
            $shippingQuote = $map->quoteForAddress($address);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $shippingMethod = strtoupper((string) $request->input('shipping_method', 'STANDARD'));
        if ($shippingMethod === 'EXPRESS' && !$shippingPricing->isHanoiAddress($address)) {
            return back()->withInput()->with('error', 'Giao Hỏa tốc hiện chỉ áp dụng cho địa chỉ tại Hà Nội.');
        }

        $distanceKm = (float) ($shippingQuote['distance_km'] ?? 0);
        $shippingFee = $shippingPricing->feeForMethod($shippingMethod, $distanceKm, $map);
        $paymentMethod = strtoupper((string) $request->input('payment_method'));


        $lock = Cache::lock('greenshop-place-order-user-' . $user->user_id, 12);
        if (!$lock->get()) {
            return back()->withInput()->with('warning', 'Đơn hàng đang được xử lý. Vui lòng không thực hiện lại thao tác.');
        }

        try {
            [$order, $onlineTransaction] = $orderCreation->create(
                user: $user,
                address: $address,
                shippingFee: (float) $shippingFee,
                shippingMethod: $shippingMethod,
                paymentMethod: $paymentMethod,
                checkoutOrderCode: $checkoutOrderCode,
                cartSignature: (string) $request->input('cart_signature'),
                voucherId: $request->filled('voucher_id') ? $request->integer('voucher_id') : null,
                shippingVoucherId: $request->filled('shipping_voucher_id') ? $request->integer('shipping_voucher_id') : null,
                buyNow: session('greenshop_buy_now_' . $user->user_id),
            );

            session()->forget(['greenshop_checkout_order_code_' . $user->user_id, 'greenshop_buy_now_' . $user->user_id]);

            if (in_array($paymentMethod, ['PAYPAL', 'PAYOS'], true) && $onlineTransaction) {
                try {
                    $started = $onlinePayments->start($onlineTransaction);

                    if (! $started->payment_url) {
                        throw PaymentGatewayException::unknown(
                            $paymentMethod,
                            $paymentMethod.' không trả về đường dẫn thanh toán.'
                        );
                    }

                    return redirect()->away((string) $started->payment_url);
                } catch (PaymentGatewayException $exception) {
                    $updated = $paymentLifecycle->recordInitiationFailure(
                        $onlineTransaction,
                        $exception
                    );

                    return redirect()
                        ->route('don-hang.show', $order->order_id)
                        ->with(
                            $updated->status === 'pending' ? 'warning' : 'error',
                            $updated->status === 'pending'
                                ? $paymentMethod.' chưa phản hồi. Đơn vẫn đang chờ thanh toán để tránh tạo giao dịch trùng.'
                                : 'Không thể khởi tạo thanh toán '.$paymentMethod.'. Đơn đã được hủy và hoàn lại tồn kho.'
                        );
                }
            }

            $paymentNotice = 'Đơn hàng đã được tạo. Bạn sẽ thanh toán khi nhận hàng.';

            return redirect()
                ->route('don-hang.show', $order->order_id)
                ->with('success', 'Đặt hàng thành công. Mã đơn hàng của bạn là ' . $order->orderCode() . '.')
                ->with('payment_notice', $paymentNotice);
        } catch (\RuntimeException $e) {
            $message = $checkoutIntegrity->businessErrorMessage($e->getMessage());
            return back()->withInput()->with('error', $message);
        } catch (\Throwable $e) {
            Log::error('Tao don hang GreenShop that bai', [
                'user_id' => $user->user_id,
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Không thể tạo đơn hàng. Vui lòng thử lại sau.');
        } finally {
            optional($lock)->release();
        }
    }
}
