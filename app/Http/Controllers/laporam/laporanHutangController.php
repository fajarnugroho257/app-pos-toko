<?php

namespace App\Http\Controllers\laporam;

use App\Http\Controllers\Controller;
use App\Models\CartData;
use App\Models\CartDraft;
use App\Models\TokoCabang;
use App\Models\TokoPusat;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class laporanHutangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Laporan Hutang';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $data['rs_cabang'] = TokoCabang::where('pusat_id', $pusat->id)->get();
        // dd($data);
        return view('laporan.hutang.index', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        // cari
        $date_start = session()->get('date_start_hutang');
        $date_end = session()->get('date_end_hutang');
        //
        $data['date_start'] = empty($date_start) ? date('Y-m-01') : $date_start;
        $data['date_end'] = empty($date_end) ? date('Y-m-t') : $date_end;
        //
        $cabang = TokoCabang::where('slug', $slug)->first();
        if (empty($cabang)) {
            return redirect()->route('logBarang')->with('error', 'Data tidak ditemukan');
        }
        $data['cabang'] = $cabang;
        $data['title'] = 'Laporan Hutang';
        // data transaksi
        $transaksi = Transaksi::whereRelation('cart.cart_draft', 'cabang_id', $cabang->id)
            ->orderBy(DB::raw('trans_date'), 'DESC')
            ->with(['cart.cart_draft', 'cart_data'])
            ->whereBetween(DB::raw('DATE(trans_date)'), [$data['date_start'], $data['date_end']])
            ->whereHas('cart', function ($q) {
                    $q->whereIn('cart_st', ['hutang']);
                })
            ->get();
        $data['rs_laba'] = $transaksi;
        // dd($transaksi);
        return view('laporan.hutang.tanggal', $data);
    }

    public function search(Request $request)
    {
        $cabang = TokoCabang::find($request->id);
        if (empty($cabang)) {
            return redirect()->route('laporanHutang')->with('error', 'Data tidak ditemukan');
        }
        if ($request->aksi == 'reset') {
            session()->forget('date_start_hutang');
            session()->forget('date_end_hutang');
        } else {
            session([
                'date_start_hutang' => $request->date_start,
                'date_end_hutang' => $request->date_end,
            ]);
        }
        return redirect()->route('showLaporanHutang', ['slug' => $cabang->slug]);
    }

    public function show_nota(Request $request)
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
        $transaksiCart = Transaksi::with('cart')->where('cart_id', $request->cart_id)->first();
        if (empty($transaksiCart)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 422);
        }
        // cart data
        $cartData = CartData::where('cart_id', $transaksiCart->cart_id)->orderBy('cart_urut', 'DESC')->get();
        $detailHutang = CartDraft::where('cart_id', $transaksiCart->cart_id)->first();
        $htmlPembeli = '';
        if (!empty($detailHutang)) {
            $htmlPembeli .= '<tr>';
            $htmlPembeli .= '   <td width="45%">Nama Pelanggan</td>';
            $htmlPembeli .= '   <td width="2%">:</td>';
            $htmlPembeli .= '   <td width="53%" class="text-right text-dark">' . $detailHutang->draft_pelanggan . '</td>';
            $htmlPembeli .= '</tr>';
            $htmlPembeli .= '<tr>';
            $htmlPembeli .= '   <td width="45%">Ttl Belanja</td>';
            $htmlPembeli .= '   <td width="2%">:</td>';
            $htmlPembeli .= '   <td width="53%" class="text-right text-success">Rp. ' . number_format($detailHutang->draft_uang_tagihan) . '</td>';
            $htmlPembeli .= '</tr>';
            $htmlPembeli .= '<tr>';
            $htmlPembeli .= '   <td width="45%">Uang Muka</td>';
            $htmlPembeli .= '   <td width="2%">:</td>';
            $htmlPembeli .= '   <td width="53%" class="text-right text-dark">Rp. ' . number_format($detailHutang->draft_uang_muka) . '</td>';
            $htmlPembeli .= '</tr>';
            $htmlPembeli .= '<tr class="table-danger">';
            $htmlPembeli .= '   <td width="45%">Kekurangan</td>';
            $htmlPembeli .= '   <td width="2%">:</td>';
            $htmlPembeli .= '   <td width="53%" class="text-right text-danger">Rp. ' . number_format($detailHutang->draft_uang_sisa) . '</td>';
            $htmlPembeli .= '</tr>';
            $htmlPembeli .= '<tr>';
            $htmlPembeli .= '   <td width="45%">Catatan</td>';
            $htmlPembeli .= '   <td width="2%">:</td>';
            $htmlPembeli .= '   <td width="53%" class="text-right text-dark"> '. $detailHutang->draft_note. '</td>';
            $htmlPembeli .= '</tr>';
        }
        // 
        $html = '';
        $no = 1;
        $grandTotal = 0;
        if (!empty($cartData)) {
            foreach ($cartData as $key => $value) {
                $cart_diskon = $value['cart_diskon'] == 'yes' ? 'Grosir' : '';
                $grandTotal += $value['cart_subtotal'];
                //
                $html .= '<tr>';
                $html .= '  <td class="text-center">';
                $html .= $no++;
                $html .= '  </td>';
                $html .= '  <td>';
                $html .= $value['cart_nama'];
                $html .= '  </td>';
                $html .= '  <td class="d-flex justify-content-between ">';
                $html .= '      <div class="text-danger"><b>' . $cart_diskon . '</b></div>';
                $html .= '      <div>Rp. ' . number_format($value['cart_harga_jual'], 0, ',', '.') . '</div>';
                $html .= '  </td>';
                $html .= '  <td class="text-center">';
                $html .= $value['cart_qty'];
                $html .= '  </td>';
                $html .= '  <td class="text-right">';
                $html .= '      Rp. ' . number_format($value['cart_subtotal'], 0, ',', '.');
                $html .= '  </td>';
                $html .= '</tr>';
            }
            $html .= '<tr>';
            $html .= '  <td class="text-right" colspan="4">';
            $html .= 'Grand Total';
            $html .= '  </td>';
            $html .= '  <td class="text-right text-success text-bold">';
            $html .= '      Rp. ' . number_format($grandTotal, 0, ',', '.');
            $html .= '  </td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr>';
            $html .= '  <td class="text-center">';
            $html .= '      <p><i>Data tidak ditemukan</i></p>';
            $html .= '  </td>';
            $html .= '</tr>';
        }
        return response()->json([
            'success' => true,
            'message' => 'Okee..!',
            'html' => $html,
            'htmlPembeli' => $htmlPembeli,
        ], 200);
    }

}
