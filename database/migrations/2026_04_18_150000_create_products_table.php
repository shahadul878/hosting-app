<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('product_type', 64);
            $table->decimal('base_price', 15, 2);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->json('config')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('product_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
