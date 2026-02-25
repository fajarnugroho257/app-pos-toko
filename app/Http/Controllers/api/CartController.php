<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BarangCabang;
use App\Models\BarangLog;
use App\Models\Cart;
use App\Models\CartData;
use App\Models\CartDraft;
use App\Models\TagihanCicilan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $data = Auth::user();
        if (Auth::check()) {
            $user_id = (Auth::user()->user_id);
            $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
            $dataSelected = [
                'cabang_id' => $dataUser->users_data->cabang_id,
                'cabang_nama' => $dataUser->users_data->toko_cabang->cabang_nama,
                'toko_pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
                'toko_pusat' => $dataUser->users_data->toko_cabang->toko_pusat->pusat_nama,
            ];
        } else {
            return response()->json([
                'success' => false,
                'errors' => 'user belum melakukan login'
            ], 422);
        }
        // jika punya draft dan ingin mengkosongkan cart dengan status draft
        $cartDart = Cart::where(['cabang_id' => $dataUser->users_data->cabang_id, 'user_id' => $dataUser->user_id, 'cart_st' => 'draft']);
        // jika tidak
        if (empty($request->keranjang) && $cartDart->count() >= 1) {
            // maka hapus cart draftnya
            if ($cartDart->delete()){
                return response()->json([
                    'success' => false,
                    'message' => 'Sukses Keranjang anda sudah kosong..',
                    'data' => $cartDart,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'errors' => 'Tidak diketahui'
                ], 422);
            }
        }
        // validasi
        $validator = Validator::make($request->all(), [
            'keranjang' => 'required|array',
            'keranjang.*.no_urut' => 'required|integer',
            'keranjang.*.barang_cabang_id' => 'required|integer',
            'keranjang.*.barang_nama' => 'required|string',
            'keranjang.*.barang_st_diskon' => 'required',
            'keranjang.*.barang_barcode' => 'required|string|min:12', // contoh panjang barcode
            'keranjang.*.barang_harga_beli' => 'required|numeric|min:1',
            'keranjang.*.barang_harga_jual' => 'required|numeric|min:1',
            'keranjang.*.pusat_id' => 'required|integer',
            'keranjang.*.cart_qty' => 'required|numeric|min:0.1',
            'keranjang.*.cart_subtotal' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // chek draft cart
        $draftCart = Cart::where('cart_st', 'draft')
            ->where('pusat_id', $dataUser->users_data->toko_cabang->toko_pusat->id)
            ->where('cabang_id', $dataUser->users_data->cabang_id)
            ->where('user_id', $dataUser->user_id)
            ->orderBy('created_at', 'DESC')->first();
        
        // check if exist
        if (!empty($draftCart)) {
            $cart_id = $draftCart->cart_id;
            // delete semua cart data
            CartData::where('cart_id', $cart_id)->delete();
            $cart = TRUE;
        } else {
            $cart_id = now()->format('YmdHis') . mt_rand(1000, 9999);
            //insert to cart
            $cart = Cart::create([
                'cart_id' => $cart_id,
                'pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
                'cabang_id' => $dataUser->users_data->cabang_id,
                'user_id' => $dataUser->user_id,
                'cart_st' => 'draft',
            ]);
        }
        // oke
        if ($cart) {
            // insert to cart data
            foreach ($request->keranjang as $key => $value) {
                // find barang cabang
                $barangCabang = BarangCabang::with('barang_master')->where('id', '=', $value['barang_cabang_id'])->where('cabang_id', $dataUser->users_data->cabang_id)->first();
                if (empty($barangCabang)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ada barang yang tidak ditemukan'
                    ], 422);
                }
                $barang_harga_jual = $barangCabang->barang_master->barang_harga_jual;
                $barang_grosir_harga_jual = $barangCabang->barang_master->barang_grosir_harga_jual;
                // insert to cart data
                $cart_diskon = $value['barang_st_diskon'];
                // 
                $cart_harga_jual = $cart_diskon == 'no' ? $barang_harga_jual : $barang_grosir_harga_jual;
                $cart_subtotal = $cart_harga_jual * $value['cart_qty'];
                // 
                CartData::create([
                    'cart_id' => $cart_id,
                    'barang_cabang_id' => $value['barang_cabang_id'],
                    'cart_barcode' => $value['barang_barcode'],
                    'cart_harga_beli' => $value['barang_harga_beli'],
                    // 'cart_harga_jual' => $value['barang_harga_jual'],
                    'cart_harga_jual' => $cart_harga_jual,
                    'cart_nama' => $value['barang_nama'],
                    'cart_diskon' => $cart_diskon,
                    'cart_qty' => $value['cart_qty'],
                    // 'cart_subtotal' => $value['cart_subtotal'],
                    'cart_subtotal' => $cart_subtotal,
                    'cart_urut' => $value['no_urut'],
                ]);
            }
            // response
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menambah ke keranjang',
                'cart_id' => $cart_id,
            ]);
        }
        // return response()->json($request->keranjang, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        if (Auth::check()) {
            $user_id = (Auth::user()->user_id);
            $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
            $dataSelected = [
                'cabang_id' => $dataUser->users_data->cabang_id,
                'cabang_nama' => $dataUser->users_data->toko_cabang->cabang_nama,
                'toko_pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
                'toko_pusat' => $dataUser->users_data->toko_cabang->toko_pusat->pusat_nama,
            ];
            // print_r($dataSelected);
        } else {
            return response()->json([
                'success' => false,
                'errors' => 'user belum melakukan login'
            ], 422);
        }

        $draft = DB::select("SELECT a.cart_id, a.cart_st, b.cart_barcode AS 'barang_barcode',
                                    b.barang_cabang_id, b.cart_nama AS 'barang_nama',
                                    IF(CONVERT(b.cart_qty, DECIMAL(10,2)) >= CONVERT(d.barang_grosir_pembelian, SIGNED), barang_grosir_harga_jual, barang_harga_jual) AS 'barang_harga_jual',
                                    a.pusat_id, b.cart_qty,
                                    b.cart_urut AS 'no_urut',
                                    b.cart_harga_beli AS 'barang_harga_beli',
                                    d.barang_harga_jual AS 'awal_barang_harga_jual',
                                    IF(CONVERT(b.cart_qty, DECIMAL(10,2)) >= CONVERT(d.barang_grosir_pembelian, SIGNED), 'yes', 'no') AS 'barang_st_diskon',
                                    d.barang_grosir_pembelian AS 'barang_grosir_pembelian',
                                    d.barang_grosir_harga_jual AS 'barang_grosir_harga_jual',
                                    IF(CONVERT(b.cart_qty, DECIMAL(10,2)) >= CONVERT(d.barang_grosir_pembelian, SIGNED), (b.cart_qty * barang_grosir_harga_jual), (b.cart_qty * barang_harga_jual)) AS 'cart_subtotal'
                                    FROM cart a
                                    INNER JOIN cart_data b ON a.cart_id = b.cart_id
                                    INNER JOIN barang_cabang c ON b.barang_cabang_id = c.id
                                    INNER JOIN barang_master d ON c.barang_id = d.id
                                    WHERE a.cart_st = 'draft'
                                    AND a.user_id = ?
                                    AND a.pusat_id = ?
                                    AND a.cabang_id = ?", [Auth::user()->user_id, $dataUser->users_data->toko_cabang->toko_pusat->id, $dataUser->users_data->cabang_id]);
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan cart draft',
            'data' => $draft,
        ]);
    }

    public function sub_total(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'errors' => 'user belum melakukan login'
            ], 422);
        }
        $grand_total = DB::table('cart_data')->where('cart_id', $request->cart_id)->sum('cart_subtotal');
        $total_item = DB::table('cart_data')->where('cart_id', $request->cart_id)->sum('cart_qty');
        // 
        $draft = DB::select("SELECT a.cart_id, a.cart_st, b.cart_barcode AS 'barang_barcode',
                                    b.barang_cabang_id, b.cart_nama AS 'barang_nama',
                                    IF(CONVERT(b.cart_qty, SIGNED) >= CONVERT(d.barang_grosir_pembelian, SIGNED), barang_grosir_harga_jual, barang_harga_jual) AS 'barang_harga_jual',
                                    a.pusat_id, CONVERT(b.cart_qty, SIGNED) AS 'cart_qty', b.cart_urut AS 'no_urut', b.cart_harga_beli AS 'barang_harga_beli',
                                    d.barang_harga_jual AS 'awal_barang_harga_jual',
                                    IF(CONVERT(b.cart_qty, SIGNED) >= CONVERT(d.barang_grosir_pembelian, SIGNED), 'yes', 'no') AS 'barang_st_diskon',
                                    d.barang_grosir_pembelian AS 'barang_grosir_pembelian',
                                    d.barang_grosir_harga_jual AS 'barang_grosir_harga_jual',
                                    IF(CONVERT(b.cart_qty, SIGNED) >= CONVERT(d.barang_grosir_pembelian, SIGNED), (b.cart_qty * barang_grosir_harga_jual), (b.cart_qty * barang_harga_jual)) AS 'cart_subtotal'
                                    FROM cart a
                                    INNER JOIN cart_data b ON a.cart_id = b.cart_id
                                    INNER JOIN barang_cabang c ON b.barang_cabang_id = c.id
                                    INNER JOIN barang_master d ON c.barang_id = d.id
                                    WHERE a.cart_st = 'draft'
                                    AND a.cart_id = ? ", [$request->cart_id]);
        // get draft data dari booking / hutang
        $cartDataDraft = CartDraft::where('cart_id', $request->cart_id)->first();
        // return
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan draft subtotal',
            'grand_total' => $grand_total,
            'total_item' => $total_item,
            'rs_draft' => $draft,
            'cartDataDraft' => empty($cartDataDraft) ? [] : $cartDataDraft,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function create_draft_pembelian(Request $request)
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
            'cart_st' => 'required',
            'trans_pelanggan' => 'nullable',
            'draft_uang_muka' => 'nullable|numeric',
            'draft_uang_sisa' => 'nullable|numeric',
            'draft_start' => 'required_if:cart_st,booking|date|nullable',
            'draft_end'   => 'required_if:cart_st,booking|date|nullable',
            'draft_note' => 'nullable',
            'draft_st' => 'required',
            'ttlBayar' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // get detail cart draft
        $cart = Cart::where('cart_id', $request->cart_id)->where('cart_st', 'draft')->first();
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan..!',
                'cart_id' => $cart,
            ]);
        }
        // ubah status
        $cart->cart_st = $request->cart_st;
        $cart->save();
        // jika hutang simpan transaksi dan kurangi stok barang cabang
        // if ($request->cart_st == 'hutang') {
        //     // insert to transaksi
        //     $stTransaksi = Transaksi::create([
        //         'cart_id' => $request->cart_id,
        //         'user_id' => Auth::user()->user_id,
        //         'trans_pelanggan' => $request->trans_pelanggan,
        //         'trans_total' => $request->ttlBayar,
        //         'trans_bayar' => "0",
        //         'trans_kembalian' => "0",
        //         'trans_date' => date('Y-m-d H:i:s')
        //     ]);
        //     $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', Auth::user()->user_id)->where('role_id', 'R0005')->first();
        //     $cartData = CartData::where('cart_id', $request->cart_id)->get();
        //     // loop
        //     foreach ($cartData as $key => $value) {
        //         // find barang cabang
        //         $barangCabang = BarangCabang::where('id', '=', $value['barang_cabang_id'])->where('cabang_id', $dataUser->users_data->cabang_id)->first();
        //         //
        //         $jlh_barang_sblm_tambah = $barangCabang->barang_stok;
        //         // kurangi
        //         $sisa = $barangCabang->barang_stok - $value['cart_qty'];
        //         $barangCabang->barang_stok = $sisa;
        //         // update stok
        //         if ($barangCabang->save()) {
        //             // insert log barang
        //             BarangLog::create([
        //                 'user_id' => Auth::user()->user_id,
        //                 'pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
        //                 'cabang_id' => $dataUser->users_data->cabang_id,
        //                 'barang_cabang_id' => $barangCabang->id,
        //                 'barang_awal' => $jlh_barang_sblm_tambah,
        //                 'barang_transaksi' => $value['cart_qty'],
        //                 'barang_transaksi_id' => $stTransaksi->id,
        //                 'barang_akhir' => $sisa,
        //                 'barang_st' => 'transaksi',
        //                 'created_at' => date('Y-m-d H:i:s'),
        //                 'updated_at' => date('Y-m-d H:i:s'),
        //             ]);
        //         }
        //     }
        //     // cari jika ada maka update
        //     $detailCartDraft = CartDraft::where('cart_id', $request->cart_id)->first();
        //     if (!empty($detailCartDraft)) {
        //         $detailCartDraft->draft_uang_muka = $request->draft_uang_muka;
        //         $detailCartDraft->draft_uang_sisa = $request->draft_uang_sisa;
        //         $detailCartDraft->draft_start = $request->draft_start;
        //         $detailCartDraft->draft_end = $request->draft_end;
        //         $detailCartDraft->draft_note = $request->draft_note;
        //         $detailCartDraft->draft_st = "no";
        //         // save
        //         $detailCartDraft->save();
        //     } else {
        //         // simpan ke card draft
        //         CartDraft::create([
        //             'cart_id' => $request->cart_id,
        //             'draft_uang_muka' => $request->draft_uang_muka,
        //             'draft_uang_sisa' => $request->draft_uang_sisa,
        //             'draft_start' => $request->draft_start,
        //             'draft_end' => $request->draft_end,
        //             'draft_note' => $request->draft_note,
        //             'draft_st' => $request->draft_st,
        //         ]);
        //     }
        //     // return
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Berhasil menambah ke daftar hutang',
        //         'cart_id' => $stTransaksi->id,
        //     ]);
        // }
        // jika booking cukup ubah status dari draft ke booking 
        // simpan ke card draft
        $detailCartDraft = CartDraft::where('cart_id', $request->cart_id)->first();
        if (!empty($detailCartDraft)) {
            $detailCartDraft->draft_uang_muka = $request->draft_uang_muka;
            $detailCartDraft->draft_uang_sisa = $request->draft_uang_sisa;
            $detailCartDraft->draft_uang_tagihan = $request->ttlBayar;
            $detailCartDraft->draft_start = $request->draft_start;
            $detailCartDraft->draft_end = $request->draft_end;
            $detailCartDraft->draft_pelanggan = $request->trans_pelanggan;
            $detailCartDraft->draft_note = $request->draft_note;
            $detailCartDraft->draft_st = $request->draft_st;
            // save
            $detailCartDraft->save();
            $message = 'Berhasil update ke daftar Booking Transaksi';
        } else {
            CartDraft::create([
                'cart_id' => $request->cart_id,
                'draft_uang_muka' => $request->draft_uang_muka,
                'draft_uang_sisa' => $request->draft_uang_sisa,
                'draft_uang_tagihan' => $request->ttlBayar,
                'draft_start' => $request->draft_start,
                'draft_end' => $request->draft_end,
                'draft_note' => $request->draft_note,
                'draft_pelanggan' => $request->trans_pelanggan,
                'draft_st' => $request->draft_st,
            ]);
            $message = 'Berhasil menambah ke daftar Booking Transaksi';
        }
        // return
        return response()->json([
            'success' => true,
            'message' => $message,
            'cart_id' => $request->cart_id,
        ]);
    }

    public function create_hutang_pembelian(Request $request)
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
            'cart_st' => 'required',
            'trans_pelanggan' => 'nullable',
            'draft_uang_muka' => 'nullable|numeric',
            'draft_uang_sisa' => 'nullable|numeric',
            'draft_note' => 'nullable',
            'draft_st' => 'required',
            'ttlBayar' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // get detail cart draft
        $cart = Cart::where('cart_id', $request->cart_id)->where('cart_st', 'draft')->first();
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan..!',
            ]);
        }
        // ubah status
        $cart->cart_st = $request->cart_st;
        $cart->save();
        // jika hutang simpan transaksi dan kurangi stok barang cabang
        // insert to transaksi
        $stTransaksi = Transaksi::create([
            'cart_id' => $request->cart_id,
            'user_id' => Auth::user()->user_id,
            'trans_pelanggan' => $request->trans_pelanggan,
            'trans_total' => $request->ttlBayar,
            'trans_bayar' => "0",
            'trans_kembalian' => "0",
            'trans_date' => date('Y-m-d H:i:s')
        ]);
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
        // insert saja jika null
        $detailCartDraft = CartDraft::where('cart_id', $request->cart_id)->first();
        if (!empty($detailCartDraft)) {
            $detailCartDraft->draft_uang_muka = $request->draft_uang_muka;
            $detailCartDraft->draft_uang_sisa = $request->draft_uang_sisa;
            $detailCartDraft->draft_uang_tagihan = $request->ttlBayar;
            $detailCartDraft->draft_pelanggan = $request->trans_pelanggan;
            $detailCartDraft->draft_note = $request->draft_note;
            $detailCartDraft->draft_st = $request->draft_st;
            // save
            $detailCartDraft->save();
            $message = 'Berhasil update ke daftar hutang Transaksi';
        } else {
            CartDraft::create([
                'cart_id' => $request->cart_id,
                'draft_uang_muka' => $request->draft_uang_muka,
                'draft_uang_sisa' => $request->draft_uang_sisa,
                'draft_uang_tagihan' => $request->ttlBayar,
                'draft_pelanggan' => $request->trans_pelanggan,
                'draft_note' => $request->draft_note,
                'draft_st' => $request->draft_st,
            ]);
            $message = 'Berhasil simpan ke daftar hutang Transaksi';
        }
        // return
        return response()->json([
            'success' => true,
            'message' => $message,
            'cart_id' => $stTransaksi->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function list_cart_by_id(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        $rs_cart = CartData::where('cart_id', $request->cart_id)->get();
        $cart_draft = CartDraft::with('tagihan_cicilan')->where('cart_id', $request->cart_id)->first();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data',
            'rs_cart' => $rs_cart,
            'cart_draft' => $cart_draft,
        ]);
    }

    public function store_cicilan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_draft_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // cek data
        $detail = TagihanCicilan::where('cart_draft_id', $request->cart_draft_id)->delete();
        // loop
        $rs_cicilan = $request->detail_cicilan;
        foreach ($rs_cicilan as $key => $cicilan) {
            TagihanCicilan::create([
                'cart_draft_id' => $request->cart_draft_id,
                'cicilan_date' => $cicilan['cicilan_date'],
                'cicilan' => $cicilan['cicilan'],
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'rs_cart' => $rs_cicilan,
        ]);
    }
    
    public function ubah_lunas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // detail data
        $cart = Cart::where(['cart_id' => $request->cart_id, 'cart_st' => 'hutang'])->first();
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ]);
        }
        $cartDraft = CartDraft::where('cart_id', $request->cart_id)->first();
        $draftUangMuka = $cartDraft->draft_uang_muka;
        // cicilan
        $rs_cicilan = TagihanCicilan::where('cart_draft_id', $cartDraft->id)->get();
        $ttlCicilan = 0;
        foreach ($rs_cicilan as $key => $cicilan) {
            $ttlCicilan += $cicilan->cicilan;
        }
        // 
        $sisaHutang = ($ttlCicilan + $draftUangMuka) - $cartDraft->draft_uang_tagihan;
        if ($sisaHutang >= 0) {
            // oke sudah lunas
            // update
            Cart::where('cart_id', $request->cart_id)->update(['cart_st' => 'yes']);
            // update transaksi
            Transaksi::where('cart_id', $request->cart_id)
                ->update([
                    'trans_bayar' => ($ttlCicilan + $draftUangMuka),
                    'trans_kembalian' => $sisaHutang,
                ]);
            $success = true;
            $message = "Hutang telah lunas";
        } else {
            $success = false;
            $message = "Pembayaran belum lunas";
        }
        // return
        return response()->json([
            'success' => $success,
            'message' => $message,
            'sisaHutang' => $sisaHutang,
        ]);
    }

}
