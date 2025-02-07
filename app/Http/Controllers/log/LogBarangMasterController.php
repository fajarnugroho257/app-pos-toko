<?php

namespace App\Http\Controllers\log;

use App\Http\Controllers\Controller;
use App\Models\MasterBarang;
use App\Models\TokoCabang;
use App\Models\TokoPusat;
use App\Models\TokoPusatUser;
use Auth;
use DB;
use Illuminate\Http\Request;

class LogBarangMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // search
        $barang_nama = session()->get('barang_nama');
        $data['barang_nama'] = $barang_nama;
        //
        $data['title'] = 'Log Data Barang Pusat';
        $pusat = TokoPusatUser::where('user_id', Auth::user()->user_id)->first();
        $barang_nama = empty($barang_nama) ? '%' : '%' . $barang_nama . '%';
        $data['rs_barang'] = MasterBarang::where('pusat_id', $pusat->pusat_id)
            ->where(DB::raw('CONCAT(barang_nama, barang_barcode)'), 'LIKE', $barang_nama)
            ->orderBy('barang_nama', 'ASC')
            ->paginate(50);

        return view('log.barangmaster.index', $data);
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
}
