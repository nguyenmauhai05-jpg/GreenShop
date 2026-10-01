<?php

namespace App\Http\Controllers;

use App\Models\DiaChi;
use App\Services\MapShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    public function geocode(Request $request, MapShippingService $map): JsonResponse
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'max:500'],
        ]);

        try {
            return response()->json([
                'ok' => true,
                'data' => $map->geocode(trim($validated['address'])),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function reverseGeocode(Request $request, MapShippingService $map): JsonResponse
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        try {
            return response()->json([
                'ok' => true,
                'data' => $map->reverseGeocode(
                    (float) $validated['lat'],
                    (float) $validated['lng']
                ),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function shippingQuote(Request $request, MapShippingService $map): JsonResponse
    {
        $validated = $request->validate([
            'address_id' => ['required', 'integer'],
        ]);

        $address = DiaChi::find((int) $validated['address_id']);

        if (!$address) {
            return response()->json([
                'ok' => false,
                'message' => 'Địa chỉ giao hàng không còn tồn tại.',
            ], 404);
        }

        if ((int) $address->user_id !== (int) Auth::user()->user_id) {
            abort(403, 'Bạn không có quyền sử dụng địa chỉ này.');
        }

        try {
            return response()->json([
                'ok' => true,
                'data' => $map->quoteForAddress($address),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
