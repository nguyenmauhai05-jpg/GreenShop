<?php

namespace App\Services\AI;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlantContextService
{
    public function getPurchasedPlants(int $userId): Collection
    {
        return DB::table('don_hang as dh')
            ->join('chi_tiet_don_hang as ct', 'ct.order_id', '=', 'dh.order_id')
            ->join('cay_canh as cc', 'cc.plant_id', '=', 'ct.plant_id')
            ->leftJoin('danh_muc as dm', 'dm.category_id', '=', 'cc.category_id')
            ->where('dh.user_id', $userId)
            ->select(
                'cc.plant_id',
                'cc.ten_cay',
                'cc.anh_dai_dien',
                'cc.chieu_cao',
                'cc.mo_ta',
                'cc.cach_cham_soc',
                'dm.ten_danh_muc'
            )
            ->distinct()
            ->orderBy('cc.ten_cay')
            ->get();
    }

    public function findPurchasedPlant(int $userId, int $plantId): ?object
    {
        return DB::table('don_hang as dh')
            ->join('chi_tiet_don_hang as ct', 'ct.order_id', '=', 'dh.order_id')
            ->join('cay_canh as cc', 'cc.plant_id', '=', 'ct.plant_id')
            ->leftJoin('danh_muc as dm', 'dm.category_id', '=', 'cc.category_id')
            ->where('dh.user_id', $userId)
            ->where('cc.plant_id', $plantId)
            ->select(
                'cc.plant_id',
                'cc.ten_cay',
                'cc.mo_ta',
                'cc.cach_cham_soc',
                'cc.chieu_cao',
                'cc.anh_dai_dien',
                'dm.ten_danh_muc'
            )
            ->first();
    }

    public function getAvailablePlants(int $limit = 60): Collection
    {
        return DB::table('cay_canh as cc')
            ->leftJoin('danh_muc as dm', 'dm.category_id', '=', 'cc.category_id')
            ->where('cc.trang_thai', 'Đang bán')
            ->where('cc.so_luong', '>', 0)
            ->select(
                'cc.plant_id',
                'cc.ten_cay',
                'cc.gia',
                'cc.so_luong',
                'cc.mo_ta',
                'cc.cach_cham_soc',
                'cc.chieu_cao',
                'cc.anh_dai_dien',
                'dm.ten_danh_muc'
            )
            ->orderBy('cc.ten_cay')
            ->limit($limit)
            ->get();
    }

    public function buildPlantContext(?object $plant): string
    {
        if (!$plant) {
            return 'Khách hàng chưa chọn cây đã mua cụ thể.';
        }

        return "Thông tin cây khách hàng đã mua và đang chọn để hỏi:\n"
            . '- ID: ' . $plant->plant_id . "\n"
            . '- Tên cây: ' . $plant->ten_cay . "\n"
            . '- Danh mục: ' . ($plant->ten_danh_muc ?? 'Chưa xác định') . "\n"
            . '- Chiều cao: ' . ($plant->chieu_cao ?? 'Chưa cập nhật') . "\n"
            . '- Mô tả: ' . ($plant->mo_ta ?? 'Chưa có') . "\n"
            . '- Hướng dẫn chăm sóc: ' . ($plant->cach_cham_soc ?? 'Chưa có');
    }

    public function buildAvailableCatalogContext(Collection $plants): string
    {
        if ($plants->isEmpty()) {
            return "DANH SÁCH CÂY ĐANG BÁN TRÊN GREENSHOP:\nHiện không có cây khả dụng.";
        }

        $lines = $plants->map(function ($plant) {
            return implode(' | ', [
                'ID=' . $plant->plant_id,
                'Tên=' . $plant->ten_cay,
                'Danh mục=' . ($plant->ten_danh_muc ?? 'Cây cảnh'),
                'Giá=' . number_format((float) $plant->gia, 0, ',', '.') . 'đ',
                'Tồn=' . (int) $plant->so_luong,
                'Chiều cao=' . ($plant->chieu_cao ?? 'Chưa cập nhật'),
                'Mô tả=' . Str::limit(strip_tags((string) ($plant->mo_ta ?? '')), 180),
                'Chăm sóc=' . Str::limit(strip_tags((string) ($plant->cach_cham_soc ?? '')), 180),
            ]);
        })->implode("\n");

        return "DANH SÁCH CÂY ĐANG BÁN TRÊN GREENSHOP:\n{$lines}";
    }

