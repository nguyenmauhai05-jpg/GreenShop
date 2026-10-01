<?php

namespace App\Http\Controllers;


use App\Models\CayCanh;



class ChiTietCayController extends Controller
{


    public function index($id)
    {


        $cay = CayCanh::with([
            'danhMuc',
            'danhGias' => function ($query) {
                $query->where('trang_thai', 'da_duyet')
                    ->orderByDesc('ngay_danh_gia');
            }
        ])
        ->where(
            'plant_id',
            $id
        )
        ->first();



        if(!$cay)
        {
            abort(404);
        }



        $cayLienQuan = CayCanh::where(
            'category_id',
            $cay->category_id
        )
        ->where(
            'plant_id',
            '!=',
            $id
        )
        ->limit(6)
        ->get();



        return view(
            'chi_tiet_cay.index',
            compact(
                'cay',
                'cayLienQuan'
            )
        );


    }

}