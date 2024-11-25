<?php

namespace App\Http\Controllers\barang;

use App\Http\Controllers\Controller;
use App\Models\BarangCabang;
use App\Models\MasterBarang;
use App\Models\TokoCabang;
use App\Models\TokoPusat;
use Auth;
use Illuminate\Http\Request;

class BarangcabangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Data Barang Cabang';
        $pusat = TokoPusat::where('user_id', Auth::user()->user_id)->first();
        $data['rs_cabang'] = TokoCabang::where('pusat_id', $pusat->id)->paginate(10);
        // dd($data);
        return view('barang.cabang.index', $data);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required',
            'cabang_id' => 'required'
        ]);
        $cabang = TokoCabang::find($request->cabang_id);
        if (empty($cabang)) {
            return redirect()->route('barangCabang')->with('error', 'Data tidak ditemukan');
        }
        // dd($request->barang_id);
        $rs_barang = $request->barang_id;
        foreach ($rs_barang as $key => $value) {
            $m_brg = MasterBarang::find($value);
            // echo $value;
            BarangCabang::create([
                'barang_id' => $value,
                'cabang_id' => $cabang->id,
                'barang_stok' => 0,
                'cabang_barang_harga' => $m_brg->barang_harga,
                'barang_st' => 'yes'
            ]);
        }
        return redirect()->route('showBarangCabang', ['slug' => $cabang->slug])->with('success', 'Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function detail(string $slug)
    {
        // cari
        $barang_cabang_nama = session()->get('barang_cabang_nama');
        $data['barang_cabang_nama'] = $barang_cabang_nama;
        $barang_cabang_nama = empty($barang_cabang_nama) ? '%' : '%' . $barang_cabang_nama . '%';
        //
        $data['title'] = 'Data Barang Cabang';
        $cabang = TokoCabang::where('slug', $slug)->first();
        if (empty($cabang)) {
            return redirect()->route('barangCabang')->with('error', 'Data tidak ditemukan');
        }
        $data['cabang'] = $cabang;
        $data['rs_barang'] = BarangCabang::with('barang_master')
            ->select('barang_cabang.*')
            ->join('barang_master', 'barang_master.id', '=', 'barang_cabang.barang_id')
            ->orderBy('barang_master.barang_nama')
            ->where('cabang_id', $cabang->id)
            ->where('barang_master.barang_nama', 'LIKE', $barang_cabang_nama)
            ->paginate(50);
        // dd($data);
        return view('barang.cabang.detail', $data);
    }

    public function get_barang_not_exits(Request $request)
    {
        $cabang = TokoCabang::find($request->cabang_id);
        if (empty($cabang)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak tersedia!',
            ], 200);
        }
        // Mendapatkan semua ID barang
        $barang_tersedia = BarangCabang::where('cabang_id', $cabang->id)->pluck('barang_id')->toArray();
        // dd($barang_tersedia);
        $data = MasterBarang::where('pusat_id', $cabang->pusat_id)
            ->whereNotIn('id', values: $barang_tersedia)
            ->get();
        // dd($data);
        $html = '';
        $no = 1;
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $html .= '<tr>';
                $html .= '  <td class="text-center">';
                $html .= $no++;
                $html .= '  </td>';
                $html .= '  <td>';
                $html .= $value['barang_nama'];
                $html .= '  </td>';
                $html .= '  <td>';
                $html .= '      Rp. ' . number_format($value['barang_harga'], 0, ',', '.');
                $html .= '  </td>';
                $html .= '</tr>';
                $html .= '<input type="hidden" name="barang_id[]" value="' . $value['id'] . '">';
            }
        } else {
            $html .= '<tr>';
            $html .= '  <td class="text-center">';
            $html .= '      <p><i>Barang baru tidak tersedia</i></p>';
            $html .= '  </td>';
            $html .= '</tr>';
        }

        return response()->json([
            'success' => true,
            'message' => 'Okee..!',
            'data' => $data,
            'html' => $html,
        ], 200);
        // dd($request->all());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['title'] = 'Ubah Data Barang Cabang';
        $detail = BarangCabang::with('barang_master', 'toko_cabang')->find($id);
        if (empty($detail)) {
            return redirect()->route('barangCabang')->with('error', 'Data tidak ditemukan');
        }
        $data['detail'] = $detail;
        return view('barang.cabang.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'barang_stok' => 'required|numeric',
            'cabang_barang_harga' => 'required|numeric',
            'barang_st' => 'required',
        ]);
        $detail = BarangCabang::find($request->id);
        if (empty($detail)) {
            return redirect()->route('barangCabang')->with('error', 'Data tidak ditemukan');
        }
        $detail->barang_stok = $request->barang_stok;
        $detail->cabang_barang_harga = $request->cabang_barang_harga;
        $detail->barang_st = $request->barang_st;
        if ($detail->save()) {
            return redirect()->route('updatebarangCabang', ['id' => $detail->id])->with('success', 'Data berhasil disimpan');
        }
    }
    public function search(Request $request)
    {
        $cabang = TokoCabang::find($request->id);
        if (empty($cabang)) {
            return redirect()->route('barangCabang')->with('error', 'Data tidak ditemukan');
        }
        session([
            'barang_cabang_nama' => $request->barang_cabang_nama
        ]);
        return redirect()->route('showBarangCabang', ['slug' => $cabang->slug]);
    }
}
