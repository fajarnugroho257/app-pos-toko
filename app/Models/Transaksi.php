<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi_cart';
    protected $fillable = ['cart_id', 'user_id', 'trans_pelanggan', 'trans_total', 'trans_bayar', 'trans_kembalian', 'trans_date'];
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
