<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DatabaseModelValidationTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function user_model_relationships_work()
    {
        $user = \App\Models\User::factory()->create();
        
        // Test relationships exist
        $this->assertTrue(method_exists($user, 'jobs'));
        $this->assertTrue(method_exists($user, 'companies'));
        $this->assertTrue(method_exists($user, 'candidates'));
    }
    
    /** @test */
    public function job_model_relationships_work()
    {
        if (class_exists('\App\Models\Job')) {
            $job = \App\Models\Job::factory()->create();
            
            $this->assertTrue(method_exists($job, 'user'));
            $this->assertTrue(method_exists($job, 'company'));
            $this->assertTrue(method_exists($job, 'applications'));
        }
    }
    
    /** @test */
    public function company_model_relationships_work()
    {
        if (class_exists('\App\Models\Company')) {
            $company = \App\Models\Company::factory()->create();
            
            $this->assertTrue(method_exists($company, 'user'));
            $this->assertTrue(method_exists($company, 'jobs'));
        }
    }
    
    /** @test */
    public function required_tables_exist()
    {
        $requiredTables = [
            'users',
            'password_resets',
            'failed_jobs',
            'personal_access_tokens',
        ];
        
        foreach ($requiredTables as $table) {
            $this->assertTrue(Schema::hasTable($table), 
                "Required table '$table' does not exist");
        }
    }
    
    /** @test */
    public function user_table_has_required_columns()
    {
        $requiredColumns = [
            'id',
            'name',
            'email',
            'password',
            'created_at',
            'updated_at',
        ];
        
        foreach ($requiredColumns as $column) {
            $this->assertTrue(Schema::hasColumn('users', $column),
                "Users table missing required column '$column'");
        }
    }
    
    /** @test */
    public function factories_work_correctly()
    {
        $user = \App\Models\User::factory()->create();
        $this->assertInstanceOf(\App\Models\User::class, $user);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
    }
    
    /** @test */
    public function models_use_proper_fillable_attributes()
    {
        $user = new \App\Models\User();
        $fillable = $user->getFillable();
        
        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertNotContains('password', $fillable); // Should be in hidden
    }
}