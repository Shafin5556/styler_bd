<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_val_id')->nullable()->after('transaction_id');
            $table->string('payment_card_type')->nullable()->after('payment_val_id');
            $table->string('payment_card_no')->nullable()->after('payment_card_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_val_id', 'payment_card_type', 'payment_card_no']);
        });
    }
};