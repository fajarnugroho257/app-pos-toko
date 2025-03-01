<?php

namespace App\Http\Controllers\toko;

use App\Http\Controllers\Controller;
use App\Models\TokoPusat;
use App\Models\TokoPusatUser;
use App\Models\User;
use Illuminate\Http\Request;

class TokopusatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Data Toko Pusat';
        $data['rs_toko'] = TokoPusat::with('users')->paginate(10);
        // dd($data);
        return view('toko.pusat.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Tambah Data Toko Pusat';
        return view('toko.pusat.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pusat_nama' => 'required',
            'pusat_pemilik' => 'required',
            'pusat_alamat' => 'required',
        ]);
        TokoPusat::create([
            'pusat_nama' => $request->pusat_nama,
            'pusat_pemilik' => $request->pusat_pemilik,
            'pusat_alamat' => $request->pusat_alamat,
        ]);
        //redirect
        return redirect()->route('tokoPusat')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slugid)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        $detail = TokoPusat::where('slug', $slug)->first();
        if (empty($detail)) {
            return redirect()->route('tokoPusat')->with('error', 'Data tidak ditemukan');
        }
        // dd($detail);
        $data['title'] = 'Ubah Data Toko Pusat';
        $data['detail'] = $detail;
        return view('toko.pusat.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // dd($request->all());
        $detail = TokoPusat::find($request->id);
        // dd($detail);
        $request->validate([
            'id' => 'required',
            'pusat_nama' => 'required',
            'pusat_pemilik' => 'required',
            'pusat_alamat' => 'required',
        ]);
        $detail->pusat_nama = $request->pusat_nama;
        $detail->pusat_pemilik = $request->pusat_pemilik;
        $detail->pusat_alamat = $request->pusat_alamat;
        //redirect
        if ($detail->save()) {
            return redirect()->route('UpdateTokoPusat', ['slug' => $detail->slug])->with('success', 'Data berhasil disimpan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $detail = TokoPusat::where('slug', $slug)->first();
        if (empty($detail)) {
            return redirect()->route('tokoPusat')->with('error', 'Data tidak ditemukan');
        }
        if ($detail->delete()) {
            return redirect()->route('tokoPusat')->with('success', 'Sukses dihapus');
        }
    }

    public function user_pusat(string $slug)
    {
        $detail = TokoPusat::where('slug', $slug)->first();
        if (empty($detail)) {
            return redirect()->route('tokoPusat')->with('error', 'Data tidak ditemukan');
        }
        // dd($detail);
        $data['title'] = 'User Admin Data Toko Pusat';
        $data['detail'] = $detail;
        // user sekarang
        $rs_users = TokoPusatUser::with('users.app_role')->where('pusat_id', $detail->id)->get();
        // dd($rs_users);
        $data['rs_users'] = $rs_users;
        $exist_users = User::with('app_role')->whereIn('role_id', ['R0004', 'R0006', 'R0007'])
            ->whereNotIn('user_id', TokoPusatUser::pluck('user_id'))
            ->get();
        $data['exist_users'] = $exist_users;
        return view('toko.pusat.user', $data);
    }

    public function add_user_pusat(string $user_id, string $pusat_id)
    {
        $user = User::where('user_id', $user_id)->first();
        $pusat = TokoPusat::where('id', $pusat_id)->first();
        if (empty($pusat) || empty($user)) {
            return redirect()->route('userTokoPusat', ['slug' => $pusat->slug])->with('error', 'Data tidak ditemukan');
        }
        TokoPusatUser::create([
            'pusat_id' => $pusat_id,
            'user_id' => $user_id,
        ]);
        return redirect()->route('userTokoPusat', ['slug' => $pusat->slug])->with('success', 'User berhasil ditambahkan');
    }

    public function delete_user_pusat(string $id, string $pusat_id)
    {
        $user = TokoPusatUser::find($id);
        $pusat = TokoPusat::where('id', $pusat_id)->first();
        if (empty($pusat) || empty($user)) {
            return redirect()->route('userTokoPusat', ['slug' => $pusat->slug])->with('error', 'Data tidak ditemukan');
        }
        if ($user->delete()) {
            return redirect()->route('userTokoPusat', ['slug' => $pusat->slug])->with('success', 'User berhasil dihapuskan');
        }
    }


}
