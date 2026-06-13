<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\SIRHService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\CommandeProduct;

class UpdateSirhUsers extends Command
{
    protected $signature = 'sirh:update {userId}';
    protected $description = 'Update SIRH users';
    protected $SIRHService;


    public function handle()
    {
        $userAuth = User::find($this->argument('userId'));

        $sirhIntegrations = config('integration.sirh');

        foreach ($sirhIntegrations as $sirhIntegration) {
            $this->SIRHService = new SIRHService($sirhIntegration['name']);

            if ($auth = $this->SIRHService->getAccessToken($userAuth->entreprise_id)) {
                $access_token = $auth['access_token'];
                $sirhCompanyId = $auth['company_id'];

                if ($sirhIntegration['method'] === 'oauth')
                    $employees = $this->SIRHService->getEmployees($access_token, $sirhCompanyId);
                else
                    $employees = $this->SIRHService->getEmployeesNoOauth($access_token, $sirhCompanyId);

                $this->createEmployees($employees, $sirhIntegration['name']);
            }
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

            // Update the user's equipment if it was attributed to him previously
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
