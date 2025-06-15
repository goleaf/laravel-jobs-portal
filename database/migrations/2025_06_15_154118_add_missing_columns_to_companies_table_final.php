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
            if (!Schema::hasColumn('companies', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('is_featured');
            }
            
            if (!Schema::hasColumn('companies', 'no_of_employees')) {
                $table->integer('no_of_employees')->nullable()->after('is_verified');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'is_verified')) {
                $table->dropColumn('is_verified');
            }
            
            if (Schema::hasColumn('companies', 'no_of_employees')) {
                $table->dropColumn('no_of_employees');
            }
        });
    }
};
