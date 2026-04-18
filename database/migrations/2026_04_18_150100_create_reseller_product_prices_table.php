<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('custom_price', 15, 2);
            $table->decimal('minimum_price', 15, 2);
            $table->timestamps();
            $table->unique(['reseller_user_id', 'product_id']);
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_product_prices');
    }
};
