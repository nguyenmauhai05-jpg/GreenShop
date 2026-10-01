<?php

namespace App\Services\Auth;

use App\Models\NguoiDung;
use App\Models\VaiTro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthenticationService
{
    public function register(array $data): void
    {
        DB::transaction(function () use ($data) {
            $role = VaiTro::firstOrCreate(
                ['ten_vai_tro' => 'Khách hàng'],
                ['mo_ta' => 'Tài khoản khách hàng GreenShop']
            );
            NguoiDung::create([
                'role_id' => $role->role_id,
                'ho_ten' => trim((string) $data['ho_ten']),
                'email' => strtolower(trim((string) $data['email'])),
                'so_dien_thoai' => trim((string) $data['so_dien_thoai']),
                'mat_khau' => Hash::make((string) $data['mat_khau']),
                'trang_thai' => 1,
            ]);
        });
    }

    public function verify(string $email, string $password): array
    {
        $email = strtolower(trim($email));
        $user = NguoiDung::where('email', $email)->first();
        if (!$user || !Hash::check($password, $user->mat_khau)) {
            return ['ok' => false, 'message' => 'Email hoặc mật khẩu không chính xác.'];
        }
        if (!$user->trang_thai) {
            return ['ok' => false, 'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'];
        }
        $role = DB::table('vai_tro')->where('role_id', $user->role_id)->first();
        if (!$role) {
            Log::warning('Tài khoản không có vai trò hợp lệ', [
                'user_id' => $user->user_id ?? null, 'email' => $user->email, 'role_id' => $user->role_id,
            ]);
            return ['ok' => false, 'message' => 'Tài khoản chưa được phân quyền.'];
        }
        return ['ok' => true, 'user' => $user, 'is_admin' => mb_strtolower(trim((string) $role->ten_vai_tro)) === 'admin'];
    }
}
