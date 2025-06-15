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
        Schema::table('cities', function (Blueprint $table) {
            // Add missing columns that factories expect
            if (!Schema::hasColumn('cities', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('state_id')->comment('Active status');
            }
            if (!Schema::hasColumn('cities', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active')->comment('Featured status');
            }
            if (!Schema::hasColumn('cities', 'is_metropolitan')) {
                $table->boolean('is_metropolitan')->default(false)->after('is_featured')->comment('Metropolitan area status');
            }
            if (!Schema::hasColumn('cities', 'is_major')) {
                $table->boolean('is_major')->default(false)->after('is_metropolitan')->comment('Major city status');
            }
            if (!Schema::hasColumn('cities', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('is_major')->comment('Latitude coordinate');
            }
            if (!Schema::hasColumn('cities', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude')->comment('Longitude coordinate');
            }
            if (!Schema::hasColumn('cities', 'timezone')) {
                $table->string('timezone', 50)->nullable()->after('longitude')->comment('Timezone identifier');
            }
            if (!Schema::hasColumn('cities', 'population')) {
                $table->integer('population')->nullable()->after('timezone')->comment('Population count');
            }
            if (!Schema::hasColumn('cities', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn([
                'is_active', 'is_featured', 'is_metropolitan', 'is_major',
                'latitude', 'longitude', 'timezone', 'population', 'deleted_at'
            ]);
        });
    }
};
