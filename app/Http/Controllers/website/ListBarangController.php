<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\MasterBarang;
use App\Models\TokoPusat;
use App\Models\User;
use App\Models\website\DetailBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barang_nama = session()->get('daftar_barang_nama');
        $detail_st = session()->get('detail_st');
        // 
        $data['daftar_barang_nama'] = $barang_nama;
        $data['detail_st'] = $detail_st;
        $barang_nama = empty($barang_nama) ? '%' : '%' . $barang_nama . '%';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $data['title'] = 'Gambar Barang';
        if ($detail_st == null ) {
            $data['rs_barang'] = MasterBarang::leftJoin('detail_barang', 'barang_master.id', '=', 'detail_barang.barang_id')
                ->where('barang_master.pusat_id', $pusat->id)
                ->where(DB::raw('CONCAT(barang_nama, barang_barcode)'), 'LIKE', $barang_nama)
                ->orderBy('barang_nama', 'ASC')
                ->paginate(50);
        } else {
            // ada parameternya
            $data['rs_barang'] = DetailBarang::join('barang_master', 'detail_barang.barang_id', '=', 'barang_master.id')
                ->where('barang_master.pusat_id', $pusat->id)
                ->where('detail_st', $detail_st)
                ->where(DB::raw("CONCAT(barang_master.barang_nama, barang_master.barang_barcode)"), 'LIKE', "%$barang_nama%")
                ->orderBy('barang_master.barang_nama', 'ASC')
                ->select('detail_barang.*', 'barang_master.*')
                ->paginate(50);
        }
        return view('website.barang.index', $data);
    }

    public function search(Request $request)
    {
        if ($request->aksi == 'reset') {
            session()->forget('daftar_barang_nama');
            session()->forget('detail_st');
        } else {
            session([
                'daftar_barang_nama' => $request->daftar_barang_nama,
                'detail_st' => $request->detail_st
            ]);
        }
        return redirect()->route('listBarang');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
            return redirect()->route('listBarang')->with('error', 'Data tidak ditemukan');
        }
        $detailGambar = DetailBarang::where('barang_id', $detail->id)->first();
        $data['title'] = 'Ubah Gambar Barang';
        $data['detail'] = $detail;
        $data['detailGambar'] = $detailGambar;
        return view('website.barang.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $detail = MasterBarang::find($id);
        if (empty($detail)) {
            return redirect()->route('listBarang')->with('error', 'Data tidak ditemukan');
        }
        $detailGambar = DetailBarang::where('barang_id', $detail->id)->first();
        $request->validate([
            'detail_st' => 'required',
        ]);
        //
        if (empty($detailGambar)) {
            $request->validate([
                'detail_image_name' => 'required|image|mimes:png|max:512',
            ]);
            // insert
            $detail_image_name = '';
            if ($request->hasFile('detail_image_name')) {
                $tujuan_upload = 'image/barang';
                $file = $request->file('detail_image_name');
                $ext  = $file->getClientOriginalExtension();
                //
                $detail_image_name = $detail->id . "." . $ext;
                if (!$file->move($tujuan_upload, $detail_image_name)) {
                    return redirect()->route('updateListBarang', [$detail->slug])->with('error', 'Gagal simpan foto');
                }
            }
            // pusat ID
            $pusat = User::with('toko_pusat_user')->where('user_id', Auth::user()->user_id)->first();
            // insert
            DetailBarang::create([
                'barang_id' => $detail->id,
                'pusat_id' => $pusat->toko_pusat_user->pusat_id,
                'detail_image_name' => $detail_image_name,
                'detail_image_path' => $tujuan_upload,
                'detail_st' => $request->detail_st,
            ]);
        } else {
            // update
            $detail_image_name = $detailGambar->detail_image_name;
            if ($request->hasFile('detail_image_name')) {
                $tujuan_upload = 'image/barang';
                $file = $request->file('detail_image_name');
                //
                if (!$file->move($tujuan_upload, $detail_image_name)) {
                    return redirect()->route('updateListBarang', [$detail->slug])->with('error', 'Gagal simpan foto');
                }
            }
            // update
            $detailGambar->update([
                'detail_image_name' => $detail_image_name,
                'detail_st' => $request->detail_st,
            ]);
        }
        
        //redirect
        return redirect()->route('updateListBarang', $detail->slug)->with('success', 'Data berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
