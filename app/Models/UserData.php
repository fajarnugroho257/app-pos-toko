<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;
    protected $table = 'users_data';
    protected $fillable = [
        'cabang_id',
        'user_id',
        'user_nama_lengkap',
        'user_alamat',
        'user_jk',
        'user_st',
        'user_image',
    ];
    public function users()
    {
        return $this->hasOne(User::class, 'user_id', 'user_id');
    }
    public function toko_cabang()
    {
        return $this->belongsTo(TokoCabang::class, 'cabang_id', 'id');
    }
}
