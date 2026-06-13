<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Aucun utilisateur ne correspond à cette adresse e-mail.'], 404);
        }

        $token = Password::getRepository()->create($user);
        $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($user->email));

        $this->emailService->sendEmail([
            'templateId' => 2,
            'to' => [['email' => $request->email]],
            'params' => [
                'cta' => $resetUrl,
                'destinataire' => $request->email,
            ]
        ]);

        return response()->json(['message' => 'Un email de réinitialisation de mot de passe vous a été envoyé.'], 200);
    }
}
