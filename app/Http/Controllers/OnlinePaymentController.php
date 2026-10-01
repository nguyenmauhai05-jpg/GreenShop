<?php

namespace App\Http\Controllers;

use App\Models\GiaoDichThanhToan;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use App\Services\Payments\OnlinePaymentService;
use App\Services\Payments\PaymentGatewayManager;
use App\Services\Payments\PaymentLifecycleService;
use App\Services\Payments\Providers\PayPalPaymentGateway;
use App\Services\Payments\Providers\PayOSPaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;
use UnexpectedValueException;

class OnlinePaymentController extends Controller
{


    /** Browser redirects from payOS are NOT proof of payment. */
    public function payosReturn(Request $request, PaymentLifecycleService $lifecycle, PayOSPaymentGateway $payos)
    {
        $transaction = GiaoDichThanhToan::with('thanhToan.donHang')
            ->whereKey($request->integer('transaction'))->where('provider', 'PAYOS')->firstOrFail();
        abort_unless($transaction->thanhToan?->donHang
            && (int) $transaction->thanhToan->donHang->user_id === (int) Auth::id(), 403);
        if ($transaction->status === 'pending') {
            try {
                $result = $payos->lookupPaidTransaction($transaction);
                if ($result) $lifecycle->applyCallback('PAYOS', $result);
            } catch (Throwable $e) {
                Log::warning('Không thể đối soát payOS khi quay về', ['transaction' => $transaction->getKey(), 'error' => $e->getMessage()]);
            }
        }
        return $this->handleReturn($request, 'PAYOS', $lifecycle);
    }

    /** CSRF-exempt public server-to-server callback; HMAC verification is mandatory. */
    public function payosWebhook(Request $request, PayOSPaymentGateway $payos, PaymentLifecycleService $lifecycle)
    {
        try {
            $result = $payos->verifyCallback($request->all());
            if (!$result->validSignature) {
                Log::warning('payOS webhook signature không hợp lệ');
                return response()->json(['success' => false, 'message' => 'Invalid signature'], 400);
            }
            if (!$result->merchantOrderId) {
                // payOS sends a signed synthetic callback when confirming webhook URL.
                // A signed unknown transaction cannot alter our database.
                Log::warning('payOS webhook không tìm thấy mã giao dịch (có thể là webhook test)');
                return response()->json(['success' => true]);
            }
            $lifecycle->applyCallback('PAYOS', $result);
            return response()->json(['success' => true]);
        } catch (UnexpectedValueException $e) {
            Log::warning('payOS webhook không khớp đơn', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Invalid payment data'], 400);
        } catch (Throwable $e) {
            Log::error('payOS webhook processing failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false], 500);
        }
    }

    public function paypalReturn(
        Request $request,
        PayPalPaymentGateway $paypal,
        PaymentLifecycleService $lifecycle,
    ) {
        $transaction = GiaoDichThanhToan::with('thanhToan.donHang')
            ->whereKey($request->integer('transaction'))->where('provider', 'PAYPAL')->firstOrFail();
        $order = $transaction->thanhToan?->donHang;
        abort_unless($order && (int)$order->user_id === (int)Auth::id(), 403);

        if ($transaction->status === 'pending') {
            try {
                $result = $paypal->capture(
                    (string) $request->query('token', ''),
                    (int) round((float) $transaction->amount),
                );
                if (!$result->validSignature) {
                    throw new UnexpectedValueException('Số tiền hoặc dữ liệu PayPal không khớp đơn hàng.');
                }
                $lifecycle->applyCallback('PAYPAL', $result);
            } catch (Throwable $e) {
                Log::error('Xác nhận PayPal thất bại', ['transaction_id' => $transaction->transaction_id, 'message' => $e->getMessage()]);
                return redirect()->route('don-hang.show', $order->order_id)
                    ->with('warning', 'Chưa thể xác nhận thanh toán PayPal. Vui lòng thử lại từ đơn hàng.');
            }
        }
        return $this->handleReturn($request, 'PAYPAL', $lifecycle);
    }

    public function paypalCancel(Request $request)
    {
        $transaction = GiaoDichThanhToan::with('thanhToan.donHang')
            ->whereKey($request->integer('transaction'))->where('provider', 'PAYPAL')->firstOrFail();
        $order = $transaction->thanhToan?->donHang;
        abort_unless($order && (int)$order->user_id === (int)Auth::id(), 403);
        return redirect()->route('don-hang.show', $order->order_id)
            ->with('warning', 'Bạn đã hủy thanh toán PayPal. Đơn vẫn đang chờ thanh toán và có thể thử lại.');
    }

    public function zaloPayCallback(
        Request $request,
        PaymentGatewayManager $gateways,
        PaymentLifecycleService $lifecycle,
    ) {
        try {
            $result = $gateways->resolve('ZALOPAY')->verifyCallback($request->all());

            if (! $result->validSignature) {
                Log::warning('ZaloPay callback có MAC không hợp lệ', [
                    'merchant_order_id' => $result->merchantOrderId,
                ]);

                return response()->json([
                    'return_code' => 2,
                    'return_message' => 'Invalid signature',
                ]);
            }

            $lifecycle->applyCallback('ZALOPAY', $result);

            return response()->json([
                'return_code' => 1,
                'return_message' => 'Success',
            ]);
        } catch (UnexpectedValueException $exception) {
            Log::warning('ZaloPay callback không khớp dữ liệu GreenShop', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'return_code' => 2,
                'return_message' => 'Invalid payment data',
            ]);
        } catch (Throwable $exception) {
            Log::error('Xử lý ZaloPay callback thất bại', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'return_code' => 2,
                'return_message' => 'Unable to process payment',
            ], 500);
        }
    }

