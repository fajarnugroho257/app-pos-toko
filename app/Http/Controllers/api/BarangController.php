<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BarangCabang;
use App\Models\MasterBarang;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $user_data = UserData::where('user_id', Auth::user()->user_id)->first();
        $queryValue = request('query');
        $cabang_id = request('cabang_id');
        $data = DB::select("SELECT a.id,
                CONCAT(b.barang_barcode, ' | ', b.barang_nama, ' | ', b.barang_harga_jual) AS name
                FROM barang_cabang a
                INNER JOIN barang_master b ON a.barang_id = b.id
                WHERE a.cabang_id = ?
                AND a.barang_st = 'yes'
                AND a.barang_stok > 0
                AND CONCAT(b.barang_barcode, b.barang_nama) LIKE '%".$queryValue."%'", [$cabang_id]);

        return response()->json($data);
    }

    public function detail(Request $request)
    {
        $user_data = UserData::where('user_id', Auth::user()->user_id)->first();
        $queryValue = request('query');
        $cabang_id = request('cabang_id');
        $data = DB::selectOne("SELECT a.id,
                CONCAT(b.barang_barcode, ' | ', b.barang_nama, ' | ', b.barang_harga_jual) AS name
                FROM barang_cabang a
                INNER JOIN barang_master b ON a.barang_id = b.id
                WHERE a.cabang_id = ?
                AND a.barang_stok > 0
                AND b.barang_barcode = ".$queryValue, [$cabang_id]);

        return response()->json($data);
    }

    public function detail_data(Request $request)
    {
        $data = BarangCabang::with('barang_master')
            ->where('cabang_id', $request->id_cabang)
            ->where('id', $request->barang_cabang_id)->first();

        return response()->json($data);
    }

    public function list_data_barang(Request $request)
    {
        // $data = BarangCabang::with('barang_master')
        //     ->where('cabang_id', $request->cabang_id)->get();

        // return response()->json([
        //     'success' => true,
        //     'data' => $data,
        // ]);

        // $user = Auth::user();
        // $toko = DataToko::with('data_toko_user')
        //     ->whereRelation('data_toko_user', 'user_id', $user->user_id)->first();
        // // DataTokoUser::select('toko_id')->where('user_id', $user->user_id)->first();
        // // $datas = MasterBarang::
        // if (empty($toko)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Anda belum login',
        //     ], 401);
        // }
        $query = DB::table('barang_cabang');
        $query->select('barang_cabang.*', 'barang_master.*');
        $query->join('barang_master', 'barang_master.id', '=', 'barang_cabang.barang_id');
        $query->orderBy('barang_master.barang_nama');
        $query->where('cabang_id', $request->cabang_id);
        $query->where(DB::raw('CONCAT(barang_master.barang_nama, barang_master.barang_barcode)'), 'LIKE', '%'.$request->search.'%');
        $data = $query->paginate(50)->withQueryString();

        // // $query->where('toko_id', $toko->id);
        // $query->orderBy('barang_nama');
        // if ($request->search) {
        //     $query->where(DB::raw('CONCAT(barang_nama, barang_barcode)'), 'LIKE', '%'.$request->search.'%');
        // }
        // $data = $query->paginate(15)->withQueryString();

        return response()->json($data);

    }

    public function list_data_barang_all(Request $request)
    {
        $query = DB::table('barang_cabang');
        $query->select('barang_cabang.id AS barang_cabang_id', 'barang_cabang.*', 'barang_master.*');
        $query->join('barang_master', 'barang_master.id', '=', 'barang_cabang.barang_id');
        $query->orderBy('barang_master.barang_nama');
        $query->where('barang_cabang.cabang_id', $request->cabang_id);
        $data = $query->get();

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
