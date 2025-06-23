<?php

namespace Tests\Unit\Requests\Location;

use App\Http\Requests\Location\DeleteStateRequest;
use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Enhanced Unit Test for DeleteStateRequest
 * Testing validation rules and authorization.
 *
 * @internal
 *
 * @coversNothing
 */
class DeleteStateRequestTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $employer;
    protected User $candidate;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles first
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Employer']);
        Role::firstOrCreate(['name' => 'Candidate']);

        // Create users and assign roles
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->employer = User::factory()->create();
        $this->employer->assignRole('Employer');

        $this->candidate = User::factory()->create();
        $this->candidate->assignRole('Candidate');
    }

    /** @test */
    public function adminIsAuthorized(): void
    {
        $request = new DeleteStateRequest();
        $request->setUserResolver(function () {
            return $this->admin;
        });

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function employerIsAuthorized(): void
    {
        $request = new DeleteStateRequest();
        $request->setUserResolver(function () {
            return $this->employer;
        });

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function candidateIsNotAuthorized(): void
    {
        $request = new DeleteStateRequest();
        $request->setUserResolver(function () {
            return $this->candidate;
        });

        $this->assertFalse($request->authorize());
    }

    /** @test */
    public function validationPassesWithValidData(): void
    {
        $request = new DeleteStateRequest();
        $data = [
            'name' => 'California',
            'code' => 'CA',
            'is_active' => true,
        ];

        $validator = Validator::make($data, $request->rules());

        // Create required country for state validation
        $country = Country::factory()->create();
        $data['country_id'] = $country->id;

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function validationFailsWithInvalidData(): void
    {
        $request = new DeleteStateRequest();
        $data = [
            'name' => '', // Empty name should fail
        ];

        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function validationSanitizesData(): void
    {
        $request = new DeleteStateRequest();
        $request->merge([
            'name' => '  Test Name  ',
            'is_active' => 'true',
        ]);

        $request->prepareForValidation();

        $this->assertEquals('Test Name', $request->input('name'));
        $this->assertTrue($request->input('is_active'));
    }

    /** @test */
    public function hasProperErrorMessages(): void
    {
        $request = new DeleteStateRequest();
        $messages = $request->messages();

        $this->assertIsArray($messages);
        $this->assertNotEmpty($messages);
    }

    /** @test */
    public function hasProperFieldAttributes(): void
    {
        $request = new DeleteStateRequest();
        $attributes = $request->attributes();

        $this->assertIsArray($attributes);
        $this->assertNotEmpty($attributes);
    }
}
