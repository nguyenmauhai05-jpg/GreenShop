<?php

namespace App\Http\Controllers;

use App\Models\CayCanh;
use App\Models\DonHang;
use Illuminate\Support\Facades\DB;

class TrangChuController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | SẢN PHẨM BÁN CHẠY
        |--------------------------------------------------------------------------
        | Tính tổng số lượng đã bán từ chi tiết đơn hàng.
        | Chỉ lấy các đơn đã giao hoặc đã hoàn thành.
        */

        $sanPhamBanChay = DB::table('chi_tiet_don_hang as ct')
            ->join('don_hang as dh', 'dh.order_id', '=', 'ct.order_id')
            ->join('cay_canh as cc', 'cc.plant_id', '=', 'ct.plant_id')
            ->where('cc.trang_thai', 'Đang bán')
            ->where('cc.so_luong', '>', 0)
            ->whereIn('dh.trang_thai', array_merge(
                DonHang::databaseStatusAliases('delivered'),
                DonHang::databaseStatusAliases('completed')
            ))
            ->select(
                'cc.plant_id',
                'cc.ten_cay',
                'cc.gia',
                'cc.anh_dai_dien',
                'cc.so_luong',
                DB::raw('SUM(ct.so_luong) as tong_da_ban')
            )
            ->groupBy(
                'cc.plant_id',
                'cc.ten_cay',
                'cc.gia',
                'cc.anh_dai_dien',
                'cc.so_luong'
            )
            ->orderByDesc('tong_da_ban')
            ->limit(4)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | SẢN PHẨM MỚI
        |--------------------------------------------------------------------------
        | Bảng cay_canh chưa có created_at nên tạm dùng plant_id giảm dần.
        */

        $sanPhamMoi = CayCanh::query()
            ->where('trang_thai', 'Đang bán')
            ->where('so_luong', '>', 0)
            ->orderByDesc('plant_id')
            ->limit(4)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ĐÁNH GIÁ KHÁCH HÀNG
        |--------------------------------------------------------------------------
        | Lấy 3 đánh giá mới nhất.
        | JOIN người dùng để lấy họ tên.
        | JOIN cây cảnh để lấy tên sản phẩm.
        */

        $danhGias = DB::table('danh_gia as dg')
            ->leftJoin(
                'nguoi_dung as nd',
                'nd.user_id',
                '=',
                'dg.user_id'
            )
            ->leftJoin(
                'cay_canh as cc',
                'cc.plant_id',
                '=',
                'dg.plant_id'
            )
            ->select(
                'dg.review_id',
                'dg.so_sao',
                'dg.noi_dung',
                'dg.ngay_danh_gia',
                'nd.ho_ten as ten_khach_hang',
                'cc.ten_cay'
            )
            ->whereNotNull('dg.noi_dung')
            ->where('dg.noi_dung', '!=', '')
            ->orderByDesc('dg.ngay_danh_gia')
            ->orderByDesc('dg.review_id')
            ->limit(3)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TRẢ DỮ LIỆU RA TRANG CHỦ
        |--------------------------------------------------------------------------
        */

        return view('trang_chu.index', compact(
            'sanPhamBanChay',
            'sanPhamMoi',
            'danhGias'
        ));
    }
}