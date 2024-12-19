<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\TokoCabang;
use App\Models\TokoPusat;
use App\Models\Transaksi;
use Auth;
use DB;
use Illuminate\Http\Request;

class LabarugiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Laporan Laba Rugi';
        $pusat = TokoPusat::where('user_id', Auth::user()->user_id)->first();
        $data['rs_cabang'] = TokoCabang::where('pusat_id', $pusat->id)->get();
        // dd($data);
        return view('laporan.laba.index', $data);
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

    public function show(string $slug)
    {
        // cari
        $date_start = session()->get('date_start');
        $date_end = session()->get('date_end');
        //
        $data['date_start'] = empty($date_start) ? date('Y-m-01') : $date_start;
        $data['date_end'] = empty($date_end) ? date('Y-m-t') : $date_end;
        //
        $cabang = TokoCabang::where('slug', $slug)->first();
        if (empty($cabang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        $data['cabang'] = $cabang;
        $data['title'] = 'Laporan Laba Rugi';
        // cart
        // for
        $trans_total = Transaksi::where(DB::raw('DATE(trans_date)'), '2024-12-17')->sum('trans_total');
        $cart_harga_beli = Cart::where('pusat_id', $cabang->pusat_id)
            ->where('cabang_id', $cabang->id)
            ->get();

        dd($cart_harga_beli);
        // return view('laporan.laba.tanggal', $data);
    }

    public function detail(string $slug)
    {
        // cari
        $date_start = session()->get('date_start');
        $date_end = session()->get('date_end');
        //
        $data['date_start'] = empty($date_start) ? date('Y-m-01') : $date_start;
        $data['date_end'] = empty($date_end) ? date('Y-m-t') : $date_end;
        //
        $data['title'] = 'Laporan Laba Rugi';
        $cabang = TokoCabang::where('slug', $slug)->first();
        if (empty($cabang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        $data['cabang'] = $cabang;
        // dd($cabang);
        // laba rugi
        $rs_laba = collect(DB::select("SELECT b.cart_id, b.cabang_id, b.cabang_id,
                        c.barang_cabang_id, c.cart_nama, c.cart_harga_beli, c.cart_harga_jual, c.cart_qty, c.cart_subtotal,
                        d.cabang_barang_harga,
                        a.trans_date, a.trans_pelanggan, a.trans_total, a.trans_bayar, a.trans_kembalian
                        FROM transaksi_cart a
                        INNER JOIN cart b ON a.cart_id = b.cart_id
                        INNER JOIN cart_data c ON b.cart_id = c.cart_id
                        INNER JOIN barang_cabang d ON c.barang_cabang_id = d.id
                        WHERE DATE(a.trans_date) >= '2024-12-01'
                        AND DATE(a.trans_date) <= '2024-12-31'
                        AND b.pusat_id = ?
                        AND b.cabang_id = ?
                        ORDER BY a.trans_date DESC", [$cabang->pusat_id, $cabang->id]));
        // dd($rs_laba);
        $data['rs_laba'] = $rs_laba;
        // // all barang cabang
        // $barangCabang = BarangCabang::select('barang_cabang.*')
        //     ->with(['barang_master', 'toko_cabang.toko_pusat'])->where('cabang_id', $cabang->id)
        //     ->join('barang_master', 'barang_cabang.barang_id', '=', 'barang_master.id')
        //     ->where(DB::raw('CONCAT(barang_master.barang_nama, barang_master.barang_barcode)'), 'LIKE', $barang_cabang_nama)
        //     ->orderBy('barang_master.barang_nama')->paginate(50);
        // $data['rs_brg_cabang'] = $barangCabang;
        // // return
        return view('laporan.laba.laba_cabang', $data);
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
