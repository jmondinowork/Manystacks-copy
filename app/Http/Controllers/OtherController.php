<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OtherController extends Controller
{
    public function bienvenue()
    {
        User::find(Auth::id())->update(['bienvenue' => 0]);
    }
    public function account_confirmation(Request $request)
    {
        $this->emailService->sendEmail([
            'templateId' => $request->template_id,
            'to' => $request->to,
            'params' => $request->params
        ]);
    }

    public function checkMailUnique(Request $request)
    {
        return response()->json(['exists' => User::where('email', $request->email)->exists()]);
    }
}
