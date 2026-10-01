<?php

namespace App\Http\Controllers;

use App\Http\Requests\DangKyRequest;
use App\Http\Requests\DangNhapRequest;
use App\Services\Auth\AuthenticationService;
use App\Services\Auth\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function dangKy(DangKyRequest $request, AuthenticationService $auth)
    {
        try {
            $auth->register($request->validated());
            return redirect()->route('dang-nhap')->with('success', 'Đăng ký tài khoản thành công. Vui lòng đăng nhập lại.');
        } catch (\Throwable $e) {
            Log::error('Đăng ký GreenShop thất bại', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Đăng ký không thành công. Vui lòng thử lại sau.');
        }
    }

    public function dangNhap(DangNhapRequest $request, AuthenticationService $auth)
    {
        try {
            $result = $auth->verify((string) $request->email, (string) $request->mat_khau);
            if (!$result['ok']) {
                return back()->withInput($request->only('email'))->with('error', $result['message']);
            }
            Auth::login($result['user']);
            $request->session()->regenerate();
            return $result['is_admin']
                ? redirect()->route('admin.dashboard')->with('success', 'Đăng nhập quản trị thành công.')
                : redirect()->route('trang-chu')->with('success', 'Đăng nhập thành công.');
        } catch (\Throwable $e) {
            Log::error('Đăng nhập GreenShop thất bại', [
                'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'email' => $request->email ?? null,
            ]);
            return back()->withInput($request->only('email'))->with('error', 'Đăng nhập không thành công. Vui lòng thử lại sau.');
        }
    }

    public function hienThiQuenMatKhau() { return view('auth.quen_mat_khau'); }

    public function guiLienKetDatLai(Request $request, PasswordResetService $passwords)
    {
        $validated = $request->validate(
            ['email' => ['required', 'email', 'max:255']],
            [
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không đúng định dạng.',
                'email.max' => 'Email không được vượt quá 255 ký tự.',
            ]
        );

        $email = strtolower(trim((string) $validated['email']));
        $status = $passwords->sendLink($email);

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()
                ->route('password.sent')
                ->with('reset_email', $email)
                ->with('success', 'Hệ thống sẽ gửi liên kết đặt lại mật khẩu đến địa chỉ email của bạn.');
        }

        if ($status === Password::INVALID_USER) {
            return back()
                ->withInput(['email' => $email])
                ->withErrors(['email' => 'Email chưa được đăng kí']);
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->withInput(['email' => $email])
                ->with('error', 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau.');
        }

        Log::warning('Không thể gửi liên kết đặt lại mật khẩu GreenShop', [
            'email' => $email,
            'status' => $status,
        ]);

        return back()
            ->withInput(['email' => $email])
            ->with('error', 'Không thể gửi liên kết đặt lại mật khẩu. Vui lòng thử lại sau.');
    }

    public function hienThiEmailDaGui(Request $request)
    {
        return view('auth.email_da_gui', ['email' => $request->session()->get('reset_email', '')]);
    }

    public function hienThiDatLaiMatKhau(Request $request, string $token)
    {
        return view('auth.dat_lai_mat_khau', ['token' => $token, 'email' => (string) $request->query('email', '')]);
    }

    public function datLaiMatKhau(Request $request, PasswordResetService $passwords)
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:72'],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ], [
            'token.required' => 'Liên kết đặt lại mật khẩu không hợp lệ.',
            'email.required' => 'Thiếu email đặt lại mật khẩu.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'password.required' => 'Mật khẩu mới không hợp lệ.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.max' => 'Mật khẩu mới không được vượt quá 72 ký tự.',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu mới.',
            'password_confirmation.same' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $status = $passwords->reset(
            $validated,
            (string) $validated['password_confirmation']
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('password.success')
                ->with('success', 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập bằng mật khẩu mới.');
        }
        $message = match ($status) {
            Password::INVALID_TOKEN => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
            Password::INVALID_USER => 'Không tìm thấy tài khoản phù hợp với email này.',
            default => 'Không thể đặt lại mật khẩu. Vui lòng yêu cầu một liên kết mới.',
        };
        return back()->withInput($request->only('email'))->with('error', $message);
    }

    public function hienThiDatLaiThanhCong() { return view('auth.dat_lai_thanh_cong'); }

    public function dangXuat(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('dang-nhap');
    }
}
