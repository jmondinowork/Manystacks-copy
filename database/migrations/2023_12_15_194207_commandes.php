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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->longText('reference_commande')->nullable();
            $table->string('commande_ion_id')->nullable();
            $table->foreignId('entreprise_id')->nullable()->constrained('entreprise_informations')->onDelete('cascade');
            $table->foreignId('signataire_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('statut')->nullable();
            $table->string('financeur')->nullable();
            $table->longText('lien_contrat')->nullable();
            $table->longText('contrat_signe')->nullable();
            $table->timestamp('date_debut_contrat')->nullable();
            $table->timestamp('date_fin_contrat')->nullable();
            $table->boolean('sign_again')->nullable()->default(0);
            $table->timestamp('date_financement')->nullable();
            $table->timestamp('date_signature')->nullable();
            $table->timestamp('date_validation')->nullable();
            $table->timestamp('date_termine')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
