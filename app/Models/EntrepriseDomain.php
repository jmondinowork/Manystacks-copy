<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntrepriseDomain extends Model
{
    use HasFactory;

    protected $fillable = ['domain', 'tenant', 'entreprise_id'];
}
