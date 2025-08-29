<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('address')->nullable()->after('status');
            $table->string('payment_method')->default('cod')->after('address'); // 'cod' or 'online'
            $table->string('payment_status')->default('pending')->after('payment_method'); // 'pending', 'paid', 'failed'
            $table->string('transaction_id')->nullable()->after('payment_status'); // For SSLCommerz tran_id
            $table->text('notes')->nullable()->after('transaction_id'); // Optional user notes
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['address', 'payment_method', 'payment_status', 'transaction_id', 'notes']);
        });
    }
};