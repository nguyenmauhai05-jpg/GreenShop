<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DanhGiaController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);

        $baseQuery = DanhGia::query();

        $hasAdminReplyColumns = true;
        $allCount = (clone $baseQuery)->count();

        $counts = [
            'all' => $allCount,
            'replied' => (clone $baseQuery)->whereNotNull('phan_hoi_admin')->count(),
            'not_replied' => (clone $baseQuery)->whereNull('phan_hoi_admin')->count(),
        ];

        try {
            $reviews = $this->filteredQuery($filters)
                ->with([
                    'nguoiDung',
                    'cayCanh.danhMuc',
                    'chiTietDonHang.donHang',
                ])
                ->orderByDesc('ngay_danh_gia')
                ->orderByDesc('review_id')
                ->paginate(10)
                ->withQueryString();

            $selectedReview = null;
            $selectedId = (int) $request->query('selected', 0);

            if ($selectedId > 0) {
                $selectedReview = DanhGia::with([
                    'nguoiDung',
                    'cayCanh.danhMuc',
                    'chiTietDonHang.donHang',
                ])->find($selectedId);
            }

            if (!$selectedReview && $reviews->count() > 0) {
                $selectedReview = DanhGia::with([
                    'nguoiDung',
                    'cayCanh.danhMuc',
                    'chiTietDonHang.donHang',
                ])->find($reviews->first()->review_id);
            }

            return view('admin.danh_gia.index', compact(
                'reviews',
                'selectedReview',
                'filters',
                'counts',
                'hasAdminReplyColumns'
            ));
        } catch (\Throwable $e) {
            Log::error('Admin tải quản lý đánh giá thất bại', [
                'message' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            return view('admin.danh_gia.index', [
                'reviews' => collect(),
                'selectedReview' => null,
                'filters' => $filters,
                'counts' => $counts,
                'hasAdminReplyColumns' => $hasAdminReplyColumns,
                'loadError' => 'Không thể tải danh sách đánh giá. Vui lòng thử lại.',
            ]);
        }
    }

    /**
     * Admin chỉ được trả lời đánh giá, không duyệt / từ chối / xóa
     * và không được thay đổi nội dung, số sao, hình ảnh gốc.
     */
    public function reply(Request $request, DanhGia $danhGia)
    {
        $validated = $request->validate([
            'phan_hoi_admin' => ['required', 'string', 'max:2000'],
        ], [
            'phan_hoi_admin.required' => 'Vui lòng nhập nội dung trả lời.',
            'phan_hoi_admin.max' => 'Nội dung trả lời không được vượt quá 2000 ký tự.',
        ]);

        try {
            DB::transaction(function () use ($danhGia, $validated) {
                $review = DanhGia::lockForUpdate()->findOrFail($danhGia->review_id);

                // Chỉ cập nhật phản hồi của Admin.
                // Tuyệt đối không thay đổi số sao, nội dung, ảnh hay người đánh giá.
                $review->phan_hoi_admin = trim($validated['phan_hoi_admin']);
                $review->phan_hoi_luc = now();
                $review->save();
            }, 3);

            return redirect()
                ->route('admin.danh-gia.index', array_merge(
                    request()->only(['q', 'stars', 'status', 'date_from', 'date_to']),
                    ['selected' => $danhGia->review_id]
                ))
                ->with('success', 'Đã gửi trả lời đánh giá.');
        } catch (\Throwable $e) {
            Log::error('Admin trả lời đánh giá thất bại', [
                'review_id' => $danhGia->review_id,
                'admin_id' => Auth::id(),
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Không thể gửi trả lời đánh giá. Vui lòng thử lại.');
        }
    }

    private function filteredQuery(array $filters): Builder
    {
        $query = DanhGia::query();

        if ($filters['q'] !== '') {
            $q = $filters['q'];

            $query->where(function (Builder $sub) use ($q) {
                $sub->where('noi_dung', 'like', '%' . $q . '%')
                    ->orWhereHas('cayCanh', function (Builder $plant) use ($q) {
                        $plant->where('ten_cay', 'like', '%' . $q . '%');
                    })
                    ->orWhereHas('nguoiDung', function (Builder $user) use ($q) {
                        $user->where('ho_ten', 'like', '%' . $q . '%')
                            ->orWhere('email', 'like', '%' . $q . '%');
                    });
            });
        }

        if ($filters['stars'] !== 'all') {
            $query->where('so_sao', (int) $filters['stars']);
        }

        if ($filters['status'] === 'replied') {
            $query->whereNotNull('phan_hoi_admin');
        } elseif ($filters['status'] === 'not_replied') {
            $query->whereNull('phan_hoi_admin');
        }

        if ($filters['date_from'] !== '') {
            $query->whereDate('ngay_danh_gia', '>=', $filters['date_from']);
        }

        if ($filters['date_to'] !== '') {
            $query->whereDate('ngay_danh_gia', '<=', $filters['date_to']);
        }

        return $query;
    }

    private function filters(Request $request): array
    {
        $status = (string) $request->query('status', 'all');
        if (!in_array($status, ['all', 'replied', 'not_replied'], true)) {
            $status = 'all';
        }

        $stars = (string) $request->query('stars', 'all');
        if (!in_array($stars, ['all', '1', '2', '3', '4', '5'], true)) {
            $stars = 'all';
        }

        return [
            'q' => mb_substr(trim((string) $request->query('q', '')), 0, 100),
            'stars' => $stars,
            'status' => $status,
            'date_from' => $this->validDate((string) $request->query('date_from', '')),
            'date_to' => $this->validDate((string) $request->query('date_to', '')),
        ];
    }

    private function validDate(string $date): string
    {
        if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return '';
        }

        return $date;
    }


}
