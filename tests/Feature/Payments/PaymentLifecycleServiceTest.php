<?php

namespace Tests\Feature\Payments;

use App\Models\GiaoDichThanhToan;
use App\Services\Payments\DTO\PaymentCallbackResult;
use App\Services\Payments\DTO\PaymentCreationResult;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use App\Services\Payments\PaymentLifecycleService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use UnexpectedValueException;

class PaymentLifecycleServiceTest extends TestCase
{
    private PaymentLifecycleService $lifecycle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dropPaymentTables();
        $this->createPaymentTables();

        $this->lifecycle = $this->app->make(PaymentLifecycleService::class);
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        $this->dropPaymentTables();

        parent::tearDown();
    }

    public function test_valid_success_callback_marks_payment_paid_and_advances_order(): void
    {
        $fixture = $this->pendingPaymentFixture();

        $updated = $this->lifecycle->applyCallback(
            'momo',
            $this->paymentCallback(
                merchantOrderId: $fixture['merchant_transaction_id'],
                providerRequestId: $fixture['provider_request_id'],
                providerTransactionId: 'MOMO-TRANS-90001',
            ),
        );

        $this->assertSame('paid', $updated->status);
        $this->assertSame('MOMO-TRANS-90001', $updated->provider_transaction_id);
        $this->assertNotNull($updated->paid_at);

        $this->assertDatabaseHas('thanh_toan', [
            'payment_id' => $fixture['payment_id'],
            'trang_thai' => 'paid',
        ]);
        $this->assertDatabaseHas('don_hang', [
            'order_id' => $fixture['order_id'],
            'trang_thai' => 'pending_confirmation',
        ]);

        // Stock was already reserved during checkout; a successful callback must not alter it.
        $this->assertSame(7, $this->plantStock($fixture['plant_id']));
    }

    public function test_callback_with_wrong_amount_is_rejected_without_state_or_stock_changes(): void
    {
        $fixture = $this->pendingPaymentFixture();

        try {
            $this->lifecycle->applyCallback(
                'MOMO',
                $this->paymentCallback(
                    merchantOrderId: $fixture['merchant_transaction_id'],
                    providerRequestId: $fixture['provider_request_id'],
                    providerTransactionId: 'MOMO-TRANS-WRONG-AMOUNT',
                    amount: 124_999,
                ),
            );

            $this->fail('A callback with a mismatched amount must be rejected.');
        } catch (UnexpectedValueException) {
            // Expected: the database transaction must roll back without lifecycle changes.
        }

        $this->assertDatabaseHas('giao_dich_thanh_toan', [
            'transaction_id' => $fixture['transaction_id'],
            'status' => 'pending',
            'provider_transaction_id' => null,
        ]);
        $this->assertDatabaseHas('thanh_toan', [
            'payment_id' => $fixture['payment_id'],
            'trang_thai' => 'pending',
        ]);
        $this->assertDatabaseHas('don_hang', [
            'order_id' => $fixture['order_id'],
            'trang_thai' => 'pending_payment',
        ]);
        $this->assertSame(7, $this->plantStock($fixture['plant_id']));
    }

    public function test_duplicate_failure_callback_restores_reserved_stock_only_once(): void
    {
        $fixture = $this->pendingPaymentFixture();
        $failedCallback = $this->paymentCallback(
            merchantOrderId: $fixture['merchant_transaction_id'],
            providerRequestId: $fixture['provider_request_id'],
            providerTransactionId: 'MOMO-FAILED-90001',
            successful: false,
            responseCode: 1006,
        );

        $this->lifecycle->applyCallback('MOMO', $failedCallback);
        $this->lifecycle->applyCallback('MOMO', $failedCallback);

        $this->assertDatabaseHas('giao_dich_thanh_toan', [
            'transaction_id' => $fixture['transaction_id'],
            'status' => 'failed',
        ]);
        $this->assertDatabaseHas('thanh_toan', [
            'payment_id' => $fixture['payment_id'],
            'trang_thai' => 'failed',
        ]);
        $this->assertDatabaseHas('don_hang', [
            'order_id' => $fixture['order_id'],
            'trang_thai' => 'cancelled',
        ]);

        // 7 reserved stock + 3 returned once. A non-idempotent callback would produce 13.
        $this->assertSame(10, $this->plantStock($fixture['plant_id']));
    }

    public function test_late_pending_callback_cannot_reopen_a_failed_transaction(): void
    {
        $fixture = $this->pendingPaymentFixture();

        $this->lifecycle->applyCallback(
            'MOMO',
            $this->paymentCallback(
                merchantOrderId: $fixture['merchant_transaction_id'],
                providerRequestId: $fixture['provider_request_id'],
                providerTransactionId: 'MOMO-FAILED-BEFORE-PENDING',
                successful: false,
                responseCode: 1006,
            ),
        );

        $latePending = new PaymentCallbackResult(
            validSignature: true,
            successful: false,
            pending: true,
            merchantOrderId: $fixture['merchant_transaction_id'],
            providerRequestId: $fixture['provider_request_id'],
            providerTransactionId: 'MOMO-FAILED-BEFORE-PENDING',
            amount: 125_000,
            responseCode: 7000,
            message: 'Processing',
        );
        $updated = $this->lifecycle->applyCallback('MOMO', $latePending);

        $this->assertSame('failed', $updated->status);
        $this->assertSame('1006', $updated->response_code);
        $this->assertDatabaseHas('thanh_toan', [
            'payment_id' => $fixture['payment_id'],
            'trang_thai' => 'failed',
        ]);
        $this->assertDatabaseHas('don_hang', [
            'order_id' => $fixture['order_id'],
            'trang_thai' => 'cancelled',
        ]);
        $this->assertSame(10, $this->plantStock($fixture['plant_id']));
    }

    public function test_late_failure_callback_cannot_overwrite_a_paid_transactions_audit_result(): void
    {
        $fixture = $this->pendingPaymentFixture();

        $this->lifecycle->applyCallback(
            'MOMO',
            $this->paymentCallback(
                merchantOrderId: $fixture['merchant_transaction_id'],
                providerRequestId: $fixture['provider_request_id'],
                providerTransactionId: 'MOMO-TRANS-90002',
            ),
        );

        $this->lifecycle->applyCallback(
            'MOMO',
            $this->paymentCallback(
                merchantOrderId: $fixture['merchant_transaction_id'],
                providerRequestId: $fixture['provider_request_id'],
                providerTransactionId: 'MOMO-TRANS-90002',
                successful: false,
                responseCode: 1006,
            ),
        );

        $transaction = GiaoDichThanhToan::findOrFail($fixture['transaction_id']);

        $this->assertSame('paid', $transaction->status);
        $this->assertSame('0', $transaction->response_code);
        $this->assertSame('Success', $transaction->response_message);
        $this->assertSame('MOMO-TRANS-90002', $transaction->provider_transaction_id);
        $this->assertDatabaseHas('thanh_toan', [
            'payment_id' => $fixture['payment_id'],
            'trang_thai' => 'paid',
        ]);
        $this->assertDatabaseHas('don_hang', [
            'order_id' => $fixture['order_id'],
            'trang_thai' => 'pending_confirmation',
        ]);
        $this->assertSame(7, $this->plantStock($fixture['plant_id']));
    }

    public function test_late_create_response_cannot_downgrade_a_paid_callback(): void
    {
        $fixture = $this->pendingPaymentFixture();

        $this->lifecycle->applyCallback(
            'MOMO',
            $this->paymentCallback(
                merchantOrderId: $fixture['merchant_transaction_id'],
                providerRequestId: $fixture['provider_request_id'],
                providerTransactionId: 'MOMO-TRANS-FAST-IPN',
            ),
        );

        $updated = $this->lifecycle->recordCreation(
            $fixture['transaction'],
            new PaymentCreationResult(
                paymentUrl: 'https://test-payment.momo.vn/pay/fast-ipn',
                providerOrderId: $fixture['merchant_transaction_id'],
                providerRequestId: $fixture['provider_request_id'],
                responseCode: 0,
                message: 'Create response arrived later',
                rawResponse: [],
            ),
        );

        $this->assertSame('paid', $updated->status);
        $this->assertSame('0', $updated->response_code);
        $this->assertSame('Success', $updated->response_message);
        $this->assertSame('MOMO-TRANS-FAST-IPN', $updated->provider_transaction_id);
        $this->assertSame('https://test-payment.momo.vn/pay/fast-ipn', $updated->payment_url);
    }

    public function test_late_initiation_error_cannot_overwrite_a_paid_callback(): void
    {
        $fixture = $this->pendingPaymentFixture();

        $this->lifecycle->applyCallback(
            'MOMO',
            $this->paymentCallback(
                merchantOrderId: $fixture['merchant_transaction_id'],
                providerRequestId: $fixture['provider_request_id'],
                providerTransactionId: 'MOMO-TRANS-BEFORE-ERROR',
            ),
        );

        $updated = $this->lifecycle->recordInitiationFailure(
            $fixture['transaction'],
            PaymentGatewayException::transport('MOMO', 'Late transport error'),
        );

        $this->assertSame('paid', $updated->status);
        $this->assertSame('0', $updated->response_code);
        $this->assertSame('Success', $updated->response_message);
        $this->assertSame('MOMO-TRANS-BEFORE-ERROR', $updated->provider_transaction_id);
    }

    public function test_duplicate_success_with_a_different_provider_transaction_id_is_rejected(): void
    {
        $fixture = $this->pendingPaymentFixture();

        $this->lifecycle->applyCallback(
            'MOMO',
            $this->paymentCallback(
                merchantOrderId: $fixture['merchant_transaction_id'],
                providerRequestId: $fixture['provider_request_id'],
                providerTransactionId: 'MOMO-TRANS-ORIGINAL',
            ),
        );

        try {
            $this->lifecycle->applyCallback(
                'MOMO',
                $this->paymentCallback(
                    merchantOrderId: $fixture['merchant_transaction_id'],
                    providerRequestId: $fixture['provider_request_id'],
                    providerTransactionId: 'MOMO-TRANS-CONFLICT',
                ),
            );

            $this->fail('A paid merchant transaction cannot accept a different provider transaction ID.');
        } catch (UnexpectedValueException) {
            // Expected: preserve the first successfully recorded provider transaction.
        }

        $transaction = GiaoDichThanhToan::findOrFail($fixture['transaction_id']);

        $this->assertSame('paid', $transaction->status);
        $this->assertSame('MOMO-TRANS-ORIGINAL', $transaction->provider_transaction_id);
        $this->assertSame('0', $transaction->response_code);
        $this->assertSame('Success', $transaction->response_message);
        $this->assertDatabaseHas('thanh_toan', [
            'payment_id' => $fixture['payment_id'],
            'trang_thai' => 'paid',
        ]);
        $this->assertDatabaseHas('don_hang', [
            'order_id' => $fixture['order_id'],
            'trang_thai' => 'pending_confirmation',
        ]);
        $this->assertSame(7, $this->plantStock($fixture['plant_id']));
    }

    public function test_success_callback_after_order_was_cancelled_requires_refund_without_reactivation(): void
    {
        CarbonImmutable::setTestNow('2026-08-14 10:30:00');
        $fixture = $this->pendingPaymentFixture(
            expiresAt: CarbonImmutable::now()->subMinute(),
        );

        $this->assertTrue($this->lifecycle->expirePending($fixture['transaction']));
        $this->assertSame(10, $this->plantStock($fixture['plant_id']));

        $updated = $this->lifecycle->applyCallback(
            'MOMO',
            $this->paymentCallback(
                merchantOrderId: $fixture['merchant_transaction_id'],
                providerRequestId: $fixture['provider_request_id'],
                providerTransactionId: 'MOMO-LATE-90001',
            ),
        );

        $this->assertSame('paid', $updated->status);
        $this->assertDatabaseHas('thanh_toan', [
            'payment_id' => $fixture['payment_id'],
            'trang_thai' => 'refund_pending',
        ]);
        $this->assertDatabaseHas('don_hang', [
            'order_id' => $fixture['order_id'],
            'trang_thai' => 'cancelled',
        ]);

        // The late payment must neither reactivate the order nor reserve the stock again.
        $this->assertSame(10, $this->plantStock($fixture['plant_id']));
    }

    public function test_expiry_is_idempotent_and_restores_reserved_stock_only_once(): void
    {
        CarbonImmutable::setTestNow('2026-08-14 10:30:00');
        $fixture = $this->pendingPaymentFixture(
            expiresAt: CarbonImmutable::now()->subSecond(),
        );

        $this->assertTrue($this->lifecycle->expirePending($fixture['transaction']));
        $this->assertFalse($this->lifecycle->expirePending($fixture['transaction']));

        $this->assertDatabaseHas('giao_dich_thanh_toan', [
            'transaction_id' => $fixture['transaction_id'],
            'status' => 'expired',
        ]);
        $this->assertDatabaseHas('thanh_toan', [
            'payment_id' => $fixture['payment_id'],
            'trang_thai' => 'expired',
        ]);
        $this->assertDatabaseHas('don_hang', [
            'order_id' => $fixture['order_id'],
            'trang_thai' => 'cancelled',
        ]);
        $this->assertSame(10, $this->plantStock($fixture['plant_id']));
    }

    /**
     * @return array{
     *     order_id: int,
     *     payment_id: int,
     *     transaction_id: int,
     *     plant_id: int,
     *     merchant_transaction_id: string,
     *     provider_request_id: string,
     *     transaction: GiaoDichThanhToan
     * }
     */
    private function pendingPaymentFixture(
        string $provider = 'MOMO',
        ?CarbonImmutable $expiresAt = null,
    ): array {
        $orderId = DB::table('don_hang')->insertGetId([
            'user_id' => 42,
            'tong_tien' => 125_000,
            'trang_thai' => 'pending_payment',
            'ngay_dat' => now(),
        ], 'order_id');

        $paymentId = DB::table('thanh_toan')->insertGetId([
            'order_id' => $orderId,
            'phuong_thuc' => $provider,
            'so_tien' => 125_000,
            'trang_thai' => 'pending',
            'ngay_thanh_toan' => null,
        ], 'payment_id');

        $plantId = DB::table('cay_canh')->insertGetId([
            'ten_cay' => 'Monstera test',
            'so_luong' => 7,
        ], 'plant_id');

        DB::table('chi_tiet_don_hang')->insert([
            'order_id' => $orderId,
            'plant_id' => $plantId,
            'so_luong' => 3,
        ]);

        $merchantTransactionId = 'GS-20260814-000001';
        $providerRequestId = 'REQ-20260814-000001';
        $transactionId = DB::table('giao_dich_thanh_toan')->insertGetId([
            'payment_id' => $paymentId,
            'provider' => $provider,
            'merchant_transaction_id' => $merchantTransactionId,
            'provider_request_id' => $providerRequestId,
            'provider_transaction_id' => null,
            'amount' => 125_000,
            'status' => 'pending',
            'response_code' => null,
            'response_message' => null,
            'payment_url' => null,
            'expires_at' => $expiresAt ?? CarbonImmutable::now()->addMinutes(15),
            'paid_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'transaction_id');

        return [
            'order_id' => $orderId,
            'payment_id' => $paymentId,
            'transaction_id' => $transactionId,
            'plant_id' => $plantId,
            'merchant_transaction_id' => $merchantTransactionId,
            'provider_request_id' => $providerRequestId,
            'transaction' => GiaoDichThanhToan::findOrFail($transactionId),
        ];
    }

    private function paymentCallback(
        string $merchantOrderId,
        string $providerRequestId,
        string $providerTransactionId,
        int $amount = 125_000,
        bool $successful = true,
        int|string $responseCode = 0,
    ): PaymentCallbackResult {
        return new PaymentCallbackResult(
            validSignature: true,
            successful: $successful,
            pending: false,
            merchantOrderId: $merchantOrderId,
            providerRequestId: $providerRequestId,
            providerTransactionId: $providerTransactionId,
            amount: $amount,
            responseCode: $responseCode,
            message: $successful ? 'Success' : 'Failed',
        );
    }

    private function plantStock(int $plantId): int
    {
        return (int) DB::table('cay_canh')
            ->where('plant_id', $plantId)
            ->value('so_luong');
    }

    private function createPaymentTables(): void
    {
        Schema::create('don_hang', function (Blueprint $table): void {
            $table->bigIncrements('order_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('tong_tien', 15, 2);
            $table->string('trang_thai', 50);
            $table->dateTime('ngay_dat')->nullable();
        });

        Schema::create('thanh_toan', function (Blueprint $table): void {
            $table->bigIncrements('payment_id');
            $table->unsignedBigInteger('order_id');
            $table->string('phuong_thuc', 30);
            $table->decimal('so_tien', 15, 2);
            $table->string('trang_thai', 30);
            $table->dateTime('ngay_thanh_toan')->nullable();
        });

        Schema::create('giao_dich_thanh_toan', function (Blueprint $table): void {
            $table->bigIncrements('transaction_id');
            $table->unsignedBigInteger('payment_id');
            $table->string('provider', 20);
            $table->string('merchant_transaction_id', 100);
            $table->string('provider_request_id', 100)->nullable();
            $table->string('provider_transaction_id', 100)->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('status', 30)->default('pending');
            $table->string('response_code', 50)->nullable();
            $table->string('response_message', 500)->nullable();
            $table->text('payment_url')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cay_canh', function (Blueprint $table): void {
            $table->bigIncrements('plant_id');
            $table->string('ten_cay');
            $table->integer('so_luong');
        });

        Schema::create('chi_tiet_don_hang', function (Blueprint $table): void {
            $table->bigIncrements('order_detail_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('plant_id');
            $table->integer('so_luong');
        });
    }

    private function dropPaymentTables(): void
    {
        Schema::dropIfExists('chi_tiet_don_hang');
        Schema::dropIfExists('cay_canh');
        Schema::dropIfExists('giao_dich_thanh_toan');
        Schema::dropIfExists('thanh_toan');
        Schema::dropIfExists('don_hang');
    }
}
