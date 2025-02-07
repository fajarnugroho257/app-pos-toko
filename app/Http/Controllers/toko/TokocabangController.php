<?php

namespace App\Http\Controllers\toko;

use App\Http\Controllers\Controller;
use App\Models\TokoCabang;
use App\Models\TokoPusat;
use Auth;
use Illuminate\Http\Request;

class TokocabangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        // dd($toko);
        $data['title'] = 'Data Toko Cabang';
        $data['rs_cabang'] = TokoCabang::with('toko_pusat')->where('pusat_id', $pusat->id)->paginate(10);
        // dd($data);
        return view('toko.cabang.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Tambah Data Toko Cabang';
        return view('toko.cabang.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cabang_nama' => 'required',
            'cabang_alamat' => 'required',
        ]);
        $pusat_id = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        TokoCabang::create([
            'pusat_id' => $pusat_id->id,
            'cabang_nama' => $request->cabang_nama,
            'cabang_alamat' => $request->cabang_alamat,
        ]);
        //redirect
        return redirect()->route('tokoCabang')->with('success', 'Data berhasil disimpan');
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
        $detail = TokoCabang::where('slug', $slug)->first();
        if (empty($detail)) {
            return redirect()->route('tokoCabang')->with('error', 'Data tidak ditemukan');
        }
        $data['title'] = 'Ubah Data Toko Cabang';
        $data['detail'] = $detail;
        return view('toko.cabang.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $detail = TokoCabang::find($request->id);
        if (empty($detail)) {
            return redirect()->route('tokoCabang')->with('error', 'Data tidak ditemukan');
        }
        $request->validate([
            'id' => 'required',
            'cabang_nama' => 'required',
            'cabang_alamat' => 'required',
        ]);
        $detail->cabang_nama = $request->cabang_nama;
        $detail->cabang_alamat = $request->cabang_alamat;
        //redirect
        if ($detail->save()) {
            return redirect()->route('updateTokoCabang', ['slug' => $detail->slug])->with('success', 'Data berhasil disimpan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $detail = TokoCabang::where('slug', $slug)->first();
        if (empty($detail)) {
            return redirect()->route('tokoCabang')->with('error', 'Data tidak ditemukan');
        }
        if ($detail->delete()) {
            return redirect()->route('tokoCabang')->with('success', 'Data berhasil dihapus');
        }
    }
}
