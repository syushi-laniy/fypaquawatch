<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->string('location_name')->nullable();
            $table->decimal('location_lat', 10, 7)->nullable();
            $table->decimal('location_lon', 10, 7)->nullable();
            $table->longText('weather_json')->nullable();
            $table->timestamp('weather_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->dropColumn([
                'location_name',
                'location_lat',
                'location_lon',
                'weather_json',
                'weather_updated_at',
            ]);
        });
    }
};
