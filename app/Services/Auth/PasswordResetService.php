<?php

namespace App\Services\Auth;

use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetService
{
    public function sendLink(string $email): string
    {
        return Password::sendResetLink(['email' => strtolower(trim($email))]);
    }

    public function reset(array $validated, string $confirmation): string
    {
        return Password::reset([
            'email' => strtolower(trim((string) $validated['email'])),
            'password' => $validated['password'],
            'password_confirmation' => $confirmation,
            'token' => $validated['token'],
        ], function (NguoiDung $user, string $password): void {
            $user->mat_khau = Hash::make($password);
            $user->save();
        });
    }
}
