<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('client_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reseller_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sub_reseller_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 32);
            $table->json('metadata')->nullable();
            $table->date('billing_started_on')->nullable();
            $table->date('renews_on')->nullable();
            $table->date('next_invoice_on')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['reseller_user_id', 'status']);
            $table->index(['sub_reseller_user_id', 'status']);
            $table->index(['client_user_id', 'status']);
            $table->index('renews_on');
            $table->index('next_invoice_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
