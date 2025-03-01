<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\TokoPusat;
use App\Models\User;
use App\Models\UserData;
use Auth;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::with(['toko_pusat_user', 'users_data'])->where('user_id', Auth::user()->user_id)->first();
        $data['user'] = $user;
        $res_user_image = empty($user->users_data->user_image) ? 'default.jpg' : $user->users_data->user_image;
        $data['res_user_image'] = $res_user_image;
        // dd($user);
        $detail = TokoPusat::where('id', $user->toko_pusat_user->pusat_id)->first();
        if (empty($detail)) {
            return redirect()->route('dashboard')->with('error', 'Data tidak ditemukan');
        }
        $data['detail'] = $detail;
        $data['user_id'] = Auth::user()->user_id;
        // dd($detail);
        $data['title'] = 'Edit Profil';
        return view('user.profil.index', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_toko(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'pusat_nama' => 'required',
            'pusat_pemilik' => 'required',
            'pusat_alamat' => 'required',
        ]);
        // dd($request->all());
        $detail = TokoPusat::find($request->id);
        if ($request->hasFile('toko_image')) {
            $request->validate([
                'toko_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        }
        //
        $user_image = $detail->user_image;
        // upload image
        if ($request->hasFile('toko_image')) {
            $tujuan_upload = 'image/profil';
            $file = $request->file('toko_image');
            //
            if (!$file->move($tujuan_upload, $file->getClientOriginalName())) {
                return redirect()->route('profil')->with('error', 'Gagal simpan foto');
            }
            // name
            $user_image = $file->getClientOriginalName();
        }
        $detail->pusat_nama = $request->pusat_nama;
        $detail->pusat_pemilik = $request->pusat_pemilik;
        $detail->pusat_alamat = $request->pusat_alamat;
        $detail->user_image = $user_image;
        // save
        if ($detail->save()) {
            return redirect()->route('profil')->with('success', 'Data berhasil diupdate');
        } else {
            return redirect()->route('profil')->with('error', 'Gagal update date');
        }
    }

    public function update_profil(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'username' => 'required|unique:users,username,' . $request->user_id . ',user_id',
        ]);
        if ($request->hasFile('user_image')) {
            $request->validate([
                'user_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        }

        // dd($request->all());
        $detail = User::with('users_data')->where('user_id', $request->user_id)->first();
        // dd($detail->users_data);
        $users_data = $detail->users_data;
        $user_image = '';
        if (!empty($users_data)) {
            $user_image = $users_data->user_image;
        }
        // upload image
        if ($request->hasFile('user_image')) {
            $tujuan_upload = 'image/profil';
            $file = $request->file('user_image');
            //
            if (!$file->move($tujuan_upload, $file->getClientOriginalName())) {
                return redirect()->route('profil')->with('error', 'Gagal simpan foto');
            }
            // name
            $user_image = $file->getClientOriginalName();
        }
        if (empty($users_data)) {
            // insert user data
            UserData::create([
                'cabang_id' => null,
                'user_id' => $detail->user_id,
                'user_nama_lengkap' => $request->name,
                'user_alamat' => $request->user_alamat,
                'user_jk' => $request->user_jk,
                'user_st' => $request->user_st,
                'user_image' => $user_image,
            ]);
        } else {
            // update
            $users_data->user_nama_lengkap = $request->name;
            $users_data->user_alamat = $request->user_alamat;
            $users_data->user_jk = $request->user_jk;
            $users_data->user_st = $request->user_st;
            $users_data->user_image = $user_image;
            $users_data->save();
        }
        // user
        $detail->name = $request->name;
        $detail->username = $request->username;
        if (!empty($request->password)) {
            $detail->password = bcrypt($request->password);
        }
        if ($detail->save()) {
            return redirect()->route('profil')->with('success', 'Data berhasil diupdate');
        } else {
            return redirect()->route('profil')->with('error', 'Gagal update date');
        }
    }

}
