<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($panier) {
            $panier->token = uniqid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function panierProducts()
    {
        return $this->hasMany(PanierProduct::class);
    }
}
