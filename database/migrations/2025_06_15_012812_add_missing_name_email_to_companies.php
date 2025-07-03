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
            // Add only missing columns that TestCase expects
            if (! Schema::hasColumn('companies', 'name')) {
                $table->string('name')->nullable()->after('id')->comment('Company name');
            }
            if (! Schema::hasColumn('companies', 'email')) {
                $table->string('email')->nullable()->after('name')->comment('Company email address');
            }
            if (! Schema::hasColumn('companies', 'phone')) {
                $table->string('phone')->nullable()->after('email')->comment('Company phone number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Drop columns if they exist
            $columns = [];
            if (Schema::hasColumn('companies', 'name')) {
                $columns[] = 'name';
            }
            if (Schema::hasColumn('companies', 'email')) {
                $columns[] = 'email';
            }
            if (Schema::hasColumn('companies', 'phone')) {
                $columns[] = 'phone';
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
