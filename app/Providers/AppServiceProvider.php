<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\OauthToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'userAuth' => function () {
                $user = Auth::user();
                $userAuth = $user ? $user->only('id', 'name', 'email', 'profile_img', 'tel', 'poste', 'role', 'entreprise_id', 'sirh_name') : null;

                if ($userAuth) {
                    $userAuth['oauth'] = OauthToken::where('entreprise_id', $userAuth['entreprise_id'])->get()->select('service_name')->pluck('service_name')->toArray();
                    $userAuth['entreprise'] = $user->entreprise;
                }

                return $userAuth;
            }
        ]);

        // if (app()->environment('local')) {
        //     DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        // }
    }
}
