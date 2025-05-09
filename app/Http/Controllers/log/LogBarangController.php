<?php

namespace App\Http\Controllers\log;

use App\Http\Controllers\Controller;
use App\Models\BarangCabang;
use App\Models\BarangLog;
use App\Models\TokoCabang;
use App\Models\TokoPusat;
use Auth;
use DB;
use Illuminate\Http\Request;

class LogBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Log Data Barang Cabang';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $data['rs_cabang'] = TokoCabang::where('pusat_id', $pusat->id)->get();
        // dd($data);
        return view('log.barang.index', $data);
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
    public function show(string $slug)
    {
        // cari
        $barang_cabang_nama = session()->get('log_barang_cabang_nama');
        $data['barang_cabang_nama'] = $barang_cabang_nama;
        $barang_cabang_nama = empty($barang_cabang_nama) ? '%' : '%' . $barang_cabang_nama . '%';
        //
        $data['title'] = 'Log Barang Cabang';
        $cabang = TokoCabang::where('slug', $slug)->first();
        if (empty($cabang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        $data['cabang'] = $cabang;
        // all barang cabang
        $barangCabang = BarangCabang::select('barang_cabang.*')
            ->with(['barang_master', 'toko_cabang.toko_pusat'])->where('cabang_id', $cabang->id)
            ->join('barang_master', 'barang_cabang.barang_id', '=', 'barang_master.id')
            ->where(DB::raw('CONCAT(barang_master.barang_nama, barang_master.barang_barcode)'), 'LIKE', $barang_cabang_nama)
            ->orderBy('barang_master.barang_nama')->paginate(50);
        $data['rs_brg_cabang'] = $barangCabang;
        // return
        return view('log.barang.barang_cabang', $data);
    }

    public function show_detail_log(string $barang_cabang_id, string $cabang_id, string $pusat_id)
    {
        $data['title'] = 'Detail Log Barang Cabang';
        $cabang = TokoCabang::find($cabang_id);
        if (empty($cabang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        $barang = BarangCabang::with('barang_master')->find($barang_cabang_id);
        if (empty($barang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        $data['cabang'] = $cabang;
        $data['barang'] = $barang;
        // log barang
        $barangLog = BarangLog::with(['barang_cabang.barang_master', 'users'])
            ->where('barang_cabang_id', $barang_cabang_id)
            ->where('cabang_id', $cabang_id)
            ->where('pusat_id', $pusat_id)
            ->orderBy('created_at', 'DESC')
            ->paginate(50);
        $data['rs_barang_log'] = $barangLog;
        // dd($data);
        return view('log.barang.detail', $data);
    }

    public function search(Request $request)
    {
        $cabang = TokoCabang::find($request->id);
        if (empty($cabang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        if ($request->aksi == 'reset') {
            session()->forget('log_barang_cabang_nama');
        } else {
            session([
                'log_barang_cabang_nama' => $request->barang_cabang_nama
            ]);
        }
        return redirect()->route('showLogBarangCabang', ['slug' => $cabang->slug]);
    }
}
