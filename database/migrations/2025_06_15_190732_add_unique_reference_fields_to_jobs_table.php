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
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('job_reference')->nullable()->unique()->after('id')->comment('Unique job reference number');
            $table->string('slug')->nullable()->unique()->after('job_title')->comment('Unique job slug for URLs');
            $table->index(['job_reference']);
            $table->index(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex(['job_reference']);
            $table->dropIndex(['slug']);
            $table->dropColumn(['job_reference', 'slug']);
        });
    }
};
