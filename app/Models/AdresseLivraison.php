<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdresseLivraison extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function entreprise()
    {
        return $this->belongsTo(EntrepriseInformation::class);
    }

    public function user()
    {
        return $this->hasMany(User::class, 'adresse_id');
    }
}
