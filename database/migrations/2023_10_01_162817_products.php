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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->longText('proprietes')->nullable();
            $table->boolean('top_produit')->default(0);
            $table->string('systeme_exploitation')->nullable();
            $table->string('camera')->nullable();
            $table->text('carac_camera')->nullable();
            $table->text('autonomie_batterie')->nullable();
            $table->string('etat')->default('Neuf');
            $table->string('slug')->nullable();
            $table->string('categorie')->nullable();
            $table->string('sous_categorie')->nullable();
            $table->string('marque')->nullable();
            $table->longText('ref_fournisseur')->nullable();
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
            $table->string('clavier')->nullable();
            $table->decimal('prix_achat', 10, 2)->nullable();
            $table->decimal('prix_location', 10, 2)->nullable();
            $table->string('type_licence')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('sku_id')->nullable();
            $table->string('plan_id')->nullable();
            $table->string('guid')->nullable();
            $table->string('fournisseur')->nullable();
            $table->boolean('deleted')->default(0);
            $table->string('delais_livraison')->nullable();
            $table->boolean('co2')->nullable();
            $table->text('appsincluses')->nullable();
            $table->string('appstype')->nullable();

            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->longText('image_url');
            $table->boolean('principale')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
};
