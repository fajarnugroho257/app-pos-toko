<?php

use App\Http\Controllers\akun\AkunkasirController;
use App\Http\Controllers\barang\BarangcabangController;
use App\Http\Controllers\barang\MasterbarangController;
use App\Http\Controllers\client\ClientController;
use App\Http\Controllers\DumpTransaksiController;
use App\Http\Controllers\laporan\LabarugiController;
use App\Http\Controllers\laporan\StokbarangController;
use App\Http\Controllers\log\LogBarangController;
use App\Http\Controllers\log\LogBarangMasterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\menu\UsertokopusatController;
use App\Http\Controllers\toko\TokocabangController;
use App\Http\Controllers\toko\TokopusatController;
use App\Http\Controllers\transaksi\TransaksiController;
use App\Http\Controllers\user\ProfilController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\menu\headingAppController;
use App\Http\Controllers\menu\menuController;
use App\Http\Controllers\menu\rolePenggunaController;
use App\Http\Controllers\menu\roleMenuController;
use App\Http\Controllers\website\AboutMeController;
use App\Http\Controllers\website\BannerController;
use App\Http\Controllers\website\FaqController;
use App\Http\Controllers\website\ListBarangController;
use App\Http\Controllers\website\PreferenceController;
use App\Http\Controllers\website\PromoController;
use App\Http\Controllers\website\TestimoniController;
use App\Http\Controllers\website\WhyChooseMeController;

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
    Route::get('/proses-cetak-nota', [DumpTransaksiController::class, 'cetakNota'])->name('proses-cetak-nota');
    Route::get('/sendToLocalServer', [DumpTransaksiController::class, 'sendToLocalServer'])->name('cetak-nota');
});

