<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
    protected $table = 'thanh_toan';
    protected $primaryKey = 'payment_id';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'phuong_thuc',
        'so_tien',
        'trang_thai',
        'ngay_thanh_toan',
    ];

    protected $casts = [
        'ngay_thanh_toan' => 'datetime',
    ];


    /**
     * Mã phương thức thanh toán đã chuẩn hóa.
     */
    public function normalizedMethod(): string
    {
        $method = strtoupper(trim((string) $this->phuong_thuc));

        return match ($method) {
            'CASH' => 'COD',
            'BANK', 'TRANSFER', 'CHUYEN_KHOAN' => 'BANK_TRANSFER',
            default => $method ?: 'COD',
        };
    }

    /**
     * Nhãn hiển thị cho khách hàng/Admin.
     *
     * Không ghép "BANK_TRANSFER - Thanh toán khi nhận hàng" để tránh
     * hiển thị sai giữa mã phương thức và hình thức thanh toán.
     */
    public function methodLabel(): string
    {
        return match ($this->normalizedMethod()) {
            'MOMO' => 'Ví MoMo',
            'PAYPAL' => 'PayPal',
            'PAYOS' => 'payOS (QR ngân hàng)',
            'BANK_TRANSFER' => 'Chuyển khoản ngân hàng',
            'COD' => 'Thanh toán khi nhận hàng',
            default => 'Chưa xác định',
        };
    }

    /**
     * Hình thức thanh toán phải tương ứng đúng với phương thức đã chọn.
     */
    public function paymentFormLabel(): string
    {
        return match ($this->normalizedMethod()) {
            'MOMO' => 'Ví MoMo',
            'PAYPAL' => 'PayPal',
            'PAYOS' => 'payOS (QR ngân hàng)',
            'BANK_TRANSFER' => 'Chuyển khoản ngân hàng',
            'COD' => 'Thanh toán khi nhận hàng',
            default => 'Chưa xác định',
        };
    }

    public function normalizedStatus(): string
    {
        $status = strtolower(trim((string) $this->trang_thai));

        return match ($status) {
            'paid', 'success', 'successful' => 'paid',
            'waiting_confirmation', 'pending_confirmation' => 'waiting_confirmation',
            'refund_pending' => 'refund_pending',
            'refunded' => 'refunded',
            'failed' => 'failed',
            'expired' => 'expired',
            'cancelled', 'canceled' => 'cancelled',
            default => 'pending',
        };
    }

    public function isPaid(): bool
    {
        return in_array($this->normalizedStatus(), ['paid', 'refund_pending', 'refunded'], true);
    }

    public function donHang()
    {
        return $this->belongsTo(
            DonHang::class,
            'order_id',
            'order_id'
        );
    }
}