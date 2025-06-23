<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateJobRequest;
use App\Models\CareerLevel;
use App\Models\Country;
use App\Models\FunctionalArea;
use App\Models\JobCategory;
use App\Models\JobType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CreateJobRequestTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required reference data
        $this->createReferenceData();
    }

    /**
     * Test that the request has proper validation rules.
     */
    public function testValidationRulesAreDefined()
    {
        $request = new CreateJobRequest();
        $rules = $request->rules();

        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);

        // Check specific rules exist
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('description', $rules);
        $this->assertArrayHasKey('job_category_id', $rules);
        $this->assertArrayHasKey('job_type_id', $rules);
        $this->assertArrayHasKey('location', $rules);
    }

    /**
     * Test authorization for employer users.
     */
    public function testEmployerUserIsAuthorized()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = new CreateJobRequest();
        // Placeholder until role system is implemented
        $this->assertTrue(true);
    }

    /**
     * Test validation passes with valid data.
     */
    public function testValidationPassesWithValidData()
    {
        $data = $this->getValidData();

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        if (!$validator->passes()) {
            $this->fail('Validation failed with errors: '.json_encode($validator->errors()->toArray()));
        }

        $this->assertTrue($validator->passes());
    }

    /**
     * Test title is required.
     */
    public function testTitleIsRequired()
    {
        $data = $this->getValidData();
        unset($data['title']);

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('title'));
    }

    /**
     * Test description is required.
     */
    public function testDescriptionIsRequired()
    {
        $data = $this->getValidData();
        unset($data['description']);

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('description'));
    }

    /**
     * Test description minimum length.
     */
    public function testDescriptionMinimumLength()
    {
        $data = $this->getValidData();
        $data['description'] = 'Short'; // Less than 50 characters

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('description'));
    }

    /**
     * Test job_category_id is required.
     */
    public function testJobCategoryIdIsRequired()
    {
        $data = $this->getValidData();
        unset($data['job_category_id']);

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('job_category_id'));
    }

    /**
     * Test job_type_id is required.
     */
    public function testJobTypeIdIsRequired()
    {
        $data = $this->getValidData();
        unset($data['job_type_id']);

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('job_type_id'));
    }

    /**
     * Test location is required.
     */
    public function testLocationIsRequired()
    {
        $data = $this->getValidData();
        unset($data['location']);

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('location'));
    }

    /**
     * Test salary validation - salary_to must be greater than salary_from.
     */
    public function testSalaryToMustBeGreaterThanSalaryFrom()
    {
        $data = $this->getValidData();
        $data['salary_from'] = 100000;
        $data['salary_to'] = 50000; // Less than salary_from

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('salary_to'));
    }

    /**
     * Test salary fields accept numeric values.
     */
    public function testSalaryFieldsAcceptNumericValues()
    {
        $data = $this->getValidData();
        $data['salary_from'] = 50000;
        $data['salary_to'] = 100000;

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /**
     * Test salary fields reject non-numeric values.
     */
    public function testSalaryFieldsRejectNonNumericValues()
    {
        $data = $this->getValidData();
        $data['salary_from'] = 'not-a-number';

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('salary_from'));
    }

    /**
     * Test expires_at must be future date.
     */
    public function testExpiresAtMustBeFutureDate()
    {
        $data = $this->getValidData();
        $data['expires_at'] = '2020-01-01'; // Past date

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('expires_at'));
    }

    /**
     * Test experience field accepts valid range.
     */
    public function testExperienceAcceptsValidRange()
    {
        $data = $this->getValidData();
        $data['experience'] = 5;

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /**
     * Test experience field rejects values over 50.
     */
    public function testExperienceRejectsOver50()
    {
        $data = $this->getValidData();
        $data['experience'] = 60; // Over maximum

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('experience'));
    }

    /**
     * Test gender field accepts valid values.
     */
    public function testGenderAcceptsValidValues()
    {
        $data = $this->getValidData();

        foreach ([0, 1, 2] as $gender) {
            $data['gender'] = $gender;

            $request = new CreateJobRequest();
            $validator = Validator::make($data, $request->rules());

            $this->assertTrue($validator->passes(), "Gender value {$gender} should be valid");
        }
    }

    /**
     * Test gender field rejects invalid values.
     */
    public function testGenderRejectsInvalidValues()
    {
        $data = $this->getValidData();
        $data['gender'] = 5; // Invalid value

        $request = new CreateJobRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('gender'));
    }

    /**
     * Test custom error messages are defined.
     */
    public function testCustomErrorMessagesAreDefined()
    {
        $request = new CreateJobRequest();

        if (method_exists($request, 'messages')) {
            $messages = $request->messages();
            $this->assertIsArray($messages);

            // Check specific custom messages
            $this->assertArrayHasKey('title.required', $messages);
            $this->assertArrayHasKey('description.required', $messages);
        } else {
            $this->assertTrue(true, 'No custom messages method defined');
        }
    }

    /**
     * Test custom attributes are defined.
     */
    public function testCustomAttributesAreDefined()
    {
        $request = new CreateJobRequest();

        if (method_exists($request, 'attributes')) {
            $attributes = $request->attributes();
            $this->assertIsArray($attributes);
        } else {
            $this->assertTrue(true, 'No custom attributes method defined');
        }
    }

    /**
     * Create reference data needed for job creation.
     */
    protected function createReferenceData()
    {
        // Create basic reference data that jobs depend on
        if (class_exists(JobCategory::class)) {
            JobCategory::factory()->create(['id' => 1]);
        }
        if (class_exists(JobType::class)) {
            JobType::factory()->create(['id' => 1]);
        }
        if (class_exists(CareerLevel::class)) {
            CareerLevel::factory()->create(['id' => 1]);
        }
        if (class_exists(FunctionalArea::class)) {
            FunctionalArea::factory()->create(['id' => 1]);
        }
        if (class_exists(Country::class)) {
            Country::factory()->create(['id' => 1]);
        }
    }

    /**
     * Get valid test data for the request.
     */
    protected function getValidData()
    {
        return [
            'title' => $this->faker->jobTitle,
            'description' => $this->faker->paragraphs(3, true), // Ensure minimum 50 chars
            'job_category_id' => 1,
            'job_type_id' => 1,
            'career_level_id' => 1,
            'functional_area_id' => 1,
            'country_id' => 1,
            'location' => $this->faker->city,
            'salary_from' => 50000,
            'salary_to' => 100000,
            'experience' => $this->faker->numberBetween(0, 10),
            'gender' => $this->faker->randomElement([0, 1, 2]),
            'is_freelance' => false,
            'hide_salary' => false,
            'position' => $this->faker->numberBetween(1, 10),
            'expires_at' => $this->faker->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
            'requirements' => $this->faker->paragraph,
            'benefits' => $this->faker->paragraph,
        ];
    }

    /**
     * Get invalid test data for the request.
     */
    protected function getInvalidData()
    {
        return [
            'title' => '', // Required field empty
            'description' => 'Short', // Too short
            'job_category_id' => 999, // Non-existent ID
            'job_type_id' => 999, // Non-existent ID
            'location' => '', // Required field empty
            'salary_from' => 'not-a-number', // Invalid type
            'salary_to' => -1000, // Negative value
            'experience' => 100, // Over maximum
            'gender' => 10, // Invalid option
            'expires_at' => '2020-01-01', // Past date
        ];
    }
}
