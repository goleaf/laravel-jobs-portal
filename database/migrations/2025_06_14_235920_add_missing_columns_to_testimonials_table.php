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
        Schema::table('testimonials', function (Blueprint $table) {
            // Add missing columns that the model expects
            $table->string('customer_title')->nullable()->after('customer_name');
            $table->string('customer_company')->nullable()->after('customer_title');
            $table->string('customer_email')->nullable()->after('customer_company');
            $table->integer('rating')->default(5)->after('description');
            $table->boolean('is_active')->default(true)->after('rating');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->boolean('is_verified')->default(false)->after('is_featured');
            $table->string('location')->nullable()->after('is_verified');
            $table->string('project_type')->nullable()->after('location');
            $table->integer('sort_order')->nullable()->after('project_type');
            $table->date('testimonial_date')->nullable()->after('sort_order');

            // Add soft deletes
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn([
                'customer_title',
                'customer_company',
                'customer_email',
                'rating',
                'is_active',
                'is_featured',
                'is_verified',
                'location',
                'project_type',
                'sort_order',
                'testimonial_date',
                'deleted_at',
            ]);
        });
    }
};
