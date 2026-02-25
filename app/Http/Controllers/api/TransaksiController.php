<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BarangCabang;
use App\Models\BarangLog;
use App\Models\Cart;
use App\Models\CartData;
use App\Models\ReturHistory;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
            'ttlBayar' => 'required',
            'valInputBayar' => 'required',
            'kembalian' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        //
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'errors' => 'user belum melakukan login'
            ], 422);
        }
        // cart datas
        $cart = Cart::where('cart_id', $request->cart_id)->first();
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'errors' => 'Data keranjang tidak tersedia..'
            ], 422);
        }
        // insert to transaksi
        $stTransaksi = Transaksi::create([
            'cart_id' => $request->cart_id,
            'user_id' => Auth::user()->user_id,
            'trans_pelanggan' => $request->valInputPelanggan,
            'trans_total' => $request->ttlBayar,
            'trans_bayar' => $request->valInputBayar,
            'trans_kembalian' => $request->kembalian,
            'trans_date' => date('Y-m-d H:i:s')
        ]);
        if ($stTransaksi) {
            // kurangi stok barang cabang
            $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', Auth::user()->user_id)->where('role_id', 'R0005')->first();
            $cartData = CartData::where('cart_id', $request->cart_id)->get();
            // loop
            foreach ($cartData as $key => $value) {
                // find barang cabang
                $barangCabang = BarangCabang::where('id', '=', $value['barang_cabang_id'])->where('cabang_id', $dataUser->users_data->cabang_id)->first();
                //
                $jlh_barang_sblm_tambah = $barangCabang->barang_stok;
                // kurangi
                $sisa = $barangCabang->barang_stok - $value['cart_qty'];
                $barangCabang->barang_stok = $sisa;
                // update stok
                if ($barangCabang->save()) {
                    // insert log barang
                    BarangLog::create([
                        'user_id' => Auth::user()->user_id,
                        'pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
                        'cabang_id' => $dataUser->users_data->cabang_id,
                        'barang_cabang_id' => $barangCabang->id,
                        'barang_awal' => $jlh_barang_sblm_tambah,
                        'barang_transaksi' => $value['cart_qty'],
                        'barang_transaksi_id' => $stTransaksi->id,
                        'barang_akhir' => $sisa,
                        'barang_st' => 'transaksi',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
            // update cart to yes
            $cart->cart_st = 'yes';
            if ($cart->save()) {
                // cetak
                // try {
                //     // exec('wmic printer get name', $printers);
                //     // dd($printers);
                //     // Nama printer sesuai konfigurasi sistem (lihat di 'Devices and Printers')
                //     // $connector = new WindowsPrintConnector("POS-58");
                //     // $connector = new WindowsPrintConnector("\\\\LAPTOP-1OLVA8NB\\POS-58");
                //     $connector = new WindowsPrintConnector("smb://LAPTOP-1OLVA8NB/POS-58");
                //     // $connector = new FilePrintConnector("LPT1");

                //     // data
                //     $transaksiCart = Transaksi::where('cart_id', $request->cart_id)->first();
                //     $cartData = CartData::where('cart_id', $transaksiCart->cart_id)->orderBy('cart_urut', 'DESC')->get();

                //     // Inisialisasi printer
                //     $printer = new Printer($connector);

                //     // Tambahkan teks atau format nota
                //     $printer->setJustification(Printer::JUSTIFY_CENTER);
                //     $printer->text($dataUser->users_data->toko_cabang->toko_pusat->pusat_nama . "\n");
                //     $printer->text($dataUser->users_data->toko_cabang->cabang_nama . "\n");
                //     // $printer->feed();
                //     $grandTotal = 0;
                //     $printer->setJustification(Printer::JUSTIFY_LEFT);
                //     $printer->text("Item        Qty    Harga\n");
                //     $printer->text("------------------------------\n");
                //     foreach ($cartData as $key => $value) {
                //         $grandTotal += $value['cart_subtotal'];
                //         $printer->text($value['cart_nama'] . "\n");
                //         $printer->text(str_pad('Rp.' . number_format($value['cart_harga_jual'], 0, ',', '.'), 13) . str_pad($value['cart_qty'], 4) . str_pad('Rp.' . number_format($value['cart_subtotal'], 0, ',', '.'), 14) . "\n");
                //     }
                //     $printer->text("------------------------------\n");
                //     $printer->text(str_pad("Total", 17) . str_pad('Rp.' . number_format($grandTotal, 0, ',', '.'), 15));
                //     $printer->text(str_pad("Cash", 17) . str_pad('Rp.' . number_format($transaksiCart->trans_bayar, 0, ',', '.'), 15));
                //     $printer->text(str_pad("Kembalian", 17) . str_pad('Rp.' . number_format($transaksiCart->trans_kembalian, 0, ',', '.'), 15));
                //     // $printer->feed(2);
                //     // Akhiri cetakan
                //     $printer->cut();
                //     $printer->close();
                //     // return
                //     return response()->json([
                //         'success' => true,
                //         'message' => 'Berhasil melakukan transaksi',
                //         'data' => $request->all(),
                //     ]);
                // } catch (\Exception $e) {
                //     // return "Terjadi kesalahan: " . $e->getMessage();
                //     return response()->json([
                //         'success' => false,
                //         'message' => "Terjadi kesalahan cetak: " . $e->getMessage(),
                //         'data' => $request->all(),
                //     ]);
                // }
                // $data = array();
                // $response = Http::post('http://192.168.1.106:8000/cetak-nota', $data);

                // return response()->json([
                //     'message' => 'Data sent to local server',
                //     'response' => $response->body(),
                // ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil melakukan transaksi',
                    'data' => $request->all(),
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', Auth::user()->user_id)->where('role_id', 'R0005')->first();
        $rs_transaksi = Transaksi::with(['cart.cart_data', 'users'])
            ->whereRelation('cart', 'cart_st', 'yes')
            ->whereRelation('cart', 'cabang_id', $dataUser->users_data->cabang_id)
            ->whereRelation('cart', 'pusat_id', $dataUser->users_data->toko_cabang->toko_pusat->id)
            ->orderBy('trans_date', 'DESC')
            ->whereBetween(DB::raw('DATE(trans_date)'), [$request['start'], $request['end']])
            ->get();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data',
            'data' => $rs_transaksi,
            'request' => $request->all(),
        ]);
    }

    public function booking(Request $request)
    {
        $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', Auth::user()->user_id)->where('role_id', 'R0005')->first();
        $rs_booking = Cart::with('cart_draft', 'cart_data')
                        ->whereBetween(DB::raw('DATE(created_at)'), [$request['start'], $request['end']])
                        ->where('cart_st', 'booking')
                        ->where('cabang_id', $dataUser->users_data->cabang_id)
                        ->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data',
            'data' => $rs_booking,
        ]);
    }

    public function hutang(Request $request)
    {
        $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', Auth::user()->user_id)->where('role_id', 'R0005')->first();
        // $rs_hutang = Cart::with('cart_draft')->where('cart_st', 'hutang')->where('cabang_id', $dataUser->users_data->cabang_id)->orderBy('created_at', 'DESC')->get();
        $rs_hutang = Transaksi::with(['cart.cart_data', 'cart.cart_draft', 'users'])
            ->whereRelation('cart', 'cart_st', 'hutang')
            ->whereRelation('cart', 'cabang_id', $dataUser->users_data->cabang_id)
            ->whereRelation('cart', 'pusat_id', $dataUser->users_data->toko_cabang->toko_pusat->id)
            ->orderBy('trans_date', 'DESC')
            ->whereBetween(DB::raw('DATE(trans_date)'), [$request['start'], $request['end']])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data',
            'data' => $rs_hutang,
        ]);
    }

    public function change_status_by_cart_id(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', Auth::user()->user_id)->where('role_id', 'R0005')->first();
        $cabang_id = $dataUser->users_data->cabang_id;
        // cek apakah ada yang draft ?
        $ttlDarft = Cart::where(['cabang_id' => $cabang_id, 'user_id' => $dataUser->user_id])->where('cart_st', 'draft')->count();
        if ($ttlDarft >= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat transaksi yang belum terselesaikan..!',
                'data' => $ttlDarft,
            ]);
        }
        // detail data
        $detailCart = Cart::where('cart_id', $request->cart_id)->where('cart_st', 'booking')->first();
        if (empty($detailCart)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan..',
                'data' => $detailCart,
            ]);
        }
        // ubah cart menjadi draft dan masuk ke transaksi
        $detailCart->cart_st = 'draft';
        $detailCart->user_id = $dataUser->user_id;
        $detailCart->save();
        // 
        return response()->json([
            'success' => true,
            'message' => 'Berhasil melakukan perubahan status',
            'data' => $detailCart,
        ]);
    }

    public function show_nota(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $transaksiCart = Transaksi::where('cart_id', $request->cart_id)->first();
        if (empty($transaksiCart)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 422);
        }
        // cart data
        $cartData = CartData::with('barang_cabang.barang_master')->where('cart_id', $transaksiCart->cart_id)->orderBy('cart_urut', 'DESC')->get();
        $rs_retur = ReturHistory::with('barang_cabang.barang_master')->where('cart_id', $transaksiCart->cart_id);
        if ($rs_retur->count() == 0) {
            $retur_data = [
                'status' => false,
            ];
        } else {
            $retur_data = [
                'status' => true,
                'rs_retur' => $rs_retur->get(),
            ];
        }
        
        // return
        return response()->json([
            'success' => true,
            'message' => 'Okee..!',
            'data' => $cartData,
            'transaksiCart' => $transaksiCart,
            'retur_data' => $retur_data,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (Auth::check()) {
            $user_id = (Auth::user()->user_id);
            $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
        } else {
            return response()->json([
                'success' => false,
                'errors' => 'user belum melakukan login'
            ], 422);
        }
        // validasi
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // detail cart
        $cart = Cart::where('cart_id', $request->cart_id)->first();
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ]);
        }
        // list cart data
        $rs_cart_data = CartData::where('cart_id', $request->cart_id)->get();
        // loop
        foreach ($rs_cart_data as $key => $cart_data) {
            // insert log
            $barangCabang = BarangCabang::find($cart_data->barang_cabang_id);
            $akhirStokBarang = $barangCabang->barang_stok + $cart_data->cart_qty;
            // ID transaksi
            $transaksi = Transaksi::where('cart_id', $cart_data->cart_id)->first();
            // insert log barang
            BarangLog::create([
                'user_id' => Auth::user()->user_id,
                'pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
                'cabang_id' => $dataUser->users_data->cabang_id,
                'barang_cabang_id' => $cart_data->barang_cabang_id,
                'barang_awal' => $barangCabang->barang_stok,
                'barang_perubahan' => $cart_data->cart_qty,
                'barang_transaksi_id' => $transaksi->id,
                'barang_akhir' => $akhirStokBarang,
                'barang_st' => 'hapus',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            // setelah insert ke log baru update ke barang cabang
            $barangCabang->barang_stok = $akhirStokBarang;
            $barangCabang->update();
        }
        // update jadi delete
        $cart->cart_st = "delete";
        $cart->update();
        // return
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus, Stok akan terupdate',
        ]);
    }

    public function cetakNota()
    {
        try {
            // exec('wmic printer get name', $printers);
            // dd($printers);
            // Nama printer sesuai konfigurasi sistem (lihat di 'Devices and Printers')
            // $connector = new WindowsPrintConnector("POS-58");
            // $connector = new WindowsPrintConnector("\\\\LAPTOP-1OLVA8NB\\POS-58");
            $connector = new WindowsPrintConnector("smb://LAPTOP-1OLVA8NB/POS-58");
            // $connector = new FilePrintConnector("LPT1");



            // Inisialisasi printer
            $printer = new Printer($connector);

            // Tambahkan teks atau format nota
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("TOKO ANDA\n");
            $printer->text("Jl. Contoh No.123\n");
            $printer->feed();

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Item        Qty    Harga\n");
            $printer->text("------------------------\n");
            $printer->text("Produk A    2      10.000\n");
            $printer->text("Produk B    1      15.000\n");
            $printer->text("------------------------\n");
            $printer->text("Total:         35.000\n");
            $printer->feed(2);

            // Akhiri cetakan
            $printer->cut();
            $printer->close();

            return "Nota berhasil dicetak.";
        } catch (\Exception $e) {
            return "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
