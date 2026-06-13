<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favoris extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_slug'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_slug', 'slug');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
