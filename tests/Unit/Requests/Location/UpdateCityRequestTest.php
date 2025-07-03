<?php

namespace Tests\Unit\Requests\Location;

use App\Http\Requests\Location\UpdateCityRequest;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Enhanced Unit Test for UpdateCityRequest
 * Testing validation rules and authorization.
 *
 * @internal
 *
 * @coversNothing
 */
class UpdateCityRequestTest extends TestCase
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
    public function admin_is_authorized(): void
    {
        $request = new UpdateCityRequest;
        $request->setUserResolver(function () {
            return $this->admin;
        });

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function employer_is_authorized(): void
    {
        $request = new UpdateCityRequest;
        $request->setUserResolver(function () {
            return $this->employer;
        });

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function candidate_is_not_authorized(): void
    {
        $request = new UpdateCityRequest;
        $request->setUserResolver(function () {
            return $this->candidate;
        });

        $this->assertFalse($request->authorize());
    }

    /** @test */
    public function validation_passes_with_valid_data(): void
    {
        $request = new UpdateCityRequest;
        $data = [
            'name' => 'Los Angeles',
            'is_active' => true,
        ];

        $validator = Validator::make($data, $request->rules());

        // Create required state for city validation
        $country = Country::factory()->create();
        $state = State::factory()->create([
            'country_id' => $country->id,
            'name' => 'Test State',
            'code' => 'TS',
        ]);
        $data['state_id'] = $state->id;

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function validation_fails_with_invalid_data(): void
    {
        $request = new UpdateCityRequest;
        $data = [
            'name' => '', // Empty name should fail
        ];

        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function validation_sanitizes_data(): void
    {
        $request = new UpdateCityRequest;
        $request->merge([
            'name' => '  Test Name  ',
            'is_active' => 'true',
        ]);

        $request->prepareForValidation();

        $this->assertEquals('Test Name', $request->input('name'));
        $this->assertTrue($request->input('is_active'));
    }

    /** @test */
    public function has_proper_error_messages(): void
    {
        $request = new UpdateCityRequest;
        $messages = $request->messages();

        $this->assertIsArray($messages);
        $this->assertNotEmpty($messages);
    }

    /** @test */
    public function has_proper_field_attributes(): void
    {
        $request = new UpdateCityRequest;
        $attributes = $request->attributes();

        $this->assertIsArray($attributes);
        $this->assertNotEmpty($attributes);
    }
}
