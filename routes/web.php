<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\StacksController;
use App\Http\Controllers\PasserCommandeController;
use App\Http\Controllers\MesCommandesController;
use App\Http\Controllers\MesEquipementsController;
use App\Http\Controllers\OtherController;
use App\Http\Controllers\MesAttributionsController;
use App\Http\Controllers\MesContratsController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\CommunController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\MicrosoftController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\MesLicencesController;
use App\Http\Controllers\SIRHController;
use App\Http\Controllers\TenantsController;

use App\Http\Controllers\Admin\CommandesController;
use App\Http\Controllers\Admin\UsersController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('auth')->group(function () {
    if (env('APP_ENV') === 'local') {
        Route::get('/test', [TestController::class, 'index'])->name('test');
    }
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('checkCollaborateur')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::post('/api/searchDashboard', [DashboardController::class, 'searchDashboard']);
        Route::post('/api/switchEntreprise', [DashboardController::class, 'switchEntreprise'])->name('switchEntreprise');
    });
    /*
    |--------------------------------------------------------------------------
    | PROFILE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/profile/{section}', [ProfileController::class, 'profile'])->name('profile');

    Route::post('/api/createTag', [ProfileController::class, 'createTag'])->name('profile.createTag');
    Route::post('/api/deleteTag', [ProfileController::class, 'deleteTag'])->name('profile.deleteTag');
    Route::post('/api/addTagToUser', [ProfileController::class, 'addTagToUser'])->name('profile.addTagToUser');
    Route::post('/api/removeTagFromUser', [ProfileController::class, 'removeTagFromUser'])->name('profile.removeTagFromUser');
    Route::post('/api/editAccount', [ProfileController::class, 'editAccount'])->name('profile.editAccount');
    Route::post('/api/editPassword', [ProfileController::class, 'editPassword'])->name('profile.editPassword');
    Route::post('/api/editCompany', [ProfileController::class, 'editCompany'])->name('profile.editCompany');
    Route::post('/api/editAdresses', [ProfileController::class, 'editAdresses'])->name('profile.editAdresses');
    Route::post('/api/setAdresseDefault', [ProfileController::class, 'setAdresseDefault'])->name('profile.setAdresseDefault');
    Route::post('/api/deleteAdresse', [ProfileController::class, 'deleteAdresse'])->name('profile.deleteAdresse');
    Route::post('/api/editCollaborateur', [ProfileController::class, 'editCollaborateur'])->name('profile.editCollaborateur');
    Route::post('/api/deleteCollaborateur', [ProfileController::class, 'deleteCollaborateur'])->name('profile.deleteCollaborateur');
    /*
    |--------------------------------------------------------------------------
    | LE CATALOGUE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/catalogue', [CatalogueController::class, 'index'])->name('catalogue');
    Route::get('/catalogue/{categorie}', [CatalogueController::class, 'categorie']);
    Route::get('/catalogue/{categorie}/{souscategorie}', [CatalogueController::class, 'souscategorie'])->name('souscategorie');
    Route::get('/catalogue/{categorie}/{souscategorie}/{slug}', [CatalogueController::class, 'product']);

    Route::post('/api/searchProducts', [CatalogueController::class, 'searchProducts']);
    /*
    |--------------------------------------------------------------------------
    | MES STACKS ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/mes-stacks', [StacksController::class, 'index'])->name('mes-stacks');
    Route::get('/mes-stacks/{slug}', [StacksController::class, 'stack']);

    Route::post('/api/addToStack', [StacksController::class, 'addToStack']);
    Route::post('/api/createStack', [StacksController::class, 'createStack']);
    Route::post('/api/deleteStack', [StacksController::class, 'deleteStack'])->name('deleteStack');
    Route::post('/api/removeProductFromStack', [StacksController::class, 'removeProductFromStack']);
    /*
    |--------------------------------------------------------------------------
    | GESTION LICENCES ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkadminaccess'])->group(function () {
        Route::get('/mes-licences', [MesLicencesController::class, 'index'])->name('mes-licences');
        Route::get('/mes-licences/{slug}', [MesLicencesController::class, 'licence'])->name('mes-licences.licence');

        // Route::post('/api/cancelOrder', [MesLicencesController::class, 'cancelOrder'])->name('cancel-order');
    });

    Route::post('/api/searchLicences', [MesLicencesController::class, 'searchLicences']);
    Route::post('/api/assignLicence', [MesLicencesController::class, 'assignLicence']);
    Route::post('/api/unassignLicence', [MesLicencesController::class, 'unassignLicence']);
    /*
    |--------------------------------------------------------------------------
    | MON CATALOGUE ROUTES
    |--------------------------------------------------------------------------
    */
    // Route::get('/mon-catalogue/{categorie?}/{souscategorie?}', [FavorisController::class, 'index'])->name('mon-catalogue');

    // Route::post('/api/toggleFavoris', [FavorisController::class, 'toggleFavoris']);
    // Route::post('/api/searchFavoris', [FavorisController::class, 'searchFavoris']);
    /*
    |--------------------------------------------------------------------------
    | PANIER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/panier', [PanierController::class, 'index'])->name('panier');

    Route::post('/api/panierLength', [PanierController::class, 'panierLength']);
    Route::post('/api/addStackToPanier', [PanierController::class, 'addStackToPanier']);
    Route::post('/api/addToPanier', [PanierController::class, 'addToPanier'])->name('addToPanier');
    Route::post('/api/updateQuantity', [PanierController::class, 'updateQuantity']);
    Route::post('/api/removeItem', [PanierController::class, 'removeItem']);
    Route::post('/api/sendPanier', [PanierController::class, 'sendPanier']);
    /*
    |--------------------------------------------------------------------------
    | PASSER COMMANDES ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('checkadminaccess')->group(function () {
        Route::middleware('checkpanier')->group(function () {
            Route::get('/passer-commande/full-licences', [PasserCommandeController::class, 'fullLicences'])->name('order-full-licences');
            Route::get('/passer-commande/entreprise', [PasserCommandeController::class, 'entreprise'])->name('entreprise');
            Route::get('/passer-commande/livraison', [PasserCommandeController::class, 'livraison'])->name('livraison');
            Route::get('/passer-commande/signataire', [PasserCommandeController::class, 'signataire'])->name('signataire');
        });
        Route::get('/passer-commande/confirme', [PasserCommandeController::class, 'confirme'])->name('confirme');

        Route::post('/api/entreprise', [PasserCommandeController::class, 'store_entreprise_informations'])->name('store_entreprise_informations');
        Route::post('/api/store_adresse', [PasserCommandeController::class, 'store_adresse'])->name('store_adresse');
        Route::post('/api/store_livraison', [PasserCommandeController::class, 'store_livraison'])->name('store_livraison');
        Route::post('/api/store_signataire', [PasserCommandeController::class, 'store_signataire'])->name('store_signataire');
        Route::post('/api/store-full-licences', [PasserCommandeController::class, 'store_full_licences'])->name('store-full-licences');
    });
    /*
    |--------------------------------------------------------------------------
    | MES COMMANDES ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkadminaccess'])->group(function () {
        Route::get('/mes-commandes', [MesCommandesController::class, 'index'])->name('mes-commandes');
        Route::get('/mes-commandes/{reference_commande}', [MesCommandesController::class, 'commande']);
    });

    Route::post('/api/signature', [MesCommandesController::class, 'signature'])->name('mes-commandes.signature');
    Route::post('/api/searchCommandes', [MesCommandesController::class, 'searchCommandes']);
    /*
    |--------------------------------------------------------------------------
    | MES EQUIPEMENTS ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/mes-equipements', [MesEquipementsController::class, 'index'])->name('mes-equipements');
    Route::get('/mes-equipements/{equipement_id}', [MesEquipementsController::class, 'equipement'])->name('mes-equipements.equipement');

    Route::middleware('checkadminaccess')->group(function () {
        Route::post('/api/editCaracteristiqueImported', [MesEquipementsController::class, 'editCaracteristiqueImported'])->name('mes-equipements.editCaracteristiqueImported');
        Route::post('/api/editEquipement', [MesEquipementsController::class, 'editEquipement'])->name('mes-equipements.edit');
        Route::post('/api/createEquipement', [MesEquipementsController::class, 'createEquipement'])->name('createEquipement');
        Route::post('/api/searchEquipements', [MesEquipementsController::class, 'searchEquipements']);
        Route::post('/api/equipements/importer-csv', [MesEquipementsController::class, 'importerCsv'])->name('equipements.importer-csv');
        Route::post('/api/editMultipleEquipements', [MesEquipementsController::class, 'editMultipleEquipements'])->name('editMultipleEquipements');
        Route::post('/api/enrollEquipement', [MesEquipementsController::class, 'enrollEquipement'])->name('enrollEquipement');
        Route::post('/api/actionEquipement', [MesEquipementsController::class, 'actionEquipement'])->name('actionEquipement');
        Route::post('/api/updateAttribution', [MesEquipementsController::class, 'updateAttribution'])->name('updateAttribution');
    });
    /*
    |--------------------------------------------------------------------------
    | MON EQUIPE / MES SALLE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/mon-equipe', [MesAttributionsController::class, 'index'])->name('mon-equipe');
    Route::get('/mes-salles', [MesAttributionsController::class, 'index'])->name('mes-salles');
    Route::get('/mon-equipe/{attribution_id}', [MesAttributionsController::class, 'attribution'])->name('attribution.mon-equipe');
    Route::get('/mes-salles/{attribution_id}', [MesAttributionsController::class, 'attribution'])->name('attribution.mes-salles');

    Route::middleware('checkadminaccess')->group(function () {
        Route::post('/api/onboardCollaborateur', [MesAttributionsController::class, 'onboardCollaborateur'])->name('onboardCollaborateur');
        Route::post('/api/offboardCollaborateur', [MesAttributionsController::class, 'offboardCollaborateur'])->name('offboardCollaborateur');
    });
    Route::post('/api/searchAttributions', [MesAttributionsController::class, 'searchAttributions']);
    /*
    |--------------------------------------------------------------------------
    | MES CONTRATS ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('checkadminaccess')->group(function () {
        Route::get('/mes-contrats', [MesContratsController::class, 'index'])->name('mes-contrats');
        Route::get('/mes-contrats/{reference_commande}', [MesContratsController::class, 'contrat'])->name('mes-contrats.contrat');

        Route::post('/api/searchContrats', [MesContratsController::class, 'searchContrats']);
        Route::post('/api/createContrat', [MesContratsController::class, 'createContrat'])->name('createContrat');
        Route::post('/api/modifyContrat', [MesContratsController::class, 'modifyContrat'])->name('modifyContrat');
        Route::post('/api/editEquipementToContrat', [MesContratsController::class, 'editEquipementToContrat'])->name('editEquipementToContrat');
    });
    /*
    |--------------------------------------------------------------------------
    | SUPPORT ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/supports/{id?}', [SupportController::class, 'index'])->name('supports');

    Route::post('/api/createSupport', [SupportController::class, 'createSupport'])->name('createSupport');
    Route::post('/api/sendMessage', [SupportController::class, 'sendMessage'])->name('sendMessage');
    Route::post('/api/seachSupports', [SupportController::class, 'seachSupports'])->name('seachSupports');

    /*
    |--------------------------------------------------------------------------
    | OTHERS ROUTES AUTH
    |--------------------------------------------------------------------------
    */
    Route::post('/api/bienvenue', [OtherController::class, 'bienvenue'])->name('bienvenue');
    /*
    |--------------------------------------------------------------------------
    | COMMUN ROUTES AUTH
    |--------------------------------------------------------------------------
    */
    Route::post('/api/filterSearch', [CommunController::class, 'filterSearch'])->name('filterSearch');
    Route::middleware('checkadminaccess')->group(function () {
        Route::delete('/api/deleteRecord', [CommunController::class, 'deleteRecord'])->name('deleteRecord');
    });
    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('checksuperadminaccess')->group(function () {
        /*
        |--------------------------------------------------------------------------
        | ADMIN COMMANDES ROUTES
        |--------------------------------------------------------------------------
        */
        Route::get('/commandesAdmin', [CommandesController::class, 'index'])->name('commandesAdmin');
        Route::get('/commandesAdmin/{reference_commande}', [CommandesController::class, 'commande']);

        Route::post('/api/filterCommande', [CommandesController::class, 'filterCommande']);
        Route::post('/api/searchCommande', [CommandesController::class, 'searchCommande']);

        Route::post('/api/financeur', [CommandesController::class, 'financeur'])->name('commande.financeur');
        Route::post('/api/confirmationAchat', [CommandesController::class, 'confirmationAchat'])->name('commande.confirmationAchat');
        Route::post('/api/validation', [CommandesController::class, 'validation'])->name('commande.validation');
        Route::post('/api/sign_again', [CommandesController::class, 'sign_again'])->name('commande.sign_again');
        Route::post('/api/livraison', [CommandesController::class, 'livraison'])->name('commande.livraison');
        Route::post('/api/contrat', [CommandesController::class, 'contrat'])->name('commande.contrat');
        Route::post('/api/termine', [CommandesController::class, 'termine'])->name('commande.termine');
        /*
        |--------------------------------------------------------------------------
        | ADMIN SUPPORT ROUTES
        |--------------------------------------------------------------------------
        */
        Route::get('/supportsAdmin/{id?}', [SupportController::class, 'index'])->name('supportsAdmin');
        Route::post('/api/clotureTicket', [SupportController::class, 'clotureTicket'])->name('clotureTicket');
        /*
        |--------------------------------------------------------------------------
        | ADMIN ALL USERS ROUTES
        |--------------------------------------------------------------------------
        */
        Route::get('/usersAdmin', [UsersController::class, 'usersAdmin'])->name('usersAdmin');
        Route::get('/superadmin/entreprise/{entrepriseId}', [UsersController::class, 'entreprise']);

        Route::post('/api/superadmin/linkEntreprise', [UsersController::class, 'linkEntreprise'])->name('linkEntreprise');
        Route::post('/api/searchUsers', [UsersController::class, 'searchUsers']);
    });
    /*
    |--------------------------------------------------------------------------
    | Tenants general (microsoft google) ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/{tenant}/auth/redirect', [TenantsController::class, 'redirect'])->name('tenants.redirect')->where('tenant', 'microsoft|google');
    Route::get('/{tenant}/auth/callback', [TenantsController::class, 'callback'])->name('tenants.callback')->where('tenant', 'microsoft|google');
    Route::get('/{tenant}/recreateTenantAccess', [TenantsController::class, 'recreateTenantAccess'])->name('tenants.recreateTenantAccess')->where('tenant', 'microsoft|google');

    Route::post('/createTenantAccount', [TenantsController::class, 'createTenantAccount'])->name('createTenantAccount');
    Route::post('/checkUserEmailTenant', [TenantsController::class, 'checkUserEmailTenant'])->name('checkUserEmailTenant');
    /*
    |--------------------------------------------------------------------------
    | SIRH ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/{sirhName}/auth/redirect', [SIRHController::class, 'redirect'])->name('sirh.redirect');
    Route::get('/{sirhName}/auth/callback', [SIRHController::class, 'callback'])->name('sirh.callback');
    Route::post('/api/store_integrationshit', [SIRHController::class, 'store_integrationshit'])->name('store_integrationshit');
});
/*
|--------------------------------------------------------------------------
| OTHERS ROUTES
|--------------------------------------------------------------------------
*/
Route::post('api/account_confirmation', [OtherController::class, 'account_confirmation'])->name('account_confirmation');
Route::post('api/checkMailUnique', [OtherController::class, 'checkMailUnique'])->name('checkMailUnique');


require __DIR__ . '/auth.php';