    public function zaloPayReturn(
        Request $request,
        PaymentLifecycleService $lifecycle,
    ) {
        return $this->handleReturn($request, 'ZALOPAY', $lifecycle);
    }

    public function retry(
        GiaoDichThanhToan $transaction,
        OnlinePaymentService $onlinePayments,
        PaymentLifecycleService $lifecycle,
    ) {
        $transaction->loadMissing('thanhToan.donHang');
        $order = $transaction->thanhToan?->donHang;

        abort_unless($order, 404);
        abort_unless((int) $order->user_id === (int) Auth::id(), 403);

        if (strtoupper((string) $transaction->provider) === 'MOMO') {
            return redirect()->route('don-hang.show', $order->order_id)
                ->with('warning', 'GreenShop đã ngừng hỗ trợ thanh toán MoMo. Vui lòng liên hệ cửa hàng về đơn hàng cũ.');
        }

        if ($transaction->expires_at?->isPast()) {
            $lifecycle->expirePending($transaction);

            return redirect()
                ->route('don-hang.show', $order->order_id)
                ->with('error', 'Phiên thanh toán đã hết hạn. Đơn hàng đã được hủy và hoàn lại tồn kho.');
        }

        if ($transaction->status !== 'pending') {
            return redirect()
                ->route('don-hang.show', $order->order_id)
                ->with('warning', 'Giao dịch này không còn ở trạng thái chờ thanh toán.');
        }

        if ($transaction->payment_url) {
            return redirect()->away($transaction->payment_url);
        }

        // TTL dài hơn timeout HTTP của ZaloPay để tránh hai yêu cầu create chạy song song.
        $lock = Cache::lock('greenshop-online-payment-'.$transaction->transaction_id, 90);
        if (! $lock->get()) {
            return redirect()
                ->route('don-hang.show', $order->order_id)
                ->with('warning', 'Giao dịch đang được xử lý. Vui lòng thử lại sau ít phút.');
        }

        try {
            $updated = $onlinePayments->start($transaction);

            return redirect()->away((string) $updated->payment_url);
        } catch (PaymentGatewayException $exception) {
            $updated = $lifecycle->recordInitiationFailure($transaction, $exception);
            $message = $updated->status === 'pending'
                ? 'Cổng thanh toán chưa phản hồi. Đơn vẫn được giữ ở trạng thái chờ để tránh thanh toán trùng.'
                : 'Không thể tạo phiên thanh toán. Đơn đã được hủy và hoàn lại tồn kho.';

            return redirect()
                ->route('don-hang.show', $order->order_id)
                ->with($updated->status === 'pending' ? 'warning' : 'error', $message);
        } catch (Throwable $exception) {
            Log::error('Thử lại thanh toán online thất bại', [
                'transaction_id' => $transaction->transaction_id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('don-hang.show', $order->order_id)
                ->with('error', 'Không thể kết nối cổng thanh toán. Vui lòng thử lại sau.');
        } finally {
            $lock->release();
        }
    }

    private function handleReturn(
        Request $request,
        string $provider,
        PaymentLifecycleService $lifecycle,
    ) {
        $transaction = GiaoDichThanhToan::with('thanhToan.donHang')
            ->whereKey($request->integer('transaction'))
            ->where('provider', $provider)
            ->firstOrFail();
        $order = $transaction->thanhToan?->donHang;

        abort_unless($order, 404);
        abort_unless((int) $order->user_id === (int) Auth::id(), 403);

        if ($transaction->status === 'pending' && $transaction->expires_at?->isPast()) {
            $lifecycle->expirePending($transaction);
            $transaction->refresh();
        }

        $transaction->load('thanhToan');
        $paymentStatus = $transaction->thanhToan?->normalizedStatus();

        if ($paymentStatus === 'refund_pending') {
            [$flashType, $message] = [
                'warning',
                'Tiền đã được ghi nhận sau khi đơn bị hủy. GreenShop đã chuyển giao dịch sang Chờ hoàn tiền để quản trị viên xử lý.',
            ];
        } elseif ($paymentStatus === 'refunded') {
            [$flashType, $message] = ['success', 'Khoản thanh toán của đơn đã được hoàn lại.'];
        } else {
            [$flashType, $message] = match ($transaction->status) {
                'paid' => ['success', 'Thanh toán thành công! Đơn hàng đang chờ cửa hàng xác nhận.'],
                'failed', 'cancelled' => ['error', "Thanh toán qua {$provider} không thành công. Đơn hàng đã được hủy."],
                'expired' => ['error', 'Phiên thanh toán đã hết hạn. Đơn hàng đã được hủy và hoàn lại tồn kho.'],
                default => ['warning', 'GreenShop đang chờ cổng thanh toán xác nhận. Vui lòng không thanh toán lại.'],
            };
        }

        return redirect()
            ->route('don-hang.show', $order->order_id)
            ->with($flashType, $message);
    }
}
