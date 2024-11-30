<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class MasterBarang extends Model
{
    use HasFactory, Sluggable;
    protected $table = 'barang_master';
    protected $fillable = ['pusat_id', 'barang_barcode', 'slug', 'barang_nama', 'barang_stok_minimal', 'barang_harga_beli', 'barang_harga_jual'];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'barang_nama',
                'onUpdate' => true,
            ]
        ];
    }
    // RELATION
    public function toko_pusat()
    {
        return $this->belongsTo(TokoPusat::class, 'pusat_id', 'id');
    }
    public function barang_cabang()
    {
        return $this->hasMany(BarangCabang::class, 'barang_id', 'id');
    }
}
