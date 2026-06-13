<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntrepriseInformation extends Model
{
    use HasFactory;

    protected $table = 'entreprise_informations';
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'entreprise_id');
    }

    public function adresses()
    {
        return $this->hasMany(AdresseLivraison::class);
    }

    public function siblings()
    {
        return self::where('group_id', $this->group_id)
            ->where('id', '!=', $this->id)
            ->whereNotNull('group_id')
            ->get();
    }
}
