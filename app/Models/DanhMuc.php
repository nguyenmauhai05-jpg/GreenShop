<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    protected $table = 'danh_muc';
    protected $primaryKey = 'category_id';

    public $timestamps = false;

    protected $fillable = [
        'ten_danh_muc',
        'mo_ta',
        'hinh_anh',
    ];

    public function cayCanhs()
    {
        return $this->hasMany(
            CayCanh::class,
            'category_id',
            'category_id'
        );
    }
}