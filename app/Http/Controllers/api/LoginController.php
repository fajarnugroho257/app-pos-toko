<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get credentials from request
        $credentials = $request->only('username', 'password');

        //if auth failed
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'username atau Password Anda salah'
            ], 401);
        }
        // user ID
        $user_id = auth()->guard('api')->user()->user_id;
        // data user
        $dataUser = User::with('users_data.toko_cabang.toko_pusat')->where('user_id', $user_id)->where('role_id', 'R0005')->first();
        // dd($dataUser);
        if (empty($dataUser)) {
            return response()->json([
                'success' => false,
                'message' => 'username atau Password Anda salah'
            ], 401);
        } else {
            //if auth success
            return response()->json([
                'success' => true,
                'user' => auth()->guard('api')->user(),
                'cabang_id' => $dataUser->users_data->cabang_id,
                'cabang_nama' => $dataUser->users_data->toko_cabang->cabang_nama,
                'toko_pusat_id' => $dataUser->users_data->toko_cabang->toko_pusat->id,
                'toko_pusat' => $dataUser->users_data->toko_cabang->toko_pusat->pusat_nama,
                'token' => $token
            ], 200);
        }
    }

    // Fungsi Logout
    public function logout()
    {
        try {
            // Mengambil token yang sedang aktif
            $token = JWTAuth::getToken();

            // Meng-invalidate token tersebut
            JWTAuth::invalidate($token);

            // Mengembalikan response sukses
            return response()->json(['message' => 'Logout berhasil']);
        } catch (JWTException $e) {
            // Jika ada error dalam meng-invalidate token
            return response()->json(['error' => 'Gagal logout, token tidak valid'], 500);
        }
    }

}
