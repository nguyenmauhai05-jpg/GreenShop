<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuocTroChuyenAI extends Model
{
    protected $table = 'cuoc_tro_chuyen_ai';

    protected $primaryKey = 'cuoc_tro_chuyen_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'plant_id',
        'tieu_de',
        'thoi_gian_tao',
        'thoi_gian_cap_nhat',
    ];


    public function tinNhans()
    {
        return $this->hasMany(
            TinNhanAI::class,
            'cuoc_tro_chuyen_id',
            'cuoc_tro_chuyen_id'
        )
        ->orderBy('thoi_gian')
        ->orderBy('tin_nhan_id');
    }


    public function cay()
    {
        return $this->belongsTo(
            CayCanh::class,
            'plant_id',
            'plant_id'
        );
    }
}