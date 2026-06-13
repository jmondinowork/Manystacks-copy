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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('email_perso')->nullable();
            $table->string('poste')->nullable();
            $table->string('type')->nullable();
            $table->string('role')->nullable();
            $table->string('tel')->nullable();
            $table->string('date_arrivee')->nullable();
            $table->string('date_sortie')->nullable();
            $table->longText('profile_img')->nullable();
            $table->string('password')->nullable();
            $table->foreignId('entreprise_id')->nullable()->constrained('entreprise_informations')->onDelete('cascade');
            $table->foreignId('adresse_id')->nullable()->constrained('adresse_livraisons')->onDelete('cascade');
            $table->boolean('bienvenue')->default(1);
            $table->string('tenant_name')->nullable();
            $table->string('tenant_company_id')->nullable();
            $table->string('tenant_user_id')->nullable();
            $table->string('sirh_name')->nullable();
            $table->string('sirh_company_id')->nullable();
            $table->string('sirh_user_id')->nullable();
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
