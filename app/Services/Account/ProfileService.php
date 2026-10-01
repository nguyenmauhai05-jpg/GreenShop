<?php

namespace App\Services\Account;

use App\Models\DiaChi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfileService
{
    public function migrateLegacyAvatar($user): void
    {
        $oldPath = (string) ($user->anh_dai_dien ?? '');
        if (!str_starts_with($oldPath, 'storage/avatars/')) {
            return;
        }

        $fileName = basename($oldPath);
        $oldFile = storage_path('app/public/avatars/' . $fileName);
        if (!is_file($oldFile)) {
            return;
        }

        $newDirectory = public_path('uploads/avatars');
        if (!is_dir($newDirectory)) {
            mkdir($newDirectory, 0755, true);
        }

        $newFile = $newDirectory . DIRECTORY_SEPARATOR . $fileName;
        if (!is_file($newFile)) {
            @copy($oldFile, $newFile);
        }

        if (is_file($newFile)) {
            $user->anh_dai_dien = 'uploads/avatars/' . $fileName;
            $user->save();
        }
    }

    public function defaultAddress($user): ?DiaChi
    {
        return $user->diaChis()
            ->orderByDesc('mac_dinh')
            ->orderBy('address_id')
            ->first();
    }

    public function validator(Request $request)
    {
        $request->merge([
            'ho_ten' => trim((string) $request->input('ho_ten')),
            'so_dien_thoai' => trim((string) $request->input('so_dien_thoai')),
            'tinh_thanh' => trim((string) $request->input('tinh_thanh')),
            'quan_huyen' => '',
            'phuong_xa' => trim((string) $request->input('phuong_xa')),
            'dia_chi' => trim((string) $request->input('dia_chi')),
            'latitude' => $request->filled('latitude') ? trim((string) $request->input('latitude')) : null,
            'longitude' => $request->filled('longitude') ? trim((string) $request->input('longitude')) : null,
        ]);

        return Validator::make($request->all(), [
            'ho_ten' => ['bail', 'required', 'string', 'regex:/^[\pL\s.\'’-]+$/u'],
            'so_dien_thoai' => ['bail', 'required', 'string', 'max:20', 'regex:/^(?:\+?84|0)[0-9\s.\-()]{8,18}$/'],
            'tinh_thanh' => ['bail', 'required', 'string'],
            'quan_huyen' => ['nullable', 'string'],
            'phuong_xa' => ['bail', 'required', 'string'],
            'dia_chi' => ['bail', 'required', 'string'],
            'address_id' => ['nullable', 'integer'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'mac_dinh' => ['nullable', 'boolean'],
            'anh_dai_dien' => ['nullable', 'file', 'max:2048'],
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ và tên.',
            'ho_ten.regex' => 'Họ và tên không đúng định dạng.',
            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại.',
            'so_dien_thoai.regex' => 'Số điện thoại không đúng định dạng.',
            'so_dien_thoai.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'tinh_thanh.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'phuong_xa.required' => 'Vui lòng chọn phường/xã.',
            'dia_chi.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'anh_dai_dien.file' => 'Không thể tải ảnh lên. Vui lòng chọn ảnh khác.',
            'anh_dai_dien.max' => 'Kích thước ảnh không được vượt quá 2 MB.',
        ]);
    }

    public function avatarError(Request $request): ?string
    {
        $file = $request->file('anh_dai_dien');
        if (!$file) {
            return null;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            return 'Định dạng ảnh không hợp lệ. Chỉ chấp nhận file JPG, JPEG hoặc PNG.';
        }
        if (!$file->isValid() || @getimagesize($file->getRealPath()) === false) {
            return 'Không thể tải ảnh lên. Vui lòng chọn ảnh khác.';
        }
        $mime = strtolower((string) $file->getMimeType());
        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
            return 'Định dạng ảnh không hợp lệ. Chỉ chấp nhận file JPG, JPEG hoặc PNG.';
        }
        return null;
    }

    public function ownedAddress(Request $request, $user): ?DiaChi
    {
        if (!$request->filled('address_id')) {
            return null;
        }
        $address = DiaChi::find($request->integer('address_id'));
        if (!$address || (int) $address->user_id !== (int) $user->user_id) {
            abort(403, 'Bạn không có quyền chỉnh sửa địa chỉ này.');
        }
        return $address;
    }

    public function update(Request $request, $user, ?DiaChi $address): void
    {
        $file = $request->file('anh_dai_dien');
        $newPath = null;
        $oldPath = $user->anh_dai_dien;

        try {
            if ($file) {
                $directory = public_path('uploads/avatars');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $extension = strtolower($file->getClientOriginalExtension());
                $fileName = 'avatar_' . $user->user_id . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
                $file->move($directory, $fileName);
                $newPath = 'uploads/avatars/' . $fileName;
            }

            DB::transaction(function () use ($request, $user, $address, $newPath) {
                $user->ho_ten = $request->ho_ten;
                $user->so_dien_thoai = $request->so_dien_thoai;
                if ($newPath) {
                    $user->anh_dai_dien = $newPath;
                }
                $user->save();

                $isDefault = $request->boolean('mac_dinh');
                if ($isDefault) {
                    DiaChi::where('user_id', $user->user_id)->update(['mac_dinh' => 0]);
                }

                $address ??= new DiaChi();
                $address->user_id = $user->user_id;
                $address->nguoi_nhan = $request->ho_ten;
                $address->so_dien_thoai = $request->so_dien_thoai;
                $address->tinh_thanh = $request->tinh_thanh;
                $address->quan_huyen = null;
                $address->phuong_xa = $request->phuong_xa;
                $address->dia_chi = $request->dia_chi;
                $address->latitude = $request->filled('latitude') ? (float) $request->input('latitude') : null;
                $address->longitude = $request->filled('longitude') ? (float) $request->input('longitude') : null;
                $address->mac_dinh = $isDefault ? 1 : 0;
                $address->save();
            });

            if ($newPath && is_string($oldPath) && $oldPath !== '') {
                $this->deleteAvatar($oldPath);
            }
        } catch (\Throwable $e) {
            if ($newPath) {
                $this->deleteAvatar($newPath);
            }
            throw $e;
        }
    }

    private function deleteAvatar(string $path): void
    {
        if (str_starts_with($path, 'uploads/avatars/')) {
            $file = public_path($path);
        } elseif (str_starts_with($path, 'storage/avatars/')) {
            $file = storage_path('app/public/' . str_replace('storage/', '', $path));
        } else {
            return;
        }
        if (is_file($file)) {
            @unlink($file);
        }
    }
}
