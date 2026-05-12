<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    protected $fillable = ['cart_id', 'pusat_id', 'cabang_id', 'user_id', 'cart_st'];

    protected $primaryKey = 'cart_id';

    protected $appends = ['total_cicilan'];

    // Tipe data primary key (string)
    protected $keyType = 'string';

    public function cart_data()
    {
        return $this->hasMany(CartData::class, 'cart_id', 'cart_id');
    }

    public function cart_draft()
    {
        return $this->hasOne(CartDraft::class, 'cart_id', 'cart_id');
    }

    public function transaksi_cart()
    {
        return $this->hasOne(Transaksi::class, 'cart_id', 'cart_id');
    }

    public function toko_pusat()
    {
        return $this->belongsTo(TokoPusat::class, 'pusat_id', 'id');
    }

    public function sum_cart_data()
    {
        $data = CartData::sum('cart_harga_beli');

        // dd($data);
        return $data;
    }

    public function getTotalCicilanAttribute()
    {
        if (! $this->cart_draft) {
            return 0;
        }

        return $this->cart_draft->tagihan_cicilan->sum('cicilan');
    }
}
