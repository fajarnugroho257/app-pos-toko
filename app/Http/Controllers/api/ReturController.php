<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BarangCabang;
use App\Models\CartData;
use App\Models\BarangLog;
use App\Models\ReturHistory;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReturController extends Controller
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
        if (Auth::check()) {
            $user_id = (Auth::user()->user_id);
            $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
        } else {
            return response()->json([
                'success' => false,
                'errors' => 'user belum melakukan login'
            ], 422);
        }
        // data
        $rs_data = $request->all();
        // loop
        $ttlRetur = 0;
        foreach ($rs_data as $key => $data) {
            if (!empty($data['qty_retur'])) {
                $ttlRetur++;
                // params
                $qty_retur = $data['qty_retur'];
                $qty_retur_uang = $data['qty_retur_uang'];
                // get cart data
                $cartData = CartData::find($data['id']);
                $res_sisa = $cartData->cart_qty - $qty_retur;
                // 
                $res_cart_subtotal =  $cartData->cart_harga_jual * $res_sisa;
                // update cart data
                $cartData->cart_subtotal = $res_cart_subtotal;
                $cartData->cart_qty = $res_sisa;
                // 
                if ($cartData->update()) {
                    // insert log
                    $barangCabang = BarangCabang::find($data['barang_cabang_id']);
                    $akhirStokBarang = $barangCabang->barang_stok + $qty_retur;
                    // ID transaksi
                    $transaksi = Transaksi::where('cart_id', $data['cart_id'])->first();
                    // insert log barang
                    BarangLog::create([
                        'user_id' => Auth::user()->user_id,
                        'pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
                        'cabang_id' => $dataUser->users_data->cabang_id,
                        'barang_cabang_id' => $data['barang_cabang_id'],
                        'barang_awal' => $barangCabang->barang_stok,
                        'barang_perubahan' => $qty_retur,
                        'barang_transaksi_id' => $transaksi->id,
                        'barang_akhir' => $akhirStokBarang,
                        'barang_st' => 'retur',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    // setelah insert ke log baru update ke barang cabang
                    $barangCabang->barang_stok = $akhirStokBarang;
                    $barangCabang->update();
                    // insert retur history
                    ReturHistory::create([
                        'user_id' => Auth::user()->user_id,
                        'cart_id' => $data['cart_id'],
                        'barang_cabang_id' => $data['barang_cabang_id'],
                        'retur_qty' => $qty_retur,
                        'retur_harga' => $qty_retur_uang,
                    ]);
                }
            }
        }
        if ($ttlRetur == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memilih barang',
            ]);
        }
        // update
        $total = DB::table('cart_data as a')
                    ->where('a.cart_id', $data['cart_id'])
                    ->sum('a.cart_subtotal');
        $transaksi->trans_total = $total;
        $transaksi->trans_kembalian = ($transaksi->trans_bayar - $total);
        $transaksi->update();
        // return
        return response()->json([
            'success' => true,
            'message' => 'Berhasil diretur',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
        // validasi
        $validator = Validator::make($request->all(), [
            'retur_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        if (Auth::check()) {
            $user_id = (Auth::user()->user_id);
            $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
        } else {
            return response()->json([
                'success' => false,
                'errors' => 'user belum melakukan login'
            ], 422);
        }
        // detail
        $detail = ReturHistory::find($request->retur_id);
        // update ke tabel utamanya
        $cartData = CartData::where(['cart_id' => $detail->cart_id, 'barang_cabang_id' => $detail->barang_cabang_id])->first();
        $akhirQty = $cartData->cart_qty + $detail->retur_qty;
        $cartData->cart_qty = $akhirQty;
        $cartData->cart_subtotal = $cartData->cart_harga_jual * $akhirQty;
        $cartData->update();
        // detail transaksi
        $transaksi = Transaksi::where('cart_id', $detail->cart_id)->first();
        // update total transaksi
        $total = DB::table('cart_data as a')
                    ->where('a.cart_id', $detail->cart_id)
                    ->sum('a.cart_subtotal');
        $transaksi->trans_total = $total;
        $transaksi->trans_kembalian = ($transaksi->trans_bayar - $total);
        $transaksi->update();
        // insert log barang
        $barangCabang = BarangCabang::find($detail->barang_cabang_id);
        $akhirStokBarang = $barangCabang->barang_stok - $detail->retur_qty;
        BarangLog::create([
            'user_id' => Auth::user()->user_id,
            'pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
            'cabang_id' => $dataUser->users_data->cabang_id,
            'barang_cabang_id' => $detail->barang_cabang_id,
            'barang_awal' => $barangCabang->barang_stok,
            'barang_perubahan' => $detail->retur_qty,
            'barang_transaksi_id' => $transaksi->id,
            'barang_akhir' => $akhirStokBarang,
            'barang_st' => 'retur_hapus',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        // setelah insert ke log baru update ke barang cabang
        $barangCabang->barang_stok = $akhirStokBarang;
        $barangCabang->update();
        // delete history
        $detail->delete();
        // return
        return response()->json([
            'success' => true,
            'message' => 'Data retur berhasil dihapus',
            'ttl' => ReturHistory::where('cart_id', $detail->cart_id)->count(),
        ], 200);
    }
}
