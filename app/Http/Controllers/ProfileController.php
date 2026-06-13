<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Historique;
use App\Models\CommandeProduct;
use App\Models\EntrepriseInformation;
use App\Models\AdresseLivraison;
use App\Models\OauthToken;
use App\Models\Tag;

class ProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ROUTES MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function profile($section = null)
    {
        if (Auth::user()->role == 'collaborateur' && $section != 'mon-compte') {
            return redirect('/profile/mon-compte');
        }

        return Inertia::render('Profile/Edit', [
            'section' => $section,
            'entreprise' => EntrepriseInformation::where('id', Auth::user()->entreprise_id)->first(),
            'integrations' => config('integration'),
            'adresses' => AdresseLivraison::where('entreprise_id', Auth::user()->entreprise_id)->get(),
            'collaborateurs' => User::where('entreprise_id', Auth::user()->entreprise_id)->where('id', '!=', Auth::id())->where('type', 'Personne')->get(),
            'salles' => User::with('adresse')->where('entreprise_id', Auth::user()->entreprise_id)->where('type', 'Salle')->get(),
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | API MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function editAccount(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $profile_img_url = $request->profile_img ?? NULL;
        if ($request->hasFile('profile_img')) {
            $profile_img_name = Auth::user()->name . "/profile_img_" . Str::random(6) . $request->name;
            Storage::disk('s3')->put($profile_img_name, file_get_contents($request->file("profile_img")), 'public');
            $profile_img_url = Storage::disk('s3')->url($profile_img_name);
        }

        $user = User::with(['commandeProducts', 'tags'])->find($request->id);
        $name = $request->type == "Personne" ? $request->fname . " " . $request->lname : $request->name;

        $requestData = $request->all();
        $requestData['profile_img'] = $profile_img_url;
        $requestData['name'] = $name;
        $user->update($requestData);

        if ($request->has('tags')) {
            if ($request->tags === '') {
                $user->tags()->detach();
            } else {
                $user->tags()->sync($request->tags);
            }
            $user->load('tags');
        }

        return $user;
    }
    public function editPassword(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
        ]);

        Auth::user()->update(['password' => Hash::make($request->password)]);
    }
    public function editCompany(Request $request)
    {
        $entreprise_id = Auth::user()->entreprise_id;
        $validatedData = $request->validate([
            'siret' => "",
            'raison_sociale' => "",
            'adresse' => "",
            'complement_adresse' => "",
            'code_postal' => "",
            'ville' => "",
            'profile_img' => '',
            'pays' => ""
        ]);

        if ($request->hasFile('profile_img')) {
            $profile_img_name =  Auth::user()->name . "/profile_img_" . Str::random(6) . $request->raison_sociale;
            Storage::disk('s3')->put($profile_img_name, file_get_contents($request->file("profile_img")), 'public');
            $profile_img_url = Storage::disk('s3')->url($profile_img_name);
        } else $profile_img_url = $request->profile_img ?? NULL;
        $validatedData['profile_img'] = $profile_img_url;


        EntrepriseInformation::where('id', $entreprise_id)->update($validatedData);
        $default = AdresseLivraison::where('entreprise_id', $entreprise_id)->where('default', 1)->exists();
        AdresseLivraison::updateOrCreate(
            ['entreprise_id' => $entreprise_id, 'primary' => 1],
            [
                'entreprise_id' => $entreprise_id,
                'adresse' => $validatedData['adresse'],
                'titre' => "Siège social",
                'primary' => 1,
                'default' => !$default,
                'code_postal' => $validatedData['code_postal'],
                'ville' => $validatedData['ville'],
                'pays' => $validatedData['pays']
            ]
        );
        User::updateOrCreate(
            ['entreprise_id' => $entreprise_id, 'role' => 'entreprise'],
            [
                'entreprise_id' => $entreprise_id,
                'name' => $validatedData['raison_sociale'],
                'profile_img' => $profile_img_url,
                'role'  => "entreprise"
            ]
        );
    }
    public function setAdresseDefault(Request $request)
    {
        AdresseLivraison::where('entreprise_id', Auth::user()->entreprise_id)->update(['default' => 0]);
        AdresseLivraison::where('id', $request->adresse_id)->update(['default' => 1]);

        return AdresseLivraison::where('entreprise_id', Auth::user()->entreprise_id)->get();
    }
    public function deleteAdresse(Request $request)
    {
        AdresseLivraison::find($request->adresse_id)->delete();
    }
    public function editCollaborateur(Request $request)
    {
        if ($request->type == "Personne") {
            $validatedData = request()->validate([
                'id' => '',
                'equipement_id' => '',
                'email' => 'required',
                'tel' => '',
                'type' => 'required',
                'poste' => '',
            ]);

            $validatedData['name'] = $request->fname . " " . $request->lname;
            $validatedData['entreprise_id'] = Auth::user()->entreprise_id;
            $validatedData['role'] = "collaborateur";

        } else {
            $validatedData = request()->validate([
                'id' => '',
                'equipement_id' => '',
                'name' => 'required|string',
                'type' => 'required|string',
                'adresse_id' => '',
            ]);

            $validatedData['entreprise_id'] = Auth::user()->entreprise_id;
        }

        $user = User::updateOrCreate(['id' => $validatedData['id']], $validatedData);


        if ($request->has('tags')) {
            $user->tags()->sync($request->tags);
            $user->load('tags');
        }

        if ($request->from == "livraison")
            return  User::where('entreprise_id', Auth::user()->entreprise_id)->where('role', '!=', null)->get();
        else if ($request->from == "collaborateurs")
            return User::with('adresse')->where('entreprise_id', Auth::user()->entreprise_id)->where('id', '!=', Auth::id())->where('type', $request->type)->get();
        else if ($request->from == "attribution") {
            CommandeProduct::where('id', $validatedData['equipement_id'])->update(['user_attributed_id' => $user->id]);

            Historique::create([
                'title' => "Changement d'attribution",
                'description' => "Attribution de l'équipement à " . $user->name,
                'equipement_id' => $validatedData['equipement_id']
            ]);

            return [
                'equipement' => CommandeProduct::with(['userAttributed', 'userAttributed.tags'])->where('id', $validatedData['equipement_id'])->first(),
                'historiques' => Historique::where('equipement_id', $validatedData['equipement_id'])->orderBy('id', 'desc')->take(10)->get(),
            ];
        } else if ($request->from == "index") {
            return [
                'mes_attributions' => User::with(['commandeProducts', 'tags'])
                    ->where('entreprise_id', Auth::user()->entreprise_id)
                    ->where('type', $request->type)
                    ->get(),
                'tmpPassword' => $password ?? null
            ];
        }
    }
    public function deleteCollaborateur(Request $request)
    {
        User::find($request->collaborateur_id)->delete();
    }
    public function createTag(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
        ]);

        $tag = Tag::create([
            'name' => $data['name'],
            'color' => $data['color'],
            'entreprise_id' => Auth::user()->entreprise_id
        ]);

        return $tag;
    }
    public function deleteTag(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:users,id',
            'tag_id' => 'required|exists:tags,id',
        ]);

        $user = User::find($data['id']);
        $user->tags()->detach($data['tag_id']);

        return User::with(['commandeProducts', 'tags'])->where('id', request('id'))->where('entreprise_id', Auth::user()->entreprise_id)->where('type', '!=', '')->first();
    }
    public function addTagToUser(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:users,id',
            'tag_id' => 'required|exists:tags,id',
        ]);

        $user = User::find($data['id']);
        $user->tags()->attach($data['tag_id']);

        $userTagIds = $user->tags->pluck('id');
        $tags = Tag::whereNotIn('id', $userTagIds)->get();

        return [
            'attribution' => User::with(['commandeProducts', 'tags'])->where('id', request('id'))->where('entreprise_id', Auth::user()->entreprise_id)->where('type', '!=', '')->first(),
            'tags' => $tags
        ];
    }
    public function removeTagFromUser(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:users,id',
            'tag_id' => 'required|exists:tags,id',
        ]);

        $user = User::find($data['id']);
        $user->tags()->detach($data['tag_id']);

        $userTagIds = $user->tags->pluck('id');
        $tags = Tag::whereNotIn('id', $userTagIds)->get();

        return [
            'attribution' => User::with(['commandeProducts', 'tags'])->where('id', request('id'))->where('entreprise_id', Auth::user()->entreprise_id)->where('type', '!=', '')->first(),
            'tags' => $tags
        ];
    }
}
