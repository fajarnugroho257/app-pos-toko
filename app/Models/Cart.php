<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'cart';
    protected $fillable = ['cart_id', 'pusat_id', 'cabang_id', 'cart_st'];
    protected $primaryKey = 'cart_id';
    // Tipe data primary key (string)
    protected $keyType = 'string';
    public function cart_data()
    {
        return $this->hasMany(CartData::class, 'cart_id', 'cart_id');
    }
}