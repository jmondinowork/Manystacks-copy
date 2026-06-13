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
        Schema::create('supports', function (Blueprint $table) {
            $table->id();
            $table->string('numero_support');
            $table->text('object');
            $table->string('status')->default('En cours');
            $table->foreignId('commande_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('equipement_id')->nullable()->constrained('commande_products')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('entreprise_id')->nullable()->constrained('entreprise_informations')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('supports_messages', function (Blueprint $table) {
            $table->id();
            $table->longText('message');
            $table->string('from')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('support_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supports');
        Schema::dropIfExists('supports_messages');
    }
};
