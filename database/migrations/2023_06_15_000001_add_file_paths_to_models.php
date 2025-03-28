<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // For User model
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image_path')->nullable();
        });

        // For Post model
        Schema::table('posts', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        // For JobCategory model
        Schema::table('job_categories', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        // For Company model
        Schema::table('companies', function (Blueprint $table) {
            $table->string('logo_path')->nullable();
        });

        // For Candidate model
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('resume_path')->nullable();
            $table->string('image_path')->nullable();
        });

        // For Testimonial model
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        // For FrontSetting model
        Schema::table('front_settings', function (Blueprint $table) {
            $table->string('header_logo_path')->nullable();
            $table->string('footer_logo_path')->nullable();
        });

        // For HeaderSlider model
        Schema::table('header_sliders', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        // For BrandingSliders model
        Schema::table('branding_sliders', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        // For ImageSlider model
        Schema::table('image_sliders', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        // For CmsServices model
        Schema::table('cms_services', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });
    }

    public function down()
    {
        // For User model
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_image_path');
        });

        // For Post model
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        // For JobCategory model
        Schema::table('job_categories', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        // For Company model
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('logo_path');
        });

        // For Candidate model
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('resume_path');
            $table->dropColumn('image_path');
        });

        // For Testimonial model
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        // For FrontSetting model
        Schema::table('front_settings', function (Blueprint $table) {
            $table->dropColumn('header_logo_path');
            $table->dropColumn('footer_logo_path');
        });

        // For HeaderSlider model
        Schema::table('header_sliders', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        // For BrandingSliders model
        Schema::table('branding_sliders', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        // For ImageSlider model
        Schema::table('image_sliders', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        // For CmsServices model
        Schema::table('cms_services', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
}; 