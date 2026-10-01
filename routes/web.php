<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChiTietCayController;
use App\Http\Controllers\CuaHangController;
use App\Http\Controllers\DanhGiaController;
use App\Http\Controllers\DonHangController;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\OnlinePaymentController;
use App\Http\Controllers\TaiKhoanController;
use App\Http\Controllers\ThanhToanController;
use App\Http\Controllers\TrangChuController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/admin/bao-cao.php';
require __DIR__ . '/admin/cai-dat-he-thong.php';
require __DIR__ . '/admin/cay-canh.php';
require __DIR__ . '/admin/danh-gia.php';
require __DIR__ . '/admin/danh-muc.php';
require __DIR__ . '/admin/dashboard.php';
require __DIR__ . '/admin/don-hang.php';
require __DIR__ . '/admin/voucher.php';


/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
| Guest / Customer / Admin đều truy cập được
*/


// =========================
// TRANG CHỦ
// =========================

Route::get(
    '/',
    [TrangChuController::class, 'index']
)->name('trang-chu');


Route::get(
    '/trang-chu',
    [TrangChuController::class, 'index']
);


// =========================
// CỬA HÀNG
// =========================


Route::view('/blog', 'blog.index')->name('blog');

Route::get(
    '/cua-hang',
    [CuaHangController::class, 'index']
)->name('cua-hang');

Route::view('/gioi-thieu', 'gioi_thieu.index')
    ->name('gioi-thieu');

// =========================
// CHI TIẾT CÂY
// =========================

Route::get(
    '/cay/{id}',
    [ChiTietCayController::class, 'index']
)->name('chi-tiet-cay');


// =========================
// ĐĂNG KÝ
// =========================

Route::get('/dang-ky', function () {

    return view('auth.dang_ky');

})->name('dang-ky');


Route::post(
    '/dang-ky',
    [AuthController::class, 'dangKy']
)->name('dang-ky.xu-ly');


// =========================
// ĐĂNG NHẬP
// =========================

Route::get('/dang-nhap', function () {

    return view('auth.dang_nhap');

})->name('dang-nhap');


Route::post(
    '/dang-nhap',
    [AuthController::class, 'dangNhap']
)->name('dang-nhap.xu-ly');


// =========================
// ĐĂNG XUẤT
// =========================

Route::post(
    '/dang-xuat',
    [AuthController::class, 'dangXuat']
)->name('dang-xuat');


// =========================================================
// QUÊN / ĐẶT LẠI MẬT KHẨU
// =========================================================

Route::get(
    '/quen-mat-khau',
    [AuthController::class, 'hienThiQuenMatKhau']
)->name('password.request');


Route::post(
    '/quen-mat-khau',
    [AuthController::class, 'guiLienKetDatLai']
)->name('password.email');


Route::get(
    '/quen-mat-khau/da-gui',
    [AuthController::class, 'hienThiEmailDaGui']
)->name('password.sent');


Route::get(
    '/dat-lai-mat-khau/thanh-cong',
    [AuthController::class, 'hienThiDatLaiThanhCong']
)->name('password.success');


Route::get(
    '/dat-lai-mat-khau/{token}',
    [AuthController::class, 'hienThiDatLaiMatKhau']
)->name('password.reset');


Route::post(
    '/dat-lai-mat-khau',
    [AuthController::class, 'datLaiMatKhau']
)->name('password.update');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER
|--------------------------------------------------------------------------
*/


// payOS must deliver webhook without a logged-in customer session.
Route::post('/thanh-toan/payos/webhook', [OnlinePaymentController::class, 'payosWebhook'])
    ->name('thanh-toan.payos.webhook');

// AI chatbot is public: guests can ask questions without logging in.
Route::get('/cham-soc-cay', [AIController::class, 'index'])->name('cham-soc-cay');
Route::post('/cham-soc-cay/hoi-ai', [AIController::class, 'chat'])->name('cham-soc-cay.chat');

