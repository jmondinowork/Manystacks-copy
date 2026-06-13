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
        Schema::create('entreprise_informations', function (Blueprint $table) {
            $table->id();
            $table->text('raison_sociale')->nullable();
            $table->boolean('auto_entreprise')->default(0);
            $table->string('siret')->nullable();
            $table->longText('profile_img')->nullable();
            $table->longText('adresse')->nullable();
            $table->longText('complement_adresse')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->nullable();
            $table->integer('group_id')->nullable();
            $table->string('ion_id')->nullable();
            $table->boolean('licence_google')->default(0);
            $table->boolean('licence_microsoft')->default(0);
            $table->timestamps();
        });

        Schema::create('entreprise_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->constrained('entreprise_informations')->onDelete('cascade');
            $table->string('domain')->nullable();
            $table->string('tenant')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprise_informations');
        Schema::dropIfExists('entreprise_domains');
    }
};
