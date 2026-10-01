<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    protected $table = 'chi_tiet_don_hang';
    protected $primaryKey = 'order_detail_id';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'plant_id',
        'don_gia',
        'so_luong',
    ];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'order_id', 'order_id');
    }

    public function cayCanh()
    {
        return $this->belongsTo(CayCanh::class, 'plant_id', 'plant_id');
    }

    public function danhGia()
    {
        return $this->hasOne(DanhGia::class, 'order_detail_id', 'order_detail_id');
    }
}
