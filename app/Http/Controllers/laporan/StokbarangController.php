<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartData;
use App\Models\TokoCabang;
use App\Models\TokoPusat;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use function Laravel\Prompts\select;

class StokbarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Laporan Barang Paling Laku';
        // cari
        $cabang_id_stok = session()->get('cabang_id_stok');
        //
        $data['cabang_id'] = empty($cabang_id_stok) ? 'gudang' : $cabang_id_stok;
        $user_id = Auth::user()->user_id;
        $dataUser = User::with('toko_pusat_user')->where('user_id', $user_id)->first();
        $res_cabang = $data['cabang_id'] == 'gudang' ? '%' : ($data['cabang_id'] == 'gudang' ? '%' : $data['cabang_id']);
        // dd($dataUser);
        $pusat_id = $dataUser->toko_pusat_user->pusat_id;
        if ($data['cabang_id'] == 'gudang') {
            $rs_terbanyak = DB::select("SELECT m_barang.id, m_barang.barang_nama, IFNULL(m_penjualan.penjualan, 0) AS 'penjualan' FROM (
                                SELECT a.id, a.barang_nama FROM barang_master a WHERE a.pusat_id = ?
                            ) m_barang LEFT JOIN (
                                SELECT e.id, SUM(c.cart_qty) AS 'penjualan'
                                FROM cart b
                                INNER JOIN cart_data c ON b.cart_id = c.cart_id
                                INNER JOIN barang_cabang d ON c.barang_cabang_id = d.id
                                INNER JOIN barang_master e ON d.barang_id = e.id
                                WHERE b.cart_st = 'yes'
                                AND b.pusat_id = ?
                                GROUP BY e.id
                            ) m_penjualan ON m_barang.id = m_penjualan.id
                            ORDER BY IFNULL(m_penjualan.penjualan, 0) DESC", [$pusat_id, $pusat_id]);
        } else {
            $rs_terbanyak = DB::select("SELECT m_barang.id, m_barang.barang_nama, m_barang.cabang_id, m_barang.cabang_nama, IFNULL(m_jual.jual, 0) AS 'cart_qty' FROM (
                    SELECT e.id, e.barang_id, e.barang_stok, e.cabang_id, f.barang_nama, g.cabang_nama
                    FROM barang_cabang e
                    INNER JOIN barang_master f ON e.barang_id = f.id
                    INNER JOIN toko_cabang g ON e.cabang_id = g.id
                    WHERE e.cabang_id = ?
                ) AS m_barang LEFT JOIN (
                    SELECT c.id, a.cabang_id, d.barang_nama, SUM(b.cart_qty) AS 'jual' FROM cart a
                    INNER JOIN cart_data b ON a.cart_id = b.cart_id
                    INNER JOIN barang_cabang c ON b.barang_cabang_id = c.id
                    INNER JOIN barang_master d ON c.barang_id = d.id
                    WHERE a.cart_st = 'yes'
                    AND a.pusat_id = ? AND a.cabang_id = ?
                    GROUP BY c.id
                ) AS m_jual ON m_barang.id = m_jual.id
                ORDER BY IFNULL(m_jual.jual, 0) DESC", [$res_cabang, $pusat_id, $res_cabang]);
            $data['rs_terbanyak'] = $rs_terbanyak;
        }
        $data['rs_terbanyak'] = $rs_terbanyak;
        // daftar cabang
        $rs_cabang = TokoCabang::where('pusat_id', $pusat_id)->get();
        $data['rs_cabang'] = $rs_cabang;
        // dd($rs_terbanyak);
        return view('laporan.stok.index', $data);
    }


    public function search(Request $request)
    {
        if ($request->aksi == 'reset') {
            session()->forget('cabang_id_stok');
        } else {
            session([
                'cabang_id_stok' => $request->cabang_id,
            ]);
        }
        return redirect()->route('stokBarang');
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
        //
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
    public function destroy(string $id)
    {
        //
    }
}
