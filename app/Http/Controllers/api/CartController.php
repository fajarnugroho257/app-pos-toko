<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BarangCabang;
use App\Models\Cart;
use App\Models\CartData;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Validator;

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
        // validasi
        $validator = Validator::make($request->all(), [
            'keranjang' => 'required|array',
            'keranjang.*.no_urut' => 'required|integer',
            'keranjang.*.barang_cabang_id' => 'required|integer',
            'keranjang.*.barang_nama' => 'required|string',
            'keranjang.*.barang_st_diskon' => 'required',
            'keranjang.*.barang_barcode' => 'required|string|size:12', // contoh panjang barcode
            'keranjang.*.barang_harga_beli' => 'required|numeric|min:1',
            'keranjang.*.barang_harga_jual' => 'required|numeric|min:1',
            'keranjang.*.pusat_id' => 'required|integer',
            'keranjang.*.cart_qty' => 'required|integer|min:1',
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
            ->orderBy('created_at', 'DESC')->first();
        //
        // if (!empty($draftCart)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Terdapat draft keranjang yang belum terselesaikan'
        //     ], 200);
        // }
        // check if exist
        if (!empty($draftCart)) {
            $cart_id = $draftCart->cart_id;
            // delete cart data
            CartData::where('cart_id', $cart_id)->delete();
            $cart = TRUE;
        } else {
            $cart_id = now()->format('YmdHis') . mt_rand(1000, 9999);
            //insert to cart
            $cart = Cart::create([
                'cart_id' => $cart_id,
                'pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
                'cabang_id' => $dataUser->users_data->cabang_id,
                'cart_st' => 'draft',
            ]);
        }
        if ($cart) {
            // insert to cart data
            foreach ($request->keranjang as $key => $value) {
                // find barang cabang
                $barangCabang = BarangCabang::where('id', '=', $value['barang_cabang_id'])->where('cabang_id', $dataUser->users_data->cabang_id)->first();
                if (empty($barangCabang)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ada barang yang tidak ditemukan'
                    ], 422);
                }
                // insert to cart data
                $cart_diskon = $value['barang_st_diskon'];
                CartData::create([
                    'cart_id' => $cart_id,
                    'barang_cabang_id' => $value['barang_cabang_id'],
                    'cart_barcode' => $value['barang_barcode'],
                    'cart_harga_beli' => $value['barang_harga_beli'],
                    'cart_harga_jual' => $value['barang_harga_jual'],
                    'cart_nama' => $value['barang_nama'],
                    'cart_diskon' => $cart_diskon,
                    'cart_qty' => $value['cart_qty'],
                    'cart_subtotal' => $value['cart_subtotal'],
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
                                    b.barang_cabang_id, b.cart_nama AS 'barang_nama', b.cart_harga_jual AS 'barang_harga_jual',
                                    a.pusat_id, b.cart_qty, b.cart_subtotal, b.cart_urut AS 'no_urut', b.cart_harga_beli AS 'barang_harga_beli',
                                    d.barang_harga_jual AS 'awal_barang_harga_jual',
                                    IF(b.cart_qty >= d.barang_grosir_pembelian, 'yes', 'no') AS 'barang_st_diskon',
                                    d.barang_grosir_pembelian AS 'barang_grosir_pembelian',
                                    d.barang_grosir_harga_jual AS 'barang_grosir_harga_jual'
                                    FROM cart a
                                    INNER JOIN cart_data b ON a.cart_id = b.cart_id
                                    INNER JOIN barang_cabang c ON b.barang_cabang_id = c.id
                                    INNER JOIN barang_master d ON c.barang_id = d.id
                                    WHERE a.cart_st = 'draft'
                                    AND a.pusat_id = ?
                                    AND a.cabang_id = ?", [$dataUser->users_data->toko_cabang->toko_pusat->id, $dataUser->users_data->cabang_id]);
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan cart draft',
            'data' => $draft,
        ]);
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
    public function destroy(string $id)
    {
        //
    }
}
