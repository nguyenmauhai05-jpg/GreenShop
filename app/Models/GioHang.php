<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    protected $table = 'gio_hang';
    protected $primaryKey = 'cart_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(
            NguoiDung::class,
            'user_id',
            'user_id'
        );
    }

    public function chiTietGioHangs()
    {
        return $this->hasMany(
            ChiTietGioHang::class,
            'cart_id',
            'cart_id'
        );
    }
}