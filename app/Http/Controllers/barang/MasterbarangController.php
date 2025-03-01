<?php

namespace App\Http\Controllers\barang;

use App\Http\Controllers\Controller;
use App\Models\BarangMasterLog;
use App\Models\MasterBarang;
use App\Models\TokoPusat;
use Auth;
use DB;
use Illuminate\Http\Request;

class MasterbarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barang_nama = session()->get('barang_nama');
        $data['barang_nama'] = $barang_nama;
        $barang_nama = empty($barang_nama) ? '%' : '%' . $barang_nama . '%';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $data['title'] = 'Master Data Barang Gudang / Pusat';
        $data['rs_barang'] = MasterBarang::where('pusat_id', $pusat->id)
            ->where(DB::raw('CONCAT(barang_nama, barang_barcode)'), 'LIKE', $barang_nama)
            ->orderBy('barang_nama', 'ASC')
            ->paginate(50);
        // dd($data);
        return view('barang.master.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Tambah Master Data Barang Gudang / Pusat';
        return view('barang.master.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_barcode' => 'required|string|min:13',
            'barang_nama' => 'required',
            'barang_stok_minimal' => 'required',
            'barang_harga_beli' => 'required',
            'barang_harga_jual' => 'required',
            'barang_grosir_harga_jual' => 'required|numeric',
            'barang_grosir_keuntungan' => 'required|numeric',
            'barang_grosir_persentase' => 'required|numeric',
            'barang_grosir_pembelian' => 'required|numeric',
            'barang_persentase' => 'required|numeric',
            'barang_keuntungan' => 'required|numeric',
        ]);
        $pusat_id = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        // check data by barcode & pusat ID
        $stBarang = MasterBarang::where('pusat_id', $pusat_id->id)->where('barang_barcode', $request->barang_barcode)->count();
        if ($stBarang >= 1) {
            return redirect()->route('tambahMasterBarang')->with('error', 'Data berdasarkan barcode sudah tersedia')->withInput();
        }
        MasterBarang::create([
            'pusat_id' => $pusat_id->id,
            'barang_barcode' => $request->barang_barcode,
            'barang_nama' => $request->barang_nama,
            'barang_stok_minimal' => $request->barang_stok_minimal,
            'barang_harga_beli' => $request->barang_harga_beli,
            'barang_harga_jual' => $request->barang_harga_jual,
            'barang_grosir_harga_jual' => $request->barang_grosir_harga_jual,
            'barang_grosir_keuntungan' => $request->barang_grosir_keuntungan,
            'barang_grosir_persentase' => $request->barang_grosir_persentase,
            'barang_grosir_pembelian' => $request->barang_grosir_pembelian,
            'barang_persentase' => $request->barang_persentase,
            'barang_keuntungan' => $request->barang_keuntungan,
        ]);
        //redirect
        return redirect()->route('masterBarang')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        $detail = MasterBarang::where('slug', $slug)->first();
        if (empty($detail)) {
            return redirect()->route('masterBarang')->with('error', 'Data tidak ditemukan');
        }
        $data['title'] = 'Ubah Data Barang Gudang / Pusat';
        $data['detail'] = $detail;
        return view('barang.master.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $detail = MasterBarang::find($request->id);
        if (empty($detail)) {
            return redirect()->route('masterBarang')->with('error', 'Data tidak ditemukan');
        }
        $request->validate([
            'id' => 'required',
            'barang_barcode' => 'required|string|min:13',
            'old_barang_barcode' => 'required',
            'barang_nama' => 'required',
            'barang_harga_beli' => 'required',
            'barang_harga_jual' => 'required|numeric',
            'barang_stok_minimal' => 'required',
            'barang_persentase' => 'required|numeric',
            'barang_keuntungan' => 'required|numeric',
            'barang_grosir_pembelian' => 'required|numeric',
            'barang_grosir_persentase' => 'required|numeric',
            'barang_grosir_keuntungan' => 'required|numeric',
            'barang_grosir_harga_jual' => 'required|numeric',
            'barang_master_stok' => 'required|numeric',
            'barang_stok_perubahan' => 'required|numeric',
            'barang_master_stok_hasil' => 'required|numeric',
        ]);
        //
        $pusat_id = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        // check data by barcode & pusat ID
        if ($request->old_barang_barcode != $request->barang_barcode) {
            $stBarang = MasterBarang::where('pusat_id', $pusat_id->id)->where('barang_barcode', $request->barang_barcode)->count();
            if ($stBarang >= 1) {
                return redirect()->route('updateMasterBarang', ['slug' => $detail->slug])->with('error', 'Data berdasarkan barcode sudah tersedia')->withInput();
            }
        }
        //
        $jlh_barang_sblm_tambah = $detail->barang_master_stok;
        //
        $detail->barang_nama = $request->barang_nama;
        $detail->barang_barcode = $request->barang_barcode;
        $detail->barang_harga_beli = $request->barang_harga_beli;
        $detail->barang_harga_jual = $request->barang_harga_jual;
        $detail->barang_stok_minimal = $request->barang_stok_minimal;
        $detail->barang_persentase = $request->barang_persentase;
        $detail->barang_keuntungan = $request->barang_keuntungan;
        $detail->barang_grosir_pembelian = $request->barang_grosir_pembelian;
        $detail->barang_grosir_persentase = $request->barang_grosir_persentase;
        $detail->barang_grosir_keuntungan = $request->barang_grosir_keuntungan;
        $detail->barang_grosir_harga_jual = $request->barang_grosir_harga_jual;
        $detail->barang_stok_perubahan = $request->barang_stok_perubahan;
        $detail->barang_master_stok = $request->barang_master_stok_hasil;
        //redirect
        if ($detail->save()) {
            // jika sama dengan 0
            if ($request->barang_stok_perubahan != '0') {
                BarangMasterLog::create([
                    'user_id' => Auth::user()->user_id,
                    'pusat_id' => $pusat_id->id,
                    'barang_master_id' => $request->id,
                    'barang_master_awal' => $jlh_barang_sblm_tambah,
                    'barang_master_perubahan' => $request->barang_stok_perubahan,
                    'barang_master_akhir' => $request->barang_master_stok_hasil,
                    'barang_st' => 'penambahan',
                ]);
            }
            return redirect()->route('updateMasterBarang', ['slug' => $detail->slug])->with('success', 'Data berhasil disimpan');
        }
    }

    public function history(string $id)
    {
        $detail = MasterBarang::find($id);
        if (empty($detail)) {
            return redirect()->route('masterBarang')->with('error', 'Data tidak ditemukan');
        }
        // get log data master barang
        $rs_log = BarangMasterLog::with(['barang_master', 'user', 'toko_cabang'])
            ->where('barang_master_id', $detail->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(50);
        $data['title'] = 'Log Data Barang Gudang / Pusat';
        $data['detail'] = $detail;
        $data['rs_log'] = $rs_log;
        // dd($rs_log);
        return view('barang.master.log', $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $detail = MasterBarang::where('slug', $slug)->first();
        if (empty($detail)) {
            return redirect()->route('masterBarang')->with('error', 'Data tidak ditemukan');
        }
        if ($detail->delete()) {
            return redirect()->route('masterBarang')->with('success', 'Data berhasil dihapus');
        }
    }

    public function search(Request $request)
    {
        if ($request->aksi == 'reset') {
            session()->forget('barang_nama');
        } else {
            session([
                'barang_nama' => $request->barang_nama
            ]);
        }
        return redirect()->route('masterBarang');
    }
}
