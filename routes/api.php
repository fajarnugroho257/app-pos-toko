<?php

use App\Http\Controllers\api\BarangController;
use App\Http\Controllers\api\CartController;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\TransaksiController;
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
    Route::get('/logout', [App\Http\Controllers\api\LoginController::class, 'logout']);

});
// cart
Route::middleware(['jwt.verify'])->group(function () {
    Route::post('/api-store-cart-data', [CartController::class, 'store']);
    Route::get('/get-cart-draft', [CartController::class, 'show']);
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
