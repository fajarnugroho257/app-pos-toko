<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\website\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pusat = User::with('toko_pusat_user')->where('user_id', Auth::user()->user_id)->first();
        $data['title'] = "Banner";
        $data['rs_data'] = Banner::where('pusat_id', $pusat->toko_pusat_user->pusat_id)->orderByRaw('CAST(banner_urut AS UNSIGNED) ASC')->paginate(10);
        // dd($data);
        return view('website.banner.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = "Tambah Banner";
        return view('website.banner.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'banner_ket' => 'required',
            'banner_urut' => 'required',
            'banner_name' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:512',
        ]);
        //
        $banner_name = '';
        if ($request->hasFile('banner_name')) {
            $tujuan_upload = 'image/banner';
            $file = $request->file('banner_name');
            //
            if (!$file->move($tujuan_upload, $file->getClientOriginalName())) {
                return redirect()->route('addBanner')->with('error', 'Gagal simpan foto');
            }
            // name
            $banner_name = $file->getClientOriginalName();
        }
        // pusat ID
        $pusat = User::with('toko_pusat_user')->where('user_id', Auth::user()->user_id)->first();
        // insert
        Banner::create([
            'pusat_id' => $pusat->toko_pusat_user->pusat_id,
            'banner_path' => $tujuan_upload,
            'banner_name' => $banner_name,
            'banner_ket' => $request->banner_ket,
            'banner_urut' => $request->banner_urut,
        ]);
        //redirect
        return redirect()->route('addBanner')->with('success', 'Data berhasil disimpan');
        
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
        $detail = Banner::find($id);
        if (empty($detail)) {
            return redirect()->route('banner')->with('error', 'Data tidak ditemukan');
        }
        $data['title'] = "Ubah Banner";
        $data['detail'] = $detail;
        return view('website.banner.edit', $data);
        // dd($detail);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $detail = Banner::find($id);
        if (empty($detail)) {
            return redirect()->route('banner')->with('error', 'Data tidak ditemukan');
        }
        $request->validate([
            'banner_ket' => 'required',
            'banner_urut' => 'required',
        ]);
        if ($request->hasFile('banner_name')) {
            $request->validate([
                'banner_name' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:512',
            ]);
        }
        // 
        $banner_name = $detail->banner_name;
        if ($request->hasFile('banner_name')) {
            $tujuan_upload = 'image/banner';
            $file = $request->file('banner_name');
            //
            if (!$file->move($tujuan_upload, $file->getClientOriginalName())) {
                return redirect()->route('addBanner')->with('error', 'Gagal simpan foto');
            }
            // name
            $banner_name = $file->getClientOriginalName();
        }
        // update
        $detail->banner_name = $banner_name;
        $detail->banner_ket = $request->banner_ket;
        $detail->banner_urut = $request->banner_urut;
        if ($detail->save()) {
            return redirect()->route('editBanner', $detail->id)->with('success', 'Data Berhasil disimpan');
        } else {
            return redirect()->route('editBanner', $detail->id)->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $detail = Banner::find($id);
        if (empty($detail)) {
            return redirect()->route('banner')->with('error', 'Data tidak ditemukan');
        }
        if ($detail->delete()) {
            return redirect()->route('banner')->with('success', 'Data Berhasil dihapus');
        } else {
            return redirect()->route('banner')->with('error', 'Data gagal dihapus');
        }
    }
}
