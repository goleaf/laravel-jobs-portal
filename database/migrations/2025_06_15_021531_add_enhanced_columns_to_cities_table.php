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
            // Check if columns exist before adding them
            if (!Schema::hasColumn('cities', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('name');
            }
            if (!Schema::hasColumn('cities', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('name');
            }
            if (!Schema::hasColumn('cities', 'is_metropolitan')) {
                $table->boolean('is_metropolitan')->default(false)->after('name');
            }
            if (!Schema::hasColumn('cities', 'is_major')) {
                $table->boolean('is_major')->default(false)->after('name');
            }
            if (!Schema::hasColumn('cities', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('name');
            }
            if (!Schema::hasColumn('cities', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('name');
            }
            if (!Schema::hasColumn('cities', 'timezone')) {
                $table->string('timezone', 50)->nullable()->after('name');
            }
            if (!Schema::hasColumn('cities', 'population')) {
                $table->unsignedBigInteger('population')->nullable()->after('name');
            }
            if (!Schema::hasColumn('cities', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // Only drop columns if they exist
            $columnsToDrop = [];
            
            if (Schema::hasColumn('cities', 'is_active')) {
                $columnsToDrop[] = 'is_active';
            }
            if (Schema::hasColumn('cities', 'is_featured')) {
                $columnsToDrop[] = 'is_featured';
            }
            if (Schema::hasColumn('cities', 'is_metropolitan')) {
                $columnsToDrop[] = 'is_metropolitan';
            }
            if (Schema::hasColumn('cities', 'is_major')) {
                $columnsToDrop[] = 'is_major';
            }
            if (Schema::hasColumn('cities', 'latitude')) {
                $columnsToDrop[] = 'latitude';
            }
            if (Schema::hasColumn('cities', 'longitude')) {
                $columnsToDrop[] = 'longitude';
            }
            if (Schema::hasColumn('cities', 'timezone')) {
                $columnsToDrop[] = 'timezone';
            }
            if (Schema::hasColumn('cities', 'population')) {
                $columnsToDrop[] = 'population';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
