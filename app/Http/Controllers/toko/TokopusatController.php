<?php

namespace App\Http\Controllers\toko;

use App\Http\Controllers\Controller;
use App\Models\TokoPusat;
use App\Models\User;
use Illuminate\Http\Request;

class TokopusatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Data Toko Pusat';
        $data['rs_toko'] = TokoPusat::with('users')->paginate(10);
        // dd($data);
        return view('toko.pusat.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Tambah Data Toko Pusat';
        $data['rs_user'] = User::all();
        return view('toko.pusat.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'pusat_nama' => 'required',
            'pusat_pemilik' => 'required',
            'pusat_alamat' => 'required',
        ]);
        TokoPusat::create([
            'user_id' => $request->user_id,
            'pusat_nama' => $request->pusat_nama,
            'pusat_pemilik' => $request->pusat_pemilik,
            'pusat_alamat' => $request->pusat_alamat,
        ]);
        //redirect
        return redirect()->route('tokoPusat')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slugid)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        $detail = TokoPusat::where('slug', $slug)->first();
        if (empty($detail)) {
            return redirect()->route('tokoPusat')->with('error', 'Data tidak ditemukan');
        }
        // dd($detail);
        $data['title'] = 'Ubah Data Toko Pusat';
        $data['rs_user'] = User::all();
        $data['detail'] = $detail;
        return view('toko.pusat.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // dd($request->all());
        $detail = TokoPusat::find($request->id);
        // dd($detail);
        $request->validate([
            'id' => 'required',
            'user_id' => 'required',
            'pusat_nama' => 'required',
            'pusat_pemilik' => 'required',
            'pusat_alamat' => 'required',
        ]);
        $detail->user_id = $request->user_id;
        $detail->pusat_nama = $request->pusat_nama;
        $detail->pusat_pemilik = $request->pusat_pemilik;
        $detail->pusat_alamat = $request->pusat_alamat;
        //redirect
        if ($detail->save()) {
            return redirect()->route('UpdateTokoPusat', ['slug' => $detail->slug])->with('success', 'Data berhasil disimpan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
