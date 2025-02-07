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
use Route;
use Validator;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Transaksi';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
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
            $html .= '  <td colspan="4">';
            $html .= '  </td>';
            $html .= '  <td class="text-right">';
            $html .= '      <a href="#" onclick="printThermal(this)" data-cart_id="' . $transaksiCart->cart_id . '" class="btn btn-success"><i class="fa fa-print"></i> Print</a>';
            $html .= '  </td>';
            $html .= '<tr>';
            $html .= '</tr>';
            $html .= '<br />';
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

    public function cetakNotaTransaksi()
    {

        try {
            // exec('wmic printer get name', $printers);
            // dd($printers);
            // Nama printer sesuai konfigurasi sistem (lihat di 'Devices and Printers')
            // $connector = new WindowsPrintConnector("POS-58");
            // $connector = new WindowsPrintConnector("\\\\LAPTOP-1OLVA8NB\\POS-58");
            $connector = new WindowsPrintConnector("smb://LAPTOP-1OLVA8NB/POS-58");
            // $connector = new FilePrintConnector("LPT1");

            // data
            $transaksiCart = Transaksi::where('cart_id', '202501221002098713')->first();
            $cartData = CartData::where('cart_id', $transaksiCart->cart_id)->orderBy('cart_urut', 'DESC')->get();

            // Inisialisasi printer
            $printer = new Printer($connector);

            // Tambahkan teks atau format nota
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            // $printer->text($dataUser->users_data->toko_cabang->toko_pusat->pusat_nama . "\n");
            // $printer->text($dataUser->users_data->toko_cabang->cabang_nama . "\n");
            // $printer->feed();
            $grandTotal = 0;
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Item        Qty    Harga\n");
            $printer->text("------------------------------\n");
            foreach ($cartData as $key => $value) {
                $grandTotal += $value['cart_subtotal'];
                $printer->text($value['cart_nama'] . "\n");
                $printer->text(str_pad('Rp.' . number_format($value['cart_harga_jual'], 0, ',', '.'), 13) . str_pad($value['cart_qty'], 4) . str_pad('Rp.' . number_format($value['cart_subtotal'], 0, ',', '.'), 14) . "\n");
            }
            $printer->text("------------------------------\n");
            $printer->text(str_pad("Total", 17) . str_pad('Rp.' . number_format($grandTotal, 0, ',', '.'), 15));
            $printer->text(str_pad("Cash", 17) . str_pad('Rp.' . number_format($transaksiCart->trans_bayar, 0, ',', '.'), 15));
            $printer->text(str_pad("Kembalian", 17) . str_pad('Rp.' . number_format($transaksiCart->trans_kembalian, 0, ',', '.'), 15));
            // $printer->feed(2);
            // Akhiri cetakan
            $printer->cut();
            $printer->close();
            // return
            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan transaksi',
                // 'data' => $request->all(),
            ]);
        } catch (\Exception $e) {
            // return "Terjadi kesalahan: " . $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => "Terjadi kesalahan cetak: " . $e->getMessage(),
                // 'data' => $request->all(),
            ]);
        }
    }

    public function getPrintData($cart_id)
    {
        // $validator = Validator::make($request->all(), [
        //     'cart_id' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'errors' => $validator->errors()
        //     ], 422);
        // }
        // detail cart
        $cart = Cart::with(relations: 'toko_pusat')->where('cart_id', $cart_id)->first();
        $cabang = TokoCabang::where('id', $cart->cabang_id)->first();
        //
        $transaksiCart = Transaksi::where('cart_id', $cart_id)->first();
        if (empty($transaksiCart)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 422);
        }

        // cart data
        $cartData = CartData::select('cart_harga_jual', 'cart_nama', 'cart_qty', 'cart_subtotal')
            ->where('cart_id', $transaksiCart->cart_id)
            ->orderBy('cart_urut', 'DESC')->get();

        return response()->json([
            'printer' => 'POS-58',
            'font-size' => 12,
            'items' => $cartData,
            'pusat_nama' => $cart->toko_pusat->pusat_nama,
            'cabang_nama' => $cabang->cabang_nama,
            'trans_total' => $transaksiCart->trans_total,
            'trans_bayar' => $transaksiCart->trans_bayar,
            'trans_kembalian' => $transaksiCart->trans_kembalian,
        ]);
    }

}
