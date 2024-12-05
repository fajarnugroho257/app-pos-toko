<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartData extends Model
{
    use HasFactory;
    protected $table = 'cart_data';
    protected $fillable = ['cart_id', 'barang_cabang_id', 'cart_barcode', 'cart_harga_jual', 'cart_nama', 'cart_qty', 'cart_subtotal', 'cart_urut'];
}
