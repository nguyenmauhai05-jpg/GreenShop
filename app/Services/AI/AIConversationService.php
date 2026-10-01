<?php

namespace App\Services\AI;

use App\Models\CuocTroChuyenAI;
use App\Models\TinNhanAI;
use Illuminate\Support\Collection;

class AIConversationService
{
    public function __construct(
        private readonly AIImageService $imageService,
    ) {
    }

    public function getHistory(int $userId): Collection
    {
        return CuocTroChuyenAI::where('user_id', $userId)
            ->orderByDesc('thoi_gian_cap_nhat')
            ->get();
    }

    /**
     * @return array{conversation:?CuocTroChuyenAI,messages:Collection}
     */
    public function getCurrentConversation(int $userId, mixed $conversationId): array
    {
        if (!$conversationId) {
            return [
                'conversation' => null,
                'messages' => collect(),
            ];
        }

        $conversation = $this->findOwned($userId, (int) $conversationId);

        if (!$conversation) {
            return [
                'conversation' => null,
                'messages' => collect(),
            ];
        }

        return [
            'conversation' => $conversation,
            'messages' => TinNhanAI::where(
                'cuoc_tro_chuyen_id',
                $conversation->cuoc_tro_chuyen_id
            )
                ->orderBy('thoi_gian')
                ->orderBy('tin_nhan_id')
                ->get()
                ->map(function (TinNhanAI $message) {
                    if ($message->nguoi_gui !== 'ai') {
                        $message->recommendation_ids = [];
                        return $message;
                    }

                    [$content, $ids] = $this->extractRecommendationMetadata((string) $message->noi_dung);
                    $message->noi_dung = $content;
                    $message->recommendation_ids = $ids;

                    return $message;
                }),
        ];
    }

    public function create(int $userId, ?int $plantId = null, string $title = 'Cuộc trò chuyện mới'): CuocTroChuyenAI
    {
        return CuocTroChuyenAI::create([
            'user_id' => $userId,
            'plant_id' => $plantId,
            'tieu_de' => $title,
            'thoi_gian_tao' => now(),
            'thoi_gian_cap_nhat' => now(),
        ]);
    }

    public function findOwned(int $userId, int $conversationId): ?CuocTroChuyenAI
    {
        return CuocTroChuyenAI::where('cuoc_tro_chuyen_id', $conversationId)
            ->where('user_id', $userId)
            ->first();
    }

    public function addMessage(
        CuocTroChuyenAI $conversation,
        string $sender,
        string $content,
        ?string $imagePath = null,
        array $recommendationIds = []
    ): TinNhanAI {
        if ($sender === 'ai' && !empty($recommendationIds)) {
            $ids = collect($recommendationIds)
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->take(4)
                ->implode(',');

            if ($ids !== '') {
                $content = rtrim($content) . "\n[[GREENSHOP_PRODUCTS:{$ids}]]";
            }
        }

        return TinNhanAI::create([
            'cuoc_tro_chuyen_id' => $conversation->cuoc_tro_chuyen_id,
            'nguoi_gui' => $sender,
            'noi_dung' => $content,
            'anh_dinh_kem' => $imagePath,
            'thoi_gian' => now(),
        ]);
    }

    /**
     * Card sản phẩm được lưu ngay trong bản ghi tin nhắn AI bằng marker nội bộ.
     * Cách này giữ tương thích DB hiện tại và cho phép mở lịch sử dựng lại card.
     *
     * @return array{0:string,1:array<int>}
     */
    private function extractRecommendationMetadata(string $content): array
    {
        $ids = [];

        if (preg_match('/\[\[GREENSHOP_PRODUCTS:\s*([^\]]*)\]\]/iu', $content, $matches)) {
            $ids = collect(explode(',', (string) ($matches[1] ?? '')))
                ->map(fn ($id) => (int) trim($id))
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->take(4)
                ->values()
                ->all();
        }

        $clean = preg_replace('/\s*\[\[GREENSHOP_PRODUCTS:[^\]]*\]\]\s*/iu', '', $content);

        return [trim((string) $clean), $ids];
    }

    public function touchAfterReply(
        CuocTroChuyenAI $conversation,
        string $message,
        ?int $plantId
    ): void {
        if ($plantId) {
            $conversation->plant_id = $plantId;
        }

        if ($conversation->tieu_de === 'Cuộc trò chuyện mới') {
            $conversation->tieu_de = mb_substr($message, 0, 80);
        }

        $conversation->thoi_gian_cap_nhat = now();
        $conversation->save();
    }

    public function deleteOne(int $userId, int $conversationId): bool
    {
        $conversation = $this->findOwned($userId, $conversationId);

        if (!$conversation) {
            return false;
        }

        $messages = TinNhanAI::where(
            'cuoc_tro_chuyen_id',
            $conversation->cuoc_tro_chuyen_id
        )->get();

        foreach ($messages as $message) {
            $this->imageService->delete($message->anh_dinh_kem);
        }

        // FK trong Dump dùng ON DELETE CASCADE cho tin_nhan_ai.
        $conversation->delete();

        return true;
    }

    public function deleteAll(int $userId): void
    {
        $conversationIds = CuocTroChuyenAI::where('user_id', $userId)
            ->pluck('cuoc_tro_chuyen_id');

        $messages = TinNhanAI::whereIn('cuoc_tro_chuyen_id', $conversationIds)->get();

        foreach ($messages as $message) {
            $this->imageService->delete($message->anh_dinh_kem);
        }

        CuocTroChuyenAI::where('user_id', $userId)->delete();
    }
}
