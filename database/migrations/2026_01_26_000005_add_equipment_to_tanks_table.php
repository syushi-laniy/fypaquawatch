<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->string('temperature_sensor')->nullable();
            $table->string('ph_sensor')->nullable();
            $table->string('turbidity_sensor')->nullable();
            $table->string('ammonia_sensor')->nullable();
            $table->string('dissolved_oxygen_sensor')->nullable();
            $table->string('water_level_sensor')->nullable();
            $table->string('heater_device')->nullable();
            $table->string('aerator_device')->nullable();
            $table->string('filter_device')->nullable();
            $table->string('dosing_device')->nullable();
            $table->string('topup_device')->nullable();
            $table->string('feeder_device')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->dropColumn([
                'temperature_sensor',
                'ph_sensor',
                'turbidity_sensor',
                'ammonia_sensor',
                'dissolved_oxygen_sensor',
                'water_level_sensor',
                'heater_device',
                'aerator_device',
                'filter_device',
                'dosing_device',
                'topup_device',
                'feeder_device',
            ]);
        });
    }
};
