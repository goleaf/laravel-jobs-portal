<?php

namespace Tests\Unit\Requests\Location;

use App\Http\Requests\Location\UpdateCountryRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Enhanced Unit Test for UpdateCountryRequest
 * Testing validation rules and authorization.
 *
 * @internal
 *
 * @coversNothing
 */
class UpdateCountryRequestTest extends TestCase
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
        $request = new UpdateCountryRequest();
        $request->setUserResolver(function () {
            return $this->admin;
        });

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function employerIsAuthorized(): void
    {
        $request = new UpdateCountryRequest();
        $request->setUserResolver(function () {
            return $this->employer;
        });

        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function candidateIsNotAuthorized(): void
    {
        $request = new UpdateCountryRequest();
        $request->setUserResolver(function () {
            return $this->candidate;
        });

        $this->assertFalse($request->authorize());
    }

    /** @test */
    public function validationPassesWithValidData(): void
    {
        $request = new UpdateCountryRequest();
        $data = [
            'name' => 'United States',
            'code' => 'US',
            'phone_code' => '+1',
            'is_active' => true,
        ];

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function validationFailsWithInvalidData(): void
    {
        $request = new UpdateCountryRequest();
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
        $request = new UpdateCountryRequest();
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
        $request = new UpdateCountryRequest();
        $messages = $request->messages();

        $this->assertIsArray($messages);
        $this->assertNotEmpty($messages);
    }

    /** @test */
    public function hasProperFieldAttributes(): void
    {
        $request = new UpdateCountryRequest();
        $attributes = $request->attributes();

        $this->assertIsArray($attributes);
        $this->assertNotEmpty($attributes);
    }
}
