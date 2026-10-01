<?php

namespace App\Services\Payments;

use App\Models\GiaoDichThanhToan;
use App\Services\Payments\Exceptions\PaymentGatewayException;

class OnlinePaymentService
{
    public function __construct(
        private readonly PaymentGatewayManager $gateways,
        private readonly PaymentLifecycleService $lifecycle,
    ) {}

    /**
     * @return array<string, array{configured: bool, demo: bool, mode: string}>
     */
    public function availability(): array
    {
        $availability = [];
        $demoMode = false;

        foreach ($this->gateways->supportedProviders() as $provider) {
            $gatewayConfigured = $this->gateways->resolve($provider)->isConfigured();
            $availability[$provider] = [
                'configured' => $demoMode || $gatewayConfigured,
                'demo' => $demoMode,
                'mode' => $demoMode ? 'demo' : ($gatewayConfigured ? 'sandbox' : 'unavailable'),
            ];
        }

        return $availability;
    }

    public function start(GiaoDichThanhToan $transaction): GiaoDichThanhToan
    {
        $transaction->loadMissing([
            'thanhToan.donHang.nguoiDung',
            'thanhToan.donHang.chiTietDonHangs.cayCanh',
        ]);

        $payment = $transaction->thanhToan;
        $order = $payment?->donHang;

        if (! $payment || ! $order) {
            throw PaymentGatewayException::invalidRequest(
                strtoupper((string) $transaction->provider),
                'Giao dịch không còn liên kết với đơn hàng.',
            );
        }

        if ($transaction->status !== 'pending') {
            throw PaymentGatewayException::invalidRequest(
                strtoupper((string) $transaction->provider),
                'Giao dịch không còn ở trạng thái chờ thanh toán.',
            );
        }

        if ($transaction->expires_at?->isPast()) {
            $this->lifecycle->expirePending($transaction);

            throw PaymentGatewayException::invalidRequest(
                strtoupper((string) $transaction->provider),
                'Phiên thanh toán đã hết hạn.',
            );
        }

        $provider = strtoupper((string) $transaction->provider);
        $gateway = $this->gateways->resolve($provider);

        if (! $gateway->isConfigured()) {
            throw PaymentGatewayException::configuration(
                $provider,
                "Cổng {$provider} chưa được cấu hình.",
            );
        }

        $result = $gateway->createPayment([
            'merchant_order_id' => (string) $transaction->merchant_transaction_id,
            'transaction_id' => (int) $transaction->transaction_id,
            'cancel_url' => $provider === 'PAYOS' ? route('thanh-toan.payos.return', ['transaction' => $transaction->transaction_id]) : null,
            'request_id' => (string) $transaction->provider_request_id,
            'amount' => (int) round((float) $transaction->amount),
            'order_info' => 'GreenShop - Thanh toán đơn '.$order->orderCode(),
            'return_url' => route($this->returnRoute($provider), [
                'transaction' => $transaction->transaction_id,
            ]),
            'callback_url' => route($this->callbackRoute($provider)),
            'user_id' => (string) $order->user_id,
            'items' => $order->chiTietDonHangs->map(function ($detail) {
                return [
                    'id' => (string) $detail->plant_id,
                    'name' => (string) ($detail->cayCanh?->ten_cay ?: 'Cây cảnh GreenShop'),
                    'price' => (int) round((float) $detail->don_gia),
                    'quantity' => (int) $detail->so_luong,
                ];
            })->values()->all(),
        ]);

        return $this->lifecycle->recordCreation($transaction, $result);
    }

    public function demoModeEnabled(): bool
    {
        return app()->environment(['local', 'testing'])
            && (bool) config('services.payments.demo_mode', false);
    }

    private function returnRoute(string $provider): string
    {
        return match ($provider) {
            'ZALOPAY' => 'thanh-toan.zalopay.return',
            'PAYPAL' => 'thanh-toan.paypal.return',
            'PAYOS' => 'thanh-toan.payos.return',
            default => throw PaymentGatewayException::invalidRequest(
                $provider,
                'Cổng thanh toán không được hỗ trợ.',
            ),
        };
    }

    private function callbackRoute(string $provider): string
    {
        return match ($provider) {
            'ZALOPAY' => 'thanh-toan.zalopay.callback',
            'PAYPAL' => 'thanh-toan.paypal.return',
            'PAYOS' => 'thanh-toan.payos.webhook',
            default => throw PaymentGatewayException::invalidRequest(
                $provider,
                'Cổng thanh toán không được hỗ trợ.',
            ),
        };
    }
}
