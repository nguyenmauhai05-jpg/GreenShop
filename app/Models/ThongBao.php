<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    protected $table = 'thong_bao';
    protected $primaryKey = 'notification_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'tieu_de',
        'noi_dung',
        'da_doc',
        'ngay_gui',
    ];

    protected $casts = [
        'da_doc' => 'boolean',
        'ngay_gui' => 'datetime',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(
            NguoiDung::class,
            'user_id',
            'user_id'
        );
    }
}