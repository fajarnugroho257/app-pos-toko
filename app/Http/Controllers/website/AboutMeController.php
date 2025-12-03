<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\TokoPusat;
use App\Models\website\Pref;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class AboutMeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Tentang kami';
        $barang_nama = empty($barang_nama) ? '%' : '%' . $barang_nama . '%';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $about_me = Pref::where('pusat_id', $pusat->id)->where('pref_name', 'aboutme')->first();
        $data['detail'] = $about_me;
        $rs_image = Pref::where('pusat_id', $pusat->id)->where('pref_name', 'pref_image')->get();
        $data['rs_image'] = $rs_image;
        return view('website.aboutme.index', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'pref_value' => 'required',
            'pusat_id' => 'required',
        ]);
        $detail = Pref::find($id);
        if (empty($detail)) {
            return redirect()->route('tentangKami')->with('error', 'Data tidak ditemukan');
        }
        // update
        if($detail->update($validated)){
            // insert image
            $path = 'image/tentang_kami/' . $request->pusat_id;
            if ($request->hasFile('pref_image')) {
                $rs_pref_image = $request->file('pref_image');
                $this->validate($request, [
                    'pref_image' => 'required',
                    'pref_image.*' => 'image|mimes:jpeg,png,jpg|max:512',
                ]);
                // loop
                foreach ($rs_pref_image as $image) {
                    $image_name = str_replace(' ', '_', $image->getClientOriginalName());
                    $image->move($path, $image_name);
                    $imageName[] = $image_name;
                }
                // insert
                foreach ($imageName as $key => $value) {
                    Pref::create([
                        'pusat_id' => $request->pusat_id,
                        'pref_name' => 'pref_image',
                        'pref_value' => $value,
                    ]);
                }
            }
        }
        return redirect()->route('tentangKami')->with('success', 'Data Berhasil disimpan');
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'pusat_id' => 'required',
        ]);
        $detail = Pref::where('pusat_id', $request->pusat_id)->where('id', $request->id)->first();
        if ($detail->delete()) {
            $path = 'image/tentang_kami/' . $request->pusat_id . '/' . $detail->pref_value;
            if (File::exists($path)) {
                File::delete($path);
            }
            return redirect()->route('tentangKami')->with('success', 'Data Berhasil dihapus');
        }
    }
    
}
