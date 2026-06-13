<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingCommande extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function adresseLivraison()
    {
        return $this->belongsTo(AdresseLivraison::class, 'adresse_livraison_id');
    }

    public function getAdresseLivraisonAttribute()
    {
        return $this->adresseLivraison()->first()->adresse;
    }
}
