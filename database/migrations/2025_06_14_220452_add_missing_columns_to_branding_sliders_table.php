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
        Schema::table('branding_sliders', function (Blueprint $table) {
            if (! Schema::hasColumn('branding_sliders', 'view_count')) {
                $table->unsignedInteger('view_count')->default(0);
            }
            if (! Schema::hasColumn('branding_sliders', 'click_count')) {
                $table->unsignedInteger('click_count')->default(0);
            }
            if (! Schema::hasColumn('branding_sliders', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0);
            }
            if (! Schema::hasColumn('branding_sliders', 'open_in_new_tab')) {
                $table->boolean('open_in_new_tab')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branding_sliders', function (Blueprint $table) {
            $table->dropColumn(['view_count', 'click_count', 'sort_order', 'open_in_new_tab']);
        });
    }
};
