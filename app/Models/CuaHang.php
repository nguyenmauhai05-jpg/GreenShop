<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuaHang extends Model
{
    protected $table = 'cua_hang';
    protected $primaryKey = 'store_id';

    public $timestamps = false;

    protected $fillable = [
        'ten_cua_hang',
        'dia_chi',
        'latitude',
        'longitude',
        'so_dien_thoai',
    ];
}