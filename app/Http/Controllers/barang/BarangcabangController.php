<?php

namespace App\Http\Controllers\barang;

use App\Http\Controllers\Controller;
use App\Models\BarangCabang;
use App\Models\BarangLog;
use App\Models\BarangMasterLog;
use App\Models\MasterBarang;
use App\Models\TokoCabang;
use App\Models\TokoPusat;
use Auth;
use DB;
use Illuminate\Http\Request;

class BarangcabangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Data Barang Cabang';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
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
                'cabang_barang_harga' => $m_brg->barang_harga_jual,
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
            ->where(DB::raw('CONCAT(barang_master.barang_nama, barang_master.barang_barcode)'), 'LIKE', $barang_cabang_nama)
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
            ->where('barang_master_stok', '>', 0)
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
                $html .= '  <td class="text-center">';
                $html .= $value['barang_master_stok'];
                $html .= '  </td>';
                $html .= '  <td class="text-right">';
                $html .= '      Rp. ' . number_format($value['barang_harga_beli'], 0, ',', '.');
                $html .= '  </td>';
                $html .= '  <td class="text-right">';
                $html .= '      Rp. ' . number_format($value['barang_harga_jual'], 0, ',', '.');
                $html .= '  </td>';
                $html .= '  <td class="text-center">';
                $html .= $value['barang_grosir_pembelian'];
                $html .= '  </td>';
                $html .= '  <td class="text-right">';
                $html .= '      Rp. ' . number_format($value['barang_grosir_harga_jual'], 0, ',', '.');
                $html .= '  </td>';
                $html .= '  <td class="text-center">';
                $html .= '      <input type="checkbox" name="barang_id[]" value="' . $value['id'] . '">';
                $html .= '  </td>';
                $html .= '</tr>';
                // $html .=
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
        // dd($detail);
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
            // 'cabang_barang_harga' => 'required|numeric',
            'barang_st' => 'required',
            'barang_stok_penambahan' => 'required|numeric',
            'barang_stok_hasil' => 'required|numeric',
        ]);
        $detail = BarangCabang::find($request->id);
        if (empty($detail)) {
            return redirect()->route('barangCabang')->with('error', 'Data tidak ditemukan');
        }
        $jlh_barang_sblm_tambah = $detail->barang_stok;
        $detail->barang_stok = $request->barang_stok_hasil;
        // $detail->cabang_barang_harga = $request->cabang_barang_harga;
        $detail->cabang_barang_harga = null;
        $detail->barang_st = $request->barang_st;
        if ($detail->save()) {
            // update stok master / pusat
            $m_barang = MasterBarang::find($detail->barang_id);
            $m_sblm_perubahan = $m_barang->barang_master_stok;
            $m_barang->barang_master_stok = $m_sblm_perubahan - $request->barang_stok_penambahan;
            if ($request->barang_stok_penambahan !== 0) {
                $m_barang->save();
            }
            // insert to barang log
            $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
            // insert log barang master
            $m_stok_awal = $request->barang_master_stok;
            $m_akhir_stok = $m_stok_awal - $request->barang_stok_penambahan;
            BarangMasterLog::create([
                'user_id' => Auth::user()->user_id,
                'pusat_id' => $pusat->id,
                'cabang_id' => $request->cabang_id,
                'barang_master_id' => $request->barang_master_id,
                'barang_master_awal' => $request->barang_master_stok,
                'barang_master_perubahan' => '-' . $request->barang_stok_penambahan,
                'barang_master_akhir' => $m_akhir_stok,
                'barang_st' => 'pengiriman',
            ]);
            // if more than one
            // if ($request->barang_stok_penambahan > 0) {
            BarangLog::create([
                'user_id' => Auth::user()->user_id,
                'pusat_id' => $pusat->id,
                'cabang_id' => $detail->cabang_id,
                'barang_cabang_id' => $request->id,
                'barang_awal' => $jlh_barang_sblm_tambah,
                'barang_perubahan' => $request->barang_stok_penambahan,
                'barang_akhir' => $request->barang_stok_hasil,
                'barang_st' => 'perubahan',
            ]);
            // }
            return redirect()->route('updatebarangCabang', ['id' => $detail->id])->with('success', 'Data berhasil disimpan');
        }
    }

    public function show_detail_log(string $barang_cabang_id, string $cabang_id, string $pusat_id)
    {
        $data['title'] = 'Detail Log Barang Cabang';
        $cabang = TokoCabang::find($cabang_id);
        if (empty($cabang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        $barang = BarangCabang::with('barang_master')->find($barang_cabang_id);
        if (empty($barang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        $data['cabang'] = $cabang;
        $data['barang'] = $barang;
        // log barang
        $barangLog = BarangLog::with(['barang_cabang.barang_master', 'users'])
            ->where('barang_cabang_id', $barang_cabang_id)
            ->where('cabang_id', $cabang_id)
            ->where('pusat_id', $pusat_id)
            ->orderBy('created_at', 'DESC')
            ->paginate(100);
        $data['rs_barang_log'] = $barangLog;
        // dd($data);
        return view('barang.cabang.log_barang', $data);
    }

    public function search(Request $request)
    {
        $cabang = TokoCabang::find($request->id);
        if (empty($cabang)) {
            return redirect()->route('barangCabang')->with('error', 'Data tidak ditemukan');
        }
        if ($request->aksi == 'reset') {
            session()->forget('barang_cabang_nama');
        } else {
            session([
                'barang_cabang_nama' => $request->barang_cabang_nama,
            ]);
        }
        return redirect()->route('showBarangCabang', ['slug' => $cabang->slug]);
    }
}
