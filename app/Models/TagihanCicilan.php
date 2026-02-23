<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanCicilan extends Model
{
    use HasFactory;
    protected $table = 'tagihan_cicilan';
    protected $fillable = ['cart_draft_id', 'cicilan', 'cicilan_date'];

    public function cart_draft()
    {
        return $this->belongsTo(CartDraft::class, 'cart_draft_id', 'id');
    }

}
