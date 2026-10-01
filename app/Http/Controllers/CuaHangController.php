<?php

namespace App\Http\Controllers;

use App\Services\Shop\ShopCatalogService;
use App\Services\Shop\ShopFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CuaHangController extends Controller
{
    private const PER_PAGE = 8;

    public function __construct(
        private readonly ShopFilterService $filterService,
        private readonly ShopCatalogService $catalogService
    ) {
    }

    /**
     * Hiển thị danh sách cây tại trang Cửa hàng.
     * Guest / Customer / Admin đều được truy cập.
     */
    public function index(Request $request)
    {
        try {
            $filters = $this->filterService->resolve($request);

            $cayCanhs = $this->catalogService->paginate($filters, self::PER_PAGE);
            $danhMucs = $this->catalogService->categories();
            $giaCaoNhat = $this->catalogService->maxPrice();
            $filterErrors = $filters['filterErrors'];
            $perPage = self::PER_PAGE;

            return view('cua_hang.index', compact(
                'cayCanhs',
                'danhMucs',
                'giaCaoNhat',
                'perPage',
                'filterErrors'
            ));
        } catch (\Throwable $e) {
            Log::error('Không thể tải danh sách cây tại cửa hàng.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return view('cua_hang.index', [
                'cayCanhs' => collect(),
                'danhMucs' => collect(),
                'giaCaoNhat' => 0,
                'perPage' => self::PER_PAGE,
                'filterErrors' => [],
                'loiHeThong' => true,
            ]);
        }
    }
}
