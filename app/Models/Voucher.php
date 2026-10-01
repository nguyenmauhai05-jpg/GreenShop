<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'vouchers';
    protected $primaryKey = 'voucher_id';

    protected $fillable = [
        'ma_voucher',
        'ten_voucher',
        'mo_ta',
        'pham_vi',
        'loai_giam',
        'gia_tri_giam',
        'giam_toi_da',
        'don_hang_toi_thieu',
        'so_luong',
        'da_su_dung',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
    ];

    protected $casts = [
        'gia_tri_giam' => 'decimal:2',
        'giam_toi_da' => 'decimal:2',
        'don_hang_toi_thieu' => 'decimal:2',
        'so_luong' => 'integer',
        'da_su_dung' => 'integer',
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
        'trang_thai' => 'boolean',
    ];

    public function conLuotSuDung(): int
    {
        return max(0, (int) $this->so_luong - (int) $this->da_su_dung);
    }

    public function dangCoHieuLuc(): bool
    {
        // Các cột ngay_bat_dau/ngay_ket_thuc là DATETIME không kèm timezone.
        // So sánh theo giờ Việt Nam để đồng nhất với dữ liệu nhập từ Admin.
        $nowLocal = now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');
        $startLocal = $this->ngay_bat_dau?->format('Y-m-d H:i:s');
        $endLocal = $this->ngay_ket_thuc?->format('Y-m-d H:i:s');

        return (bool) $this->trang_thai
            && $startLocal !== null
            && $endLocal !== null
            && $startLocal <= $nowLocal
            && $endLocal >= $nowLocal
            && $this->conLuotSuDung();
    }


    public function laVoucherVanChuyen(): bool
    {
        return ($this->pham_vi ?? 'don_hang') === 'van_chuyen';
    }

    public function tinhTienGiamPhiShip(float $shippingFee, float $subtotal): float
    {
        if ($shippingFee <= 0 || !$this->dangCoHieuLuc() || $subtotal < (float) $this->don_hang_toi_thieu) {
            return 0;
        }

        if ($this->loai_giam === 'phan_tram') {
            $discount = $shippingFee * ((float) $this->gia_tri_giam / 100);
            if ($this->giam_toi_da !== null) {
                $discount = min($discount, (float) $this->giam_toi_da);
            }
            return max(0, min($discount, $shippingFee));
        }

        return max(0, min((float) $this->gia_tri_giam, $shippingFee));
    }

    public function tinhTienGiam(float $subtotal): float
    {
        if ($subtotal <= 0 || !$this->dangCoHieuLuc()) {
            return 0;
        }

        if ($subtotal < (float) $this->don_hang_toi_thieu) {
            return 0;
        }

        if ($this->loai_giam === 'phan_tram') {
            $discount = $subtotal * ((float) $this->gia_tri_giam / 100);

            if ($this->giam_toi_da !== null) {
                $discount = min($discount, (float) $this->giam_toi_da);
            }

            return max(0, min($discount, $subtotal));
        }

        return max(0, min((float) $this->gia_tri_giam, $subtotal));
    }
}
