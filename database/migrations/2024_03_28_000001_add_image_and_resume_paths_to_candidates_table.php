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
        Schema::table('candidates', function (Blueprint $table) {
            if (!Schema::hasColumn('candidates', 'image_path')) {
                $table->string('image_path')->nullable()->after('expected_salary');
            }
            if (!Schema::hasColumn('candidates', 'resume_path')) {
                $table->string('resume_path')->nullable()->after('image_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (Schema::hasColumn('candidates', 'image_path')) {
                $table->dropColumn('image_path');
            }
            if (Schema::hasColumn('candidates', 'resume_path')) {
                $table->dropColumn('resume_path');
            }
        });
    }
};
