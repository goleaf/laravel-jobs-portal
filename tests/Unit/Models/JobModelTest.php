<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Job;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_belongs_to_user(): void
    {
        $user = $this->createTestUser();
        $job = $this->createTestJob(['user_id' => $user->id]);
        
        $this->assertInstanceOf(User::class, $job->user);
        $this->assertEquals($user->id, $job->user->id);
    }

    public function test_job_belongs_to_company(): void
    {
        $user = $this->createTestUser();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $job = $this->createTestJob([
            'user_id' => $user->id,
            'company_id' => $company->id
        ]);
        
        $this->assertInstanceOf(Company::class, $job->company);
        $this->assertEquals($company->id, $job->company->id);
    }

    public function test_job_has_correct_fillable_attributes(): void
    {
        $job = new Job();
        $fillable = $job->getFillable();
        
        $expectedFillable = [
            'title', 'description', 'benefits', 'skills', 'experience',
            'career_level_id', 'job_type_id', 'job_category_id',
            'job_shift_id', 'num_of_positions', 'gender', 'expires_on',
            'salary_from', 'salary_to', 'salary_currency_id', 'salary_period_id',
            'functional_area_id', 'degree_level_id', 'position', 'company_id',
            'country_id', 'state_id', 'city_id', 'is_freelance', 'is_suspended',
            'status', 'is_featured', 'user_id'
        ];
        
        foreach ($expectedFillable as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_job_has_correct_casts(): void
    {
        $job = new Job();
        $casts = $job->getCasts();
        
        $this->assertEquals('date', $casts['expires_on']);
        $this->assertEquals('boolean', $casts['is_freelance']);
        $this->assertEquals('boolean', $casts['is_suspended']);
        $this->assertEquals('boolean', $casts['is_featured']);
    }

    public function test_job_can_be_created_with_required_attributes(): void
    {
        $user = $this->createTestUser();
        
        $jobData = [
            'title' => 'Software Developer',
            'description' => 'We are looking for a skilled developer',
            'user_id' => $user->id,
            'expires_on' => now()->addDays(30),
        ];
        
        $job = Job::create($jobData);
        
        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals('Software Developer', $job->title);
        $this->assertEquals($user->id, $job->user_id);
    }
}