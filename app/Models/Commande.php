<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function commandeProducts()
    {
        return $this->hasMany(CommandeProduct::class, 'commande_id');
    }

    public function entreprise()
    {
        return $this->belongsTo(EntrepriseInformation::class, 'entreprise_id');
    }

    public function signataire()
    {
        return $this->belongsTo(Signataire::class, 'signataire_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
