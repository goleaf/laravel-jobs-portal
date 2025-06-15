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
        Schema::table('candidate_educations', function (Blueprint $table) {
            $table->json('settings')->nullable()->after('is_verified')
                ->comment('Laravel Model Settings for education record management, privacy, verification, and display options');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_educations', function (Blueprint $table) {
            $table->dropColumn('settings');
        });
    }
};
