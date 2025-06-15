<?php

namespace Tests\Unit\Models;

use App\Models\CustomMedia;
use App\Helpers\ValidationHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CustomMediaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanBeCreated()
    {
        $model = CustomMedia::factory()->create();

        $this->assertInstanceOf(CustomMedia::class, $model);
        $this->assertDatabaseHas('custommedias', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new CustomMedia();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
        
        // Test modern Laravel validation with actual fillable fields
        $requiredFields = ['name', 'file_name', 'mime_type', 'collection_name', 'size'];
        
        // Check that fillable contains all required fields
        foreach ($requiredFields as $field) {
            $this->assertContains($field, $fillable, "Fillable should contain required field: {$field}");
        }
        
        // Test using Arr::hasAll() - check if fillable array contains our required keys
        $fillableKeys = array_flip($fillable); // Convert to associative array for Arr::hasAll()
        $this->assertTrue(Arr::hasAll($fillableKeys, $requiredFields), 'Model should have all required fillable fields');
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new CustomMedia();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        
        // Test specific casts using modern Laravel techniques
        $expectedCasts = ['is_active', 'is_featured', 'is_processed'];
        $this->assertTrue(Arr::hasAll($casts, $expectedCasts), 'Model should have boolean casts for status fields');
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = CustomMedia::factory()->create();

        // Use only fillable attributes to avoid mass assignment exception
        $fillableAttributes = $model->getFillable();
        $newData = CustomMedia::factory()->make()->only($fillableAttributes);
        
        // Remove any attributes that don't exist in the database
        $safeData = Arr::only($newData, ['name', 'file_name', 'mime_type', 'collection_name', 'size']);
        
        $model->update($safeData);

        $this->assertDatabaseHas('custommedias', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = CustomMedia::factory()->create();
        $modelId = $model->id;

        $model->delete();

        // Since the model uses SoftDeletes, use assertSoftDeleted
        $this->assertSoftDeleted('custommedias', [
            'id' => $modelId,
        ]);
    }

    /** @test */
    public function itValidatesRequiredFieldsUsingArrHasAll()
    {
        $validData = [
            'name' => 'test-file',
            'file_name' => 'test.jpg',
            'mime_type' => 'image/jpeg',
            'collection_name' => 'uploads',
            'size' => 1024
        ];

        $this->assertTrue(CustomMedia::validateRequiredFields($validData));

        $invalidData = [
            'name' => 'test-file',
            // missing required fields
        ];

        $this->assertFalse(CustomMedia::validateRequiredFields($invalidData));
    }

    /** @test */
    public function itValidatesNestedPropertiesUsingDotNotation()
    {
        $validNestedData = [
            'custom_properties' => [
                'alt_text' => 'Alternative text',
                'caption' => 'Image caption'
            ]
        ];

        $this->assertTrue(CustomMedia::validateNestedProperties($validNestedData));

        $invalidNestedData = [
            'custom_properties' => [
                'alt_text' => 'Alternative text',
                // missing caption
            ]
        ];

        $this->assertFalse(CustomMedia::validateNestedProperties($invalidNestedData));
    }

    /** @test */
    public function itCanUseEnhancedFactoryValidation()
    {
        $this->expectNotToPerformAssertions();
        
        // This should not throw an exception
        $model = CustomMedia::factory()->withValidation()->create();
    }

    /** @test */
    public function itCanCreateSpecificMediaTypes(): void
    {
        // Test image media
        $imageMedia = CustomMedia::factory()->image()->create();
        $this->assertInstanceOf(CustomMedia::class, $imageMedia);
        $this->assertStringContainsString('image/', $imageMedia->mime_type);

        // Test document media
        $documentMedia = CustomMedia::factory()->document()->create();
        $this->assertInstanceOf(CustomMedia::class, $documentMedia);
        $this->assertStringContainsString('application/', $documentMedia->mime_type);

        // Test video media
        $videoMedia = CustomMedia::factory()->video()->create();
        $this->assertInstanceOf(CustomMedia::class, $videoMedia);
        $this->assertStringContainsString('video/', $videoMedia->mime_type);
    }
}
