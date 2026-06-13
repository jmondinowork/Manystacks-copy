<?php

namespace App\Http\Controllers;

use App\Models\OauthToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CommandeProduct;
use Carbon\Carbon;
use App\Providers\RouteServiceProvider;
use App\Services\SIRHService;
use Inertia\Inertia;

class SIRHController extends Controller
{
    public function redirect(Request $request, $sirhName)
    {
        $this->initializeSIRHService($sirhName);

        session(['redirect_to' => $request->input('redirect_to')]);
        return redirect($this->SIRHService->getAuthorizationUrl());
    }

    public function callback(Request $request, $sirhName)
    {
        $redirect_to = session('redirect_to');
        session()->forget('redirect_to');

        try {
            $this->initializeSIRHService($sirhName);

            $oauth = $this->SIRHService->initializeAccessToken($request->input('code'));
            $access_token = $oauth['access_token'];
            $sirhCompanyId = $oauth['company_id'];

            $employees = $this->SIRHService->getEmployees($access_token, $sirhCompanyId);

            $this->createEmployees($employees, $sirhName);

            return redirect($redirect_to)->with('success', 'L\'intégration a réussi, vos collaborateurs ont été ajoutés avec succès');
        } catch (\Throwable $th) {
            return redirect($redirect_to)->with('error', 'L\'intégration a échoué, veuillez bien vérifier les informations saisies');
        }
    }

    public function store_integrationshit(Request $request)
    {
        try {
            $access_token = $request->apikey;
            $sirhCompanyId = $request->domain;
            $sirhName = $request->name;

            $this->initializeSIRHService($sirhName);
            $employees = $this->SIRHService->getEmployeesNoOauth($access_token, $sirhCompanyId);

            $this->createEmployees($employees, $sirhName);

            OauthToken::create([
                'service_name' => $sirhName,
                'company_id' => $sirhCompanyId,
                'access_token' => $access_token,
                'entreprise_id' => Auth::user()->entreprise_id,
            ]);

            return response()->json(['message' => 'L\'intégration a réussi, vos collaborateurs ont été ajoutés avec succès'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'L\'intégration a échoué, veuillez bien vérifier les informations saisies'], 500);
        }
    }

    public function initEmployee($sirhName, $employee)
    {
        $domainName = explode('@', Auth::user()->email)[1];

        $newEmployee = [
            'sirh_user_id' => $employee['id'],
            'type' => 'Personne',
            'sirh_name' => $sirhName,
            'entreprise_id' => Auth::user()->entreprise_id,
        ];
        switch ($sirhName) {
            case 'payfit':
                $emailPerso = null;
                $emailPro = null;
                foreach ($employee['emails'] as $mail) {
                    if ($domainName === explode('@', $mail['email'])[1])
                        $emailPro = $mail['email'];
                    else
                        $emailPerso = $mail['email'];
                }
                $lastName = $employee['birthName'] ?? $employee['lastName'];
                $name = $employee['firstName'] . ' ' . $lastName;

                $newEmployee += [
                    'name' => $name,
                    'email' => $emailPro,
                    'email_perso' => $emailPerso,
                    'tel' => $employee['phoneNumbers'] ? $employee['phoneNumbers'][0]['phoneNumber'] : null,
                    'date_arrivee' => $employee['contracts'] ? Carbon::parse($employee['contracts'][0]['startDate'])->translatedFormat('j F Y') : null,
                    'date_sortie' =>  $employee['contracts'] ? Carbon::parse($employee['contracts'][0]['endDate'])->translatedFormat('j F Y') : null,
                ];
                break;

            case 'lucca':
                $emailPerso = null;
                $emailPro = null;
                if ($domainName === explode('@', $employee['mail']))
                    $emailPro = $employee['mail'];
                else
                    $emailPerso = $employee['mail'];

                $newEmployee += [
                    'name' => $employee['firstName'] . ' ' . $employee['lastName'],
                    'email' => $emailPro,
                    'email_perso' => $emailPerso,
                    'tel' => trim($employee['directLine']),
                    'date_arrivee' => $employee['dtContractStart'] ? Carbon::parse($employee['dtContractStart'])->translatedFormat('j F Y') : null,
                    'date_sortie' =>  $employee['dtContractEnd'] ? Carbon::parse($employee['dtContractEnd'])->translatedFormat('j F Y') : null,
                ];
                break;
        }

        return $newEmployee;
    }

    public function createEmployees($employees, $sirhName)
    {
        $entreprise_id = Auth::user()->entreprise_id;

        foreach ($employees as $employee) {
            $user = null;
            $newEmployee = $this->initEmployee($sirhName, $employee);

            // Check if the user already exists
            if ($newEmployee['email'])
                $user = User::where('email', $newEmployee['email'])->where('entreprise_id', $entreprise_id)->first();
            if (!$user && $newEmployee['email_perso'])
                $user = User::where('email_perso', $newEmployee['email_perso'])->where('entreprise_id', $entreprise_id)->first();
            if (!$user && $newEmployee['tel']) {
                $user = User::where('tel', $newEmployee['tel'])->where('entreprise_id', $entreprise_id)->first();
            }
            if (!$user && $newEmployee['name']) {
                $user = User::whereRaw('LOWER(name) = ?', [$newEmployee['name']])->where('entreprise_id', $entreprise_id)->first();
            }

            // Update the user if it exists, else create it
            if ($user) {
                $filteredEmployee = array_filter($newEmployee, function ($value) {
                    return !is_null($value);
                });

                $user->update($filteredEmployee);
            } else {
                $newEmployee['role'] = 'collaborateur';

                $user = User::create($newEmployee);
            }

            // Update the user's equipment if it was attributed to him
            $equipements = null;
            if ($user->email)
                $equipements = CommandeProduct::where('attribution_waiting', $user->email)->where('entreprise_id', $user->entreprise_id)->get();
            else if ($user->email_perso)
                $equipements = CommandeProduct::where('attribution_waiting', $user->email_perso)->where('entreprise_id', $user->entreprise_id)->get();

            if ($equipements) {
                foreach ($equipements as $equipement) {
                    $equipement->update(['user_attributed_id' => $user->id, 'attribution_waiting' => null]);
                }
            }
        }
    }
}
