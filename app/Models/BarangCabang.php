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
}
