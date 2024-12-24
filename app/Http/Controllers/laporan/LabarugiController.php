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
        // data transaksi
        $transaksi = Transaksi::whereRelation('cart', 'cabang_id', $cabang->id)
            ->orderBy(DB::raw('trans_date'), 'DESC')
            ->with(['cart', 'cart_data'])
            ->whereBetween(DB::raw('DATE(trans_date)'), [$data['date_start'], $data['date_end']])
            ->get();
        // dd($transaksi);
        $data['rs_laba'] = $transaksi;
        return view('laporan.laba.tanggal', $data);
    }

    public function search(Request $request)
    {
        $cabang = TokoCabang::find($request->id);
        if (empty($cabang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        if ($request->aksi == 'reset') {
            session()->forget('date_start');
            session()->forget('date_end');
        } else {
            session([
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
            ]);
        }
        return redirect()->route('showLabaRugi', ['slug' => $cabang->slug]);
    }

}
