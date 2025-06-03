<?php

namespace Tests\Unit\Models;

use App\Models\JobCategory;
use PHPUnit\Framework\TestCase;

class JobCategoryModelTest extends TestCase
{
    /** @test */
    public function it_has_correct_table_name()
    {
        $jobCategory = new JobCategory();
        $this->assertEquals('job_categories', $jobCategory->getTable());
    }

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $jobCategory = new JobCategory();
        $fillable = $jobCategory->getFillable();

        $expectedFillable = [
            'name',
            'image',
            'is_featured',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $jobCategory = new JobCategory();
        $casts = $jobCategory->getCasts();

        $expectedCasts = [
            'id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'is_featured' => 'boolean',
            'is_default' => 'boolean',
        ];

        foreach ($expectedCasts as $attribute => $cast) {
            $this->assertEquals($cast, $casts[$attribute]);
        }
    }

    /** @test */
    public function it_has_featured_constants()
    {
        $expectedFeatured = [
            2 => 'All',
            1 => 'Featured',
            0 => 'Not featured',
        ];

        $this->assertEquals($expectedFeatured, JobCategory::FEATURED);
    }

    /** @test */
    public function it_can_be_instantiated_with_attributes()
    {
        $jobCategory = new JobCategory([
            'name' => 'Technology',
            'image' => 'tech.jpg',
            'is_featured' => true,
        ]);

        $this->assertEquals('Technology', $jobCategory->name);
        $this->assertEquals('tech.jpg', $jobCategory->image);
        $this->assertEquals(true, $jobCategory->is_featured);
    }

    /** @test */
    public function it_has_relationship_methods()
    {
        $jobCategory = new JobCategory();
        
        // Test that relationship methods exist
        $this->assertTrue(method_exists($jobCategory, 'jobs'));
    }

    /** @test */
    public function it_has_validation_rules()
    {
        $expectedRules = [
            'name' => 'required|max:160|unique:job_categories,name',
            'customer_image' => 'nullable|mimes:jpeg,jpg,png',
        ];

        $this->assertEquals($expectedRules, JobCategory::$rules);
    }

    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals(2, JobCategory::ALL);
        $this->assertEquals(1, JobCategory::IS_FEATURED);
        $this->assertEquals(0, JobCategory::NOT_FEATURED);
    }

    /** @test */
    public function it_has_path_constant()
    {
        $this->assertEquals('job_category', JobCategory::PATH);
    }
} 