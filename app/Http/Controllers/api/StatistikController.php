<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\CartData;
use App\Models\CartDraft;
use App\Models\TagihanCicilan;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            $dataUser = User::with('users_data.toko_cabang.toko_pusat')
                ->where('user_id', Auth::user()->user_id)
                ->where('role_id', 'R0005')
                ->first();
        } else {
            return response()->json([
                'success' => false,
                'errors' => 'user belum melakukan login',
            ], 422);
        }
        $cabang_id = $dataUser->users_data->cabang_id;
        //
        $start = $request->start;
        $end = $request->end;
        $transaksi_by_range_tanggal = $this->get_transaksi($dataUser->users_data->cabang_id, $start, $end);
        // loop
        $ttlTransaksi = 0;
        $ttlPihutang = 0;
        $ttlCicilan = 0;
        $ttlLaba = 0;
        $ttlBeli = 0;
        //
        foreach ($transaksi_by_range_tanggal->get() as $key => $value) {
            if ($value->cart->cart_st == 'yes') {
                $ttlTransaksi += $value->trans_total;
            } elseif ($value->cart->cart_st == 'hutang') {
                $draft = CartDraft::where('cart_id', $value->cart_id)->first();
                $ttlTransaksi += $draft->draft_uang_muka;
                $ttlPihutang += $draft->draft_uang_sisa;
                //
                $ttlCicilan += TagihanCicilan::where('cart_draft_id', $draft->id)->sum('cicilan');
            }
            foreach ($value->cart->cart_data as $cartData) {
                $hargaBeli = $cartData->cart_harga_beli;
                $qty = $cartData->cart_qty;
                $hargaJual = $cartData->cart_harga_jual;
                //
                $jual = $hargaJual * $qty;
                $beli = $hargaBeli * $qty;
                //
                $laba = $jual - $beli;
                // total
                $ttlLaba += $laba;
                $ttlBeli += $beli;
            }
        }
        //
        $data['transaksi'] = $transaksi_by_range_tanggal->count();
        $data['transRupiah'] = $ttlTransaksi;
        $data['ttlPihutang'] = $ttlPihutang;
        $data['ttlCicilan'] = $ttlCicilan;
        $data['ttlPendapatanKotor'] = $transaksi_by_range_tanggal->sum('trans_total');
        $data['ttlLaba'] = $ttlLaba;
        $data['ttlBeli'] = $ttlBeli;
        //
        $data['resPiutang'] = $ttlPihutang - $ttlCicilan;
        $data['resTransRupiah'] = $ttlTransaksi + $ttlCicilan;
        //
        $period = CarbonPeriod::create($start, $end);
        foreach ($period as $key => $date) {
            $tranMonth[$key]['pendapatan'] = (int) $this->get_transaksi_perday($cabang_id, $date->toDateString())->sum('trans_total');
            $tranMonth[$key]['jlh_transaksi'] = $this->get_transaksi_perday($cabang_id, $date->toDateString())->count();
            $tranMonth[$key]['tanggal'] = $date->toDateString();
            $tranMonth[$key]['laba'] = $this->get_laba_perday($cabang_id, $date->toDateString());
        }

        // return
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data',
            'data' => $data,
            'tranMonth' => $tranMonth,
        ]);
    }

    private function get_transaksi($cabang_id, $startDateDash, $endtDateDash)
    {
        $transaksi = Transaksi::with('cart.cart_data')
            ->whereRelation('cart', 'cabang_id', $cabang_id)
            ->whereHas('cart', function ($q) {
                $q->whereIn('cart_st', ['yes', 'hutang']);
            })
            ->whereBetween(DB::raw('DATE(trans_date)'), [$startDateDash, $endtDateDash]);

        return $transaksi;
    }

    private function get_transaksi_perday($cabang_id, $trans_date)
    {
        $transaksi = Transaksi::with('cart')
            ->whereRelation('cart', 'cabang_id', $cabang_id)
            ->whereHas('cart', function ($q) {
                $q->whereIn('cart_st', ['yes', 'hutang']);
            })
            ->where(DB::raw('DATE(trans_date)'), $trans_date);

        return $transaksi;
    }

    private function get_laba_perday($cabang_id, $trans_date)
    {
        $transaksi = Transaksi::whereRelation('cart', 'cabang_id', $cabang_id)
            ->whereHas('cart', function ($q) {
                $q->whereIn('cart_st', ['yes', 'hutang']);
            })
            ->where(DB::raw('DATE(trans_date)'), $trans_date)->get();
        $ttlLaba = 0;
        foreach ($transaksi as $trans) {
            $rsCartData = CartData::where('cart_id', $trans->cart_id)->get();
            foreach ($rsCartData as $CartData) {
                $beli = $CartData->cart_harga_beli;
                $jual = $CartData->cart_harga_jual;
                $qty = $CartData->cart_qty;
                //
                $laba = ($jual * $qty) - ($beli * $qty);
                $ttlLaba += $laba;
            }
        }

        return $ttlLaba;
        // return $transaksi->get()
    }
}
