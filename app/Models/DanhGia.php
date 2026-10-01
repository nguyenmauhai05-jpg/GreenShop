<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'danh_gia';
    protected $primaryKey = 'review_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'plant_id',
        'order_detail_id',
        'so_sao',
        'noi_dung',
        'hinh_anh',
        'trang_thai',
        'phan_hoi_admin',
        'phan_hoi_luc',
        'ngay_danh_gia',
    ];

    protected $casts = [
        'hinh_anh' => 'array',
        'ngay_danh_gia' => 'datetime',
        'phan_hoi_luc' => 'datetime',
        'so_sao' => 'integer',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'user_id', 'user_id');
    }

    public function cayCanh()
    {
        return $this->belongsTo(CayCanh::class, 'plant_id', 'plant_id');
    }

    public function chiTietDonHang()
    {
        return $this->belongsTo(ChiTietDonHang::class, 'order_detail_id', 'order_detail_id');
    }

    public function statusLabel(): string
    {
        return 'Công khai';
    }
}
