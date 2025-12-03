<?php

namespace App\Models\website;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $table = 'web_banner';
    protected $fillable = ['pusat_id', 'banner_path', 'banner_name', 'banner_ket', 'banner_urut'];
}
