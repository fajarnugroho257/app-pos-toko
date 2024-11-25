<?php

namespace App\Http\Controllers\barang;

use App\Http\Controllers\Controller;
use App\Models\MasterBarang;
use App\Models\TokoPusat;
use Auth;
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
            ->where('barang_nama', 'LIKE', $barang_nama)
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
            'barang_harga' => 'required'
        ]);
        $pusat_id = TokoPusat::where('user_id', Auth::user()->user_id)->first();
        MasterBarang::create([
            'pusat_id' => $pusat_id->id,
            'barang_nama' => $request->barang_nama,
            'barang_harga' => $request->barang_harga,
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
            'barang_nama' => 'required',
            'barang_harga' => 'required'
        ]);
        $detail->barang_nama = $request->barang_nama;
        $detail->barang_harga = $request->barang_harga;
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
        session([
            'barang_nama' => $request->barang_nama
        ]);
        return redirect()->route('masterBarang');
    }
}
