<?php

namespace App\Http\Controllers\menu;

use App\Http\Controllers\Controller;
use App\Models\TokoPusat;
use App\Models\TokoPusatUser;
use App\Models\User;
use App\Models\UserPusat;
use Illuminate\Http\Request;

class UsertokopusatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Akun Toko Pusat';
        $data['rs_user'] = UserPusat::with(['toko_pusat', 'user'])->paginate(20);
        return view('akunPusat.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Tambah Akun Toko Pusat';
        $data['rs_users'] = User::whereNotIn('user_id', TokoPusatUser::pluck('user_id'))->get();
        $data['rs_toko_pusat'] = TokoPusat::all();
        // dd($data);
        return view('akunPusat.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'pusat_id' => 'required',
        ]);
        TokoPusatUser::create($validated);
        return redirect()->route('tambahUserPusat')->with('success', 'Data Berhasil disimpan');
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
        $detail = TokoPusatUser::find($id);
        if (empty($detail)) {
            return redirect()->route('userPusat')->with('error', 'Data tidak ditemukan');
        }
        $detail->delete();
        return redirect()->route('userPusat')->with('success', 'Data berhasil dihapus');
    }
}
