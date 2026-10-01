<?php

namespace Tests\Unit;

use App\Models\DonHang;
use App\Models\GiaoDichThanhToan;
use App\Models\ThanhToan;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PaymentModelsTest extends TestCase
{
    #[DataProvider('paymentStatuses')]
    public function test_it_normalizes_payment_statuses(
        ?string $status,
        string $expected,
        bool $isPaid
    ): void {
        $payment = new ThanhToan(['trang_thai' => $status]);

        $this->assertSame($expected, $payment->normalizedStatus());
        $this->assertSame($isPaid, $payment->isPaid());
    }

    public static function paymentStatuses(): array
    {
        return [
            'empty is pending' => [null, 'pending', false],
            'legacy pending' => ['waiting_confirmation', 'pending', false],
            'legacy Vietnamese paid' => ['Đã thanh toán', 'paid', true],
            'provider success' => ['SUCCESS', 'paid', true],
            'cancelled variant' => ['canceled', 'cancelled', false],
            'refunded payment is not paid' => ['đã hoàn tiền', 'refunded', false],
            'unknown remains inspectable' => ['manual_review', 'manual_review', false],
        ];
    }

    public function test_pending_payment_order_has_expected_customer_behavior(): void
    {
        $order = new DonHang(['trang_thai' => 'chờ thanh toán']);

        $this->assertSame('pending_payment', $order->normalizedStatus());
        $this->assertSame('Chờ thanh toán', $order->statusLabel());
        $this->assertSame(0, $order->progressStep());
        $this->assertTrue($order->isCancelable());
    }

    public function test_gateway_transaction_has_expected_casts_and_fillable_fields(): void
    {
        $transaction = new GiaoDichThanhToan([
            'payment_id' => 10,
            'provider' => 'MOMO',
            'merchant_transaction_id' => 'GS-20260814-0001',
            'amount' => 125000,
            'status' => 'pending',
        ]);

        $this->assertSame('125000.00', $transaction->amount);
        $this->assertSame('MOMO', $transaction->provider);
        $this->assertSame('GS-20260814-0001', $transaction->merchant_transaction_id);
    }
}
