<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->dropColumn([
                'temperature_sensor',
                'ammonia_sensor',
                'dissolved_oxygen_sensor',
                'heater_device',
                'aerator_device',
                'filter_device',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->string('temperature_sensor')->nullable();
            $table->string('ammonia_sensor')->nullable();
            $table->string('dissolved_oxygen_sensor')->nullable();
            $table->string('heater_device')->nullable();
            $table->string('aerator_device')->nullable();
            $table->string('filter_device')->nullable();
        });
    }
};
