<?php

namespace App\Services\Categories;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function pageData(Request $request): array
    {
        $keyword = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        $query = $this->query($keyword, $status, true);
        $danhMucs = $query->orderByDesc('dm.category_id')->paginate(10)->withQueryString();

        return [
            'danhMucs' => $danhMucs,
            'tongDanhMuc' => DB::table('danh_muc')->count(),
            'tongHienThi' => DB::table('danh_muc')->where('trang_thai', 'Hiển thị')->count(),
            'tongAn' => DB::table('danh_muc')->where('trang_thai', 'Ẩn')->count(),
        ];
    }

    public function emptyPage(Request $request): array
    {
        return [
            'danhMucs' => new LengthAwarePaginator([], 0, 10, 1, ['path' => $request->url(), 'query' => $request->query()]),
            'tongDanhMuc' => 0, 'tongHienThi' => 0, 'tongAn' => 0, 'loiHeThong' => true,
        ];
    }

    public function create(array $validated): void
    {
        DB::table('danh_muc')->insert([
            'ten_danh_muc' => trim((string) $validated['ten_danh_muc']),
            'mo_ta' => !empty($validated['mo_ta']) ? trim((string) $validated['mo_ta']) : null,
            'hinh_anh' => null,
            'trang_thai' => $validated['trang_thai'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function update(int $id, array $validated): bool
    {
        if (!DB::table('danh_muc')->where('category_id', $id)->exists()) return false;
        DB::table('danh_muc')->where('category_id', $id)->update([
            'ten_danh_muc' => trim((string) $validated['ten_danh_muc']),
            'mo_ta' => !empty($validated['mo_ta']) ? trim((string) $validated['mo_ta']) : null,
            'trang_thai' => $validated['trang_thai'],
            'updated_at' => now(),
        ]);
        return true;
    }

    public function delete(int $id): array
    {
        if (!DB::table('danh_muc')->where('category_id', $id)->exists()) {
            return ['ok' => false, 'message' => 'Danh mục không tồn tại.'];
        }
        $count = DB::table('cay_canh')->where('category_id', $id)->count();
        if ($count > 0) {
            return ['ok' => false, 'message' => 'Danh mục đang chứa ' . $count . ' cây nên không thể xóa. Vui lòng chuyển cây sang danh mục khác trước khi xóa.'];
        }
        DB::table('danh_muc')->where('category_id', $id)->delete();
        return ['ok' => true, 'message' => 'Xóa danh mục thành công.'];
    }

    public function exportRows(Request $request)
    {
        $keyword = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        return $this->query($keyword, $status, false)->orderByDesc('dm.category_id')->get();
    }

    private function query(string $keyword, string $status, bool $withUpdated)
    {
        $columns = ['dm.category_id','dm.ten_danh_muc','dm.mo_ta','dm.hinh_anh','dm.trang_thai','dm.created_at'];
        if ($withUpdated) $columns[] = 'dm.updated_at';
        $columns[] = DB::raw('COUNT(cc.plant_id) AS so_cay');

        $query = DB::table('danh_muc as dm')
            ->leftJoin('cay_canh as cc', 'cc.category_id', '=', 'dm.category_id')
            ->select($columns)
            ->groupBy('dm.category_id','dm.ten_danh_muc','dm.mo_ta','dm.hinh_anh','dm.trang_thai','dm.created_at');
        if ($withUpdated) $query->groupBy('dm.updated_at');
        if ($keyword !== '') $query->where('dm.ten_danh_muc', 'LIKE', '%' . $keyword . '%');
        if (in_array($status, ['Hiển thị','Ẩn'], true)) $query->where('dm.trang_thai', $status);
        return $query;
    }
}
