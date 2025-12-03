<?php

namespace App\Models\website;

use App\Models\MasterBarang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBarang extends Model
{
    use HasFactory;
    protected $table = 'detail_barang';
    protected $fillable = ['barang_id', 'pusat_id', 'detail_image_name', 'detail_image_path', 'detail_st'];
    
    public function barang_master()
    {
        return $this->hasOne(MasterBarang::class, 'id', 'barang_id');
    }
}
