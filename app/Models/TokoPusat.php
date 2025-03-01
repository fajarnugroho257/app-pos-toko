<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class TokoPusat extends Model
{
    use HasFactory, Sluggable;
    protected $table = 'toko_pusat';
    protected $fillable = ['pusat_nama', 'slug', 'pusat_pemilik', 'pusat_alamat'];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'pusat_nama',
                'onUpdate' => true,
            ]
        ];
    }
    // RELATION
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function toko_cabang()
    {
        return $this->hasMany(User::class, 'pusat_id', 'id');
    }

    public function barang_master()
    {
        return $this->hasMany(MasterBarang::class, 'pusat_id', 'id');
    }
    public function cart()
    {
        return $this->hasMany(Cart::class, 'pusat_id', 'id');
    }
    public function toko_pusat_user()
    {
        return $this->hasOne(TokoPusatUser::class, 'pusat_id', 'id');
    }
}
