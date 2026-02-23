<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartDraft extends Model
{
    use HasFactory;
    protected $table = 'cart_draft';
    protected $fillable = ['cart_id', 'draft_uang_muka', 'draft_uang_sisa', 'draft_uang_tagihan', 'draft_start', 'draft_end', 'draft_note', 'draft_pelanggan', 'draft_st'];

    public function cart()
    {
        return $this->hasOne(cart::class, 'cart_id', 'cart_id');
    }

    public function tagihan_cicilan()
    {
        return $this->hasMany(TagihanCicilan::class, 'cart_draft_id', 'id');
    }
}
