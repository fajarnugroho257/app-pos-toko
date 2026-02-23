<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturHistory extends Model
{
    use HasFactory;
    protected $table = 'retur_history';
    protected $fillable = ['user_id', 'cart_id', 'barang_cabang_id', 'retur_qty', 'retur_harga'];

    public function barang_cabang()
    {
        return $this->belongsTo(BarangCabang::class, 'barang_cabang_id', 'id');
    }
}
