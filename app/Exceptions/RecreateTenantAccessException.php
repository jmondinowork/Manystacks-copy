<?php

namespace App\Exceptions;

use Exception;

class RecreateTenantAccessException extends Exception
{
    protected $tenant;

    public function __construct($tenant = 'microsoft')
    {
        parent::__construct("Needs to recreate access token for {$tenant}", 403);
        $this->tenant = $tenant;
    }

    public function getTenant()
    {
        return $this->tenant;
    }
}
