<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CommandeProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function userLivraison()
    {
        return $this->belongsTo(User::class, 'user_livraison_id');
    }

    public function userAttributed()
    {
        return $this->belongsTo(User::class, 'user_attributed_id');
    }

    public function entreprise()
    {
        return $this->belongsTo(EntrepriseInformation::class, 'entreprise_id');
    }

    public function adresseLivraison()
    {
        return $this->belongsTo(AdresseLivraison::class, 'adresse_livraison_id');
    }

    public function setDateDebutLicenceAttribute($value)
    {
        $this->attributes['date_debut_licence'] = Carbon::parse($value)->toDateTimeString();
    }

    public function setDateFinLicenceAttribute($value)
    {
        $this->attributes['date_fin_licence'] = Carbon::parse($value)->toDateTimeString();
    }
}
