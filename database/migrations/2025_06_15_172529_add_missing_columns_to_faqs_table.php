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
        Schema::table('faqs', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
            $table->unsignedBigInteger('user_id')->nullable()->after('category');
            $table->boolean('is_active')->default(true)->after('user_id');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->boolean('is_published')->default(true)->after('is_featured');
            $table->unsignedInteger('view_count')->default(0)->after('is_published');
            $table->unsignedInteger('helpful_count')->default(0)->after('view_count');
            $table->unsignedInteger('not_helpful_count')->default(0)->after('helpful_count');
            $table->unsignedInteger('sort_order')->default(0)->after('not_helpful_count');
            $table->json('tags')->nullable()->after('sort_order');
            $table->json('meta')->nullable()->after('tags');
            $table->timestamp('published_at')->nullable()->after('meta');
            $table->softDeletes()->after('updated_at');
            
            // Add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'category',
                'user_id', 
                'is_active',
                'is_featured',
                'is_published',
                'view_count',
                'helpful_count',
                'not_helpful_count',
                'sort_order',
                'tags',
                'meta',
                'published_at',
                'deleted_at'
            ]);
        });
    }
};
