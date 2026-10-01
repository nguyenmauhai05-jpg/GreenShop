<?php

namespace App\Services\Account;

use App\Models\DiaChi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AddressBookService
{
    public function listFor($user)
    {
        $hasDefault = DiaChi::where('user_id', $user->user_id)->where('mac_dinh', 1)->exists();
        if (!$hasDefault) {
            $first = DiaChi::where('user_id', $user->user_id)->orderBy('address_id')->first();
            if ($first) {
                $first->mac_dinh = 1;
                $first->save();
            }
        }

        return $user->diaChis()->orderByDesc('mac_dinh')->orderByDesc('address_id')->get();
    }

    public function validator(Request $request)
    {
        $request->merge([
            'nguoi_nhan' => trim((string) $request->input('nguoi_nhan')),
            'so_dien_thoai' => trim((string) $request->input('so_dien_thoai')),
            'tinh_thanh' => trim((string) $request->input('tinh_thanh')),
            'phuong_xa' => trim((string) $request->input('phuong_xa')),
            'dia_chi' => trim((string) $request->input('dia_chi')),
            'latitude' => $request->filled('latitude') ? trim((string) $request->input('latitude')) : null,
            'longitude' => $request->filled('longitude') ? trim((string) $request->input('longitude')) : null,
        ]);

        return Validator::make($request->all(), [
            'address_id' => ['nullable', 'integer'],
            'address_mode' => ['nullable', 'in:manual,map'],
            'nguoi_nhan' => ['bail', 'required', 'string', 'max:100', 'regex:/^[\pL\s.\'’-]+$/u'],
            'so_dien_thoai' => ['bail', 'required', 'string', 'max:20', 'regex:/^(?:\+?84|0)[0-9\s.\-()]{8,18}$/'],
            'tinh_thanh' => ['bail', 'required', 'string', 'max:100'],
            'phuong_xa' => ['bail', 'required', 'string', 'max:100'],
            'dia_chi' => ['bail', 'required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'mac_dinh' => ['nullable', 'boolean'],
        ], [
            'nguoi_nhan.required' => 'Vui lòng nhập tên người nhận.',
            'nguoi_nhan.regex' => 'Tên người nhận không đúng định dạng.',
            'nguoi_nhan.max' => 'Tên người nhận không được vượt quá 100 ký tự.',
            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại.',
            'so_dien_thoai.regex' => 'Số điện thoại không đúng định dạng.',
            'tinh_thanh.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'phuong_xa.required' => 'Vui lòng chọn phường/xã.',
            'dia_chi.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'dia_chi.max' => 'Địa chỉ chi tiết không được vượt quá 255 ký tự.',
            'latitude.numeric' => 'Vĩ độ không hợp lệ.',
            'longitude.numeric' => 'Kinh độ không hợp lệ.',
        ]);
    }

    public function create(Request $request, $user): void
    {
        DB::transaction(function () use ($request, $user) {
            $hasAddress = DiaChi::where('user_id', $user->user_id)->exists();
            $isDefault = !$hasAddress || $request->boolean('mac_dinh');
            if ($isDefault) {
                DiaChi::where('user_id', $user->user_id)->update(['mac_dinh' => 0]);
            }
            DiaChi::create($this->payload($request, $user->user_id, $isDefault));
        });
    }

    public function update(Request $request, $user, DiaChi $address): void
    {
        $this->ensureOwned($address, (int) $user->user_id);
        DB::transaction(function () use ($request, $user, $address) {
            $isDefault = $request->boolean('mac_dinh');
            if (!$isDefault && $address->mac_dinh) {
                $hasOtherDefault = DiaChi::where('user_id', $user->user_id)
                    ->where('address_id', '!=', $address->address_id)
                    ->where('mac_dinh', 1)->exists();
                if (!$hasOtherDefault) {
                    $isDefault = true;
                }
            }
            if ($isDefault) {
                DiaChi::where('user_id', $user->user_id)
                    ->where('address_id', '!=', $address->address_id)
                    ->update(['mac_dinh' => 0]);
            }
            $address->fill($this->payload($request, $user->user_id, $isDefault));
            $address->save();
        });
    }

    public function setDefault($user, DiaChi $address): void
    {
        $this->ensureOwned($address, (int) $user->user_id);
        DB::transaction(function () use ($user, $address) {
            DiaChi::where('user_id', $user->user_id)->update(['mac_dinh' => 0]);
            $address->mac_dinh = 1;
            $address->save();
        });
    }

    public function delete($user, DiaChi $address): bool
    {
        $this->ensureOwned($address, (int) $user->user_id);
        if ($address->donHangs()->exists()) {
            return false;
        }
        DB::transaction(function () use ($user, $address) {
            $wasDefault = (bool) $address->mac_dinh;
            $address->delete();
            if ($wasDefault) {
                $next = DiaChi::where('user_id', $user->user_id)->orderBy('address_id')->first();
                if ($next) {
                    $next->mac_dinh = 1;
                    $next->save();
                }
            }
        });
        return true;
    }

    public function ensureOwned(DiaChi $address, int $userId): void
    {
        if ((int) $address->user_id !== $userId) {
            abort(403, 'Bạn không có quyền thao tác với địa chỉ này.');
        }
    }

    private function payload(Request $request, int $userId, bool $isDefault): array
    {
        return [
            'user_id' => $userId,
            'nguoi_nhan' => $request->nguoi_nhan,
            'so_dien_thoai' => $request->so_dien_thoai,
            'dia_chi' => $request->dia_chi,
            'phuong_xa' => $request->phuong_xa,
            'quan_huyen' => null,
            'tinh_thanh' => $request->tinh_thanh,
            'latitude' => $request->filled('latitude') ? (float) $request->latitude : null,
            'longitude' => $request->filled('longitude') ? (float) $request->longitude : null,
            'mac_dinh' => $isDefault ? 1 : 0,
        ];
    }
}
