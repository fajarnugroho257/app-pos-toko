<?php

namespace App\Http\Controllers\barang;

use App\Http\Controllers\Controller;
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
        $pusat = TokoPusat::where('user_id', Auth::user()->user_id)->first();
        $data['title'] = 'Master Data Barang';
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
        $data['title'] = 'Tambah Master Data Barang';
        return view('barang.master.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_nama' => 'required',
            'barang_barcode' => 'required|unique:barang_master,barang_barcode',
            'barang_harga_beli' => 'required',
            'barang_harga_jual' => 'required',
            'barang_stok_minimal' => 'required'
        ]);
        $pusat_id = TokoPusat::where('user_id', Auth::user()->user_id)->first();
        MasterBarang::create([
            'pusat_id' => $pusat_id->id,
            'barang_barcode' => $request->barang_barcode,
            'barang_nama' => $request->barang_nama,
            'barang_stok_minimal' => $request->barang_stok_minimal,
            'barang_harga_beli' => $request->barang_harga_beli,
            'barang_harga_jual' => $request->barang_harga_jual,
        ]);
        //redirect
        return redirect()->route('masterBarang')->with('success', 'Data berhasil disimpan');
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
    public function edit(string $slug)
    {
        $detail = MasterBarang::where('slug', $slug)->first();
        if (empty($detail)) {
            return redirect()->route('masterBarang')->with('error', 'Data tidak ditemukan');
        }
        $data['title'] = 'Ubah Data Toko Cabang';
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
            'barang_barcode' => 'required|unique:barang_master,barang_barcode,' . $request->id . ',id',
            'barang_nama' => 'required',
            'barang_harga_beli' => 'required',
            'barang_stok_minimal' => 'required'
        ]);
        $detail->barang_nama = $request->barang_nama;
        $detail->barang_barcode = $request->barang_barcode;
        $detail->barang_harga_beli = $request->barang_harga_beli;
        $detail->barang_harga_jual = $request->barang_harga_jual;
        $detail->barang_stok_minimal = $request->barang_stok_minimal;
        //redirect
        if ($detail->save()) {
            return redirect()->route('updateMasterBarang', ['slug' => $detail->slug])->with('success', 'Data berhasil disimpan');
        }
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
