<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDanhMucRequest;
use App\Http\Requests\Admin\UpdateDanhMucRequest;
use App\Services\Categories\CategoryExportService;
use App\Services\Categories\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DanhMucController extends Controller
{
    public function index(Request $request, CategoryService $categories)
    {
        try {
            return view('admin.danh_muc.index', $categories->pageData($request));
        } catch (\Throwable $e) {
            Log::error('Tải danh mục Admin thất bại.', ['user_id' => Auth::id(), 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return view('admin.danh_muc.index', $categories->emptyPage($request));
        }
    }

    public function store(StoreDanhMucRequest $request, CategoryService $categories)
    {
        try {
            $categories->create($request->validated());
            return redirect()->route('admin.danh-muc.index')->with('success', 'Thêm danh mục thành công.');
        } catch (\Throwable $e) {
            Log::error('Thêm danh mục Admin thất bại.', ['user_id' => Auth::id(), 'message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Không thể thêm danh mục. Vui lòng thử lại.');
        }
    }

    public function update(UpdateDanhMucRequest $request, $id, CategoryService $categories)
    {
        try {
            if (!$categories->update((int) $id, $request->validated())) return back()->with('error', 'Danh mục không tồn tại.');
            return redirect()->route('admin.danh-muc.index')->with('success', 'Cập nhật danh mục thành công.');
        } catch (\Throwable $e) {
            Log::error('Cập nhật danh mục Admin thất bại.', ['user_id' => Auth::id(), 'category_id' => $id, 'message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Không thể cập nhật danh mục. Vui lòng thử lại.');
        }
    }

    public function destroy($id, CategoryService $categories)
    {
        try {
            $result = $categories->delete((int) $id);
            return $result['ok']
                ? redirect()->route('admin.danh-muc.index')->with('success', $result['message'])
                : back()->with('error', $result['message']);
        } catch (\Throwable $e) {
            Log::error('Xóa danh mục Admin thất bại.', ['user_id' => Auth::id(), 'category_id' => $id, 'message' => $e->getMessage()]);
            return back()->with('error', 'Không thể xóa danh mục. Vui lòng thử lại.');
        }
    }

    public function exportExcel(Request $request, CategoryExportService $export)
    {
        return $export->excel($request);
    }
}
