<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BarangCabang;
use App\Models\MasterBarang;
use DB;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
    public function show(Request $request)
    {
        $queryValue = request('query');
        $data = DB::select("SELECT a.id,
                CONCAT(b.barang_barcode, ' | ', b.barang_nama, ' | ', b.barang_harga_jual) AS name
                FROM barang_cabang a
                INNER JOIN barang_master b ON a.barang_id = b.id
                WHERE a.cabang_id = ?
                AND CONCAT(b.barang_barcode, b.barang_nama) LIKE '%" . $queryValue . "%'", [6]);
        return response()->json($data);
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
}
