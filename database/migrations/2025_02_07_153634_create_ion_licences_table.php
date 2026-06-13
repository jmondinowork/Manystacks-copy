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
        Schema::create('ion_licences', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->longText('description_marketing')->nullable();
            $table->longText('image_principale')->nullable();
            $table->string('fournisseur')->nullable();
            $table->string('product_id')->nullable();
            $table->string('sku_id')->nullable();
            $table->string('plan_id')->nullable();
            $table->string('guid')->nullable();
            $table->string('facturation_period')->nullable();
            $table->string('engagement_period')->nullable();
            $table->string('apps_inclus')->nullable();
            $table->string('app_type')->nullable();
            $table->string('prix')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ion_licences');
    }
};
