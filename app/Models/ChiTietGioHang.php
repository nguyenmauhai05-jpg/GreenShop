<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietGioHang extends Model
{
    protected $table = 'chi_tiet_gio_hang';
    protected $primaryKey = 'cart_detail_id';

    public $timestamps = false;

    protected $fillable = [
        'cart_id',
        'plant_id',
        'so_luong',
    ];

    public function gioHang()
    {
        return $this->belongsTo(
            GioHang::class,
            'cart_id',
            'cart_id'
        );
    }

    public function cayCanh()
    {
        return $this->belongsTo(
            CayCanh::class,
            'plant_id',
            'plant_id'
        );
    }
}