<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokoPusatUser extends Model
{
    use HasFactory;
    protected $table = 'toko_pusat_user';
    protected $fillable = ['pusat_id', 'user_id'];
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function toko_pusat()
    {
        return $this->belongsTo(TokoPusat::class, 'pusat_id', 'id');
    }
}
