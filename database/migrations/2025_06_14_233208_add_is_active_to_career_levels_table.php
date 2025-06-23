<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('career_levels', function (Blueprint $table) {
            if (!Schema::hasColumn('career_levels', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('level_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('career_levels', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
