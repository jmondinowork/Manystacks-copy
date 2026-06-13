<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signataire extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'representant_legal' => 'boolean',
    ];

    public function getFullNameAttribute()
    {
        return $this->prenom . '_' . $this->nom;
    }

    public function getIbanAttribute($value)
    {
        if (!$value)
            return null;
        return ['name' => 'Iban_' . $this->full_name, 'url' => $value];
    }

    public function getPieceIdentiteRectoAttribute($value)
    {
        if (!$value)
            return null;
        return ['name' => 'Piece_identite_recto_' . $this->full_name, 'url' => $value];
    }

    public function getPieceIdentiteVersoAttribute($value)
    {
        if (!$value)
            return null;
        return ['name' => 'Piece_identite_verso_' . $this->full_name, 'url' => $value];
    }

    public function getPouvoirAttribute($value)
    {
        if (!$value)
            return null;
        return ['name' => 'Pouvoir_' . $this->full_name, 'url' => $value];
    }
}
