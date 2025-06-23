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
        Schema::table('cms_services', function (Blueprint $table) {
            // Add missing deleted_at column for SoftDeletes functionality
            if (!Schema::hasColumn('cms_services', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_services', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
