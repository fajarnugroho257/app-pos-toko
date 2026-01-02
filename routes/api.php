<?php

use App\Http\Controllers\api\BarangController;
use App\Http\Controllers\api\CartController;
use App\Http\Controllers\api\LoginController;
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
    Route::get('/list-transaksi-data-barang-cabang', [TransaksiController::class, 'show']);
    Route::post('/detail-nota', [TransaksiController::class, 'show_nota']);
    Route::get('/logout', [App\Http\Controllers\api\LoginController::class, 'logout']);

});
// cart
Route::middleware(['jwt.verify'])->group(function () {
    Route::post('/api-store-cart-data', [CartController::class, 'store']);
    Route::get('/get-cart-draft', [CartController::class, 'show']);
    Route::post('/get-cart-subtotal-draft', [CartController::class, 'sub_total']);
});
// transaksi
Route::middleware(['jwt.verify'])->group(function () {
    Route::post('/store-bayar', [TransaksiController::class, 'store']);
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

