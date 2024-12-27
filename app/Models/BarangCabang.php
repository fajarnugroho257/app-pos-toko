<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangCabang extends Model
{
    use HasFactory;
    protected $table = 'barang_cabang';
    protected $fillable = ['barang_id', 'cabang_id', 'barang_stok', 'cabang_barang_harga'];
    public function barang_master()
    {
        return $this->belongsTo(MasterBarang::class, 'barang_id', 'id');
    }
    public function toko_cabang()
    {
        return $this->belongsTo(TokoCabang::class, 'cabang_id', 'id');
    }
    public function barang_cabang_log()
    {
        return $this->hasMany(BarangLog::class, 'barang_cabang_id', 'id');
    }
    public function cart_data()
    {
        return $this->hasMany(CartData::class, 'barang_cabang_id', 'id');
    }
}
