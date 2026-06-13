<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'tel',
        'password',
        'profile_img',
        'role',
        'entreprise_id',
        'adresse_id',
        'bienvenue',
        'type',
        'poste',
        'date_arrivee',
        'date_sortie',
        'tenant_name',
        'tenant_company_id',
        'tenant_user_id',
        'sirh_name',
        'sirh_company_id',
        'sirh_user_id',
        'email_perso',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'email_verified_at',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function panier()
    {
        return $this->hasMany(Panier::class);
    }

    public function favoris()
    {
        return $this->hasMany(Favoris::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(EntrepriseInformation::class, 'entreprise_id');
    }

    public function getRaisonSocialeAttribute()
    {
        return $this->entreprise->raison_sociale;
    }

    public function adresse()
    {
        return $this->belongsTo(AdresseLivraison::class, 'adresse_id');
    }

    public function commandeProducts()
    {
        return $this->hasMany(CommandeProduct::class, 'user_attributed_id')
            ->where('status', '!=', 'pending');
    }

    public function signataire()
    {
        return $this->hasMany(Signataire::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
