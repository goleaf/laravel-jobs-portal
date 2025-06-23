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
        Schema::table('email_jobs', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('friend_email');
            $table->boolean('is_sent')->default(false)->after('is_active');
            $table->string('status')->default('pending')->after('is_sent');
            $table->integer('open_count')->default(0)->after('status');
            $table->integer('click_count')->default(0)->after('open_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_jobs', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'is_sent', 'status', 'open_count', 'click_count']);
        });
    }
};
