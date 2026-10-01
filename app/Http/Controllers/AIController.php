<?php

namespace App\Http\Controllers;

use App\Services\AI\AIConversationService;
use App\Services\AI\AIImageService;
use App\Services\AI\GeminiCareService;
use App\Services\AI\PlantContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    public function __construct(
        private readonly PlantContextService $plantContextService,
        private readonly AIConversationService $conversationService,
        private readonly AIImageService $imageService,
        private readonly GeminiCareService $geminiService,
    ) {
    }

    public function index(Request $request)
    {
        // Guest không tạo bản ghi hội thoại trong DB. Lịch sử guest được frontend
        // giữ riêng bằng localStorage; user đăng nhập vẫn dùng DB như trước.
        if (!Auth::check()) {
            return view('cham_soc_cay.index', [
                'lichSu' => collect(),
                'cuocTroChuyen' => null,
                'tinNhans' => collect(),
                'cayDaMua' => collect(),
            ]);
        }

        $userId = (int) Auth::id();
        $current = $this->conversationService->getCurrentConversation(
            $userId,
            $request->input('conversation')
        );

        $messages = $current['messages']->map(function ($message) {
            $message->recommendations = $this->serializeRecommendations(
                $this->plantContextService->recommendationProducts($message->recommendation_ids ?? [])
            );

            return $message;
        });

        return view('cham_soc_cay.index', [
            'lichSu' => $this->conversationService->getHistory($userId),
            'cuocTroChuyen' => $current['conversation'],
            'tinNhans' => $messages,
            'cayDaMua' => $this->plantContextService->getPurchasedPlants($userId),
        ]);
    }

    public function taoCuocTroChuyen()
    {
        $conversation = $this->conversationService->create((int) Auth::id());

        return redirect()->route('cham-soc-cay', [
            'conversation' => $conversation->cuoc_tro_chuyen_id,
        ]);
    }

    public function chat(Request $request)
    {
        $request->validate(
            [
                'message' => ['required', 'string', 'max:1000'],
                'cuoc_tro_chuyen_id' => ['nullable', 'integer'],
                'plant_id' => ['nullable', 'integer'],
                'anh' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],
            ],
            [
                'message.required' => 'Vui lòng nhập câu hỏi.',
                'message.max' => 'Câu hỏi không được vượt quá 1000 ký tự.',
                'anh.image' => 'File đính kèm phải là hình ảnh.',
                'anh.mimes' => 'Ảnh chỉ hỗ trợ JPG, JPEG, PNG hoặc WEBP.',
                'anh.max' => 'Ảnh không được vượt quá 5MB.',
            ]
        );

        try {
            $isAuthenticated = Auth::check();
            $userId = $isAuthenticated ? (int) Auth::id() : null;
            $message = trim((string) $request->message);
            $selectedPlant = null;

            if ($isAuthenticated && $request->filled('plant_id')) {
                $selectedPlant = $this->plantContextService->findPurchasedPlant(
                    $userId,
                    (int) $request->plant_id
                );

                if (!$selectedPlant) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cây được chọn không thuộc lịch sử mua hàng của bạn.',
                    ], 403);
                }
            }

            $conversation = null;
            if ($isAuthenticated && $request->filled('cuoc_tro_chuyen_id')) {
                $conversation = $this->conversationService->findOwned(
                    $userId,
                    (int) $request->cuoc_tro_chuyen_id
                );

                if (!$conversation) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cuộc trò chuyện không tồn tại.',
                    ], 404);
                }
            }

            if ($isAuthenticated && !$conversation) {
                $conversation = $this->conversationService->create(
                    $userId,
                    $selectedPlant?->plant_id,
                    mb_substr($message, 0, 80)
                );
            }

            $imagePath = null;
            if ($request->hasFile('anh')) {
                $imagePath = $this->imageService->store($request->file('anh'));
            }

            if ($isAuthenticated) {
                $this->conversationService->addMessage(
                    $conversation,
                    'user',
                    $message,
                    $imagePath
                );
            }

            $availablePlants = $this->plantContextService->getAvailablePlants();
            $plantContext = $this->plantContextService->buildPlantContext($selectedPlant);
            $catalogContext = $this->plantContextService->buildAvailableCatalogContext($availablePlants);

            $aiResult = $this->geminiService->generateReply(
                $message,
                $plantContext,
                $catalogContext,
                $imagePath
            );

            $reply = $aiResult['reply'];
            $isShoppingIntent = $this->plantContextService->looksLikeShoppingIntent($message);

            // Card chỉ dành cho nhu cầu mua/tư vấn chọn cây. Câu hỏi chăm sóc không được ép card.
            $recommendedIds = $isShoppingIntent
                ? $aiResult['recommended_ids']
                : [];

            if ($isShoppingIntent && empty($recommendedIds)) {
                $recommendedIds = $this->plantContextService->fallbackRecommendationIds(
                    $message,
                    $availablePlants
                );
            }

            $recommendations = $this->serializeRecommendations(
                $this->plantContextService->recommendationProducts($recommendedIds)
            );

            if ($isAuthenticated) {
                $this->conversationService->addMessage(
                    $conversation,
                    'ai',
                    $reply,
                    null,
                    $recommendations->pluck('plant_id')->all()
                );

                $this->conversationService->touchAfterReply(
                    $conversation,
                    $message,
                    $selectedPlant?->plant_id
                );
            }

            return response()->json([
                'success' => true,
                'reply' => $reply,
                'recommendations' => $recommendations,
                'cuoc_tro_chuyen_id' => $conversation?->cuoc_tro_chuyen_id,
                'tieu_de' => $conversation?->tieu_de,
                'anh_url' => $this->imageService->publicUrl($imagePath),
            ]);
        } catch (\Throwable $e) {
            Log::error('Chat AI thất bại.', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi câu hỏi lúc này. Vui lòng thử lại.',
            ], 500);
        }
    }

    private function serializeRecommendations($plants)
    {
        return $plants->map(function ($plant) {
            return [
                'plant_id' => (int) $plant->plant_id,
                'name' => $plant->ten_cay,
                'category' => $plant->ten_danh_muc ?? 'Cây cảnh',
                'price' => (float) $plant->gia,
                'price_formatted' => number_format((float) $plant->gia, 0, ',', '.') . '₫',
                'stock' => (int) $plant->so_luong,
                'height' => $plant->chieu_cao,
                'description' => $plant->mo_ta,
                'image_url' => $plant->anh_dai_dien ? asset($plant->anh_dai_dien) : null,
                'detail_url' => route('chi-tiet-cay', $plant->plant_id),
                'add_cart_url' => route('gio-hang.them'),
            ];
        })->values();
    }

    public function xoaCuocTroChuyen(int $id)
    {
        $deleted = $this->conversationService->deleteOne(
            (int) Auth::id(),
            $id
        );

        if (!$deleted) {
            return back()->with('error', 'Cuộc trò chuyện không tồn tại.');
        }

        return redirect()
            ->route('cham-soc-cay')
            ->with('success', 'Đã xóa cuộc trò chuyện.');
    }

    public function xoaTatCa()
    {
        $this->conversationService->deleteAll((int) Auth::id());

        return redirect()
            ->route('cham-soc-cay')
            ->with('success', 'Đã xóa toàn bộ lịch sử trò chuyện.');
    }
}
