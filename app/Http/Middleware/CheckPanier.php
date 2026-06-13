<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Panier;
use App\Models\User;

class CheckPanier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::with('entreprise')->find(Auth::id());
        // Récupère le panier de l'utilisateur authentifié avec les produits associés
        $panier = Panier::with(['panierProducts.product'])
            ->where('user_id', Auth::id())
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->first();

        // Calcule le total du panier
        $total = 0;
        $count_products = 0;
        $count_licences = 0;
        if ($panier) {
            foreach ($panier->panierProducts as $panierProduct) {
                if ($panierProduct->product->categorie === 'licences')
                    $count_licences++;
                else
                    $count_products++;

                $total += $panierProduct->type_contrat === 'achat' ? $panierProduct->product->prix_achat * $panierProduct->quantity : $panierProduct->product->prix_location * $panierProduct->quantity;
            }
        }

        if ($count_licences > 0 && !$user->entreprise->ion_id) {
            return redirect('/panier')->with('error', "Avant de passer commande pour de la licence, nous devons établir une relation tripartite entre Manystacks, votre entreprise et notre partenaire.");
        }

        // Vérifie si le total est inférieur ou égal à 16
        if ($total <= 16 && $count_products > 0) {
            return redirect('/panier')->with('error', "Le montant total du panier doit être supérieur à 16 euros pour l'achat de matériel informatique.");
        }

        return $next($request);
    }
}
