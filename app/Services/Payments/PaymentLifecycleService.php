<?php

namespace App\Services\Payments;

use App\Models\DonHang;
use App\Models\GiaoDichThanhToan;
use App\Models\ThanhToan;
use App\Services\Payments\DTO\PaymentCallbackResult;
use App\Services\Payments\DTO\PaymentCreationResult;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use UnexpectedValueException;

class PaymentLifecycleService
{
    /**
     * Lưu kết quả tạo phiên thanh toán mà không lưu toàn bộ payload của cổng.
     */
    public function recordCreation(
        GiaoDichThanhToan $transaction,
        PaymentCreationResult $result,
    ): GiaoDichThanhToan {
        return DB::transaction(function () use ($transaction, $result) {
            $locked = GiaoDichThanhToan::whereKey($transaction->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! hash_equals(
                (string) $locked->merchant_transaction_id,
                (string) $result->providerOrderId,
            )) {
                throw new UnexpectedValueException('Mã giao dịch trả về không khớp với giao dịch đã tạo.');
            }

            if ($locked->provider_request_id
                && $result->providerRequestId !== ''
                && ! hash_equals(
                    (string) $locked->provider_request_id,
                    (string) $result->providerRequestId,
                )) {
                throw new UnexpectedValueException('Mã yêu cầu trả về không khớp với giao dịch đã tạo.');
            }

            // IPN/callback có thể đến trước khi luồng tạo phiên kịp lưu phản hồi HTTP.
            // Chỉ bổ sung URL trong trường hợp đó, không hạ trạng thái hay ghi đè
            // bằng chứng callback của một giao dịch đã kết thúc.
            if ($locked->status !== 'pending') {
                if (! $locked->payment_url && $result->paymentUrl) {
                    $locked->payment_url = $result->paymentUrl;
                    $locked->save();
                }

                return $locked->fresh();
            }

            $locked->fill([
                'payment_url' => $result->paymentUrl,
                'response_code' => $this->stringOrNull($result->responseCode),
                'response_message' => mb_substr($result->message, 0, 500),
                'status' => 'pending',
            ])->save();

            return $locked->fresh();
        }, 3);
    }

    /**
     * Ghi nhận lỗi khởi tạo. Lỗi bị cổng từ chối là kết quả cuối cùng; lỗi mạng
     * được giữ ở trạng thái pending để callback hoặc thao tác thử lại có thể đối soát.
     */
    public function recordInitiationFailure(
        GiaoDichThanhToan $transaction,
        PaymentGatewayException $exception,
    ): GiaoDichThanhToan {
        $isDefinitive = in_array($exception->type, [
            PaymentGatewayException::CONFIGURATION,
            PaymentGatewayException::INVALID_REQUEST,
            PaymentGatewayException::REJECTED,
        ], true);

        return DB::transaction(function () use ($transaction, $exception, $isDefinitive) {
            $locked = GiaoDichThanhToan::whereKey($transaction->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'pending') {
                return $locked->fresh();
            }

            $locked->response_code = $this->stringOrNull($exception->responseCode)
                ?? $exception->type;
            $locked->response_message = mb_substr($exception->getMessage(), 0, 500);

            if ($isDefinitive && $locked->status !== 'paid') {
                $locked->status = 'failed';
                $this->cancelUnpaidOrder($locked, 'failed');
            }

            $locked->save();

            return $locked->fresh();
        }, 3);
    }

