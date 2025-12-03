<?php

namespace App\Models\website;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Why extends Model
{
    use HasFactory;
    protected $table = 'why_choose';
    protected $fillable = ['pusat_id', 'title', 'desc'];
}
