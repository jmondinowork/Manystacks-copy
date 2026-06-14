<?php

namespace Tests\Feature;

use App\Models\Commande;
use App\Models\CommandeProduct;
use App\Models\EntrepriseInformation;
use App\Models\Support;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_the_company_overview(): void
    {
        $entreprise = EntrepriseInformation::create([
            'raison_sociale' => 'Test Company',
            'siret' => '12345678900012',
        ]);

        $user = User::factory()->create([
            'entreprise_id' => $entreprise->id,
            'type' => 'Personne',
        ]);

        User::factory()->create([
            'entreprise_id' => $entreprise->id,
            'type' => 'Personne',
        ]);

        $commande = Commande::create([
            'entreprise_id' => $entreprise->id,
            'reference_commande' => 'CMD-100',
            'financeur' => 'location',
        ]);

        CommandeProduct::create([
            'entreprise_id' => $entreprise->id,
            'commande_id' => $commande->id,
            'name' => 'Test Licence',
            'prix' => 15.00,
            'categorie' => 'licences',
            'type_licence' => 'Mensuel',
            'status' => 'active',
            'slug' => 'test-licence',
        ]);

        $support = new Support();
        $support->entreprise_id = $entreprise->id;
        $support->user_id = $user->id;
        $support->numero_support = 'SUP-999';
        $support->object = 'Test Support Ticket';
        $support->status = 'En cours';
        $support->save();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Index')
            ->where('count.collaborateurs', 2)
            ->where('count.licences', 1)
            ->where('count.prix_licences_month', 15)
            ->has('commandes', 1)
            ->has('supports', 1)
            ->has('licencesMonth', 1)
            ->has('entreprise')
        );
    }
}
