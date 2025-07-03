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
        // Add city_id to companies table
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable()->after('location');
                $table->foreign('city_id')->references('id')->on('cities')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            }
        });

        // Add city_id to candidates table
        Schema::table('candidates', function (Blueprint $table) {
            if (! Schema::hasColumn('candidates', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable()->after('address');
                $table->foreign('city_id')->references('id')->on('cities')
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
        // Remove city_id from companies table
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'city_id')) {
                $table->dropForeign(['city_id']);
                $table->dropColumn('city_id');
            }
        });

        // Remove city_id from candidates table
        Schema::table('candidates', function (Blueprint $table) {
            if (Schema::hasColumn('candidates', 'city_id')) {
                $table->dropForeign(['city_id']);
                $table->dropColumn('city_id');
            }
        });
    }
};
