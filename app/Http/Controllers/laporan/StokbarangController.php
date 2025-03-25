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
            $rs_terbanyak = DB::table('cart as a')
                ->selectRaw('d.id, d.barang_nama, SUM(b.cart_qty) AS cart_qty')
                ->join('cart_data as b', 'a.cart_id', '=', 'b.cart_id')
                ->join('barang_cabang as c', 'b.barang_cabang_id', '=', 'c.id')
                ->join('barang_master as d', 'c.barang_id', '=', 'd.id')
                ->where('a.cart_st', 'yes')
                ->where('a.pusat_id', $pusat_id)
                ->groupBy('d.id', 'd.barang_nama')
                ->get();
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
