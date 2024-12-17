<?php

namespace App\Http\Controllers\transaksi;

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

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Transaksi';
        $pusat = TokoPusat::where('user_id', Auth::user()->user_id)->first();
        $data['rs_cabang'] = TokoCabang::where('pusat_id', $pusat->id)->paginate(10);
        // dd($data);
        return view('transaksi.penjualan.index', $data);
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
    public function show(string $slug)
    {
        // cari
        $date_start = session()->get('date_start');
        $date_end = session()->get('date_end');
        //
        $data['date_start'] = empty($date_start) ? date('Y-m-01') : $date_start;
        $data['date_end'] = empty($date_end) ? date('Y-m-t') : $date_end;
        // dd($data);
        //
        $cabang = TokoCabang::with('toko_pusat')->where('slug', $slug)->first();
        if (empty($cabang)) {
            return redirect()->route('transaksi')->with('error', 'Data tidak ditemukan');
        }
        $data['cabang'] = $cabang;
        //
        $data['title'] = 'Detail Transaksi';
        $rs_transaksi = Transaksi::with(['cart.cart_data', 'users'])
            ->whereRelation('cart', 'cabang_id', $cabang->id)
            ->whereRelation('cart', 'pusat_id', $cabang->toko_pusat->id)
            ->orderBy('trans_date', 'DESC')
            ->whereBetween(DB::raw('DATE(trans_date)'), [$data['date_start'], $data['date_end']])

            ->get();
        // dd($rs_transaksi);
        $data['rs_transaksi'] = $rs_transaksi;
        // return response()->json($rs_transaksi, 200);
        return view('transaksi.penjualan.detail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
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
        $grandTotal = 0;
        if (!empty($cartData)) {
            foreach ($cartData as $key => $value) {
                $grandTotal += $value['cart_subtotal'];
                //
                $html .= '<tr>';
                $html .= '  <td class="text-center">';
                $html .= $no++;
                $html .= '  </td>';
                $html .= '  <td>';
                $html .= $value['cart_nama'];
                $html .= '  </td>';
                $html .= '  <td class="text-right">';
                $html .= '      Rp. ' . number_format($value['cart_harga_jual'], 0, ',', '.');
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
            $html .= '  <td class="text-right text-danger text-bold">';
            $html .= '      Rp. ' . number_format($grandTotal, 0, ',', '.');
            $html .= '  </td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '  <td class="text-right" colspan="4">';
            $html .= 'Cash';
            $html .= '  </td>';
            $html .= '  <td class="text-right text-info text-bold">';
            $html .= '      Rp. ' . number_format($transaksiCart->trans_bayar, 0, ',', '.');
            $html .= '  </td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '  <td class="text-right" colspan="4">';
            $html .= 'Kembalian';
            $html .= '  </td>';
            $html .= '  <td class="text-right text-success text-bold">';
            $html .= '      Rp. ' . number_format($transaksiCart->trans_kembalian, 0, ',', '.');
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
        return redirect()->route('transaksiCabang', ['slug' => $cabang->slug]);

    }

}
