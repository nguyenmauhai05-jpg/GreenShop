<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DonHang extends Model
{
    protected $table = 'don_hang';
    protected $primaryKey = 'order_id';

    public $timestamps = false;

    protected $fillable = [
        'order_code',
        'user_id',
        'address_id',
        'tong_tien',
        'phi_van_chuyen',
        'phuong_thuc_van_chuyen',
        'ma_voucher_van_chuyen',
        'giam_phi_van_chuyen',
        'voucher_id',
        'tien_giam',
        'trang_thai',
        'ngay_dat',
    ];

    protected $casts = [
        'ngay_dat' => 'datetime',
        'tong_tien' => 'decimal:2',
        'phi_van_chuyen' => 'decimal:2',
        'giam_phi_van_chuyen' => 'decimal:2',
        'tien_giam' => 'decimal:2',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'user_id', 'user_id');
    }

    public function diaChi()
    {
        return $this->belongsTo(DiaChi::class, 'address_id', 'address_id');
    }

    public function chiTietDonHangs()
    {
        return $this->hasMany(ChiTietDonHang::class, 'order_id', 'order_id');
    }

    public function thanhToan()
    {
        return $this->hasOne(ThanhToan::class, 'order_id', 'order_id');
    }

    public function orderCode(): string
    {
        if (!empty($this->order_code)) {
            return str_starts_with((string) $this->order_code, '#')
                ? (string) $this->order_code
                : '#' . $this->order_code;
        }

        // Tương thích các đơn cũ đã tồn tại trước khi có cột order_code.
        $date = $this->ngay_dat ? $this->ngay_dat->format('ymd') : now()->format('ymd');
        return '#GS' . $date . '-' . str_pad((string) $this->order_id, 4, '0', STR_PAD_LEFT);
    }

    public function normalizedStatus(): string
    {
        $value = Str::of((string) $this->trang_thai)
            ->trim()
            ->lower()
            ->ascii()
            ->replace([' ', '-'], '_')
            ->value();

        foreach (self::statusAliasMap() as $normalized => $aliases) {
            $normalizedAliases = array_map(function ($alias) {
                return Str::of($alias)->trim()->lower()->ascii()->replace([' ', '-'], '_')->value();
            }, $aliases);

            if (in_array($value, $normalizedAliases, true)) {
                return $normalized;
            }
        }

        return $value ?: 'pending_confirmation';
    }

    public function statusLabel(): string
    {
        return match ($this->normalizedStatus()) {
            'pending_payment' => 'Chờ thanh toán',
            'pending_confirmation' => 'Chờ xác nhận',
            'preparing' => 'Đang chuẩn bị',
            'shipping' => 'Đang giao',
            'delivered' => 'Đã giao',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy',
            default => 'Đang xử lý',
        };
    }

    public function isCancelable(): bool
    {
        return in_array($this->normalizedStatus(), ['pending_payment', 'pending_confirmation', 'preparing'], true);
    }

    public function progressStep(): int
    {
        return match ($this->normalizedStatus()) {
            'pending_confirmation' => 0,
            'preparing' => 1,
            'shipping' => 2,
            'delivered' => 3,
            'completed' => 4,
            default => -1,
        };
    }

    public static function databaseStatusAliases(string $normalized): array
    {
        return self::statusAliasMap()[$normalized] ?? [$normalized];
    }

    private static function statusAliasMap(): array
    {
        return [
            'pending_payment' => ['pending_payment', 'cho_thanh_toan', 'chờ thanh toán', 'cho thanh toan'],
            'pending_confirmation' => ['pending_confirmation', 'pending', 'cho_xac_nhan', 'chờ xác nhận', 'cho xac nhan'],
            'preparing' => ['preparing', 'dang_chuan_bi', 'đang chuẩn bị', 'dang chuan bi'],
            'shipping' => ['shipping', 'dang_giao', 'đang giao', 'dang giao'],
            'delivered' => ['delivered', 'da_giao', 'đã giao', 'da giao'],
            'completed' => ['completed', 'hoan_thanh', 'da_hoan_thanh', 'đã hoàn thành', 'hoàn thành', 'da hoan thanh'],
            'cancelled' => ['cancelled', 'canceled', 'da_huy', 'đã hủy', 'da huy'],
        ];
    }
}
