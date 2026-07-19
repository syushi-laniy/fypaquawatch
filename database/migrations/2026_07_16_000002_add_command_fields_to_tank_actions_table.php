<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tank_actions', function (Blueprint $table) {
            $table->string('action_type')->nullable()->after('action');
            $table->foreignId('requested_by')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->string('status')->default('recorded')->after('note');
            $table->timestamp('requested_at')->nullable()->after('status');
            $table->timestamp('completed_at')->nullable()->after('requested_at');

            $table->index(['tank_id', 'action_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('tank_actions', function (Blueprint $table) {
            $table->dropIndex(['tank_id', 'action_type', 'status']);
            $table->dropConstrainedForeignId('requested_by');
            $table->dropColumn([
                'action_type',
                'status',
                'requested_at',
                'completed_at',
            ]);
        });
    }
};
