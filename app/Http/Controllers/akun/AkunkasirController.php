<?php

namespace App\Http\Controllers\akun;

use App\Http\Controllers\Controller;
use App\Models\TokoCabang;
use App\Models\TokoPusat;
use App\Models\User;
use App\Models\UserData;
use Auth;
use Illuminate\Http\Request;

class AkunkasirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // dd(Auth::user());
        $pusat = User::with('toko_pusat_user')->where('user_id', Auth::user()->user_id)->first();
        // dd($pusat->toko_pusat_user->pusat_id);
        $data['title'] = 'Data Akun Kasir';
        $data['rs_user'] = User::with('users_data.toko_cabang')->whereRelation('users_data.toko_cabang', 'pusat_id', $pusat->toko_pusat_user->pusat_id)->where('role_id', 'R0005')->paginate(5);
        // dd($data);
        return view('akun.kasir.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Tambah Akun Kasir';
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $data['rs_cabang'] = TokoCabang::where('pusat_id', $pusat->id)->get();
        return view('akun.kasir.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'name' => 'required',
            'cabang_id' => 'required',
            'user_alamat' => 'required',
            'user_jk' => 'required',
            'user_st' => 'required',
            'user_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        //
        $user_image = '';
        if ($request->hasFile('user_image')) {
            $tujuan_upload = 'image/profil';
            $file = $request->file('user_image');
            //
            if (!$file->move($tujuan_upload, $file->getClientOriginalName())) {
                return redirect()->route('tambahAkunKasir')->with('error', 'Gagal simpan foto');
            }
            // name
            $user_image = $file->getClientOriginalName();
        }
        //
        $user_id = $this->last_user_id();
        if ($user_id) {
            User::create([
                'user_id' => $user_id,
                'name' => $request->name,
                'role_id' => 'R0005',
                'username' => $request->username,
                'password' => bcrypt($request->password),
            ]);
            // insert user data
            UserData::create([
                'cabang_id' => $request->cabang_id,
                'user_id' => $user_id,
                'user_nama_lengkap' => $request->name,
                'user_alamat' => $request->user_alamat,
                'user_jk' => $request->user_jk,
                'user_st' => $request->user_st,
                'user_image' => $user_image,
            ]);
            //redirect
            return redirect()->route('tambahAkunKasir')->with('success', 'Data berhasil disimpan');
        }
    }
    function last_user_id()
    {
        // get last user id
        $last_user = User::select('user_id')->orderBy('user_id', 'DESC')->first();
        $last_number = substr($last_user->user_id, 1, 6) + 1;
        $zero = '';
        for ($i = strlen($last_number); $i <= 3; $i++) {
            $zero .= '0';
        }
        $new_id = 'U' . $zero . $last_number;
        //
        return $new_id;
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
        $data['title'] = 'Ubah Akun Kasir';
        $detail = User::with('users_data')->where('user_id', $id)->first();
        $data['detail'] = $detail;
        $pusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', Auth::user()->user_id)->first();
        $data['rs_cabang'] = TokoCabang::where('pusat_id', $pusat->id)->get();
        // dd($detail);
        return view('akun.kasir.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'username' => 'required|unique:users,username,' . $request->user_id . ',user_id',
            'name' => 'required',
            'cabang_id' => 'required',
            'user_alamat' => 'required',
            'user_jk' => 'required',
            'user_st' => 'required',
        ]);
        if ($request->hasFile('user_image')) {
            $request->validate([
                'user_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        }
        //
        $detail = User::where('user_id', $request->user_id)->first();
        if (empty($detail)) {
            return redirect()->route('akunPelanggan')->with('error', 'Data tidak ditemukan');
        }
        $detail->name = $request->name;
        $detail->username = $request->username;
        if (!empty($request->password)) {
            $detail->password = bcrypt($request->password);
        }
        if ($detail->save()) {
            $detailUser = UserData::where('user_id', $request->user_id)->first();
            //
            $user_image = $detailUser->user_image;
            if ($request->hasFile('user_image')) {
                $tujuan_upload = 'image/profil';
                $file = $request->file('user_image');
                //
                if (!$file->move($tujuan_upload, $file->getClientOriginalName())) {
                    return redirect()->route('addAkunAdmin')->with('error', 'Gagal simpan foto');
                }
                // name
                $user_image = $file->getClientOriginalName();
            }
            if (!empty($detailUser)) {
                $detailUser->cabang_id = $request->cabang_id;
                $detailUser->user_alamat = $request->user_alamat;
                $detailUser->user_nama_lengkap = $request->name;
                $detailUser->user_jk = $request->user_jk;
                $detailUser->user_st = $request->user_st;
                $detailUser->user_image = $user_image;
                //
                if ($detailUser->save()) {
                    return redirect()->route('UpdateAkunKasir', [$request->user_id])->with('success', 'Data berhasil diupdate');
                }
            }
        } else {
            return redirect()->route('akunKasir')->with('error', 'Gagal update date');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $user_id)
    {
        $detail = User::where('user_id', $user_id)->first();
        if (empty($detail)) {
            return redirect()->route('akunKasir')->with('error', 'Data tidak ditemukan');
        }
        if ($detail->delete()) {
            return redirect()->route('akunKasir')->with('success', 'Data berhasil dihapus');
        } else {
            return redirect()->route('akunKasir')->with('error', 'Data gagal dihapus');
        }
    }
}
