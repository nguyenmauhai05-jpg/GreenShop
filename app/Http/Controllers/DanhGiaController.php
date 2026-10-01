<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDonHang;
use App\Models\DanhGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DanhGiaController extends Controller
{
    public function index(Request $request)
    {
        $userId = (int) Auth::id();
        $tab = $request->query('tab', 'pending');
        $tab = in_array($tab, ['pending', 'reviewed'], true) ? $tab : 'pending';

        $eligibleBase = ChiTietDonHang::query()
            ->whereHas('donHang', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereIn('trang_thai', [
                        'delivered', 'da_giao', 'Đã giao', 'đã giao',
                        'completed', 'hoan_thanh', 'da_hoan_thanh', 'Đã hoàn thành', 'đã hoàn thành',
                    ]);
            })
            ->whereDoesntHave('danhGia');

        $reviewedBase = DanhGia::query()
            ->where('user_id', $userId);

        $eligibleCount = (clone $eligibleBase)->count();
        $reviewedCount = (clone $reviewedBase)->count();

        if ($tab === 'reviewed') {
            $eligibleDetails = collect();
            $reviewed = $reviewedBase
                ->with(['cayCanh.danhMuc', 'chiTietDonHang.donHang'])
                ->orderByDesc('ngay_danh_gia')
                ->orderByDesc('review_id')
                ->paginate(8)
                ->withQueryString();
        } else {
            $eligibleDetails = $eligibleBase
                ->with(['donHang', 'cayCanh.danhMuc'])
                ->orderByDesc('order_id')
                ->paginate(8)
                ->withQueryString();
            $reviewed = collect();
        }

        return view('danh_gia.index', compact(
            'eligibleDetails',
            'reviewed',
            'eligibleCount',
            'reviewedCount',
            'tab'
        ));
    }

    public function create(ChiTietDonHang $orderDetail)
    {
        $this->ensureEligible($orderDetail);
        $orderDetail->load(['donHang', 'cayCanh.danhMuc']);

        return view('danh_gia.form', compact('orderDetail'));
    }

    public function store(Request $request, ChiTietDonHang $orderDetail)
    {
        $this->ensureEligible($orderDetail);

        $validated = $request->validate([
            'so_sao' => ['required', 'integer', 'between:1,5'],
            'noi_dung' => ['required', 'string', 'min:5', 'max:2000'],
            'hinh_anh' => ['nullable', 'array', 'max:5'],
            'hinh_anh.*' => ['file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'so_sao.required' => 'Vui lòng chọn số sao đánh giá.',
            'so_sao.between' => 'Số sao phải từ 1 đến 5.',
            'noi_dung.required' => 'Vui lòng nhập nội dung đánh giá.',
            'noi_dung.min' => 'Nội dung đánh giá phải có ít nhất 5 ký tự.',
            'noi_dung.max' => 'Nội dung đánh giá không được vượt quá 2000 ký tự.',
            'hinh_anh.max' => 'Bạn chỉ được tải lên tối đa 5 hình ảnh.',
            'hinh_anh.*.image' => 'Tệp tải lên phải là hình ảnh.',
            'hinh_anh.*.mimes' => 'Ảnh chỉ chấp nhận định dạng JPG, JPEG hoặc PNG.',
            'hinh_anh.*.max' => 'Mỗi ảnh không được vượt quá 5MB.',
        ]);

        if (DanhGia::where('order_detail_id', $orderDetail->order_detail_id)->exists()) {
            return redirect()->route('danh-gia.index', ['tab' => 'reviewed'])
                ->with('warning', 'Sản phẩm trong đơn hàng này đã được đánh giá.');
        }

        $savedImages = [];

        try {
            foreach ($request->file('hinh_anh', []) as $image) {
                $dir = public_path('uploads/reviews');
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $extension = strtolower($image->getClientOriginalExtension() ?: 'jpg');
                $fileName = 'review_' . Auth::id() . '_' . $orderDetail->order_detail_id . '_' . uniqid() . '.' . $extension;
                $image->move($dir, $fileName);
                $savedImages[] = 'uploads/reviews/' . $fileName;
            }

            DB::transaction(function () use ($validated, $orderDetail, $savedImages) {
                DanhGia::create([
                    'user_id' => Auth::id(),
                    'plant_id' => $orderDetail->plant_id,
                    'order_detail_id' => $orderDetail->order_detail_id,
                    'so_sao' => $validated['so_sao'],
                    'noi_dung' => trim($validated['noi_dung']),
                    'hinh_anh' => $savedImages,
                    'trang_thai' => 'da_duyet',
                    'ngay_danh_gia' => now(),
                ]);
            }, 3);
        } catch (\Throwable $e) {
            foreach ($savedImages as $path) {
                $fullPath = public_path($path);
                if (is_file($fullPath)) {
                    @unlink($fullPath);
                }
            }

            Log::error('Gui danh gia GreenShop that bai', [
                'user_id' => Auth::id(),
                'order_detail_id' => $orderDetail->order_detail_id,
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Không thể gửi đánh giá. Vui lòng thử lại sau.');
        }

        return redirect()->route('danh-gia.index', ['tab' => 'reviewed'])
            ->with('success', 'Gửi đánh giá thành công. Đánh giá của bạn đã được hiển thị công khai.');
    }

    private function ensureEligible(ChiTietDonHang $orderDetail): void
    {
        $orderDetail->loadMissing('donHang');

        if (!$orderDetail->donHang || (int) $orderDetail->donHang->user_id !== (int) Auth::id()) {
            abort(403, 'Bạn không có quyền đánh giá sản phẩm này.');
        }

        $status = $orderDetail->donHang->normalizedStatus();
        if (!in_array($status, ['delivered', 'completed'], true)) {
            abort(403, 'Chỉ có thể đánh giá sản phẩm thuộc đơn hàng đã giao thành công.');
        }

        if (DanhGia::where('order_detail_id', $orderDetail->order_detail_id)->exists()) {
            abort(409, 'Sản phẩm trong chi tiết đơn hàng này đã được đánh giá.');
        }
    }
}
