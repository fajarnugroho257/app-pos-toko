<?php

use App\Http\Controllers\api\BarangController;
use App\Http\Controllers\api\CartController;
use App\Http\Controllers\api\OfflineController;
use App\Http\Controllers\api\ReturController;
use App\Http\Controllers\api\TokenController;
use App\Http\Controllers\api\TransaksiController;
use App\Http\Controllers\api\WebsiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login-api', App\Http\Controllers\api\LoginController::class);
Route::middleware(['jwt.verify'])->group(function () {
    Route::get('/api-data-barang-cabang', [BarangController::class, 'show']);
    Route::get('/api-barcode-data-barang-cabang', [BarangController::class, 'detail']);
    Route::post('/detail-api-barcode-data-barang-cabang', [BarangController::class, 'detail_data']);
    Route::post('/list-data-barang-by-cabang', [BarangController::class, 'list_data_barang']);
    Route::post('/list-data-barang-by-cabang-all', [BarangController::class, 'list_data_barang_all']);
    Route::post('/list-transaksi-data-barang-cabang', [TransaksiController::class, 'show']);
    Route::post('/list-transaksi-data-barang-cabang-booking', [TransaksiController::class, 'booking']);
    Route::post('/list-transaksi-data-barang-cabang-hutang', [TransaksiController::class, 'hutang']);
    Route::post('/change-status-by-cart-id', [TransaksiController::class, 'change_status_by_cart_id']);
    Route::post('/detail-nota', [TransaksiController::class, 'show_nota']);
    Route::get('/logout', [App\Http\Controllers\api\LoginController::class, 'logout']);

});
// cart
Route::middleware(['jwt.verify'])->group(function () {
    Route::post('/api-store-cart-data', [CartController::class, 'store']);
    Route::get('/get-cart-draft', [CartController::class, 'show']);
    Route::post('/get-cart-subtotal-draft', [CartController::class, 'sub_total']);
    Route::post('/create-draft-pembelian', [CartController::class, 'create_draft_pembelian']);
    Route::post('/create-hutang-pembelian', [CartController::class, 'create_hutang_pembelian']);
    Route::get('/list-cart-data', [CartController::class, 'list_cart_by_id']);
    Route::post('/store-cicilan', [CartController::class, 'store_cicilan']);
    Route::get('/ubah-lunas', [CartController::class, 'ubah_lunas']);
});
// transaksi
Route::middleware(['jwt.verify'])->group(function () {
    Route::post('/store-bayar', [TransaksiController::class, 'store']);
    Route::post('/delete-transaksi', [TransaksiController::class, 'destroy']);
});

// transaksi
Route::middleware(['jwt.verify'])->group(function () {
    Route::post('/store-transaksi-offline', [OfflineController::class, 'store']);
    Route::post('/store-transaksi-offline-byId', [OfflineController::class, 'store_one_data']);
});

// retur
Route::middleware(['jwt.verify'])->group(function () {
    Route::post('/store-retur', [ReturController::class, 'store']);
    Route::post('/delete-retur', [ReturController::class, 'destroy']);
});
// check token
Route::get('/api-cek-token', [App\Http\Controllers\api\LoginController::class, 'check_token']);

// Route::get('/non-get-cart-draft', [CartController::class, 'show']);
// test non jwt
Route::get('/test-api-barcode-data-barang-cabang', [BarangController::class, 'detail']);

// route untuk get data barang dan user berdasarkan token cabang
Route::post('/get-data-barang-by-token', [TokenController::class, 'show']);
//
Route::post('/post-data-transaksi', [TokenController::class, 'store']);
// WEBSITE
Route::post('/get-banner-by-id', [WebsiteController::class, 'index']);
Route::post('/get-promo-by-id', [WebsiteController::class, 'promo']);
Route::post('/get-all-barang', [WebsiteController::class, 'barang']);
Route::post('/about-me', [WebsiteController::class, 'tentang_kami']);
Route::post('/get-terbanyak', [WebsiteController::class, 'produk_terbanyak']);
Route::post('/get-pref', [WebsiteController::class, 'preference']);
Route::post('/get-why-choose-me', [WebsiteController::class, 'get_why_choose']);
Route::post('/get-testimoni', [WebsiteController::class, 'get_testimoni']);
Route::post('/get-faq', [WebsiteController::class, 'get_faq']);