    public function recommendationProducts(array $plantIds, int $limit = 4): Collection
    {
        $ids = collect($plantIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->take($limit)
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $plants = DB::table('cay_canh as cc')
            ->leftJoin('danh_muc as dm', 'dm.category_id', '=', 'cc.category_id')
            ->whereIn('cc.plant_id', $ids->all())
            ->where('cc.trang_thai', 'Đang bán')
            ->where('cc.so_luong', '>', 0)
            ->select(
                'cc.plant_id',
                'cc.ten_cay',
                'cc.gia',
                'cc.so_luong',
                'cc.mo_ta',
                'cc.chieu_cao',
                'cc.anh_dai_dien',
                'dm.ten_danh_muc'
            )
            ->get()
            ->keyBy('plant_id');

        return $ids
            ->map(fn ($id) => $plants->get($id))
            ->filter()
            ->values();
    }

    public function looksLikeShoppingIntent(string $message): bool
    {
        $text = Str::lower(trim($message));

        // Các câu mô tả triệu chứng/chăm sóc phải ưu tiên là care, kể cả có tên vị trí như "phòng ngủ".
        foreach ([
            'chăm', 'tưới', 'nước', 'ánh sáng', 'đất', 'phân bón', 'bón phân',
            'độ ẩm', 'sâu', 'bệnh', 'vàng lá', 'rụng lá', 'khô lá', 'héo', 'úng',
            'thối rễ', 'rễ', 'nấm', 'đốm lá', 'phục hồi', 'cứu cây', 'bao lâu tưới',
            'lá bị', 'cây bị', 'cây của tôi', 'cây nhà tôi',
        ] as $careKeyword) {
            if (Str::contains($text, $careKeyword)) {
                return false;
            }
        }

        foreach ([
            'mua', 'muốn mua', 'nên mua', 'gợi ý cây', 'tư vấn mua', 'tư vấn cây',
            'chọn cây', 'cây nào phù hợp', 'đề xuất cây', 'giá', 'bao nhiêu tiền',
            'dưới ', 'khoảng ', 'ngân sách', 'còn hàng', 'sản phẩm', 'để bàn',
            'trồng cây gì', 'nên chọn cây', 'làm quà', 'trang trí',
        ] as $shoppingKeyword) {
            if (Str::contains($text, $shoppingKeyword)) {
                return true;
            }
        }

        return false;
    }

    public function fallbackRecommendationIds(string $message, Collection $plants, int $limit = 3): array
    {
        if (!$this->looksLikeShoppingIntent($message) || $plants->isEmpty()) {
            return [];
        }

        $text = Str::lower($message);
        $maxPrice = null;

        if (preg_match('/(?:dưới|duoi|<)\s*([0-9]+(?:[\.,][0-9]+)?)\s*(k|nghìn|nghin|tr|triệu|trieu)?/iu', $message, $matches)) {
            $value = (float) str_replace(',', '.', $matches[1]);
            $unit = Str::lower($matches[2] ?? '');
            $maxPrice = match (true) {
                Str::contains($unit, ['tr', 'triệu', 'trieu']) => $value * 1000000,
                Str::contains($unit, ['k', 'nghìn', 'nghin']) => $value * 1000,
                default => $value,
            };
        }

        $tokens = collect(preg_split('/[^\pL\pN]+/u', $text))
            ->filter(fn ($token) => mb_strlen($token) >= 3)
            ->reject(fn ($token) => in_array($token, [
                'mua', 'cây', 'nào', 'cho', 'tôi', 'muốn', 'cần', 'phù', 'hợp',
                'giúp', 'gợi', 'đề', 'xuất', 'khoảng', 'dưới', 'trên',
            ], true))
            ->values();

        return $plants
            ->filter(fn ($plant) => $maxPrice === null || (float) $plant->gia <= $maxPrice)
            ->map(function ($plant) use ($tokens) {
                $haystack = Str::lower(implode(' ', [
                    $plant->ten_cay,
                    $plant->ten_danh_muc ?? '',
                    $plant->mo_ta ?? '',
                    $plant->cach_cham_soc ?? '',
                ]));

                $score = $tokens->sum(fn ($token) => Str::contains($haystack, $token) ? 1 : 0);

                return ['id' => (int) $plant->plant_id, 'score' => $score];
            })
            ->sortByDesc('score')
            ->take($limit)
            ->pluck('id')
            ->values()
            ->all();
    }
}
