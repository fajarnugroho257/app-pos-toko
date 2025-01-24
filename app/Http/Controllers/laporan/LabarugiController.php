<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartData;
use App\Models\TokoCabang;
use App\Models\TokoPusat;
use App\Models\Transaksi;
use Auth;
use DB;
use Illuminate\Http\Request;
use Validator;

class LabarugiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Laporan Laba Rugi';
        $pusat = TokoPusat::where('user_id', Auth::user()->user_id)->first();
        $data['rs_cabang'] = TokoCabang::where('pusat_id', $pusat->id)->get();
        // dd($data);
        return view('laporan.laba.index', $data);
    }

    public function show(string $slug)
    {
        // cari
        $date_start = session()->get('date_start');
        $date_end = session()->get('date_end');
        //
        $data['date_start'] = empty($date_start) ? date('Y-m-01') : $date_start;
        $data['date_end'] = empty($date_end) ? date('Y-m-t') : $date_end;
        //
        $cabang = TokoCabang::where('slug', $slug)->first();
        if (empty($cabang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        $data['cabang'] = $cabang;
        $data['title'] = 'Laporan Laba Rugi';
        // data transaksi
        $transaksi = Transaksi::whereRelation('cart', 'cabang_id', $cabang->id)
            ->orderBy(DB::raw('trans_date'), 'DESC')
            ->with(['cart', 'cart_data'])
            ->whereBetween(DB::raw('DATE(trans_date)'), [$data['date_start'], $data['date_end']])
            ->get();
        // dd($transaksi);
        $data['rs_laba'] = $transaksi;
        return view('laporan.laba.tanggal', $data);
    }

    public function search(Request $request)
    {
        $cabang = TokoCabang::find($request->id);
        if (empty($cabang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        if ($request->aksi == 'reset') {
            session()->forget('date_start');
            session()->forget('date_end');
        } else {
            session([
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
            ]);
        }
        return redirect()->route('showLabaRugi', ['slug' => $cabang->slug]);
    }

    public function detail_laba(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $transaksiCart = Transaksi::where('cart_id', $request->cart_id)->first();
        if (empty($transaksiCart)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 422);
        }
        // cart data
        $cartData = CartData::where('cart_id', $transaksiCart->cart_id)->orderBy('cart_urut', 'DESC')->get();
        // dd($cartData);
        $html = '';
        $no = 1;
        $totalgrandLaba = 0;
        if (!empty($cartData)) {
            foreach ($cartData as $key => $value) {
                $laba = $value['cart_harga_jual'] - $value['cart_harga_beli'];
                $grandLaba = $laba * $value['cart_qty'];
                $totalgrandLaba += $grandLaba;
                //
                $html .= '<tr>';
                $html .= '  <td class="text-center">';
                $html .= $no++;
                $html .= '  </td>';
                $html .= '  <td>';
                $html .= $value['cart_nama'];
                $html .= '  </td>';
                $html .= '  <td class="text-right">';
                $html .= '      Rp. ' . number_format($value['cart_harga_beli'], 0, ',', '.');
                $html .= '  </td>';
                $html .= '  </td>';
                $html .= '  <td class="text-right">';
                $html .= '      Rp. ' . number_format($value['cart_harga_jual'], 0, ',', '.');
                $html .= '  </td>';
                $html .= '  <td class="text-right">';
                $html .= '      Rp. ' . number_format($laba, 0, ',', '.');
                $html .= '  </td>';
                $html .= '  <td class="text-center">';
                $html .= $value['cart_qty'];
                $html .= '  </td>';
                $html .= '  <td class="text-right">';
                $html .= '      Rp. ' . number_format($grandLaba, 0, ',', '.');
                $html .= '  </td>';
                $html .= '</tr>';
            }
            $html .= '<tr>';
            $html .= '  <td class="text-right" colspan="6">';
            $html .= 'Grand Total Laba';
            $html .= '  </td>';
            $html .= '  <td class="text-right text-success text-bold">';
            $html .= '      Rp. ' . number_format($totalgrandLaba, 0, ',', '.');
            $html .= '  </td>';
            $html .= '</tr>';
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
            'html' => $html,
        ], 200);
    }
}
