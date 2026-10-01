<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        $type = trim((string) $request->input('type', ''));
        $scope = trim((string) $request->input('scope', ''));
        // So sánh theo giờ Việt Nam để dữ liệu DATETIME trong MySQL không bị lệch 7 tiếng.
        $nowLocal = now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');

        $query = Voucher::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ma_voucher', 'like', '%' . $search . '%')
                    ->orWhere('ten_voucher', 'like', '%' . $search . '%')
                    ->orWhere('mo_ta', 'like', '%' . $search . '%');
            });
        }

        if (in_array($scope, ['don_hang', 'van_chuyen'], true)) {
            $query->where('pham_vi', $scope);
        }

        if (in_array($type, ['phan_tram', 'so_tien'], true)) {
            $query->where('loai_giam', $type);
        }

        // Lọc theo trạng thái thực tế, không chỉ theo cột bật/tắt.
        if ($status === 'active') {
            $query->where('trang_thai', 1)
                ->where('ngay_bat_dau', '<=', $nowLocal)
                ->where('ngay_ket_thuc', '>=', $nowLocal)
                ->whereColumn('da_su_dung', '<', 'so_luong');
        } elseif ($status === 'upcoming') {
            $query->where('trang_thai', 1)
                ->where('ngay_bat_dau', '>', $nowLocal)
                ->whereColumn('da_su_dung', '<', 'so_luong');
        } elseif ($status === 'paused') {
            $query->where('trang_thai', 0);
        } elseif ($status === 'expired') {
            $query->where(function ($q) use ($nowLocal) {
                $q->where('ngay_ket_thuc', '<', $nowLocal)
                    ->orWhereColumn('da_su_dung', '>=', 'so_luong');
            });
        }

        $vouchers = $query
            ->orderByDesc('voucher_id')
            ->paginate(10)
            ->withQueryString();

        $tongVoucher = Voucher::count();

        $dangHoatDong = Voucher::where('trang_thai', 1)
            ->where('ngay_bat_dau', '<=', $nowLocal)
            ->where('ngay_ket_thuc', '>=', $nowLocal)
            ->whereColumn('da_su_dung', '<', 'so_luong')
            ->count();

        $sapDienRa = Voucher::where('trang_thai', 1)
            ->where('ngay_bat_dau', '>', $nowLocal)
            ->whereColumn('da_su_dung', '<', 'so_luong')
            ->count();

        $hetHan = Voucher::where(function ($q) use ($nowLocal) {
            $q->where('ngay_ket_thuc', '<', $nowLocal)
                ->orWhereColumn('da_su_dung', '>=', 'so_luong');
        })->count();

        $tamDung = Voucher::where('trang_thai', 0)->count();

        return view('admin.voucher.index', compact(
            'vouchers',
            'tongVoucher',
            'dangHoatDong',
            'sapDienRa',
            'hetHan',
            'tamDung',
            'nowLocal'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateVoucher($request);
        $validated['ma_voucher'] = strtoupper(trim($validated['ma_voucher']));
        $validated['da_su_dung'] = 0;
        $validated['trang_thai'] = (int) $validated['trang_thai'];

        Voucher::create($validated);

        return redirect()
            ->route('admin.voucher.index')
            ->with('success', 'Thêm voucher thành công.');
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $this->validateVoucher($request, $voucher->voucher_id);
        $validated['ma_voucher'] = strtoupper(trim($validated['ma_voucher']));
        $validated['trang_thai'] = (int) $validated['trang_thai'];

        if ((int) $validated['so_luong'] < (int) $voucher->da_su_dung) {
            throw ValidationException::withMessages([
                'so_luong' => 'Số lượng voucher không thể nhỏ hơn số lượt đã sử dụng.',
            ]);
        }

        $voucher->update($validated);

        return redirect()
            ->route('admin.voucher.index')
            ->with('success', 'Cập nhật voucher thành công.');
    }

    public function toggle(Voucher $voucher)
    {
        $newStatus = (bool) $voucher->trang_thai ? 0 : 1;
        $voucher->update(['trang_thai' => $newStatus]);

        return back()->with(
            'success',
            $newStatus === 1 ? 'Đã bật voucher.' : 'Đã tạm dừng voucher.'
        );
    }

    public function destroy(Voucher $voucher)
    {
        if ((int) $voucher->da_su_dung > 0) {
            return back()->with(
                'error',
                'Voucher đã có lượt sử dụng nên không thể xóa. Hãy chuyển voucher sang trạng thái Tạm dừng.'
            );
        }

        $voucher->delete();

        return back()->with('success', 'Xóa voucher thành công.');
    }

    private function validateVoucher(Request $request, ?int $voucherId = null): array
    {
        $validated = $request->validate([
            'ma_voucher' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('vouchers', 'ma_voucher')->ignore($voucherId, 'voucher_id'),
            ],
            'ten_voucher' => ['required', 'string', 'max:150'],
            'mo_ta' => ['nullable', 'string', 'max:500'],
            'pham_vi' => ['required', Rule::in(['don_hang', 'van_chuyen'])],
            'loai_giam' => ['required', Rule::in(['phan_tram', 'so_tien'])],
            'gia_tri_giam' => ['required', 'numeric', 'gt:0'],
            'giam_toi_da' => ['nullable', 'numeric', 'gte:0'],
            'don_hang_toi_thieu' => ['required', 'numeric', 'gte:0'],
            'so_luong' => ['required', 'integer', 'min:1'],
            'ngay_bat_dau' => ['required', 'date'],
            'ngay_ket_thuc' => ['required', 'date', 'after:ngay_bat_dau'],
            // DB đang dùng TINYINT/BOOLEAN: 1 = hoạt động, 0 = tạm dừng.
            'trang_thai' => ['required', Rule::in(['1', '0'])],
        ], [
            'ma_voucher.required' => 'Vui lòng nhập mã voucher.',
            'ma_voucher.regex' => 'Mã voucher chỉ được chứa chữ, số, dấu gạch ngang và gạch dưới.',
            'ma_voucher.unique' => 'Mã voucher đã tồn tại.',
            'ten_voucher.required' => 'Vui lòng nhập tên chương trình.',
            'pham_vi.required' => 'Vui lòng chọn nhóm voucher.',
            'pham_vi.in' => 'Nhóm voucher không hợp lệ.',
            'loai_giam.required' => 'Vui lòng chọn loại giảm.',
            'loai_giam.in' => 'Loại giảm không hợp lệ.',
            'gia_tri_giam.required' => 'Vui lòng nhập giá trị giảm.',
            'gia_tri_giam.gt' => 'Giá trị giảm phải lớn hơn 0.',
            'giam_toi_da.gte' => 'Giảm tối đa không được nhỏ hơn 0.',
            'don_hang_toi_thieu.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu.',
            'don_hang_toi_thieu.gte' => 'Đơn hàng tối thiểu không được nhỏ hơn 0.',
            'so_luong.required' => 'Vui lòng nhập số lượng voucher.',
            'so_luong.min' => 'Số lượng voucher phải ít nhất là 1.',
            'ngay_bat_dau.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'ngay_ket_thuc.required' => 'Vui lòng chọn thời gian kết thúc.',
            'ngay_ket_thuc.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.in' => 'Trạng thái voucher không hợp lệ.',
        ]);

        if ($validated['loai_giam'] === 'phan_tram') {
            if ((float) $validated['gia_tri_giam'] > 100) {
                throw ValidationException::withMessages([
                    'gia_tri_giam' => 'Voucher phần trăm không được giảm quá 100%.',
                ]);
            }

            if ($validated['giam_toi_da'] !== null && (float) $validated['giam_toi_da'] <= 0) {
                $validated['giam_toi_da'] = null;
            }
        } else {
            $validated['giam_toi_da'] = null;
        }

        return $validated;
    }


}
