<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\TokoPusat;
use App\Models\website\Banner;
use App\Models\website\DetailBarang;
use App\Models\website\Faq;
use App\Models\website\Pref;
use App\Models\website\Promo;
use App\Models\website\Testimoni;
use App\Models\website\Why;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'pusat_id' => 'required',
        ]);
        // error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // 
        $pusat = TokoPusat::find($request->pusat_id);
        if (empty($pusat)) {
            return response()->json([
                'success' => false,
                'error' => null,
                'message' => 'Toko tidak ditemukan'
            ], 422);
        }
        $rs_data = Banner::where('pusat_id', $pusat->id)->orderByRaw('CAST(banner_urut AS UNSIGNED) ASC')->get();
        if (count($rs_data) == 0 ) {
            $status = false;
            $message = 'Tidak Ditemukan';
        } else {
            $status = true;
            $message = 'Ditemukan';
        }
        // 
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $rs_data,
            'pusat_id' => $request->pusat_id,
        ]);
    }

    public function promo(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'pusat_id' => 'required',
        ]);
        // error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // 
        $pusat = TokoPusat::find($request->pusat_id);
        if (empty($pusat)) {
            return response()->json([
                'success' => false,
                'error' => null,
                'message' => 'Toko tidak ditemukan'
            ], 422);
        }
        $rs_data = Promo::with('barang_master.detail_barang')->where('pusat_id', $pusat->id)->where('promo_st', 'yes')->orderBy('promo_start', 'DESC')->get();
        if (count($rs_data) == 0 ) {
            $status = false;
            $message = 'Tidak Ditemukan';
        } else {
            $status = true;
            $message = 'Ditemukan ya';
        }
        // 
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $rs_data,
            'pusat_id' => $request->pusat_id,
        ]);
    }

    public function barang(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'pusat_id' => 'required',
        ]);
        // error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // 
        $pusat = TokoPusat::find($request->pusat_id);
        if (empty($pusat)) {
            return response()->json([
                'success' => false,
                'error' => null,
                'message' => 'Toko tidak ditemukan'
            ], 422);
        }
        // get all barang by toko pusat
        $rs_barang = DetailBarang::join('barang_master', 'detail_barang.barang_id', '=', 'barang_master.id')
            ->where('barang_master.pusat_id', $pusat->id)
            ->where('detail_st', 'yes')
            ->where(DB::raw("LOWER(CONCAT(barang_master.barang_nama, barang_master.barang_barcode))"), 'LIKE', "%".strtolower($request->params)."%")
            ->orderBy('barang_master.barang_nama', 'ASC')
            ->select('detail_barang.*', 'barang_master.*')
            ->paginate(8);
        if (count($rs_barang) == 0 ) {
            $status = false;
            $message = 'Tidak Ditemukan';
        } else {
            $status = true;
            $message = 'Ditemukan';
        }
        // 
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $rs_barang,
            'pusat_id' => $request->pusat_id,
        ]);
    }

    public function tentang_kami(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'pusat_id' => 'required',
        ]);
        // error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // 
        $pusat = TokoPusat::find($request->pusat_id);
        if (empty($pusat)) {
            return response()->json([
                'success' => false,
                'error' => null,
                'message' => 'Toko tidak ditemukan'
            ], 422);
        }
        $about_me = Pref::where('pusat_id', $pusat->id)->where('pref_name', 'aboutme')->first();
        $rs_image = Pref::where('pusat_id', $pusat->id)->where('pref_name', 'pref_image')->get();
        if (empty($about_me)) {
            $status = false;
            $message = 'Tidak Ditemukan';
        } else {
            $status = true;
            $message = 'Ditemukan';
        }
        // 
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $about_me->pref_value,
            'rs_image' => $rs_image,
            'pusat_id' => $request->pusat_id,
        ]);
    }

    public function produk_terbanyak(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'pusat_id' => 'required',
        ]);
        // error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // 
        $pusat = TokoPusat::find($request->pusat_id);
        if (empty($pusat)) {
            return response()->json([
                'success' => false,
                'error' => null,
                'message' => 'Toko tidak ditemukan'
            ], 422);
        }
        $rs_terbanyak = DB::select("SELECT * FROM (
                            SELECT COUNT(*) AS 'total', c.barang_id, d.barang_harga_jual, d.barang_grosir_pembelian, d.barang_grosir_harga_jual, d.barang_nama, e.detail_image_path, e.detail_image_name
                            FROM cart a
                            INNER JOIN cart_data b ON a.cart_id = b.cart_id
                            INNER JOIN barang_cabang c ON b.barang_cabang_id = c.id
                            INNER JOIN barang_master d ON c.barang_id = d.id
                            INNER JOIN detail_barang e ON d.id = e.barang_id
                            WHERE a.pusat_id = ?
                            GROUP BY  c.barang_id, d.barang_harga_jual, d.barang_grosir_pembelian, d.barang_grosir_harga_jual, d.barang_nama, e.detail_image_path, e.detail_image_name
                        )res ORDER BY res.total DESC", [$request->pusat_id]);
        if (empty($rs_terbanyak)) {
            $status = false;
            $message = 'Tidak Ditemukan';
        } else {
            $status = true;
            $message = 'Ditemukan';
        }
        // 
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $rs_terbanyak,
            'pusat_id' => $request->pusat_id,
        ]);
    }

    public function preference(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'pusat_id' => 'required',
            'pref_name' => 'required',
        ]);
        // error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // 
        $pusat = TokoPusat::find($request->pusat_id);
        if (empty($pusat)) {
            return response()->json([
                'success' => false,
                'error' => null,
                'message' => 'Toko tidak ditemukan'
            ], 422);
        }
        $rs_pref = Pref::where('pusat_id', $request->pusat_id)
            ->where('pref_name', $request->pref_name)
            ->first();
        if (empty($rs_pref)) {
            $status = false;
            $message = 'Tidak Ditemukan';
        } else {
            $status = true;
            $message = 'Ditemukan';
        }
        // 
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $rs_pref->pref_value,
            'pusat_id' => $request->pusat_id,
        ]);
    }

    public function get_why_choose(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'pusat_id' => 'required',
        ]);
        // error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // 
        $pusat = TokoPusat::find($request->pusat_id);
        if (empty($pusat)) {
            return response()->json([
                'success' => false,
                'error' => null,
                'message' => 'Toko tidak ditemukan'
            ], 422);
        }
        $rs_data = Why::where('pusat_id', $pusat->id)->get();
        if (empty($rs_data)) {
            $status = false;
            $message = 'Tidak Ditemukan';
        } else {
            $status = true;
            $message = 'Ditemukan';
        }
        // 
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $rs_data,
            'pusat_id' => $request->pusat_id,
        ]);
    }

    public function get_testimoni(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'pusat_id' => 'required',
        ]);
        // error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // 
        $pusat = TokoPusat::find($request->pusat_id);
        if (empty($pusat)) {
            return response()->json([
                'success' => false,
                'error' => null,
                'message' => 'Toko tidak ditemukan'
            ], 422);
        }
        $rs_data = Testimoni::where('pusat_id', $pusat->id)->get();
        if (empty($rs_data)) {
            $status = false;
            $message = 'Tidak Ditemukan';
        } else {
            $status = true;
            $message = 'Ditemukan';
        }
        // 
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $rs_data,
            'pusat_id' => $request->pusat_id,
        ]);
    }

    public function get_faq(Request $request)
    {
        // validasi
        $validator = Validator::make($request->all(), [
            'pusat_id' => 'required',
        ]);
        // error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // 
        $pusat = TokoPusat::find($request->pusat_id);
        if (empty($pusat)) {
            return response()->json([
                'success' => false,
                'error' => null,
                'message' => 'Toko tidak ditemukan'
            ], 422);
        }
        $rs_data = Faq::where('pusat_id', $pusat->id)->get();
        if (empty($rs_data)) {
            $status = false;
            $message = 'Tidak Ditemukan';
        } else {
            $status = true;
            $message = 'Ditemukan';
        }
        // 
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $rs_data,
            'pusat_id' => $request->pusat_id,
        ]);
    }

}
