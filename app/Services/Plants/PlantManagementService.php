<?php

namespace App\Services\Plants;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PlantManagementService
{
    public function __construct(
        private readonly PlantImageService $imageService,
    ) {
    }

    public function find(int $id): ?object
    {
        return DB::table('cay_canh')
            ->where('plant_id', $id)
            ->first();
    }

    public function create(array $data, UploadedFile $image): void
    {
        $imagePath = $this->imageService->store($image);

        try {
            DB::table('cay_canh')->insert(
                $this->buildPayload($data, $imagePath)
            );
        } catch (\Throwable $e) {
            // Nếu insert DB thất bại thì không để lại file ảnh rác.
            $this->imageService->delete($imagePath);
            throw $e;
        }
    }

    public function update(int $id, array $data, ?UploadedFile $newImage): bool
    {
        $plant = $this->find($id);

        if (!$plant) {
            return false;
        }

        $imagePath = $plant->anh_dai_dien;
        $newImagePath = null;

        if ($newImage) {
            // Upload ảnh mới trước; chỉ xóa ảnh cũ sau khi DB update thành công.
            $newImagePath = $this->imageService->store($newImage);
            $imagePath = $newImagePath;
        }

        try {
            DB::table('cay_canh')
                ->where('plant_id', $id)
                ->update($this->buildPayload($data, $imagePath));
        } catch (\Throwable $e) {
            if ($newImagePath) {
                $this->imageService->delete($newImagePath);
            }

            throw $e;
        }

        if ($newImagePath) {
            $this->imageService->delete($plant->anh_dai_dien);
        }

        return true;
    }

    /**
     * @return array{result:string,message:string}
     */
    public function deleteOrHide(int $id): array
    {
        $plant = $this->find($id);

        if (!$plant) {
            return [
                'result' => 'not_found',
                'message' => 'Cây không tồn tại.',
            ];
        }

        if ($this->hasRelatedData($id)) {
            DB::table('cay_canh')
                ->where('plant_id', $id)
                ->update(['trang_thai' => 'Ẩn']);

            return [
                'result' => 'hidden',
                'message' => 'Cây đã phát sinh dữ liệu liên quan nên hệ thống đã chuyển cây sang trạng thái Ẩn.',
            ];
        }

        // Xóa DB trước, sau đó mới dọn ảnh để không làm mất ảnh nếu câu DELETE thất bại.
        DB::table('cay_canh')
            ->where('plant_id', $id)
            ->delete();

        $this->imageService->delete($plant->anh_dai_dien);

        return [
            'result' => 'deleted',
            'message' => 'Xóa cây thành công.',
        ];
    }

    private function hasRelatedData(int $id): bool
    {
        return DB::table('chi_tiet_don_hang')
            ->where('plant_id', $id)
            ->exists()
            || DB::table('danh_gia')
                ->where('plant_id', $id)
                ->exists()
            || DB::table('ma_qr')
                ->where('plant_id', $id)
                ->exists();
    }

    private function buildPayload(array $data, ?string $imagePath): array
    {
        return [
            'category_id' => $data['category_id'],
            'ten_cay' => $data['ten_cay'],
            'gia' => $data['gia'],
            'so_luong' => $data['so_luong'],
            'mo_ta' => $data['mo_ta'] ?? null,
            'cach_cham_soc' => $data['cach_cham_soc'] ?? null,
            'chieu_cao' => $data['chieu_cao'] ?? null,
            'anh_dai_dien' => $imagePath,
            'trang_thai' => $data['trang_thai'],
        ];
    }
}
