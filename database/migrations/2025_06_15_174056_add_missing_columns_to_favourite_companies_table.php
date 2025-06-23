<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('favourite_companies', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('company_id');
            $table->boolean('is_featured')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favourite_companies', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'is_featured']);
        });
    }
};
