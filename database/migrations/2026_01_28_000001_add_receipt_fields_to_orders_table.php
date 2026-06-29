<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('receipt_url')->nullable()->after('payment_intent_id');
            $table->timestamp('paid_at')->nullable()->after('receipt_url');
            $table->timestamp('receipt_sent_at')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['receipt_url', 'paid_at', 'receipt_sent_at']);
        });
    }
};
