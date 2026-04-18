<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->string('mvp_role', 32)->default('client')->after('email');
            $table->string('account_status', 16)->default('active')->after('mvp_role');
            $table->index('parent_id');
            $table->index(['mvp_role', 'account_status']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['mvp_role', 'account_status']);
            $table->dropColumn(['parent_id', 'mvp_role', 'account_status']);
        });
    }
};