    /**
     * Áp dụng callback đã được gateway xác minh chữ ký.
     *
     * Hàm có tính idempotent: callback thành công gửi lặp lại không ghi nhận tiền
     * hoặc hoàn tồn kho thêm lần nữa.
     */
    public function applyCallback(
        string $provider,
        PaymentCallbackResult $result,
    ): GiaoDichThanhToan {
        if (! $result->validSignature) {
            throw new UnexpectedValueException('Chữ ký callback thanh toán không hợp lệ.');
        }

        $merchantOrderId = trim((string) $result->merchantOrderId);
        if ($merchantOrderId === '') {
            throw new UnexpectedValueException('Callback không có mã giao dịch của GreenShop.');
        }

        return DB::transaction(function () use ($provider, $result, $merchantOrderId) {
            $transaction = GiaoDichThanhToan::where('provider', strtoupper($provider))
                ->where('merchant_transaction_id', $merchantOrderId)
                ->lockForUpdate()
                ->first();

            if (! $transaction) {
                throw new RuntimeException('Không tìm thấy giao dịch thanh toán tương ứng.');
            }

            if ($result->amount === null
                || (int) round((float) $transaction->amount) !== $result->amount) {
                throw new UnexpectedValueException('Số tiền callback không khớp với đơn hàng.');
            }

            if ($result->providerRequestId
                && $transaction->provider_request_id
                && ! hash_equals(
                    (string) $transaction->provider_request_id,
                    (string) $result->providerRequestId,
                )) {
                throw new UnexpectedValueException('Mã yêu cầu callback không khớp.');
            }

            // Callback pending/thất bại đến sai thứ tự không được mở lại một
            // giao dịch đã kết thúc. Riêng callback thành công đến muộn vẫn
            // được xử lý bên dưới để chuyển khoản tiền sang refund_pending.
            if (! $result->successful
                && in_array($transaction->status, ['failed', 'expired', 'cancelled'], true)) {
                return $transaction->fresh();
            }

            $payment = ThanhToan::whereKey($transaction->payment_id)
                ->lockForUpdate()
                ->firstOrFail();
            $order = DonHang::whereKey($payment->order_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Callback đến muộn không được phép sửa trạng thái hoặc bằng chứng của giao dịch đã trả tiền.
            if ($transaction->status === 'paid') {
                if ($result->successful
                    && $result->providerTransactionId
                    && $transaction->provider_transaction_id
                    && ! hash_equals(
                        (string) $transaction->provider_transaction_id,
                        (string) $result->providerTransactionId,
                    )) {
                    throw new UnexpectedValueException('Mã giao dịch nhà cung cấp không khớp với giao dịch đã thanh toán.');
                }

                return $transaction->fresh();
            }

            $transaction->response_code = $this->stringOrNull($result->responseCode);
            $transaction->response_message = mb_substr($result->message, 0, 500);

            if ($result->pending) {
                $transaction->status = 'pending';
                $transaction->save();

                return $transaction->fresh();
            }

            if ($result->successful) {
                if (! $result->providerTransactionId) {
                    throw new UnexpectedValueException('Callback thành công thiếu mã giao dịch nhà cung cấp.');
                }

                $transaction->status = 'paid';
                $transaction->provider_transaction_id = $result->providerTransactionId;
                $transaction->paid_at = now();

                if ($order->normalizedStatus() === 'cancelled') {
                    // Tiền về sau khi đơn đã hủy: không tự kích hoạt lại đơn đã hoàn kho.
                    $payment->trang_thai = 'refund_pending';
                    $payment->ngay_thanh_toan = now();
                } else {
                    $payment->trang_thai = 'paid';
                    $payment->ngay_thanh_toan = now();

                    if ($order->normalizedStatus() === 'pending_payment') {
                        $order->trang_thai = 'pending_confirmation';
                        $order->save();
                    }
                }

                $payment->save();
                $transaction->save();

                return $transaction->fresh();
            }

            $transaction->status = 'failed';
            if (! $payment->isPaid()) {
                $this->cancelUnpaidOrder($transaction, 'failed', $payment, $order);
            }
            $transaction->save();

            return $transaction->fresh();
        }, 3);
    }

    public function expirePending(GiaoDichThanhToan $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $locked = GiaoDichThanhToan::whereKey($transaction->getKey())
                ->lockForUpdate()
                ->first();

            if (! $locked
                || $locked->status !== 'pending'
                || ! $locked->expires_at
                || $locked->expires_at->isFuture()) {
                return false;
            }

            $locked->status = 'expired';
            $locked->response_code = $locked->response_code ?: 'expired';
            $locked->response_message = $locked->response_message
                ?: 'Phiên thanh toán đã hết hạn.';
            $this->cancelUnpaidOrder($locked, 'expired');
            $locked->save();

            return true;
        }, 3);
    }

    private function cancelUnpaidOrder(
        GiaoDichThanhToan $transaction,
        string $paymentStatus,
        ?ThanhToan $payment = null,
        ?DonHang $order = null,
    ): void {
        $payment ??= ThanhToan::whereKey($transaction->payment_id)
            ->lockForUpdate()
            ->first();
        if (! $payment || $payment->isPaid()) {
            return;
        }

        $order ??= DonHang::whereKey($payment->order_id)
            ->lockForUpdate()
            ->first();

        $payment->trang_thai = $paymentStatus;
        $payment->save();

        if (! $order || $order->normalizedStatus() !== 'pending_payment') {
            return;
        }

        $details = DB::table('chi_tiet_don_hang')
            ->where('order_id', $order->order_id)
            ->lockForUpdate()
            ->get(['plant_id', 'so_luong']);

        foreach ($details as $detail) {
            if ($detail->plant_id && (int) $detail->so_luong > 0) {
                DB::table('cay_canh')
                    ->where('plant_id', $detail->plant_id)
                    ->increment('so_luong', (int) $detail->so_luong);
            }
        }

        if (! empty($order->voucher_id)) {
            DB::table('vouchers')
                ->where('voucher_id', $order->voucher_id)
                ->where('da_su_dung', '>', 0)
                ->decrement('da_su_dung');
        }

        $order->trang_thai = 'cancelled';
        $order->save();
    }

    private function stringOrNull(int|string|null $value): ?string
    {
        return $value === null ? null : mb_substr((string) $value, 0, 50);
    }
}
