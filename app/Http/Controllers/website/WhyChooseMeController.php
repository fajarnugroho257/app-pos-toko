<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\TokoPusat;
use App\Models\website\Why;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhyChooseMeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Kenapa Pilih Kami';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $rs_data = Why::where('pusat_id', $pusat->id)->get();
        $data['rs_data'] = $rs_data;
        return view('website.why.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $jlh = Why::where('pusat_id', $pusat->id)->count();
        if ($jlh >= 3) {
            return redirect()->route('whyChooseMe')->with('error', 'Data tidak bisa lebih dari 3');
        }
        $data['title'] = 'Tambah Kenapa Pilih Kami';
        return view('website.why.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'desc' => 'required',
        ]);
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $validated['pusat_id'] = $pusat->id;
        Why::create($validated);
        return redirect()->route('whyChooseMe')->with('success', 'Data berhasil disimpan');
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
        $data['title'] = 'Ubah Kenapa Pilih Kami';
        $detail = Why::find($id);
        if (empty($detail)) {
            return redirect()->route('whyChooseMe')->with('error', 'Data tidak ditemukan');
        }
        $data['detail'] = $detail;
        return view('website.why.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title' => 'required',
            'desc' => 'required',
        ]);
        $detail = Why::find($id);
        if (empty($detail)) {
            return redirect()->route('whyChooseMe')->with('error', 'Data tidak ditemukan');
        }
        $detail->update($validated);
        return redirect()->route('editWhyChooseMe', $detail->id)->with('success', 'Data berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $detail = Why::find($id);
        if (empty($detail)) {
            return redirect()->route('whyChooseMe')->with('error', 'Data tidak ditemukan');
        }
        $detail->delete();
        return redirect()->route('whyChooseMe')->with('success', 'Data Berhasil dihapus');
    }
}
