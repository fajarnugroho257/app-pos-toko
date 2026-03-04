<?php

namespace App\Http\Controllers\laporam;

use App\Http\Controllers\Controller;
use App\Models\TokoCabang;
use App\Models\TokoPusat;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class laporanHutangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Laporan Hutang';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $data['rs_cabang'] = TokoCabang::where('pusat_id', $pusat->id)->get();
        // dd($data);
        return view('laporan.hutang.index', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        // cari
        $date_start = session()->get('date_start_hutang');
        $date_end = session()->get('date_end_hutang');
        //
        $data['date_start'] = empty($date_start) ? date('Y-m-01') : $date_start;
        $data['date_end'] = empty($date_end) ? date('Y-m-t') : $date_end;
        //
        $cabang = TokoCabang::where('slug', $slug)->first();
        if (empty($cabang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        $data['cabang'] = $cabang;
        $data['title'] = 'Laporan Hutang';
        // data transaksi
        $transaksi = Transaksi::whereRelation('cart.cart_draft', 'cabang_id', $cabang->id)
            ->orderBy(DB::raw('trans_date'), 'DESC')
            ->with(['cart.cart_draft', 'cart_data'])
            ->whereBetween(DB::raw('DATE(trans_date)'), [$data['date_start'], $data['date_end']])
            ->whereHas('cart', function ($q) {
                    $q->whereIn('cart_st', ['hutang']);
                })
            ->get();
        $data['rs_laba'] = $transaksi;
        // dd($transaksi);
        return view('laporan.hutang.tanggal', $data);
    }

    public function search(Request $request)
    {
        $cabang = TokoCabang::find($request->id);
        if (empty($cabang)) {
            return redirect()->route('laporanHutang')->with('error', 'Data tidak ditemukan');
        }
        if ($request->aksi == 'reset') {
            session()->forget('date_start_hutang');
            session()->forget('date_end_hutang');
        } else {
            session([
                'date_start_hutang' => $request->date_start,
                'date_end_hutang' => $request->date_end,
            ]);
        }
        return redirect()->route('showLaporanHutang', ['slug' => $cabang->slug]);
    }
}
