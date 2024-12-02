<?php

use App\Http\Controllers\api\BarangController;
use App\Http\Controllers\barang\BarangcabangController;
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

Route::get('/api-data-barang-cabang', [BarangController::class, 'show']);
Route::get('/api-barcode-data-barang-cabang', [BarangController::class, 'detail']);
Route::post('/detail-api-barcode-data-barang-cabang', [BarangController::class, 'detail_data']);
