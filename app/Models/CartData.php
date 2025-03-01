<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartData extends Model
{
    use HasFactory;
    protected $table = 'cart_data';
    protected $fillable = ['cart_id', 'barang_cabang_id', 'cart_barcode', 'cart_harga_beli', 'cart_harga_jual', 'cart_nama', 'cart_qty', 'cart_subtotal', 'cart_urut', 'cart_diskon'];
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'cart_id', 'cart_id');
    }
    public function cart()
    {
        return $this->belongsTo(cart::class, 'cart_id', 'cart_id');
    }
    public function barang_cabang()
    {
        return $this->belongsTo(BarangCabang::class, 'barang_cabang_id', 'id');
    }
}
