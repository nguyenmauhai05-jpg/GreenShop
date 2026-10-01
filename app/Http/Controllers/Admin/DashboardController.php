<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\AdminDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct(private readonly AdminDashboardService $dashboardService)
    {
    }

    public function index(Request $request)
    {
        try {
            $data = $this->dashboardService->build(
                $request->filled('tu_ngay') ? (string) $request->input('tu_ngay') : null,
                $request->filled('den_ngay') ? (string) $request->input('den_ngay') : null,
            );
        } catch (\Throwable $e) {
            Log::error('Tải Dashboard Admin thất bại', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $data = $this->dashboardService->emptyState();
        }

        return view('admin.dashboard.index', $data);
    }
}
