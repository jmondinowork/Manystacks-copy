<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OauthToken extends Model
{
    use HasFactory;

    protected $table = 'oauth_tokens';

    protected $fillable = [
        'service_name',
        'access_token',
        'refresh_token',
        'access_token_expires_at',
        'refresh_token_expires_at',
        'entreprise_id',
        'company_id',
        'type',
    ];

    protected $casts = [
        'access_token_expires_at' => 'datetime',
        'refresh_token_expires_at' => 'datetime',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    public $timestamps = true;

    public function isAccessTokenExpired()
    {
        return $this->access_token_expires_at && $this->access_token_expires_at->isPast();
    }

    public function isRefreshTokenExpired()
    {
        return $this->refresh_token_expires_at && $this->refresh_token_expires_at->isPast();
    }
}
