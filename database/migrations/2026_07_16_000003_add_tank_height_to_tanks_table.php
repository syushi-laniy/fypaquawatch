<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->decimal('tank_height_cm', 6, 2)->default(20.30)->after('control_mode');
        });
    }

    public function down(): void
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->dropColumn('tank_height_cm');
        });
    }
};
