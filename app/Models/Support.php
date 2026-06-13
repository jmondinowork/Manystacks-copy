<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    public function messages()
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function equipement()
    {
        return $this->belongsTo(CommandeProduct::class, 'equipement_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
