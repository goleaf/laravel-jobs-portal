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
            if (! Schema::hasColumn('inquiries', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('is_active');
            }

            if (! Schema::hasColumn('inquiries', 'is_resolved')) {
                $table->boolean('is_resolved')->default(false)->after('is_read');
            }

            if (! Schema::hasColumn('inquiries', 'status')) {
                $table->string('status')->default('pending')->after('is_resolved');
            }

            if (! Schema::hasColumn('inquiries', 'priority')) {
                $table->integer('priority')->default(1)->after('status');
            }

            if (! Schema::hasColumn('inquiries', 'category')) {
                $table->string('category')->default('general')->after('priority');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $columns = ['is_read', 'is_resolved', 'status', 'priority', 'category'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('inquiries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