Route::middleware(['auth'])->group(function () {
    // dahsboard
    Route::middleware(['hasRole.page:dashboard'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/process-cari-sumary', [DashboardController::class, 'search_summary'])->name('cariSumary');
        Route::get('/show-barang-minim/{cabang_id}', [DashboardController::class, 'show_barang_minim'])->name('showBarangMinim');
        Route::get('/show-pendapatan/{cabang_id}', [DashboardController::class, 'show_pendapatan'])->name('showPendapatan');
        Route::get('/show-transaksi/{cabang_id}', [DashboardController::class, 'show_transaksi'])->name('showTransaksi');

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
        Route::post('/process-search-data-user', [UserController::class, 'search'])->name('cariUser');

    });

    /* YOUR ROUTE APLICATION */

    // Route::middleware(['hasRole.page:dataPenduduk'])->group(function () {
    Route::get('/data-transaksi', [TransaksiController::class, 'index'])->name('dataPenduduk');
    //
    Route::get('/tray', [DumpTransaksiController::class, 'index'])->name('tray');

    Route::get('/test', [DumpTransaksiController::class, 'cetakNota'])->name('cetakNota');
    // Route::get('/add-data-user', [UserController::class, 'create'])->name('tambahUser');
    // Route::post('/process-add-data-user', [UserController::class, 'store'])->name('aksiTambahUser');
    // Route::get('/update-data-user/{user_id}', [UserController::class, 'edit'])->name('UpdateUser');
    // Route::post('/process-update-data-user/{user_id}', [UserController::class, 'update'])->name('aksiUpdateUser');
    // Route::get('/process-delete-data-user/{user_id}', [UserController::class, 'destroy'])->name('deleteUser');
    // });
    // Toko Pusat
    Route::middleware(['hasRole.page:tokoPusat'])->group(function () {
        Route::get('/data-toko-pusat', [TokopusatController::class, 'index'])->name('tokoPusat');
        Route::get('/add-toko-pusat', [TokopusatController::class, 'create'])->name('tambahTokoPusat');
        Route::post('/process-add-toko-pusat', [TokopusatController::class, 'store'])->name('aksiTambahTokoPusat');
        Route::get('/update-toko-pusat/{slug}', [TokopusatController::class, 'edit'])->name('UpdateTokoPusat');
        Route::post('/process-update-toko-pusat', [TokopusatController::class, 'update'])->name('processUpdateTokoPusat');
        Route::get('/process-delete-toko-pusat/{slug}', [TokopusatController::class, 'destroy'])->name('processDeleteToko');
        Route::get('/user-toko-pusat/{slug}', [TokopusatController::class, 'user_pusat'])->name('userTokoPusat');
        Route::get('/add-user-toko-pusat/{user_id}/{pusat_id}', [TokopusatController::class, 'add_user_pusat'])->name('processAddUserToko');
        Route::get('/delete-user-toko-pusat/{id}/{pusat_id}', [TokopusatController::class, 'delete_user_pusat'])->name('processDeleteUserToko');
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
    // MASTER BARANG
    Route::middleware(['hasRole.page:masterBarang'])->group(function () {
        Route::get('/data-master-barang', [MasterbarangController::class, 'index'])->name('masterBarang');
        Route::get('/add-master-barang', [MasterbarangController::class, 'create'])->name('tambahMasterBarang');
        Route::post('/process-add-master-barang', [MasterbarangController::class, 'store'])->name('aksiTambahMasterBarang');
        Route::get('/update-master-barang/{slug}', [MasterbarangController::class, 'edit'])->name('updateMasterBarang');
        Route::post('/process-update-master-barang', [MasterbarangController::class, 'update'])->name('processUpdateMasterBarang');
        Route::get('/process-delete-master-barang/{id}', [MasterbarangController::class, 'destroy'])->name('processDeleteMasterBarang');
        Route::post('/process-cari-master-barang', [MasterbarangController::class, 'search'])->name('cariMasterBarang');
        //
        Route::get('/history-master-barang/{slug}', [MasterbarangController::class, 'history'])->name('historyMasterBarang');


    });
    // BARANG CABANG
    Route::middleware(['hasRole.page:barangCabang'])->group(function () {
        Route::get('/data-barang-cabang', [BarangcabangController::class, 'index'])->name('barangCabang');
        Route::get('/show-barang-cabang/{slug}', [BarangcabangController::class, 'detail'])->name('showBarangCabang');
        Route::get('/show-detail-barang-cabang/{barang_cabang_id}/{cabang_id}/{pusat_id}', [BarangcabangController::class, 'show_detail_log'])->name('showDetailCabangLog');
        Route::post('/get-not-exist-barang-cabang', [BarangcabangController::class, 'get_barang_not_exits'])->name('getDataProduk');
        Route::post('/process-add-barang-cabang', [BarangcabangController::class, 'store'])->name('processAddBarangCabang');
        Route::get('/update-barang-cabang/{id}', [BarangcabangController::class, 'edit'])->name('updatebarangCabang');
        Route::post('/process-update-barang-cabang', [BarangcabangController::class, 'update'])->name('processUpdateBarangCabang');
        Route::post('/process-cari-barang-cabang', [BarangcabangController::class, 'search'])->name('cariBarangCabang');
    });
    // AKUN KASIR
    Route::middleware(['hasRole.page:akunKasir'])->group(function () {
        Route::get('/data-akun-kasir', [AkunkasirController::class, 'index'])->name('akunKasir');
        Route::get('/add-akun-kasir', [AkunkasirController::class, 'create'])->name('tambahAkunKasir');
        Route::post('/process-add-akun-kasir', [AkunKasirController::class, 'store'])->name('aksiTambahAkunKasir');
        Route::get('/update-akun-kasir/{slug}', [AkunKasirController::class, 'edit'])->name('UpdateAkunKasir');
        Route::post('/process-update-akun-kasir', [AkunKasirController::class, 'update'])->name('processUpdateAkunKasir');
        Route::get('/process-delete-akun-kasir/{user_id}', [AkunKasirController::class, 'destroy'])->name('deleteAkunKasir');
    });
    // TRANSAKSI
    Route::middleware(['hasRole.page:transaksi'])->group(function () {
        Route::get('/data-transaksi', [TransaksiController::class, 'index'])->name('transaksi');
        Route::get('/data-transaksi-cabang/{slug}', [TransaksiController::class, 'show'])->name('transaksiCabang');
        Route::post('/show-nota', [TransaksiController::class, 'show_nota'])->name('show_nota');
        Route::post('/process-cari-transaksi', [TransaksiController::class, 'search'])->name('cariTransaksi');
        Route::get('/cetak-transaksi', [TransaksiController::class, 'cetakNotaTransaksi'])->name('cetakNotaTransaksi');
        // QZ TRAY
        Route::get('/get-data-print/{cart_id}', [TransaksiController::class, 'getPrintData'])->name('getPrintData');
    });
    // Log Barang
    Route::middleware(['hasRole.page:logBarang'])->group(function () {
        Route::get('/data-log-barang', [LogBarangController::class, 'index'])->name('logBarang');
        Route::get('/show-data-log-barang/{slug}', [LogBarangController::class, 'show'])->name('showLogBarangCabang');
        Route::get('/show-detail-data-log-barang/{barang_cabang_id}/{cabang_id}/{pusat_id}', [LogBarangController::class, 'show_detail_log'])->name('showDetailLog');
        Route::post('/process-cari-log-barang-cabang', [LogBarangController::class, 'search'])->name('cariLogBarangCabang');
    });
    // LABA RUGI
    Route::middleware(['hasRole.page:labaRugi'])->group(function () {
        Route::get('/data-laba-rugi', [LabarugiController::class, 'index'])->name('labaRugi');
        Route::get('/show-data-laba-rugi/{slug}', [LabarugiController::class, 'show'])->name('showLabaRugi');
        Route::post('/process-cari-laba', [LabarugiController::class, 'search'])->name('cariLaba');
        Route::post('/detail-laba-rugi', [LabarugiController::class, 'detail_laba'])->name('detailLabaRugi');
        Route::get('/process-cetak-pdf-laba-rugi/{slug}', [LabarugiController::class, 'download'])->name('downloadLabaRugi');
    });
    // Profil
    Route::middleware(['hasRole.page:dashboard'])->group(function () {
        Route::get('/data-user-profil', [ProfilController::class, 'index'])->name('profil');
        Route::post('/process-update-data-toko', [ProfilController::class, 'update_toko'])->name('processUpdateToko');
        Route::post('/process-update-data-user-profil', [ProfilController::class, 'update_profil'])->name('processUpdateProfil');
    });
    // Log Barang Master
    Route::middleware(['hasRole.page:logBarangMaster'])->group(function () {
        Route::get('/data-log-barang-master', [LogBarangMasterController::class, 'index'])->name('logBarangMaster');
        Route::get('/show-data-log-barang-master/{slug}', [LogBarangMasterController::class, 'show'])->name('showLogBarangMaster');
        Route::get('/show-detail-data-log-barang-master/{barang_cabang_id}/{cabang_id}/{pusat_id}', [LogBarangMasterController::class, 'show_detail_log'])->name('showDetailLogBarangMaster');
        Route::post('/process-cari-log-barang-cabang-master', [LogBarangMasterController::class, 'search'])->name('cariLogBarangMaster');
    });
    // User Pusat
    Route::middleware(['hasRole.page:userPusat'])->group(function () {
        Route::get('/user-pusat', [UsertokopusatController::class, 'index'])->name('userPusat');
        Route::get('/add-user-pusat', [UsertokopusatController::class, 'create'])->name('tambahUserPusat');
        Route::post('/process-add-user-pusat', [UsertokopusatController::class, 'store'])->name('processTambahUserPusat');
        Route::get('/process-delete-user-pusat/{id}', [UsertokopusatController::class, 'destroy'])->name('processDeleteAkun');
        

    });
    //
    Route::middleware(['hasRole.page:stokBarang'])->group(function () {
        Route::get('/stok-barang', [StokbarangController::class, 'index'])->name('stokBarang');
        Route::post('/process-stok-barang', [StokbarangController::class, 'search'])->name('cariStokBarang');
        Route::get('/process-cetak-pdf-stok-barang', [StokbarangController::class, 'download'])->name('downloadStokBarang');

    });

    // CLIENT TOKEN
    Route::middleware(['hasRole.page:clientToken'])->group(function () {
        Route::get('/client-token', [ClientController::class, 'index'])->name('clientToken');
        Route::get('/add-client-token', [ClientController::class, 'create'])->name('addClientToken');
        Route::post('/process-add-client-token', [ClientController::class, 'store'])->name('aksiAddClientToken');
        Route::get('/get-user-by-cabang', [ClientController::class, 'show']);
    });

    // **** WEBSITE **** //

    // BANNER
    Route::middleware(['hasRole.page:banner'])->group(function () {
        Route::get('/banner', [BannerController::class, 'index'])->name('banner');
        Route::get('/add-banner', [BannerController::class, 'create'])->name('addBanner');
        Route::post('/process-add-banner', [BannerController::class, 'store'])->name('processAddBanner');
        Route::get('/update-banner/{id}', [BannerController::class, 'edit'])->name('editBanner');
        Route::post('/process-update-banner/{id}', [BannerController::class, 'update'])->name('processEditBanner');
        Route::get('/process-delete-banner/{id}', [BannerController::class, 'destroy'])->name('processDeleteBanner');
    });

    // List Barang
    Route::middleware(['hasRole.page:listBarang'])->group(function () {
        Route::get('/daftar-barang', [ListBarangController::class, 'index'])->name('listBarang');
        Route::get('/update-daftar-barang/{slug}', [ListBarangController::class, 'edit'])->name('updateListBarang');
        Route::post('/process-update-daftar-barang/{slug}', [ListBarangController::class, 'update'])->name('processUpdateListBarang');
        Route::post('/process-cari-daftar-barang', [ListBarangController::class, 'search'])->name('cariListBarang');
    });

    // promo
    Route::middleware(['hasRole.page:promo'])->group(function () {
        Route::get('/promo', [PromoController::class, 'index'])->name('promo');
        Route::get('/add-promo', [PromoController::class, 'create'])->name('addPromo');
        Route::post('/process-add-promo', [PromoController::class, 'store'])->name('processAddPromo');
        Route::get('/edit-promo/{id}', [PromoController::class, 'edit'])->name('editPromo');
        Route::post('/process-edit-promo/{id}', [PromoController::class, 'update'])->name('processEditPromo');
        Route::post('/process-cari-promo', [PromoController::class, 'search'])->name('cariPromo');
        Route::get('/process-delete-promo/{id}', [PromoController::class, 'destroy'])->name('deletePromo');
        // ajax
        Route::post('/get_detail_promo', [PromoController::class, 'detail_barang_by_id'])->name('detailBarang');
        
    });

    // tentang kami
    Route::middleware(['hasRole.page:tentangKami'])->group(function () {
        Route::get('/about-me', [AboutMeController::class, 'index'])->name('tentangKami');
        Route::post('/process-edit-about-me/{id}', [AboutMeController::class, 'update'])->name('processEditTentangKami');
        Route::get('/process-delete-image', [AboutMeController::class, 'destroy'])->name('hapusImage');
    });

    // Pref
    Route::middleware(['hasRole.page:preference'])->group(function () {
        Route::get('/preference', [PreferenceController::class, 'index'])->name('preference');
        Route::get('/edit-preference', [PreferenceController::class, 'edit'])->name('editPreference');
        Route::post('/process-edit-preference/{id}', [PreferenceController::class, 'update'])->name('processEditPreference');
    });

    // why choose me
    Route::middleware(['hasRole.page:whyChooseMe'])->group(function () {
        Route::get('/why-choose-me', [WhyChooseMeController::class, 'index'])->name('whyChooseMe');
        Route::get('/add-why-choose-me', [WhyChooseMeController::class, 'create'])->name('addWhyChooseMe');
        Route::post('/process-add-why-choose-me', [WhyChooseMeController::class, 'store'])->name('processAddWhyChooseMe');
        Route::get('/edit-why-choose-me/{id}', [WhyChooseMeController::class, 'edit'])->name('editWhyChooseMe');
        Route::post('/process-edit-why-choose-me/{id}', [WhyChooseMeController::class, 'update'])->name('processEditWhyChooseMe');
        Route::get('/process-delete-why-choose-me/{id}', [WhyChooseMeController::class, 'destroy'])->name('processDeleteWhyChooseMe');
    });

    // testimoni
    Route::middleware(['hasRole.page:testimoni'])->group(function () {
        Route::get('/testimoni', [TestimoniController::class, 'index'])->name('testimoni');
        Route::get('/add-testimoni', [TestimoniController::class, 'create'])->name('addTestimoni');
        Route::post('/process-add-testimoni', [TestimoniController::class, 'store'])->name('processAddTestimoni');
        Route::get('/edit-testimoni/{id}', [TestimoniController::class, 'edit'])->name('editTestimoni');
        Route::post('/process-edit-testimoni/{id}', [TestimoniController::class, 'update'])->name('processEditTestimoni');
        Route::get('/process-delete-testimoni/{id}', [TestimoniController::class, 'destroy'])->name('processDeleteTestimoni');
    });

    // faq
    Route::middleware(['hasRole.page:faq'])->group(function () {
        Route::get('/faq', [FaqController::class, 'index'])->name('faq');
        Route::get('/add-faq', [FaqController::class, 'create'])->name('addFaq');
        Route::post('/process-add-faq', [FaqController::class, 'store'])->name('processAddFaq');
        Route::get('/edit-faq/{id}', [FaqController::class, 'edit'])->name('editFaq');
        Route::post('/process-edit-faq/{id}', [FaqController::class, 'update'])->name('processEditFaq');
        Route::get('/process-delete-faq/{id}', [FaqController::class, 'destroy'])->name('processDeleteFaq');
    });
    
    /* END YOUR ROUTE APLICATION */
});


