<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\TokoPusat;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detail = User::with('toko_pusat')->where('user_id', Auth::user()->user_id)->first();
        if (empty($detail)) {
            return redirect()->route('dashboard')->with('error', 'Data tidak ditemukan');
        }
        $data['detail'] = $detail;
        // dd($detail);
        $data['title'] = 'Edit Profil';
        return view('user.profil.index', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'user_id' => 'required',
            'username' => 'required|unique:users,username,' . $request->user_id . ',user_id',
            'pusat_nama' => 'required',
            'pusat_pemilik' => 'required',
            'pusat_alamat' => 'required',
        ]);
        // dd($request->all());
        $detail = TokoPusat::with('users')->where('id', $request->id)->where('user_id', $request->user_id)->first();
        if ($request->hasFile('user_image')) {
            $request->validate([
                'user_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        }
        //
        $user_image = $detail->user_image;
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
        $detail->pusat_nama = $request->pusat_nama;
        $detail->pusat_pemilik = $request->pusat_pemilik;
        $detail->pusat_alamat = $request->pusat_alamat;
        $detail->user_image = $user_image;
        // user
        $user = User::where('user_id', $request->user_id)->first();
        $user->name = $request->pusat_nama;
        $user->username = $request->username;
        if (!empty($request->password)) {
            $user->password = bcrypt($request->password);
        }
        if ($detail->save()) {
            $user->save();
            return redirect()->route('profil')->with('success', 'Data berhasil diupdate');
        } else {
            return redirect()->route('profil')->with('error', 'Gagal update date');
        }
    }

}
