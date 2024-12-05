<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangLog extends Model
{
    use HasFactory;
    protected $table = 'barang_cabang_log';
    protected $fillable = [
        'user_id',
        'pusat_id',
        'cabang_id',
        'barang_cabang_id',
        'barang_awal',
        'barang_perubahan',
        'barang_transaksi',
        'barang_transaksi_id',
        'barang_akhir',
        'barang_st',
    ];
    public function toko_pusat()
    {
        return $this->belongsTo(TokoPusat::class, 'pusat_id', 'id');
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function barang_cabang()
    {
        return $this->belongsTo(BarangCabang::class, 'barang_cabang_id', 'id');
    }
}
