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
        Schema::table('states', function (Blueprint $table) {
            // Add missing columns for Context7 pattern enhancement
            if (! Schema::hasColumn('states', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('country_id')->comment('Active status');
            }
            if (! Schema::hasColumn('states', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active')->comment('Featured status');
            }
            if (! Schema::hasColumn('states', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_featured')->comment('Sort order for display');
            }
            if (! Schema::hasColumn('states', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('states', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'is_featured', 'sort_order', 'deleted_at']);
        });
    }
};
