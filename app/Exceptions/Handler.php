<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Exceptions\RecreateTenantAccessException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof RecreateTenantAccessException) {
            // Perform your redirect when a tenant token needs re-creation
            return redirect()->route(
                'tenants.recreateTenantAccess',
                ['tenant' => $e->getTenant()]
            );
        }

        // For all other exceptions, use the default behavior:
        return parent::render($request, $e);
    }
}
