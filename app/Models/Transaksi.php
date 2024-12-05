<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi_cart';
    protected $fillable = ['cart_id', 'user_id', 'trans_pelanggan', 'trans_total', 'trans_bayar', 'trans_kembalian', 'trans_date'];
}
