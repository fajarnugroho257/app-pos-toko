<?php

namespace App\Models;

use App\Models\website\DetailBarang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class MasterBarang extends Model
{
    use HasFactory, Sluggable;
    protected $table = 'barang_master';
    protected $fillable = [
        'pusat_id',
        'barang_barcode',
        'slug',
        'barang_nama',
        'barang_stok_minimal',
        'barang_harga_beli',
        'barang_harga_jual',
        'barang_master_stok',
        'barang_master_stok_hasil',
        'barang_stok_perubahan',
        'barang_grosir_harga_jual',
        'barang_grosir_keuntungan',
        'barang_grosir_persentase',
        'barang_grosir_pembelian',
        'barang_keuntungan',
        'barang_persentase'
    ];
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
    public function barang_master_log()
    {
        return $this->hasMany(BarangMasterLog::class, 'barang_master_id', 'id');
    }
    public function detail_barang()
    {
        return $this->hasOne(DetailBarang::class, 'barang_id', 'id');
    }
}
