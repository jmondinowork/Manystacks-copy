<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\OauthToken;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        $user = User::with('entreprise')->find(Auth::id());

        // J'ai fais des commandes artisan parce que je me dit que peut-être plus tard on voudra les lancer en background
        if ($user->role == 'admin') {
            if ($user->entreprise && $user->entreprise->ion_id) {
                Artisan::call('licenses:getNew', ['userId' => $user->id]); // A voir si on le garde parce que ils ne sont pas censé acheter ailleurs que chez nous
                Artisan::call('ionCommandes:update', ['userId' => $user->id]);
                Artisan::call('licenses:update', ['userId' => $user->id]);
            }

            if (OauthToken::where('entreprise_id', $user->entreprise_id)->where('service_name', 'microsoft')->exists()) {
                Artisan::call('microsoft:users', ['userId' => $user->id]);
                Artisan::call('microsoft:devices', ['userId' => $user->id]);
            }

            if (OauthToken::where('entreprise_id', $user->entreprise_id)->where('service_name', 'google')->exists()) {
                Artisan::call('google:update', ['userId' => $user->id]);
                // Artisan::call('google:createUserAfterLicenceIsActive', ['userId' => $user->id]);
            }

            if (OauthToken::where('entreprise_id', $user->entreprise_id)->where('type', 'sirh')->exists()) {
                Artisan::call('sirh:update', ['userId' => $user->id]);
            }
        }

        if ($user->role == 'superadmin')
            return redirect(route('commandesAdmin'));
        else
            return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function getDataLoginUser(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::with('entreprise')->where('email', $validated['email'])->first();

        if ($user && Hash::check($validated['password'], $user->password)) {
            if (!$user->entreprise) {
                $data = [
                    'name'       => $user->name,
                    'ion_id'     => false,
                    'microsoft'  => false,
                    'google'     => false,
                    'sirh'       => false,
                ];
            } else {
                $oauthTokens = OauthToken::where('entreprise_id', $user->entreprise_id)->get();

                $data = [
                    'name'       => $user->name,
                    'ion_id'     => $user->entreprise->ion_id ? true : false,
                    'microsoft'  => $oauthTokens->where('service_name', 'microsoft')->isNotEmpty(),
                    'google'     => $oauthTokens->where('service_name', 'google')->isNotEmpty(),
                    'sirh'       => $oauthTokens->where('type', 'sirh')->isNotEmpty(),
                ];
            }

            return response()->json($data);
        }

        // Optionally, return an error response if the credentials don't match.
        return response()->json(['error' => 'Invalid credentials'], 401);
    }
}
