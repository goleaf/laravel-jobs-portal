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
        Schema::table('post_categories', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('is_default');
            $table->integer('sort_order')->nullable()->after('is_active');
            $table->string('color', 7)->nullable()->after('sort_order');
            $table->string('icon', 50)->nullable()->after('color');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_categories', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'sort_order', 'color', 'icon']);
            $table->dropSoftDeletes();
        });
    }
};
