<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('image');
            $table->timestamps();
        });

        // Optional: Remove the image column from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    public function down(): void
    {
        // Restore the image column in products table
        Schema::table('products', function (Blueprint $table) {
            $table->string('image')->nullable()->after('category_id');
        });

        Schema::dropIfExists('product_images');
    }
};