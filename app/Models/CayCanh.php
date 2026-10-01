<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CayCanh extends Model
{

    protected $table = 'cay_canh';

    protected $primaryKey = 'plant_id';

    public $timestamps = false;


    protected $fillable = [

        'category_id',
        'ten_cay',
        'gia',
        'so_luong',
        'mo_ta',
        'cach_cham_soc',
        'chieu_cao',
        'anh_dai_dien',
        'trang_thai'

    ];



    public function danhMuc()
    {
        return $this->belongsTo(
            DanhMuc::class,
            'category_id',
            'category_id'
        );
    }



    public function danhGias()
    {
        return $this->hasMany(
            DanhGia::class,
            'plant_id',
            'plant_id'
        );
    }

}