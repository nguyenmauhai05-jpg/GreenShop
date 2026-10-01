<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCayCanhRequest;
use App\Http\Requests\Admin\UpdateCayCanhRequest;
use App\Services\Plants\PlantExcelExportService;
use App\Services\Plants\PlantManagementService;
use App\Services\Plants\PlantQueryService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CayCanhController extends Controller
{
    public function __construct(
        private readonly PlantQueryService $queryService,
        private readonly PlantManagementService $plantService,
        private readonly PlantExcelExportService $excelService,
    ) {
    }

    public function index(Request $request)
    {
        try {
            return view(
                'admin.cay_canh.index',
                $this->queryService->getIndexData($request)
            );
        } catch (\Throwable $e) {
            $this->logError('Tải danh sách cây Admin thất bại.', $e);

            return view('admin.cay_canh.index', [
                'cayCanhs' => new LengthAwarePaginator(
                    [],
                    0,
                    10,
                    1,
                    ['path' => request()->url(), 'query' => request()->query()]
                ),
                'danhMucs' => collect(),
                'tongCay' => 0,
                'tongDangBan' => 0,
                'tongSapHet' => 0,
                'tongHetHang' => 0,
                'perPage' => 10,
                'loiHeThong' => true,
            ]);
        }
    }

    public function create()
    {
        return view('admin.cay_canh.create', [
            'danhMucs' => $this->queryService->getCategories(),
        ]);
    }

    public function store(StoreCayCanhRequest $request)
    {
        try {
            $this->plantService->create(
                $request->validated(),
                $request->file('anh_dai_dien')
            );

            return redirect()
                ->route('admin.cay-canh.index')
                ->with('success', 'Thêm cây thành công.');
        } catch (\Throwable $e) {
            $this->logError('Thêm cây Admin thất bại.', $e);

            return back()
                ->withInput()
                ->with('error', 'Không thể thêm cây. Vui lòng thử lại sau.');
        }
    }

    public function show(int $id)
    {
        $cay = $this->plantService->find($id);

        if (!$cay) {
            abort(404);
        }

        $danhMuc = $this->queryService->getCategories()
            ->firstWhere('category_id', $cay->category_id);

        $cay->ten_danh_muc = $danhMuc?->ten_danh_muc ?? 'Chưa phân loại';

        return view('admin.cay_canh.show', [
            'cay' => $cay,
        ]);
    }

    public function edit(int $id)
    {
        $cay = $this->plantService->find($id);

        if (!$cay) {
            abort(404);
        }

        return view('admin.cay_canh.edit', [
            'cay' => $cay,
            'danhMucs' => $this->queryService->getCategories(),
        ]);
    }

    public function update(UpdateCayCanhRequest $request, int $id)
    {
        try {
            $updated = $this->plantService->update(
                $id,
                $request->validated(),
                $request->file('anh_dai_dien')
            );

            if (!$updated) {
                abort(404);
            }

            return redirect()
                ->route('admin.cay-canh.index')
                ->with('success', 'Cập nhật cây thành công.');
        } catch (\Throwable $e) {
            $this->logError('Cập nhật cây Admin thất bại.', $e, [
                'plant_id' => $id,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Không thể cập nhật cây. Vui lòng thử lại sau.');
        }
    }

    public function destroy(int $id)
    {
        try {
            $result = $this->plantService->deleteOrHide($id);

            $flashType = $result['result'] === 'not_found'
                ? 'error'
                : 'success';

            return redirect()
                ->route('admin.cay-canh.index')
                ->with($flashType, $result['message']);
        } catch (\Throwable $e) {
            $this->logError('Xóa cây Admin thất bại.', $e, [
                'plant_id' => $id,
            ]);

            return back()->with(
                'error',
                'Không thể xóa cây. Vui lòng thử lại sau.'
            );
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $plants = $this->queryService->getExportPlants($request);
            $file = $this->excelService->create($plants);

            return response()
                ->download($file['path'], $file['fileName'])
                ->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            $this->logError('Xuất Excel danh sách cây thất bại.', $e);

            return back()->with(
                'error',
                'Không thể xuất Excel. Vui lòng thử lại sau.'
            );
        }
    }

    private function logError(
        string $message,
        \Throwable $exception,
        array $context = []
    ): void {
        Log::error($message, [
            'user_id' => Auth::id(),
            ...$context,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }
}
