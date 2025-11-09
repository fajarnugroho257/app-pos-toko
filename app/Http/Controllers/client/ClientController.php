<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\TokoCabang;
use App\Models\TokoPusat;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $pusat = User::with('toko_pusat_user')->where('user_id', Auth::user()->user_id)->first();
        $data['title'] = "Data Client Token Aplikasi";
        $data['rs_data'] = Client::with(['cabang', 'user'])->whereRelation('cabang.toko_pusat', 'pusat_id', $pusat->toko_pusat_user->pusat_id)->orderBy('token_date', 'DESC')->paginate(50);
        // dd($data);
        return view('client.token.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = "Tambah Data Client Token Aplikasi";
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $data['rs_cabang'] = TokoCabang::where('pusat_id', $pusat->id)->get();
        $data['token_value'] = $this->createToken(6);
        // dd($data);
        return view('client.token.add', $data);
    }

    // 
    function createToken($length = 6)
    {
        do {
            // Kombinasi huruf besar dan angka (tanpa karakter mirip)
            $token = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, $length);
        } while (Client::where('token_value', $token)->exists());

        // Simpan ke tabel tokens
        return $token;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cabang_id' => 'required',
            'user_id' => 'required',
            'token_value' => 'required|unique:client_auth,token_value',
        ]);
        $ttlTokenAktive = Client::where('cabang_id', $request->cabang_id)->where('token_active', 'yes')->count();
        if ($ttlTokenAktive >= 1) {
            return redirect()->back()->withInput()->with('error', 'Token aktif untuk cabang ini sudah ada, silahkan nonaktifkan token sebelumnya');
        }
        // simpan
        $request->merge(['token_date' => date('Y-m-d')]);
        $request->merge(['token_active' => 'yes']);
        if(Client::create($request->all())){
            return redirect()->route('addClientToken')->with('success', 'Data berhasil disimpan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        // dd($request->all());
        $kasir = UserData::where('cabang_id', $request->all())->get();
        return response()->json($kasir);
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
