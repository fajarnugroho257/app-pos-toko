<?php

namespace App\Http\Controllers;

use App\Models\CartData;
use App\Models\Transaksi;
use Auth;
use Illuminate\Http\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class DumpTransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('transaksi.index');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function cetakNota()
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
            $printer->text(Auth::user()->name . "\n");
            $printer->text("Jl. Contoh No.123\n");
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
            dd($printer);
            // dd($cartData);
            // Akhiri cetakan
            $printer->cut();
            $printer->close();

            return "Nota berhasil dicetak.";
        } catch (\Exception $e) {
            return "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
