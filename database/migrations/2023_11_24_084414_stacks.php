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
        Schema::create('stacks', function (Blueprint $table) {
            $table->id();
            $table->string('stack_name');
            $table->string('slug')->nullable();
            $table->string('color')->nullable();
            $table->boolean('public')->default(false);
            $table->foreignId('entreprise_id')->nullable()->constrained()->onDelete('cascade');
            $table->longText('img')->nullable();

            $table->timestamps();
        });

        Schema::create('product_stack', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('stack_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stacks');
        Schema::dropIfExists('product_stack');
    }
};
