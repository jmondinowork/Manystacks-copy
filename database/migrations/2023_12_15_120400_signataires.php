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
        Schema::create('signataires', function (Blueprint $table) {
            $table->id();
            $table->string('prenom');
            $table->string('nom');
            $table->string('telephone')->nullable();
            $table->string('mail');
            $table->string('date_naissance')->nullable();
            $table->string('ville_naissance')->nullable();
            $table->boolean('representant_legal');
            $table->foreignId('entreprise_id')->nullable()->constrained('entreprise_informations')->onDelete('cascade');

            $table->longText('piece_identite_recto')->nullable();
            $table->longText('piece_identite_verso')->nullable();
            $table->longText('pouvoir')->nullable();
            $table->longText('iban')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signataires');
    }
};
