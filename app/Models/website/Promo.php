<?php

namespace App\Models\website;

use App\Models\MasterBarang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;
    protected $table = 'promo';
    protected $fillable = ['barang_id', 'pusat_id', 'promo_start', 'promo_end', 'promo_st', 'promo_harga', 'promo_pembelian', 'promo_grosir_harga', 'promo_grosir_pembelian'];
    
    public function barang_master()
    {
        return $this->hasOne(MasterBarang::class, 'id', 'barang_id');
    }

}
