<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commande_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->longText('proprietes')->nullable();
            $table->string('systeme_exploitation')->nullable();
            $table->string('camera')->nullable();
            $table->string('carac_camera')->nullable();
            $table->text('autonomie_batterie')->nullable();
            $table->string('etat')->nullable();
            $table->string('categorie')->nullable();
            $table->string('sous_categorie')->nullable();
            $table->string('marque')->nullable();
            $table->longText('ref_fournisseur')->nullable();
            $table->string('config')->nullable();
            $table->string('IMEI1')->nullable();
            $table->string('IMEI2')->nullable();
            $table->string('modele')->nullable();
            $table->longText('description')->nullable();
            $table->string('ram')->nullable();
            $table->string('stockage')->nullable();
            $table->string('type_stockage')->nullable();
            $table->string('processeur')->nullable();
            $table->string('type_ecran')->nullable();
            $table->string('resolution_ecran')->nullable();
            $table->string('frequence_ecran')->nullable();
            $table->string('temps_reponse_ecran')->nullable();
            $table->string('taille_ecran')->nullable();
            $table->string('carte_graphique')->nullable();
            $table->text('connectivite')->nullable();
            $table->text('connectique')->nullable();
            $table->text('alimentation')->nullable();
            $table->text('audio')->nullable();
            $table->text('luminosite_ecran')->nullable();
            $table->string('couleur')->nullable();
            $table->string('dimension')->nullable();
            $table->string('poids')->nullable();
            $table->longText('informations-techniques')->nullable();
            $table->string('norme_environement')->nullable();
            $table->string('empreinte_carbonne')->nullable();
            $table->string('garantie')->nullable();
            $table->integer('quantity')->nullable();
            $table->longText('lien_livraison')->nullable();
            $table->longText('note')->nullable();
            $table->string('commande_status')->nullable();
            $table->string('status')->default('pending');
            $table->longText('image_principale')->nullable();
            $table->text('numero_unique')->nullable();
            $table->decimal('prix', 10, 2)->nullable();
            $table->string('type_licence')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('sku_id')->nullable();
            $table->string('plan_id')->nullable();
            $table->string('licence_resource_id')->nullable();
            $table->string('licence_id')->nullable();
            $table->string('licence_item_id')->nullable();
            $table->string('guid')->nullable();
            $table->string('fournisseur')->nullable();
            $table->dateTime('date_debut_licence')->nullable();
            $table->dateTime('date_fin_licence')->nullable();
            $table->boolean('auto_renew')->default(0);
            $table->string('clavier')->nullable();
            $table->string('type_contrat')->nullable();
            // $table->string('numero_contrat_imported')->nullable();
            // $table->string('date_debut_contrat_imported')->nullable();
            // $table->string('date_fin_contrat_imported')->nullable();
            $table->boolean('public')->default(0);
            $table->foreignId('adresse_livraison_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_livraison_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('user_attributed_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('attribution_waiting')->nullable();
            $table->longText('enrollment_id')->nullable();
            $table->foreignId('entreprise_id')->nullable()->constrained('entreprise_informations')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commande_products');
    }
};
