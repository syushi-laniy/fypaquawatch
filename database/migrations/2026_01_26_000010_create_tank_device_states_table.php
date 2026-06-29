<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tank_device_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tank_id')->constrained()->cascadeOnDelete();
            $table->string('device_key');
            $table->boolean('state')->default(false);
            $table->timestamps();

            $table->unique(['tank_id', 'device_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tank_device_states');
    }
};
