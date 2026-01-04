<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BarangCabang;
use App\Models\BarangLog;
use App\Models\Cart;
use App\Models\CartData;
use App\Models\Client;
use App\Models\TokoCabang;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TokenController extends Controller
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
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token_value' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // cek token
        $clientToken = Client::where('token_value', $request->token_value)->where('token_active', 'yes')->first();
        if(empty($clientToken)){
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak valid atau tidak aktif',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Token valid',
            'data' => [
                'token_value' => $request->token_value,
                'token_date' => $clientToken->token_date,
                'cabang_id' => $clientToken->cabang_id,
                'cabang_nama' => $clientToken->cabang->cabang_nama,
                'kasir_data' => User::where('user_id', $clientToken->user_id)->get(['user_id', 'name'])->first(),
                'data_barang' => BarangCabang::
                    with(['barang_master' => function($query){
                        $query->select('id', 'barang_nama', 'barang_barcode', 
                        DB::raw('CAST(CEIL(barang_harga_beli) AS CHAR) AS barang_harga_beli'),
                        DB::raw('CAST(CEIL(barang_harga_jual) AS CHAR) AS barang_harga_jual'),
                        DB::raw('CAST(CEIL(barang_grosir_harga_jual) AS CHAR) AS barang_grosir_harga_jual'), 'barang_grosir_pembelian');
                    }])
                    ->where('cabang_id', $clientToken->cabang_id)
                    ->select('id', 'barang_stok', 'barang_id')
                    ->get(),
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'postTransaksi' => 'required',
            'cabang_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // 
        $postTransaksi = $request->postTransaksi;
        foreach ($postTransaksi as $key1 => $value) {
            // cart ID
            $cart_id = now()->format('YmdHis') . mt_rand(1000, 9999);
            // pusat ID 
            $pusat = TokoCabang::find($request->cabang_id);
            //insert to cart
            $cart = Cart::create([
                'cart_id' => $cart_id,
                'pusat_id' => $pusat->pusat_id,
                'cabang_id' => $request->cabang_id,
                'cart_st' => 'yes',
            ]);

            $cartDatas = array_reverse($value['cartData']);

            // loop keranjang
            foreach ($cartDatas as $key2 => $cartData) {
                CartData::create([
                    'cart_id' => $cart_id,
                    'barang_cabang_id' => $cartData['id'],
                    'cart_barcode' => $cartData['barang_barcode'],
                    'cart_harga_beli' => $cartData['barang_harga_beli'],
                    'cart_harga_jual' => $cartData['stGrosir'] == 'yes' ? $cartData['barang_grosir_harga_jual'] : $cartData['harga'],
                    'cart_nama' => $cartData['barang_nama'],
                    'cart_diskon' => $cartData['stGrosir'],
                    'cart_qty' => $cartData['qty'],
                    'cart_subtotal' => $cartData['subTotal'],
                    'cart_urut' => ($key2+1),
                ]);
            }
            $trans_date = $value['tanggal'] . " " . $value['jam'];
            // insert transaksi
            $stTransaksi = Transaksi::create([
                'cart_id' => $cart_id,
                'user_id' => $request->user_id,
                'trans_pelanggan' => $value['pelanggan'],
                'trans_total' => $value['totalBelanja'],
                'trans_bayar' => preg_replace('/^0(?!$)/', '', $value['bayar']),
                'trans_kembalian' => $value['kembalian'],
                'trans_date' => $trans_date,
            ]);
            // kurangi stok barang
            foreach ($cartDatas as $key => $value) {
                // find barang cabang
                $barangCabang = BarangCabang::where('id', '=', $value['id'])->where('cabang_id', $request->cabang_id)->first();
                //
                $jlh_barang_sblm_tambah = $barangCabang->barang_stok;
                // kurangi
                $sisa = $barangCabang->barang_stok - $value['qty'];
                $barangCabang->barang_stok = $sisa;
                // update stok
                if ($barangCabang->save()) {
                    // insert log barang
                    BarangLog::create([
                        'user_id' => $request->user_id,
                        'pusat_id' => $pusat->pusat_id,
                        'cabang_id' => $request->cabang_id,
                        'barang_cabang_id' => $barangCabang->id,
                        'barang_awal' => $jlh_barang_sblm_tambah,
                        'barang_transaksi' => $value['qty'],
                        'barang_transaksi_id' => $stTransaksi->id,
                        'barang_akhir' => $sisa,
                        'barang_st' => 'transaksi',
                        'created_at' => $trans_date,
                        'updated_at' => $trans_date,
                    ]);
                }
            }
        }
        // response
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambah ke keranjang',
            'postTransaksi' => $postTransaksi,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
