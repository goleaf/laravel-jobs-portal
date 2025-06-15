<?php

namespace Tests\Unit\Models;

use App\Models\JobCategory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobCategoryModelTest extends TestCase
{
    /** @test */
    public function itHasCorrectTableName()
    {
        $jobCategory = new JobCategory();
        $this->assertEquals('job_categories', $jobCategory->getTable());
    }

    /** @test */
    public function itHasCorrectFillableAttributes()
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
    public function itHasCorrectCasts()
    {
        $jobCategory = new JobCategory();
        $casts = $jobCategory->getCasts();

        $expectedCasts = [
            'id' => 'int',
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
    public function itHasFeaturedConstants()
    {
        $expectedFeatured = [
            2 => 'All',
            1 => 'Featured',
            0 => 'Not featured',
        ];

        $this->assertEquals($expectedFeatured, JobCategory::FEATURED);
    }

    /** @test */
    public function itCanBeInstantiatedWithAttributes()
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
    public function itHasRelationshipMethods()
    {
        $jobCategory = new JobCategory();

        // Test that relationship methods exist
        $this->assertTrue(method_exists($jobCategory, 'jobs'));
    }

    /** @test */
    public function itHasValidationRules()
    {
        $expectedRules = [
            'name' => 'required|max:160|unique:job_categories,name',
            'customer_image' => 'nullable|mimes:jpeg,jpg,png',
        ];

        $this->assertEquals($expectedRules, JobCategory::$rules);
    }

    /** @test */
    public function itHasStatusConstants()
    {
        $this->assertEquals(2, JobCategory::ALL);
        $this->assertEquals(1, JobCategory::IS_FEATURED);
        $this->assertEquals(0, JobCategory::NOT_FEATURED);
    }

    /** @test */
    public function itHasPathConstant()
    {
        $this->assertEquals('job_category', JobCategory::PATH);
    }
}
