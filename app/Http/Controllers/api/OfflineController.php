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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OfflineController extends Controller
{
    public function store(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            '*.sortedCart' => 'required|array',
            '*.sortedCart.*.no_urut' => 'required|integer',
            '*.sortedCart.*.barang_cabang_id' => 'required|integer',
            '*.sortedCart.*.barang_nama' => 'required|string',
            '*.sortedCart.*.barang_st_diskon' => 'required',
            '*.sortedCart.*.barang_barcode' => 'required|string|min:12',
            '*.sortedCart.*.barang_harga_beli' => 'required|numeric|min:1',
            '*.sortedCart.*.barang_harga_jual' => 'required|numeric|min:1',
            '*.sortedCart.*.cart_qty' => 'required|numeric|min:0.1',
            '*.sortedCart.*.cart_subtotal' => 'required|numeric|min:1',

            // parent level
            '*.cart_id' => 'required|string',
            '*.cart_st' => 'required|in:yes,no',
            '*.trans_bayar' => 'required|numeric',
            '*.trans_date' => 'required|date',
            '*.trans_kembalian' => 'required|numeric',
            '*.trans_st' => 'required|in:yes,no',
            '*.trans_total' => 'required|numeric',
            '*.user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        //
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'errors' => 'user belum melakukan login',
            ], 422);
        }

        // return response()->json([
        //     'success' => false,
        //     'errors' => 'user belum melakukan login',
        //     'data' => $request->all(),
        // ], 200);
        // data transaksi offline
        $datas = $request->all();
        $data_sukses = 0;
        $list_cart = [];
        foreach ($datas as $key => $data) {
            if ($data['upload_st'] != 'yes') {
                // user ID
                $user_id = $data['user_id'];
                // cart ID
                $cart_id = now()->format('YmdHis').mt_rand(1000, 9999);
                $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
                // step 1 insert to cart
                $cart = Cart::create([
                    'cart_id' => $cart_id,
                    'pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
                    'cabang_id' => $dataUser->users_data->cabang_id,
                    'user_id' => $dataUser->user_id,
                    'cart_st' => 'draft',
                ]);
                // step 2 memasukkan
                if ($cart) {
                    foreach ($data['sortedCart'] as $key => $value) {
                        // find barang cabang
                        $barangCabang = BarangCabang::with('barang_master')->where('id', '=', $value['barang_cabang_id'])->where('cabang_id', $dataUser->users_data->cabang_id)->first();
                        if (empty($barangCabang)) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Ada barang yang tidak ditemukan',
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
                }
                // step 3 memasukkan ke transaksi
                $stTransaksi = Transaksi::create([
                    'cart_id' => $cart_id,
                    'user_id' => $dataUser->user_id,
                    'trans_pelanggan' => $data['trans_pelanggan'],
                    'trans_total' => $data['trans_total'],
                    'trans_bayar' => $data['trans_bayar'],
                    'trans_kembalian' => $data['trans_kembalian'],
                    'trans_date' => $data['trans_date'],
                ]);
                if ($stTransaksi) {
                    // kurangi stok barang cabang
                    $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
                    $cartData = CartData::where('cart_id', $cart_id)->get();
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
                                'user_id' => $user_id,
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
                    Cart::where('cart_id', $cart_id)->update(
                        [
                            'cart_st' => 'yes',
                        ]);
                    $list_cart[] = $data['cart_id'];
                    $data_sukses++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil terupload di server, Sebanyak '.$data_sukses.' data',
            'data_sukses' => $data_sukses,
            'list_cart' => $list_cart,
        ], 200);

    }

    public function store_one_data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|string',
            'cart_st' => 'required|in:yes,no',
            'user_id' => 'required|string',

            'trans_total' => 'required|numeric',
            'trans_bayar' => 'required|numeric',
            'trans_kembalian' => 'required|numeric',
            'trans_date' => 'required|date',

            'upload_st' => 'required|in:yes,no',

            // array sortedCart
            'sortedCart' => 'required|array|min:1',

            // isi dalam sortedCart
            'sortedCart.*.barang_cabang_id' => 'required|integer',
            'sortedCart.*.barang_nama' => 'required|string',
            'sortedCart.*.barang_barcode' => 'nullable|string',

            'sortedCart.*.cart_qty' => 'required|numeric|min:1',
            'sortedCart.*.cart_subtotal' => 'required|numeric',

            'sortedCart.*.barang_harga_jual' => 'required|numeric',
            'sortedCart.*.barang_harga_beli' => 'nullable|numeric',

            'sortedCart.*.no_urut' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        //
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'errors' => 'user belum melakukan login',
            ], 422);
        }

        // data transaksi offline
        $data = $request->all();
        $data_sukses = 0;
        $list_cart = [];
        // user ID
        $user_id = $data['user_id'];
        // cart ID
        $cart_id = now()->format('YmdHis').mt_rand(1000, 9999);
        $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
        // step 1 insert to cart
        $cart = Cart::create([
            'cart_id' => $cart_id,
            'pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
            'cabang_id' => $dataUser->users_data->cabang_id,
            'user_id' => $dataUser->user_id,
            'cart_st' => 'draft',
        ]);
        // step 2 memasukkan
        if ($cart) {
            foreach ($data['sortedCart'] as $key => $value) {
                // find barang cabang
                $barangCabang = BarangCabang::with('barang_master')->where('id', '=', $value['barang_cabang_id'])->where('cabang_id', $dataUser->users_data->cabang_id)->first();
                if (empty($barangCabang)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ada barang yang tidak ditemukan',
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
        }
        // step 3 memasukkan ke transaksi
        $stTransaksi = Transaksi::create([
            'cart_id' => $cart_id,
            'user_id' => $dataUser->user_id,
            'trans_pelanggan' => $data['trans_pelanggan'],
            'trans_total' => $data['trans_total'],
            'trans_bayar' => $data['trans_bayar'],
            'trans_kembalian' => $data['trans_kembalian'],
            'trans_date' => $data['trans_date'],
        ]);
        if ($stTransaksi) {
            // kurangi stok barang cabang
            $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
            $cartData = CartData::where('cart_id', $cart_id)->get();
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
                        'user_id' => $user_id,
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
            Cart::where('cart_id', $cart_id)->update(
                [
                    'cart_st' => 'yes',
                ]);
            $list_cart[] = $data['cart_id'];
            $data_sukses++;
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil terupload di server, Sebanyak '.$data_sukses.' data',
            'data_sukses' => $data_sukses,
            'list_cart' => $list_cart,
        ], 200);

    }

    public function store_hutang(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            '*.cart_id' => 'required|string',
            '*.user_id' => 'required|string',
            '*.cart_st' => 'required|in:hutang,lunas,draft',

            '*.cabang_id' => 'required|string',

            '*.draft_st' => 'required|in:yes,no',
            '*.upload_st' => 'required|in:yes,no',

            '*.ttlBayar' => 'required|numeric',

            '*.sortedCart' => 'required|array',
            '*.sortedCart.*.barang_cabang_id' => 'required|integer',
            '*.sortedCart.*.barang_nama' => 'required|string',

            '*.detail_cicilan' => 'nullable|array',
            '*.detail_cicilan.*.cicilan_date' => 'required_with:*.detail_cicilan|date',
            '*.detail_cicilan.*.cicilan' => 'required_with:*.detail_cicilan|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        //
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'errors' => 'user belum melakukan login',
            ], 422);
        }

        // return response()->json([
        //     'success' => false,
        //     'errors' => 'user belum melakukan login',
        //     'data' => $request->all(),
        // ], 200);
        // data transaksi offline
        $datas = $request->all();
        $data_sukses = 0;
        $list_cart = [];

        // return response()->json([
        //     'success' => true,
        //     'errors' => 'user belum melakukan login',
        //     'data' => $datas,
        // ], 200);
        $message = '';
        foreach ($datas as $key => $data) {
            if ($data['upload_st'] != 'yes') {
                // user ID
                $user_id = $data['user_id'];
                // cart ID
                $cart_id = now()->format('YmdHis').mt_rand(1000, 9999);
                $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
                // step 1 insert to cart
                $cart = Cart::create([
                    'cart_id' => $cart_id,
                    'pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
                    'cabang_id' => $dataUser->users_data->cabang_id,
                    'user_id' => $dataUser->user_id,
                    'cart_st' => 'hutang',
                ]);
                // step 2 memasukkan ke cart data
                if ($cart) {
                    foreach ($data['sortedCart'] as $key => $value) {
                        // find barang cabang
                        $barangCabang = BarangCabang::with('barang_master')->where('id', '=', $value['barang_cabang_id'])->where('cabang_id', $dataUser->users_data->cabang_id)->first();
                        if (empty($barangCabang)) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Ada barang yang tidak ditemukan',
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
                }
                // step 3 memasukkan ke transaksi
                $stTransaksi = Transaksi::create([
                    'cart_id' => $cart_id,
                    'user_id' => $dataUser->user_id,
                    'trans_pelanggan' => $data['trans_pelanggan'],
                    'trans_total' => $data['ttlBayar'],
                    'trans_bayar' => '0',
                    'trans_kembalian' => '0',
                    'trans_date' => $data['created_at'],
                ]);
                // kurangi stok barang cabang
                $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
                $cartData = CartData::where('cart_id', $cart_id)->get();
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
                            'user_id' => $user_id,
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
                // send to tabel cart draft
                // insert saja jika null
                $detailCartDraft = CartDraft::where('cart_id', $cart_id)->first();

                $stCartDtaft = CartDraft::create([
                    'cart_id' => $cart_id,
                    'draft_uang_muka' => $data['draft_uang_muka'],
                    'draft_uang_sisa' => $data['draft_uang_sisa'],
                    'draft_uang_tagihan' => $data['ttlBayar'],
                    'draft_pelanggan' => $data['trans_pelanggan'],
                    'draft_note' => $data['draft_note'],
                    'draft_st' => 'no',
                ]);
                $message = 'Berhasil simpan ke daftar hutang Transaksi';
                $cart_draft_id = $stCartDtaft->id;

                $detail = TagihanCicilan::where('cart_draft_id', $cart_draft_id)->delete();
                // loop
                if (! empty($data['detail_cicilan'])) {
                    $detailCicilan = $data['detail_cicilan'];
                    foreach ($detailCicilan as $key => $cicilan) {
                        TagihanCicilan::create([
                            'cart_draft_id' => $cart_draft_id,
                            'cicilan_date' => $cicilan['cicilan_date'],
                            'cicilan' => $cicilan['cicilan'],
                        ]);
                    }
                }

                $list_cart[] = $data['cart_id'];
                $data_sukses++;
            }
        }
        if ($data_sukses > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil terupload di server, Sebanyak '.$data_sukses.' data'.$message,
                'data_sukses' => $data_sukses,
                'list_cart' => $list_cart,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data hutang yang terupload',
                'data_sukses' => $data_sukses,
                'list_cart' => $list_cart,
            ], 200);
        }

    }

    public function store_booking(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            '*.cart_id' => 'required|string',
            '*.user_id' => 'required|string',
            '*.cart_st' => 'required|in:hutang,lunas,draft,booking',

            '*.cabang_id' => 'required|string',

            '*.draft_st' => 'required|in:yes,no',
            '*.upload_st' => 'required|in:yes,no',

            '*.ttlBayar' => 'required|numeric',

            '*.sortedCart' => 'required|array',
            '*.sortedCart.*.barang_cabang_id' => 'required|integer',
            '*.sortedCart.*.barang_nama' => 'required|string',

            '*.detail_cicilan' => 'nullable|array',
            '*.detail_cicilan.*.cicilan_date' => 'required_with:*.detail_cicilan|date',
            '*.detail_cicilan.*.cicilan' => 'required_with:*.detail_cicilan|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        //
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'errors' => 'user belum melakukan login',
            ], 422);
        }

        // return response()->json([
        //     'success' => false,
        //     'errors' => 'user belum melakukan login',
        //     'data' => $request->all(),
        // ], 200);
        // data transaksi offline
        $datas = $request->all();
        $data_sukses = 0;
        $list_cart = [];

        // return response()->json([
        //     'success' => true,
        //     'errors' => 'user belum melakukan login',
        //     'data' => $datas,
        // ], 200);
        $message = '';
        foreach ($datas as $key => $data) {
            if ($data['upload_st'] != 'yes') {
                // user ID
                $user_id = $data['user_id'];
                // cart ID
                $cart_id = now()->format('YmdHis').mt_rand(1000, 9999);
                $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
                // step 1 insert to cart
                $cart = Cart::create([
                    'cart_id' => $cart_id,
                    'pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
                    'cabang_id' => $dataUser->users_data->cabang_id,
                    'user_id' => $dataUser->user_id,
                    'cart_st' => 'booking',
                ]);
                // step 2 memasukkan ke cart data
                if ($cart) {
                    foreach ($data['sortedCart'] as $key => $value) {
                        // find barang cabang
                        $barangCabang = BarangCabang::with('barang_master')->where('id', '=', $value['barang_cabang_id'])->where('cabang_id', $dataUser->users_data->cabang_id)->first();
                        if (empty($barangCabang)) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Ada barang yang tidak ditemukan',
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
                            'cart_harga_jual' => $cart_harga_jual,
                            'cart_nama' => $value['barang_nama'],
                            'cart_diskon' => $cart_diskon,
                            'cart_qty' => $value['cart_qty'],
                            'cart_subtotal' => $cart_subtotal,
                            'cart_urut' => $value['no_urut'],
                        ]);
                    }
                }
                // cukup inset ke cart & cart data, Selanjutnya ke cart draft
                $detailCartDraft = CartDraft::where('cart_id', $cart_id)->first();
                $stCartDtaft = CartDraft::create([
                    'cart_id' => $cart_id,
                    'draft_uang_muka' => $data['draft_uang_muka'],
                    'draft_uang_sisa' => $data['draft_uang_sisa'],
                    'draft_uang_tagihan' => $data['ttlBayar'],
                    'draft_pelanggan' => $data['trans_pelanggan'],
                    'draft_note' => $data['draft_note'],
                    'draft_st' => 'no',
                    'draft_end' => $data['draft_end'],
                    'draft_start' => $data['draft_start'],
                ]);
                $message = 'Berhasil simpan ke daftar Draft Transaksi';
                $cart_draft_id = $stCartDtaft->id;

                $list_cart[] = $data['cart_id'];
                $data_sukses++;
            }
        }
        if ($data_sukses > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil terupload di server, Sebanyak '.$data_sukses.' data'.$message,
                'data_sukses' => $data_sukses,
                'list_cart' => $list_cart,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data Draft yang terupload',
                'data_sukses' => $data_sukses,
                'list_cart' => $list_cart,
            ], 200);
        }

    }
}
