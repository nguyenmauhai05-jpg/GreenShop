<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaChi extends Model
{
    protected $table = 'dia_chi';
    protected $primaryKey = 'address_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nguoi_nhan',
        'so_dien_thoai',
        'dia_chi',
        'phuong_xa',
        'quan_huyen',
        'tinh_thanh',
        'latitude',
        'longitude',
        'mac_dinh',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'mac_dinh' => 'boolean',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(
            NguoiDung::class,
            'user_id',
            'user_id'
        );
    }

    public function donHangs()
    {
        return $this->hasMany(
            DonHang::class,
            'address_id',
            'address_id'
        );
    }
}