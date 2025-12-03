<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\TokoPusat;
use App\Models\website\Pref;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenceController extends Controller
{

    public function index()
    {
        $data['title'] = 'Web Preference';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $rs_pref = Pref::where('pusat_id', $pusat->id)
            ->whereIn('pref_name', ['pref_hp', 'pref_alamat', 'pref_jam'])
            ->get();
        $data['rs_pref'] = $rs_pref;
        // dd($data);
        return view('website.preference.index', $data);
    }

    public function edit(Request $request)
    {
        $data['title'] = 'Ubah Web Preference';
        $validated = $request->validate([
            'id' => 'required',
            'pusat_id' => 'required',
        ]);
        $detail = Pref::where('id', $request->id)->where('pusat_id', $request->pusat_id)->first();
        if (empty($detail)) {
            return redirect()->route('preference')->with('error', 'Data tidak ditemukan');
        }
        $data['detail'] = $detail;
        return view('website.preference.edit', $data);
    }
    
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'pusat_id' => 'required',
            'pref_value' => 'required',
        ]);
        $detail = Pref::where('id', $id)->where('pusat_id', $request->pusat_id)->first();
        if (empty($detail)) {
            return redirect()->route('preference')->with('error', 'Data tidak ditemukan');
        }
        $detail->update($validated);
        return redirect()->route('editPreference', ['id' => $detail->id, 'pusat_id' => $detail->pusat_id])->with('success', 'Data berhasil disimpan');
    }

}
