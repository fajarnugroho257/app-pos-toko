<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class TokoCabang extends Model
{
    use HasFactory, Sluggable;
    protected $table = 'toko_cabang';
    protected $fillable = ['pusat_id', 'cabang_nama', 'slug', 'cabang_alamat'];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'cabang_nama',
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
        return $this->hasMany(BarangCabang::class, 'cabang_id', 'id');
    }
    public function users_data()
    {
        return $this->hasMany(UserData::class, 'cabang_id', 'id');
    }
}
