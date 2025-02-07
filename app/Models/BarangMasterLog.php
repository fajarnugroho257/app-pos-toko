<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasterLog extends Model
{
    use HasFactory;
    protected $table = 'barang_master_log';
    protected $fillable = [
        'user_id',
        'pusat_id',
        'barang_master_id',
        'barang_master_awal',
        'barang_master_perubahan',
        'barang_master_akhir',
        'barang_st',
    ];
}