Route::middleware('auth')->group(function () {


    Route::get('/thanh-toan/payos/return', [OnlinePaymentController::class, 'payosReturn'])->name('thanh-toan.payos.return');
    Route::get('/thanh-toan/paypal/return', [OnlinePaymentController::class, 'paypalReturn'])->name('thanh-toan.paypal.return');
    Route::get('/thanh-toan/paypal/cancel', [OnlinePaymentController::class, 'paypalCancel'])->name('thanh-toan.paypal.cancel');

    Route::get(
        '/thanh-toan/online/{transaction}/retry',
        [OnlinePaymentController::class, 'retry']
    )->name('thanh-toan.online.retry');

    /*
    |--------------------------------------------------------------------------
    | GIỎ HÀNG
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/gio-hang',
        [GioHangController::class, 'index']
    )->name('gio-hang');


    Route::post(
        '/gio-hang/them',
        [GioHangController::class, 'them']
    )->name('gio-hang.them');

    Route::patch('/thanh-toan/so-luong', [ThanhToanController::class, 'updateCheckoutQuantity'])
        ->name('thanh-toan.quantity');

    Route::post(
        '/gio-hang/mua-ngay',
        [GioHangController::class, 'muaNgay']
    )->name('gio-hang.mua-ngay');


    Route::patch(
        '/gio-hang/{cartDetailId}',
        [GioHangController::class, 'capNhat']
    )->name('gio-hang.cap-nhat');


    Route::delete(
        '/gio-hang/{cartDetailId}',
        [GioHangController::class, 'xoa']
    )->name('gio-hang.xoa');



    /*
    |--------------------------------------------------------------------------
    | AI CHĂM SÓC CÂY
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/cham-soc-cay/cuoc-tro-chuyen-moi',
        [AIController::class, 'taoCuocTroChuyen']
    )->name('cham-soc-cay.tao-moi');


    Route::delete(
        '/cham-soc-cay/cuoc-tro-chuyen/{id}',
        [AIController::class, 'xoaCuocTroChuyen']
    )
        ->whereNumber('id')
        ->name('cham-soc-cay.xoa');


    Route::delete(
        '/cham-soc-cay/lich-su',
        [AIController::class, 'xoaTatCa']
    )->name('cham-soc-cay.xoa-tat-ca');



    /*
    |--------------------------------------------------------------------------
    | HỒ SƠ CÁ NHÂN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/tai-khoan/ho-so',
        [TaiKhoanController::class, 'hoSo']
    )->name('tai-khoan.ho-so');


    Route::put(
        '/tai-khoan/ho-so',
        [TaiKhoanController::class, 'capNhatHoSo']
    )->name('tai-khoan.ho-so.cap-nhat');


    /*
    |--------------------------------------------------------------------------
    | SỔ ĐỊA CHỈ
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/tai-khoan/so-dia-chi',
        [TaiKhoanController::class, 'soDiaChi']
    )->name('tai-khoan.so-dia-chi');

    Route::post(
        '/tai-khoan/so-dia-chi',
        [TaiKhoanController::class, 'themDiaChi']
    )->name('tai-khoan.so-dia-chi.them');

    Route::put(
        '/tai-khoan/so-dia-chi/{diaChi}',
        [TaiKhoanController::class, 'capNhatDiaChi']
    )->whereNumber('diaChi')->name('tai-khoan.so-dia-chi.cap-nhat');

    Route::patch(
        '/tai-khoan/so-dia-chi/{diaChi}/mac-dinh',
        [TaiKhoanController::class, 'datDiaChiMacDinh']
    )->whereNumber('diaChi')->name('tai-khoan.so-dia-chi.mac-dinh');

    Route::delete(
        '/tai-khoan/so-dia-chi/{diaChi}',
        [TaiKhoanController::class, 'xoaDiaChi']
    )->whereNumber('diaChi')->name('tai-khoan.so-dia-chi.xoa');






    /*
    |--------------------------------------------------------------------------
    | CHECKOUT / THANH TOÁN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/thanh-toan',
        [ThanhToanController::class, 'index']
    )->name('thanh-toan.index');


    Route::get('/thanh-toan/cap-nhat-gio', [ThanhToanController::class, 'refreshCart'])
        ->name('thanh-toan.cart-refresh');

    Route::post(
        '/thanh-toan/dat-hang',
        [ThanhToanController::class, 'placeOrder']
    )->name('thanh-toan.place-order');


    // API bản đồ: OpenStreetMap/Nominatim + OSRM.
    Route::get(
        '/api/map/geocode',
        [MapController::class, 'geocode']
    )->name('map.geocode');

    Route::get(
        '/api/map/reverse-geocode',
        [MapController::class, 'reverseGeocode']
    )->name('map.reverse-geocode');

    Route::get(
        '/api/map/phi-van-chuyen',
        [MapController::class, 'shippingQuote']
    )->name('map.shipping-quote');





    /*
    |--------------------------------------------------------------------------
    | ĐƠN HÀNG
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/don-hang',
        [DonHangController::class, 'index']
    )->name('don-hang.index');


    Route::get(
        '/don-hang/{donHang}',
        [DonHangController::class, 'show']
    )
    ->whereNumber('donHang')
    ->name('don-hang.show');


    Route::post(
        '/don-hang/{donHang}/huy',
        [DonHangController::class, 'cancel']
    )
    ->whereNumber('donHang')
    ->name('don-hang.cancel');

    // Chỉ khách hàng sở hữu đơn Đã giao được xác nhận nhận hàng.
    Route::post(
        '/don-hang/{donHang}/xac-nhan-nhan-hang',
        [DonHangController::class, 'confirmReceipt']
    )
    ->whereNumber('donHang')
    ->name('don-hang.confirm-receipt');



    /*
    |--------------------------------------------------------------------------
    | ĐÁNH GIÁ SẢN PHẨM
    |--------------------------------------------------------------------------
    */

    Route::get('/danh-gia', [DanhGiaController::class, 'index'])
        ->name('danh-gia.index');

    Route::get('/danh-gia/{orderDetail}/viet', [DanhGiaController::class, 'create'])
        ->whereNumber('orderDetail')
        ->name('danh-gia.create');

    Route::post('/danh-gia/{orderDetail}', [DanhGiaController::class, 'store'])
        ->whereNumber('orderDetail')
        ->name('danh-gia.store');



});