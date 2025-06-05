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
            $table->string('facebook_url')->nullable()->after('fax');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('linkedin_url')->nullable()->after('twitter_url');
            $table->string('google_plus_url')->nullable()->after('linkedin_url');
            $table->string('pinterest_url')->nullable()->after('google_plus_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url',
                'twitter_url', 
                'linkedin_url',
                'google_plus_url',
                'pinterest_url'
            ]);
        });
    }
};
