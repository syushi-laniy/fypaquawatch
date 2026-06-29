<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('species', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('min_ph', 4, 2);
            $table->decimal('max_ph', 4, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tank_species', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tank_id')->constrained()->cascadeOnDelete();
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tank_id', 'species_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tank_species');
        Schema::dropIfExists('species');
    }
};
