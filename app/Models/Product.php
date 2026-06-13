<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $appends = ['image_principale'];
    protected $hidden = ['pivot'];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function getImagePrincipaleAttribute()
    {
        $imagePrincipale = $this->images->firstWhere('principale', true);

        return $imagePrincipale ? $imagePrincipale->image_url : null;
    }

    public function panier()
    {
        return $this->hasMany(Panier::class);
    }

    public function commande()
    {
        return $this->hasMany(Commande::class);
    }

    public function pendingCommande()
    {
        return $this->hasMany(PendingCommande::class);
    }

    public function favoris()
    {
        return $this->hasMany(Favoris::class, 'product_slug', 'slug');
    }

    public function stacks()
    {
        return $this->belongsToMany(Stack::class);
    }
}
