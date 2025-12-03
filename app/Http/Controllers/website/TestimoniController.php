<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\TokoPusat;
use App\Models\website\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestimoniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Testimoni';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $rs_data = Testimoni::where('pusat_id', $pusat->id)->get();
        $data['rs_data'] = $rs_data;
        return view('website.testi.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Tambah Testimoni';
        return view('website.testi.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'person' => 'required',
            'desc' => 'required',
        ]);
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $validated['pusat_id'] = $pusat->id;
        Testimoni::create($validated);
        return redirect()->route('addTestimoni')->with('success', 'Data berhasil disimpan');
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
        $data['title'] = 'Ubah Testimoni';
        $detail = Testimoni::find($id);
        if (empty($detail)) {
            return redirect()->route('testimoni')->with('error', 'Data tidak ditemukan');
        }
        $data['detail'] = $detail;
        return view('website.testi.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'person' => 'required',
            'desc' => 'required',
        ]);
        $detail = Testimoni::find($id);
        if (empty($detail)) {
            return redirect()->route('testimoni')->with('error', 'Data tidak ditemukan');
        }
        $detail->update($validated);
        return redirect()->route('editTestimoni', $id)->with('success', 'Data berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $detail = Testimoni::find($id);
        if (empty($detail)) {
            return redirect()->route('testimoni')->with('error', 'Data tidak ditemukan');
        }
        $detail->delete();
        return redirect()->route('testimoni')->with('success', 'Data Berhasil dihapus');
    }
}
