<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TinNhanAI extends Model
{
    protected $table = 'tin_nhan_ai';

    protected $primaryKey = 'tin_nhan_id';

    public $timestamps = false;

    protected $fillable = [
        'cuoc_tro_chuyen_id',
        'nguoi_gui',
        'noi_dung',
        'anh_dinh_kem',
        'thoi_gian',
    ];


    public function cuocTroChuyen()
    {
        return $this->belongsTo(
            CuocTroChuyenAI::class,
            'cuoc_tro_chuyen_id',
            'cuoc_tro_chuyen_id'
        );
    }
}