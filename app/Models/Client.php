<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table = 'client_auth';
    protected $fillable = ['cabang_id', 'user_id', 'token_date', 'token_value', 'token_usage', 'token_active'];
    // 
    public function cabang()
    {
        return $this->belongsTo(TokoCabang::class, 'cabang_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
