<?php

namespace Tests\Unit\Requests\Location;

use Tests\TestCase;
use App\Http\Requests\Location\StoreStateRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

/**
 * Enhanced Unit Test for StoreStateRequest
 * Testing validation rules and authorization
 */
class StoreStateRequestTest extends TestCase
{
    use RefreshDatabase;
    
    protected User $admin;
    protected User $employer;
    protected User $candidate;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles first
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Employer']);  
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Candidate']);
        
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
        $request = new StoreStateRequest();
        $request->setUserResolver(function () {
            return $this->admin;
        });
        
        $this->assertTrue($request->authorize());
    }
    
    /** @test */
    public function employer_is_authorized(): void
    {
        $request = new StoreStateRequest();
        $request->setUserResolver(function () {
            return $this->employer;
        });
        
        $this->assertTrue($request->authorize());
    }
    
    /** @test */
    public function candidate_is_not_authorized(): void
    {
        $request = new StoreStateRequest();
        $request->setUserResolver(function () {
            return $this->candidate;
        });
        
        $this->assertFalse($request->authorize());
    }
    
    /** @test */
    public function validation_passes_with_valid_data(): void
    {
        $request = new StoreStateRequest();
        $data = [
            'name' => 'California',
            'code' => 'CA',
            'is_active' => true,
        ];
        
        $validator = Validator::make($data, $request->rules());
        
        // Create required country for state validation
        $country = \App\Models\Country::factory()->create();
        $data['country_id'] = $country->id;
        
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }
    
    /** @test */
    public function validation_fails_with_invalid_data(): void
    {
        $request = new StoreStateRequest();
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
        $request = new StoreStateRequest();
        $request->merge([
            'name' => '  Test Name  ',
            'is_active' => 'true'
        ]);
        
        $request->prepareForValidation();
        
        $this->assertEquals('Test Name', $request->input('name'));
        $this->assertTrue($request->input('is_active'));
    }
    
    /** @test */
    public function has_proper_error_messages(): void
    {
        $request = new StoreStateRequest();
        $messages = $request->messages();
        
        $this->assertIsArray($messages);
        $this->assertNotEmpty($messages);
    }
    
    /** @test */
    public function has_proper_field_attributes(): void
    {
        $request = new StoreStateRequest();
        $attributes = $request->attributes();
        
        $this->assertIsArray($attributes);
        $this->assertNotEmpty($attributes);
    }
}