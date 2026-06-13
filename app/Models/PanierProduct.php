<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanierProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function panier()
    {
        return $this->belongsTo(Panier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
