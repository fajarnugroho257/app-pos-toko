<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\MasterBarang;
use App\Models\TokoPusat;
use App\Models\website\DetailBarang;
use App\Models\website\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barang_nama = session()->get('promo_barang_nama');
        // 
        $data['promo_barang_nama'] = $barang_nama;
        $barang_nama = empty($barang_nama) ? '%' : '%' . $barang_nama . '%';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $data['title'] = 'Daftar Promo';
        $rs_data = Promo::with('barang_master.detail_barang')
            ->whereRelation('barang_master', DB::raw('CONCAT(barang_nama, barang_barcode)'), 'LIKE', '%'.$barang_nama.'%')
            ->where('pusat_id', $pusat->id)
            ->paginate(50);
        $data['rs_data'] = $rs_data;
        return view('website.promo.index', $data);
    }

    public function search(Request $request)
    {
        if ($request->aksi == 'reset') {
            session()->forget('promo_barang_nama');
        } else {
            session([
                'promo_barang_nama' => $request->promo_barang_nama,
            ]);
        }
        return redirect()->route('promo');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = "Tambah Promo";
        // barang by pusat ID
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $rs_barang = DetailBarang::with('barang_master')
        ->whereNotIn('barang_id', function($query){
            $query->select('barang_id')->from('promo');
        })
        ->where('detail_barang.pusat_id', $pusat->id)
        ->join('barang_master', 'barang_master.id', '=', 'detail_barang.barang_id')
        ->orderBy('barang_master.barang_nama')
        ->get();
        // dd($rs_barang);
        $data['rs_barang'] = $rs_barang;
        return view('website.promo.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required',
            'promo_start' => 'required',
            'promo_end' => 'required',
            'promo_st' => 'required',
            'promo_harga' => 'required',
            'promo_grosir_harga' => 'required',
            'promo_grosir_pembelian' => 'required',
        ]);
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $validated['pusat_id'] = $pusat->id;
        // dd($validated);
        Promo::create($validated);
        return redirect()->route('addPromo')->with('success', 'Data berhasil disimpan');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['title'] = "Ubah Promo";
        $detail = Promo::find($id);
        if (empty($detail)) {
            return redirect()->route('promo')->with('error', 'Data tidak ditemukan');
        }
        // barang by pusat ID
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $rs_barang = DetailBarang::with('barang_master')
        ->whereNotIn('barang_id', function($query) use ($id){
            $query->select('barang_id')->orWhere('barang_id', $id)->from('promo');
        })
        ->where('detail_barang.pusat_id', $pusat->id)
        ->join('barang_master', 'barang_master.id', '=', 'detail_barang.barang_id')
        ->orderBy('barang_master.barang_nama')
        ->get();
        // dd($rs_barang);
        $data['rs_barang'] = $rs_barang;
        $data['detail'] = $detail;
        return view('website.promo.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'barang_id' => 'required',
            'promo_start' => 'required',
            'promo_end' => 'required',
            'promo_st' => 'required',
            'promo_harga' => 'required',
            'promo_grosir_harga' => 'required',
            'promo_grosir_pembelian' => 'required',
        ]);
        // detail data
        $promo = Promo::findOrFail($id);
        if (empty($promo)) {
            return redirect()->route('promo')->with('error', 'Data tidak ditemukan');
        }
        $promo->update($validated);
        return redirect()->back()->with('success', 'Data promo berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $promo = Promo::find($id);
        if (empty($promo)) {
            return redirect()->route('promo')->with('error', 'Data tidak ditemukan');
        }
        $promo->delete();
        return redirect()->route('promo')->with('success', 'Data berhasil dihapus');
    }

    public function detail_barang_by_id(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'barang_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // detail barang
        $detail = MasterBarang::find($request->barang_id);
        if (empty($detail)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 200);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data ditemukan',
            'detail' => $detail,
        ], 200);
    }

}
