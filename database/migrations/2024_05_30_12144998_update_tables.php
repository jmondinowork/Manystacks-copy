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
        Schema::table('commande_products', function (Blueprint $table) {
            if (!Schema::hasColumn('commande_products', 'date_debut_licence')) {
                $table->dateTime('date_debut_licence')->nullable();
            }
            if (!Schema::hasColumn('commande_products', 'date_fin_licence')) {
                $table->dateTime('date_fin_licence')->nullable();
            }
            if (!Schema::hasColumn('commande_products', 'auto_renew')) {
                $table->boolean('auto_renew')->default(0);
            }
        });

        Schema::table('commandes', function (Blueprint $table) {
            if (!Schema::hasColumn('commandes', 'commande_ion_id')) {
                $table->string('commande_ion_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
