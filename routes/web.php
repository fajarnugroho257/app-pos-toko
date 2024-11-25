<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\toko\TokocabangController;
use App\Http\Controllers\toko\TokopusatController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\menu\headingAppController;
use App\Http\Controllers\menu\menuController;
use App\Http\Controllers\menu\rolePenggunaController;
use App\Http\Controllers\menu\roleMenuController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// login
Route::middleware(['guest'])->group(function () {
    Route::get('/', [LoginController::class, 'index'])->name('login');
    Route::post('/login-process', [LoginController::class, 'loginProcess'])->name('login-process');
});

Route::middleware(['auth'])->group(function () {
    // dahsboard
    Route::middleware(['hasRole.page:dashboard'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
    // logout
    Route::get('/log-out', [LoginController::class, 'logOut'])->name('logOut');
    // heading
    Route::middleware(['hasRole.page:headingApp'])->group(function () {
        Route::get('/heading-aplikasi', [headingAppController::class, 'index'])->name('headingApp');
        Route::get('/add-heading-aplikasi', [headingAppController::class, 'create'])->name('tambahHeadingApp');
        Route::post('/process-add-heading-aplikasi', [headingAppController::class, 'store'])->name('aksiTambahHeadingApp');
        Route::get('/update-heading-aplikasi/{app_heading_id}', [headingAppController::class, 'edit'])->name('updateHeadingApp');
        Route::post('/aksi-update-heading-aplikasi/{app_heading_id}', [headingAppController::class, 'update'])->name('aksiUpdateHeadingApp');
        Route::get('/process-delete-heading-aplikasi/{app_heading_id}', [headingAppController::class, 'destroy'])->name('deleteHeadingApp');
    });
    // menu
    Route::middleware(['hasRole.page:menuApp'])->group(function () {
        Route::get('/menu-aplikasi', [menuController::class, 'index'])->name('menuApp');
        Route::get('/add-menu-aplikasi', [menuController::class, 'create'])->name('tambahMenuApp');
        Route::post('/process-add-menu-aplikasi', [menuController::class, 'store'])->name('aksiTambahMenuApp');
        Route::get('/update-menu-aplikasi/{menu_id}', [menuController::class, 'edit'])->name('updateMenuApp');
        Route::post('/aksi-update-menu-aplikasi/{menu_id}', [menuController::class, 'update'])->name('aksiUpdateMenuApp');
        Route::get('/process-delete-menu-aplikasi/{menu_id}', [menuController::class, 'destroy'])->name('deleteMenuApp');
    });
    // role
    Route::middleware(['hasRole.page:rolePengguna'])->group(function () {
        Route::get('/role-pengguna', [rolePenggunaController::class, 'index'])->name('rolePengguna');
        Route::get('/add-role-pengguna', [rolePenggunaController::class, 'create'])->name('tambahRolePengguna');
        Route::post('/process-add-role-pengguna', [rolePenggunaController::class, 'store'])->name('aksiTambahRolePengguna');
        Route::get('/update-role-pengguna/{role_id}', [rolePenggunaController::class, 'edit'])->name('updateRolePengguna');
        Route::post('/aksi-update-role-pengguna/{role_id}', [rolePenggunaController::class, 'update'])->name('aksiUpdateRolePengguna');
    });
    // menu
    Route::middleware(['hasRole.page:roleMenu'])->group(function () {
        Route::get('/role-menu', [roleMenuController::class, 'index'])->name('roleMenu');
        Route::get('/list-data-role-menu/{role_id}', [roleMenuController::class, 'listDataRoleMenu'])->name('listDataRoleMenu');
        Route::post('/add-role-menu', [roleMenuController::class, 'tambahRoleMenu'])->name('tambahRoleMenu');
    });
    // User
    Route::middleware(['hasRole.page:dataUser'])->group(function () {
        Route::get('/data-user', [UserController::class, 'index'])->name('dataUser');
        Route::get('/add-data-user', [UserController::class, 'create'])->name('tambahUser');
        Route::post('/process-add-data-user', [UserController::class, 'store'])->name('aksiTambahUser');
        Route::get('/update-data-user/{user_id}', [UserController::class, 'edit'])->name('UpdateUser');
        Route::post('/process-update-data-user/{user_id}', [UserController::class, 'update'])->name('aksiUpdateUser');
        Route::get('/process-delete-data-user/{user_id}', [UserController::class, 'destroy'])->name('deleteUser');
    });

    /* YOUR ROUTE APLICATION */

    Route::middleware(['hasRole.page:dataPenduduk'])->group(function () {
        Route::get('/data-transaksi', [TransaksiController::class, 'index'])->name('dataPenduduk');
        Route::get('/print-data', function () {
            return response()->json([
                'content' => "Test Print Thermal",
                'options' => [
                    'printer' => "POS-58", // Sesuaikan nama printer
                    'font-size' => 12
                ]
            ]);
        })->name('dataPrint');

        Route::get('/test', [TransaksiController::class, 'cetakNota'])->name('cetakNota');
        // Route::get('/add-data-user', [UserController::class, 'create'])->name('tambahUser');
        // Route::post('/process-add-data-user', [UserController::class, 'store'])->name('aksiTambahUser');
        // Route::get('/update-data-user/{user_id}', [UserController::class, 'edit'])->name('UpdateUser');
        // Route::post('/process-update-data-user/{user_id}', [UserController::class, 'update'])->name('aksiUpdateUser');
        // Route::get('/process-delete-data-user/{user_id}', [UserController::class, 'destroy'])->name('deleteUser');
    });
    // Toko Pusat
    Route::middleware(['hasRole.page:tokoPusat'])->group(function () {
        Route::get('/data-toko-pusat', [TokopusatController::class, 'index'])->name('tokoPusat');
        Route::get('/add-toko-pusat', [TokopusatController::class, 'create'])->name('tambahTokoPusat');
        Route::post('/process-add-toko-pusat', [TokopusatController::class, 'store'])->name('aksiTambahTokoPusat');
        Route::get('/update-toko-pusat/{slug}', [TokopusatController::class, 'edit'])->name('UpdateTokoPusat');
        Route::post('/process-update-toko-pusat', [TokopusatController::class, 'update'])->name('processUpdateTokoPusat');
    });
    // TOKO CABANG
    Route::middleware(['hasRole.page:tokoCabang'])->group(function () {
        Route::get('/data-toko-cabang', [TokocabangController::class, 'index'])->name('tokoCabang');
        Route::get('/add-toko-cabang', [TokocabangController::class, 'create'])->name('tambahTokoCabang');
        Route::post('/process-add-toko-cabang', [TokocabangController::class, 'store'])->name('aksiTambahTokoCabang');
        Route::get('/update-toko-cabang/{slug}', [TokocabangController::class, 'edit'])->name('updateTokoCabang');
        Route::post('/process-update-toko-cabang', [TokocabangController::class, 'update'])->name('processUpdatetokoCabang');
        Route::get('/process-delete-toko-cabang/{id}', [TokocabangController::class, 'destroy'])->name('processDeletetokoCabang');
    });

    /* END YOUR ROUTE APLICATION */
});


