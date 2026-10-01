<?php

namespace Tests\Feature\Payments;

use App\Http\Middleware\SystemMaintenanceMode;
use App\Models\GiaoDichThanhToan;
use App\Models\NguoiDung;
use App\Services\Payments\OnlinePaymentService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DemoPaymentTest extends TestCase
{
    private string $originalEnvironment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalEnvironment = $this->app->environment();
        $this->withoutMiddleware(SystemMaintenanceMode::class);
        $this->dropTables();
        $this->createTables();

        config([
            'services.payments.demo_mode' => true,
            'services.momo.partner_code' => null,
            'services.momo.access_key' => null,
            'services.momo.secret_key' => null,
            'services.zalopay.app_id' => 0,
            'services.zalopay.key1' => null,
            'services.zalopay.key2' => null,
        ]);
    }

    protected function tearDown(): void
    {
        $this->app['env'] = $this->originalEnvironment;
        $this->dropTables();

        parent::tearDown();
    }

    public function test_demo_mode_enables_unconfigured_gateways_only_in_local_or_testing(): void
    {
        $payments = $this->app->make(OnlinePaymentService::class);

        $this->assertTrue($payments->demoModeEnabled());
        $this->assertSame([
            'MOMO' => ['configured' => true, 'demo' => true, 'mode' => 'demo'],
            'ZALOPAY' => ['configured' => true, 'demo' => true, 'mode' => 'demo'],
        ], $payments->availability());

        $this->app['env'] = 'production';

        $this->assertFalse($payments->demoModeEnabled());
        $this->assertSame([
            'MOMO' => ['configured' => false, 'demo' => false, 'mode' => 'unavailable'],
            'ZALOPAY' => ['configured' => false, 'demo' => false, 'mode' => 'unavailable'],
        ], $payments->availability());
    }

    public function test_start_creates_a_local_demo_url_without_calling_a_real_gateway(): void
    {
        Http::preventStrayRequests();
        $fixture = $this->pendingFixture();

        $updated = $this->app
            ->make(OnlinePaymentService::class)
            ->start($fixture['transaction']);

        $this->assertSame('pending', $updated->status);
        $this->assertSame('DEMO_READY', $updated->response_code);
        $this->assertSame(
            route('thanh-toan.demo.show', $fixture['transaction_id']),
            $updated->payment_url,
        );
        Http::assertNothingSent();
    }

    public function test_owner_can_open_demo_but_another_user_cannot(): void
    {
        $fixture = $this->pendingFixture();
        $this->actingAs($this->user($fixture['user_id']));

        $this->get(route('thanh-toan.demo.show', $fixture['transaction_id']))
            ->assertOk()
            ->assertViewIs('thanh_toan.demo')
            ->assertViewHas('provider', 'MOMO');

        $this->actingAs($this->createUser('other@example.test'));

        $this->get(route('thanh-toan.demo.show', $fixture['transaction_id']))
            ->assertForbidden();
    }

    public function test_demo_routes_return_not_found_when_demo_mode_is_disabled(): void
    {
        $fixture = $this->pendingFixture();
        $this->actingAs($this->user($fixture['user_id']));
        config(['services.payments.demo_mode' => false]);

        $this->get(route('thanh-toan.demo.show', $fixture['transaction_id']))
            ->assertNotFound();
        $this->post(route('thanh-toan.demo.complete', $fixture['transaction_id']))
            ->assertNotFound();
    }

    public function test_owner_can_complete_demo_payment_idempotently(): void
    {
        $fixture = $this->pendingFixture();
        $this->actingAs($this->user($fixture['user_id']));
        $url = route('thanh-toan.demo.complete', $fixture['transaction_id']);

        $this->post($url)
            ->assertRedirect(route('don-hang.show', $fixture['order_id']))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('giao_dich_thanh_toan', [
            'transaction_id' => $fixture['transaction_id'],
            'status' => 'paid',
            'response_code' => 'DEMO_SUCCESS',
            'provider_transaction_id' => 'DEMO-MOMO-'.$fixture['transaction_id'],
        ]);
        $this->assertDatabaseHas('thanh_toan', [
            'payment_id' => $fixture['payment_id'],
            'trang_thai' => 'paid',
        ]);
        $this->assertDatabaseHas('don_hang', [
            'order_id' => $fixture['order_id'],
            'trang_thai' => 'pending_confirmation',
        ]);
        $this->assertSame(7, $this->stock($fixture['plant_id']));

        $this->post($url)
            ->assertRedirect(route('don-hang.show', $fixture['order_id']))
            ->assertSessionHas('success');

        $this->assertSame(7, $this->stock($fixture['plant_id']));
        $this->assertSame(
            1,
            DB::table('giao_dich_thanh_toan')
                ->where('transaction_id', $fixture['transaction_id'])
                ->where('status', 'paid')
                ->count(),
        );
    }

    public function test_cancel_demo_payment_restores_stock_only_once(): void
    {
        $fixture = $this->pendingFixture(provider: 'ZALOPAY');
        $this->actingAs($this->user($fixture['user_id']));
        $url = route('thanh-toan.demo.cancel', $fixture['transaction_id']);

        $this->post($url)
            ->assertRedirect(route('don-hang.show', $fixture['order_id']))
            ->assertSessionHas('warning');

        $this->assertDatabaseHas('giao_dich_thanh_toan', [
            'transaction_id' => $fixture['transaction_id'],
            'status' => 'failed',
            'response_code' => 'DEMO_CANCELLED',
            'provider_transaction_id' => null,
        ]);
        $this->assertDatabaseHas('thanh_toan', [
            'payment_id' => $fixture['payment_id'],
            'trang_thai' => 'failed',
        ]);
        $this->assertDatabaseHas('don_hang', [
            'order_id' => $fixture['order_id'],
            'trang_thai' => 'cancelled',
        ]);
        $this->assertSame(10, $this->stock($fixture['plant_id']));

        $this->post($url)->assertRedirect(route('don-hang.show', $fixture['order_id']));

        $this->assertSame(10, $this->stock($fixture['plant_id']));
    }

    public function test_another_user_cannot_complete_someone_elses_demo_payment(): void
    {
        $fixture = $this->pendingFixture();
        $this->actingAs($this->createUser('attacker@example.test'));

        $this->post(route('thanh-toan.demo.complete', $fixture['transaction_id']))
            ->assertForbidden();

        $this->assertDatabaseHas('giao_dich_thanh_toan', [
            'transaction_id' => $fixture['transaction_id'],
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('don_hang', [
            'order_id' => $fixture['order_id'],
            'trang_thai' => 'pending_payment',
        ]);
        $this->assertSame(7, $this->stock($fixture['plant_id']));
    }

    public function test_expired_demo_payment_cannot_be_completed_and_restores_stock(): void
    {
        $fixture = $this->pendingFixture(expired: true);
        $this->actingAs($this->user($fixture['user_id']));

        $this->post(route('thanh-toan.demo.complete', $fixture['transaction_id']))
            ->assertRedirect(route('don-hang.show', $fixture['order_id']))
            ->assertSessionHas('error');

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
        $this->assertSame(10, $this->stock($fixture['plant_id']));
    }

    /**
     * @return array{
     *     user_id: int,
     *     order_id: int,
     *     payment_id: int,
     *     transaction_id: int,
     *     plant_id: int,
     *     transaction: GiaoDichThanhToan
     * }
     */
    private function pendingFixture(string $provider = 'MOMO', bool $expired = false): array
    {
        $user = $this->createUser('owner@example.test');
        $orderId = DB::table('don_hang')->insertGetId([
            'order_code' => 'GS260814-DEMO01',
            'user_id' => $user->user_id,
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
            'ten_cay' => 'Monstera demo',
            'gia' => 125_000,
            'so_luong' => 7,
        ], 'plant_id');
        DB::table('chi_tiet_don_hang')->insert([
            'order_id' => $orderId,
            'plant_id' => $plantId,
            'don_gia' => 125_000,
            'so_luong' => 3,
        ]);
        $transactionId = DB::table('giao_dich_thanh_toan')->insertGetId([
            'payment_id' => $paymentId,
            'provider' => $provider,
            'merchant_transaction_id' => 'GS260814-DEMO01',
            'provider_request_id' => 'REQ-DEMO-000001',
            'provider_transaction_id' => null,
            'amount' => 125_000,
            'status' => 'pending',
            'response_code' => null,
            'response_message' => null,
            'payment_url' => null,
            'expires_at' => $expired ? now()->subMinute() : now()->addMinutes(15),
            'paid_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'transaction_id');

        return [
            'user_id' => (int) $user->user_id,
            'order_id' => $orderId,
            'payment_id' => $paymentId,
            'transaction_id' => $transactionId,
            'plant_id' => $plantId,
            'transaction' => GiaoDichThanhToan::findOrFail($transactionId),
        ];
    }

    private function createUser(string $email): NguoiDung
    {
        $userId = DB::table('nguoi_dung')->insertGetId([
            'ho_ten' => 'Demo User',
            'email' => $email,
            'mat_khau' => 'not-used-in-test',
            'trang_thai' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ], 'user_id');

        return NguoiDung::findOrFail($userId);
    }

    private function user(int $userId): NguoiDung
    {
        return NguoiDung::findOrFail($userId);
    }

    private function stock(int $plantId): int
    {
        return (int) DB::table('cay_canh')
            ->where('plant_id', $plantId)
            ->value('so_luong');
    }

    private function createTables(): void
    {
        Schema::create('nguoi_dung', function (Blueprint $table): void {
            $table->bigIncrements('user_id');
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->string('mat_khau');
            $table->string('trang_thai')->nullable();
            $table->timestamps();
        });

        Schema::create('don_hang', function (Blueprint $table): void {
            $table->bigIncrements('order_id');
            $table->string('order_code')->nullable();
            $table->unsignedBigInteger('user_id');
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
            $table->decimal('gia', 15, 2)->nullable();
            $table->integer('so_luong')->default(0);
        });

        Schema::create('chi_tiet_don_hang', function (Blueprint $table): void {
            $table->bigIncrements('order_detail_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('plant_id');
            $table->decimal('don_gia', 15, 2);
            $table->unsignedInteger('so_luong');
        });

        // The shared customer header renders the cart badge on the demo page.
        Schema::create('gio_hang', function (Blueprint $table): void {
            $table->bigIncrements('cart_id');
            $table->unsignedBigInteger('user_id');
        });

        Schema::create('chi_tiet_gio_hang', function (Blueprint $table): void {
            $table->bigIncrements('cart_detail_id');
            $table->unsignedBigInteger('cart_id');
            $table->unsignedInteger('so_luong');
        });
    }

    private function dropTables(): void
    {
        Schema::dropIfExists('chi_tiet_gio_hang');
        Schema::dropIfExists('gio_hang');
        Schema::dropIfExists('chi_tiet_don_hang');
        Schema::dropIfExists('cay_canh');
        Schema::dropIfExists('giao_dich_thanh_toan');
        Schema::dropIfExists('thanh_toan');
        Schema::dropIfExists('don_hang');
        Schema::dropIfExists('nguoi_dung');
    }
}
