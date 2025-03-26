<?php

namespace App\Http\Controllers;
use App\Models\BarangCabang;
use App\Models\BarangMasterLog;
use App\Models\Cart;
use App\Models\CartData;
use App\Models\MasterBarang;
use App\Models\TokoCabang;
use App\Models\Transaksi;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data['title'] = 'Dashboard';
        $role_id = Auth::user()->role_id;
        // dd($role_id);
        if ($role_id != 'R0001') {
            // date
            // $startDate = Carbon::create($year, $month, 1)->toDateString();
            // $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
            //
            $user_id = Auth::user()->user_id;
            $data['title'] = 'Dashboard';
            // cari
            $startDateDash = session()->get('startDateDash');
            $endDateDash = session()->get('endDateDash');
            $cabang_id = session()->get('cabang_id');
            //
            $data['startDateDash'] = empty($startDateDash) ? date('Y-m-01') : $startDateDash;
            $data['endDateDash'] = empty($endDateDash) ? date('Y-m-d') : $endDateDash;
            $data['cabang_id'] = empty($cabang_id) ? 'all' : $cabang_id;
            //
            $res_cabang = $data['cabang_id'] == 'all' ? '%' : ($data['cabang_id'] == 'gudang' ? '%' : $data['cabang_id']);
            // echo $res_cabang;
            //
            $dataUser = User::with('toko_pusat_user')->where('user_id', $user_id)->first();
            // dd($dataUser);
            $pusat_id = $dataUser->toko_pusat_user->pusat_id;
            // daftar cabang
            $rs_cabang = TokoCabang::where('pusat_id', $pusat_id)->get();
            // dd($rs_cabang);
            $data['rs_cabang'] = $rs_cabang;
            // kurang stok
            $kurangStok = BarangCabang::with(['barang_master', 'toko_cabang'])
                ->whereRelation('barang_master', 'pusat_id', $pusat_id)
                ->where(
                    DB::raw('CONVERT(barang_stok, SIGNED)'),
                    '<',
                    function ($query) {
                        $query->select('barang_stok_minimal')
                            ->from('barang_master')
                            ->whereColumn('barang_master.id', 'barang_cabang.barang_id')
                            ->limit(1);
                    }
                )
                ->where('cabang_id', 'LIKE', $res_cabang);
            // jlh cabang
            $jlhCabang = TokoCabang::where('pusat_id', $pusat_id)->count();
            // data
            $transaksi_by_tanggal = $this->get_transaksi($pusat_id, $data['startDateDash'], $data['endDateDash'], $res_cabang);
            //
            $data['transaksi'] = $transaksi_by_tanggal->count();
            $data['transRupiah'] = $transaksi_by_tanggal->sum('trans_total');
            $data['kurangStok'] = $kurangStok->count();
            $data['rs_stok'] = $kurangStok->get();
            $data['jlhCabang'] = $jlhCabang;
            // stok minim digudang
            $kurangStokGudang = MasterBarang::where('pusat_id', $pusat_id)
                ->where(DB::raw('CONVERT(barang_master_stok, SIGNED)'), '<=', DB::raw('CONVERT(barang_stok_minimal, SIGNED)'));
            $data['rs_stok_gudang'] = $kurangStokGudang->get();
            $data['kurangStokGudang'] = $kurangStokGudang->count();
            // grafik
            $period = CarbonPeriod::create($data['startDateDash'], $data['endDateDash']);
            foreach ($period as $key => $date) {
                $tranMonth[$key]['pendapatan'] = (int) $this->get_transaksi_perday($pusat_id, $date->toDateString(), $res_cabang)->sum('trans_total');
                $tranMonth[$key]['jlh_transaksi'] = $this->get_transaksi_perday($pusat_id, $date->toDateString(), $res_cabang)->count();
                $tranMonth[$key]['tanggal'] = $date->toDateString();
            }
            // dd($tranMonth);
            $data['tranMonth'] = $tranMonth;
            if ($cabang_id == 'all' || empty($cabang_id)) {
                $rs_terbanyak = DB::select("SELECT m_barang.id, m_barang.barang_nama, 'All' AS 'cabang_nama', IFNULL(m_penjualan.penjualan, 0) AS 'cart_qty' FROM (
                        SELECT a.id, a.barang_nama FROM barang_master a WHERE a.pusat_id = ?
                    ) m_barang LEFT JOIN (
                        SELECT e.id, SUM(c.cart_qty) AS 'penjualan'
                        FROM cart b
                        INNER JOIN cart_data c ON b.cart_id = c.cart_id
                        INNER JOIN barang_cabang d ON c.barang_cabang_id = d.id
                        INNER JOIN barang_master e ON d.barang_id = e.id
                        INNER JOIN transaksi_cart f ON c.cart_id = f.cart_id
                        WHERE b.cart_st = 'yes'
                        AND b.pusat_id = ?
                        AND DATE(f.trans_date) >= ?
                        AND DATE(f.trans_date) <= ?
                        GROUP BY e.id
                    ) m_penjualan ON m_barang.id = m_penjualan.id
                    ORDER BY IFNULL(m_penjualan.penjualan, 0) DESC", [$pusat_id, $pusat_id, $data['startDateDash'], $data['endDateDash']]);
            } else {
                $rs_terbanyak = DB::select("SELECT m_barang.id, m_barang.barang_nama, m_barang.cabang_id, m_barang.cabang_nama, IFNULL(m_jual.jual, 0) AS 'cart_qty' FROM (
                    SELECT e.id, e.barang_id, e.barang_stok, e.cabang_id, f.barang_nama, g.cabang_nama
                    FROM barang_cabang e
                    INNER JOIN barang_master f ON e.barang_id = f.id
                    INNER JOIN toko_cabang g ON e.cabang_id = g.id
                    WHERE e.cabang_id = ?
                ) AS m_barang LEFT JOIN (
                    SELECT c.id, a.cabang_id, d.barang_nama, SUM(b.cart_qty) AS 'jual' FROM cart a
                    INNER JOIN cart_data b ON a.cart_id = b.cart_id
                    INNER JOIN barang_cabang c ON b.barang_cabang_id = c.id
                    INNER JOIN barang_master d ON c.barang_id = d.id
                    INNER JOIN transaksi_cart e ON b.cart_id = e.cart_id
                    WHERE a.cart_st = 'yes'
                    AND a.pusat_id = ? AND a.cabang_id = ?
                    AND DATE(e.trans_date) >= ?
                    AND DATE(e.trans_date) <= ?
                    GROUP BY c.id
                ) AS m_jual ON m_barang.id = m_jual.id
                ORDER BY IFNULL(m_jual.jual, 0) DESC", [$res_cabang, $pusat_id, $res_cabang, $data['startDateDash'], $data['endDateDash']]);
            }
            $data['rs_terbanyak'] = $rs_terbanyak;
            // dd($rs_terbanyak);
            // return
            return view('dashboard.dashboard', $data);
        }
        return view('dashboard.dashboard_dev', $data);
    }

    private function get_transaksi($pusat_id, $startDateDash, $endtDateDash, $res_cabang)
    {
        $transaksi = Transaksi::with('cart')
            ->whereRelation('cart', 'pusat_id', $pusat_id)
            ->whereBetween(DB::raw('DATE(trans_date)'), [$startDateDash, $endtDateDash])
            ->whereRelation('cart', 'cabang_id', 'LIKE', $res_cabang);
        return $transaksi;
    }
    private function get_transaksi_perday($pusat_id, $trans_date, $res_cabang)
    {
        $transaksi = Transaksi::with('cart')
            ->whereRelation('cart', 'pusat_id', $pusat_id)
            ->where(DB::raw('DATE(trans_date)'), $trans_date)
            ->whereRelation('cart', 'cabang_id', 'LIKE', $res_cabang);
        return $transaksi;
    }
    //
    public function show_barang_minim(string $cabang_id)
    {
        $cabang = TokoCabang::find($cabang_id);
        // dd($cabang);
        if (empty($cabang)) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('showBarangCabang', ['slug' => $cabang->slug]);
    }

    public function show_pendapatan(string $cabang_id)
    {
        $cabang = TokoCabang::find($cabang_id);
        // dd($cabang);
        if (empty($cabang)) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('transaksiCabang', ['slug' => $cabang->slug]);
    }

    public function show_transaksi(string $cabang_id)
    {
        $cabang = TokoCabang::find($cabang_id);
        // dd($cabang);
        if (empty($cabang)) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('transaksiCabang', ['slug' => $cabang->slug]);
    }

    public function search_summary(Request $request)
    {
        if ($request->aksi == 'reset') {
            session()->forget('startDateDash');
            session()->forget('endDateDash');
            session()->forget('cabang_id');
        } else {
            session([
                'startDateDash' => $request->startDateDash,
                'endDateDash' => $request->endDateDash,
                'cabang_id' => $request->cabang_id,
            ]);
        }
        return redirect()->route('dashboard');
    }
}
