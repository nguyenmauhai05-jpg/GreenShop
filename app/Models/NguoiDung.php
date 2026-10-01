<?php

namespace App\Models;

use App\Notifications\DatLaiMatKhauNotification;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class NguoiDung extends Authenticatable
{
    use Notifiable;

    protected $table = 'nguoi_dung';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'role_id',
        'ho_ten',
        'email',
        'mat_khau',
        'so_dien_thoai',
        'ngay_sinh',
        'gioi_tinh',
        'anh_dai_dien',
        'trang_thai',
    ];

    protected $hidden = [
        'mat_khau',
    ];

    public function getAuthPassword()
    {
        return $this->mat_khau;
    }


    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new DatLaiMatKhauNotification($token));
    }

    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class, 'role_id', 'role_id');
    }

    public function diaChis()
    {
        return $this->hasMany(DiaChi::class, 'user_id', 'user_id');
    }

    public function gioHang()
    {
        return $this->hasOne(GioHang::class, 'user_id', 'user_id');
    }

    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'user_id', 'user_id');
    }

    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'user_id', 'user_id');
    }
    public function thongBaos()
    {
        return $this->hasMany(ThongBao::class, 'user_id', 'user_id');
    }
}