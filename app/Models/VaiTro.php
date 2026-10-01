<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    protected $table = 'vai_tro';
    protected $primaryKey = 'role_id';

    public $timestamps = false;

    protected $fillable = [
        'ten_vai_tro',
        'mo_ta',
    ];

    // Một vai trò có nhiều người dùng
    public function nguoiDungs()
    {
        return $this->hasMany(NguoiDung::class, 'role_id', 'role_id');
    }
}