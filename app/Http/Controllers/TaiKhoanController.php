<?php

namespace App\Http\Controllers;

use App\Models\DiaChi;
use App\Services\Account\AddressBookService;
use App\Services\Account\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaiKhoanController extends Controller
{
    public function hoSo(ProfileService $profiles)
    {
        $nguoiDung = Auth::user();
        $profiles->migrateLegacyAvatar($nguoiDung);
        $diaChi = $profiles->defaultAddress($nguoiDung);

        return view('tai_khoan.ho_so', compact('nguoiDung', 'diaChi'));
    }

    public function capNhatHoSo(Request $request, ProfileService $profiles)
    {
        $nguoiDung = Auth::user();
        $validator = $profiles->validator($request);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()
                ->with('error', 'Vui lòng kiểm tra lại thông tin đã nhập.');
        }

        if ($avatarError = $profiles->avatarError($request)) {
            return back()->withErrors(['anh_dai_dien' => $avatarError])->withInput()
                ->with('error', 'Vui lòng kiểm tra lại thông tin đã nhập.');
        }

        $diaChi = $profiles->ownedAddress($request, $nguoiDung);

        try {
            $profiles->update($request, $nguoiDung, $diaChi);
            return redirect()->route('tai-khoan.ho-so')
                ->with('success', 'Cập nhật hồ sơ thành công.');
        } catch (\Throwable $e) {
            Log::error('Cap nhat ho so GreenShop that bai', [
                'user_id' => $nguoiDung->user_id,
                'message' => $e->getMessage(),
            ]);
            return back()->withInput()->with('error', 'Không thể cập nhật hồ sơ. Vui lòng thử lại sau.');
        }
    }

    public function soDiaChi(AddressBookService $addresses)
    {
        $nguoiDung = Auth::user();
        $diaChis = $addresses->listFor($nguoiDung);
        return view('tai_khoan.so_dia_chi', compact('nguoiDung', 'diaChis'));
    }

    public function themDiaChi(Request $request, AddressBookService $addresses)
    {
        $nguoiDung = Auth::user();
        $validator = $addresses->validator($request);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()
                ->with('error', 'Vui lòng kiểm tra lại thông tin địa chỉ.');
        }

        try {
            $addresses->create($request, $nguoiDung);
            return redirect()->route('tai-khoan.so-dia-chi')
                ->with('success', 'Đã thêm địa chỉ nhận hàng mới.');
        } catch (\Throwable $e) {
            Log::error('Them dia chi GreenShop that bai', [
                'user_id' => $nguoiDung->user_id,
                'message' => $e->getMessage(),
            ]);
            return back()->withInput()->with('error', 'Không thể thêm địa chỉ. Vui lòng thử lại sau.');
        }
    }

    public function capNhatDiaChi(Request $request, DiaChi $diaChi, AddressBookService $addresses)
    {
        $nguoiDung = Auth::user();
        $addresses->ensureOwned($diaChi, (int) $nguoiDung->user_id);
        $request->merge(['address_id' => $diaChi->address_id]);
        $validator = $addresses->validator($request);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()
                ->with('error', 'Vui lòng kiểm tra lại thông tin địa chỉ.');
        }

        try {
            $addresses->update($request, $nguoiDung, $diaChi);
            return redirect()->route('tai-khoan.so-dia-chi')
                ->with('success', 'Đã cập nhật địa chỉ nhận hàng.');
        } catch (\Throwable $e) {
            Log::error('Cap nhat dia chi GreenShop that bai', [
                'user_id' => $nguoiDung->user_id,
                'address_id' => $diaChi->address_id,
                'message' => $e->getMessage(),
            ]);
            return back()->withInput()->with('error', 'Không thể cập nhật địa chỉ. Vui lòng thử lại sau.');
        }
    }

    public function datDiaChiMacDinh(DiaChi $diaChi, AddressBookService $addresses)
    {
        $nguoiDung = Auth::user();
        $addresses->setDefault($nguoiDung, $diaChi);
        return redirect()->route('tai-khoan.so-dia-chi')->with('success', 'Đã đặt địa chỉ mặc định.');
    }

    public function xoaDiaChi(DiaChi $diaChi, AddressBookService $addresses)
    {
        $nguoiDung = Auth::user();
        if (!$addresses->delete($nguoiDung, $diaChi)) {
            return redirect()->route('tai-khoan.so-dia-chi')
                ->with('error', 'Địa chỉ này đã được dùng trong đơn hàng nên không thể xóa. Bạn vẫn có thể thêm địa chỉ mới và đặt làm mặc định.');
        }
        return redirect()->route('tai-khoan.so-dia-chi')->with('success', 'Đã xóa địa chỉ nhận hàng.');
    }
}
