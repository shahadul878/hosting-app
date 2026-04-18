<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('domain_name');
            $table->date('expires_on')->nullable();
            $table->timestamps();
            $table->index('domain_name');
            $table->index('expires_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
