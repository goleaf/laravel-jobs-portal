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
        Schema::table('inquiries', function (Blueprint $table) {
            // Add missing required columns
            if (!Schema::hasColumn('inquiries', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('inquiries', 'email')) {
                $table->string('email')->nullable()->after('name');
            }
            if (!Schema::hasColumn('inquiries', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('inquiries', 'subject')) {
                $table->string('subject')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('inquiries', 'message')) {
                $table->text('message')->nullable()->after('subject');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['name', 'email', 'phone', 'subject', 'message']);
        });
    }
};
