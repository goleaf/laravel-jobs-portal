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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('city_id');
            
            // Add foreign key constraint if companies table exists
            if (Schema::hasTable('companies')) {
                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraint if it exists
            if (Schema::hasTable('companies')) {
                $table->dropForeign(['company_id']);
            }
            $table->dropColumn('company_id');
        });
    }
};
