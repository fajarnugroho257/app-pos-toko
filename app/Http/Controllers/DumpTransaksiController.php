<?php

namespace App\Http\Controllers;

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



            // Inisialisasi printer
            $printer = new Printer($connector);

            // Tambahkan teks atau format nota
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("TOKO ANDA\n");
            $printer->text("Jl. Contoh No.123\n");
            $printer->feed();

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Item        Qty    Harga\n");
            $printer->text("------------------------\n");
            $printer->text("Produk A    2      10.000\n");
            $printer->text("Produk B    1      15.000\n");
            $printer->text("------------------------\n");
            $printer->text("Total:         35.000\n");
            $printer->feed(2);

            // Akhiri cetakan
            $printer->cut();
            $printer->close();

            return "Nota berhasil dicetak.";
        } catch (\Exception $e) {
            return "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
