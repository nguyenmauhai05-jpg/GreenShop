<?php

namespace Tests\Feature;

use App\Models\NguoiDung;
use App\Notifications\DatLaiMatKhauNotification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->increments('user_id');
            $table->unsignedInteger('role_id')->nullable();
            $table->string('ho_ten')->nullable();
            $table->string('email')->unique();
            $table->string('mat_khau');
            $table->string('so_dien_thoai', 20)->nullable();
            $table->boolean('trang_thai')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('nguoi_dung');

        parent::tearDown();
    }

    private function createUser(string $email = 'khachhang@example.com'): NguoiDung
    {
        return NguoiDung::create([
            'ho_ten' => 'Nguyễn Văn A',
            'email' => $email,
            'mat_khau' => Hash::make('MatKhauCu123!'),
            'trang_thai' => true,
        ]);
    }

    public function test_forgot_password_validates_required_email(): void
    {
        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => '   '])
            ->assertRedirect(route('password.request'))
            ->assertSessionHasErrors([
                'email' => 'Vui lòng nhập email.',
            ]);
    }

    public function test_forgot_password_validates_email_format_and_length(): void
    {
        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'khong-phai-email'])
            ->assertSessionHasErrors([
                'email' => 'Email không đúng định dạng.',
            ]);

        $tooLong = str_repeat('a', 244) . '@example.com';

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => $tooLong])
            ->assertSessionHasErrors([
                'email' => 'Email không được vượt quá 255 ký tự.',
            ]);
    }

    public function test_forgot_password_rejects_unregistered_email(): void
    {
        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'chuatontai@example.com'])
            ->assertSessionHasErrors([
                'email' => 'Email chưa được đăng kí',
            ]);
    }

    public function test_forgot_password_sends_reset_notification_and_throttles_repeat_request(): void
    {
        Notification::fake();
        $user = $this->createUser();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.sent'))
            ->assertSessionHas('success', 'Hệ thống sẽ gửi liên kết đặt lại mật khẩu đến địa chỉ email của bạn.');

        Notification::assertSentTo($user, DatLaiMatKhauNotification::class);

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('error', 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau.');
    }

    public function test_reset_password_rejects_whitespace_only_password(): void
    {
        $user = $this->createUser();
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => '        ',
            'password_confirmation' => '        ',
        ])->assertSessionHasErrors([
            'password' => 'Mật khẩu mới không hợp lệ.',
        ]);
    }

    public function test_reset_password_requires_confirmation_and_checks_match(): void
    {
        $user = $this->createUser();
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'MatKhauMoi123!',
            'password_confirmation' => '',
        ])->assertSessionHasErrors([
            'password_confirmation' => 'Vui lòng xác nhận mật khẩu mới.',
        ]);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'MatKhauMoi123!',
            'password_confirmation' => 'KhongTrung123!',
        ])->assertSessionHasErrors([
            'password_confirmation' => 'Xác nhận mật khẩu không khớp.',
        ]);
    }

    public function test_reset_password_updates_hash_and_redirects_to_success_page(): void
    {
        $user = $this->createUser();
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'MatKhauMoi123!',
            'password_confirmation' => 'MatKhauMoi123!',
        ])->assertRedirect(route('password.success'))
            ->assertSessionHas('success', 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập bằng mật khẩu mới.');

        $user->refresh();
        $this->assertTrue(Hash::check('MatKhauMoi123!', $user->mat_khau));
        $this->assertFalse(Hash::check('MatKhauCu123!', $user->mat_khau));
    }
}
