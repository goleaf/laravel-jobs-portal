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
        Schema::table('companies', function (Blueprint $table) {
            // Add country_id column if it doesn't exist
            if (! Schema::hasColumn('companies', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('location');
                $table->foreign('country_id')->references('id')->on('countries')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            }

            // Add state_id column if it doesn't exist
            if (! Schema::hasColumn('companies', 'state_id')) {
                $table->unsignedBigInteger('state_id')->nullable()->after('country_id');
                $table->foreign('state_id')->references('id')->on('states')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Remove state_id column if it exists
            if (Schema::hasColumn('companies', 'state_id')) {
                $table->dropForeign(['state_id']);
                $table->dropColumn('state_id');
            }

            // Remove country_id column if it exists
            if (Schema::hasColumn('companies', 'country_id')) {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
            }
        });
    }
};
