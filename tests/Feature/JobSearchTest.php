<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use App\Models\Company;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobSearchTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function guest_can_view_job_listing_page()
    {
        $response = $this->get(route('jobs.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('jobs.index');
    }
    
    /** @test */
    public function guest_can_view_active_jobs()
    {
        $category = Category::factory()->create();
        
        $company = Company::factory()->create(['status' => 'active']);
        
        $activeJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'status' => 'active',
            'title' => 'Active Software Developer Position'
        ]);
        
        $inactiveJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'status' => 'inactive',
            'title' => 'Inactive Software Developer Position'
        ]);
        
        $response = $this->get(route('jobs.index'));
        
        $response->assertStatus(200);
        $response->assertSee($activeJob->title);
        $response->assertDontSee($inactiveJob->title);
    }
    
    /** @test */
    public function guest_can_view_job_details()
    {
        $category = Category::factory()->create();
        $company = Company::factory()->create(['status' => 'active']);
        
        $job = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'status' => 'active',
            'title' => 'Software Engineer',
            'description' => 'This is a test job description'
        ]);
        
        $response = $this->get(route('jobs.show', $job->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('jobs.show');
        $response->assertSee($job->title);
        $response->assertSee($job->description);
        $response->assertSee($company->name);
    }
    
    /** @test */
    public function guest_cannot_view_inactive_job_details()
    {
        $category = Category::factory()->create();
        $company = Company::factory()->create(['status' => 'active']);
        
        $job = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'status' => 'inactive',
        ]);
        
        $response = $this->get(route('jobs.show', $job->id));
        
        $response->assertStatus(404);
    }
    
    /** @test */
    public function jobs_can_be_filtered_by_category()
    {
        $category1 = Category::factory()->create(['name' => 'IT']);
        $category2 = Category::factory()->create(['name' => 'Marketing']);
        
        $company = Company::factory()->create(['status' => 'active']);
        
        $itJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category1->id,
            'status' => 'active',
            'title' => 'IT Job Position'
        ]);
        
        $marketingJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category2->id,
            'status' => 'active',
            'title' => 'Marketing Job Position'
        ]);
        
        $response = $this->get(route('jobs.index', ['category' => $category1->id]));
        
        $response->assertStatus(200);
        $response->assertSee($itJob->title);
        $response->assertDontSee($marketingJob->title);
    }
    
    /** @test */
    public function jobs_can_be_filtered_by_location()
    {
        $category = Category::factory()->create();
        $company = Company::factory()->create(['status' => 'active']);
        
        $remoteJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'status' => 'active',
            'location' => 'Remote',
            'title' => 'Remote Developer Job'
        ]);
        
        $officeJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'status' => 'active',
            'location' => 'New York',
            'title' => 'Office Developer Job'
        ]);
        
        $response = $this->get(route('jobs.index', ['location' => 'Remote']));
        
        $response->assertStatus(200);
        $response->assertSee($remoteJob->title);
        $response->assertDontSee($officeJob->title);
    }
    
    /** @test */
    public function jobs_can_be_filtered_by_job_type()
    {
        $category = Category::factory()->create();
        $company = Company::factory()->create(['status' => 'active']);
        
        $fulltimeJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'status' => 'active',
            'job_type' => 'Full-time',
            'title' => 'Full-time Developer Job'
        ]);
        
        $parttimeJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'status' => 'active',
            'job_type' => 'Part-time',
            'title' => 'Part-time Developer Job'
        ]);
        
        $response = $this->get(route('jobs.index', ['job_type' => 'Full-time']));
        
        $response->assertStatus(200);
        $response->assertSee($fulltimeJob->title);
        $response->assertDontSee($parttimeJob->title);
    }
    
    /** @test */
    public function jobs_can_be_searched_by_keyword()
    {
        $category = Category::factory()->create();
        $company = Company::factory()->create(['status' => 'active']);
        
        $pythonJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'status' => 'active',
            'title' => 'Python Developer',
            'description' => 'We are looking for a skilled Python developer'
        ]);
        
        $javaJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'status' => 'active',
            'title' => 'Java Developer',
            'description' => 'We are looking for a skilled Java developer'
        ]);
        
        $response = $this->get(route('jobs.index', ['search' => 'Python']));
        
        $response->assertStatus(200);
        $response->assertSee($pythonJob->title);
        $response->assertDontSee($javaJob->title);
    }
    
    /** @test */
    public function jobs_can_be_filtered_by_multiple_criteria()
    {
        $category1 = Category::factory()->create(['name' => 'IT']);
        $category2 = Category::factory()->create(['name' => 'Engineering']);
        
        $company = Company::factory()->create(['status' => 'active']);
        
        // Job that should match our filters
        $matchingJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category1->id,
            'status' => 'active',
            'location' => 'Remote',
            'job_type' => 'Full-time',
            'title' => 'Remote Python Developer'
        ]);
        
        // Jobs that should not match our filters
        $differentCategoryJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category2->id,
            'status' => 'active',
            'location' => 'Remote',
            'job_type' => 'Full-time',
            'title' => 'Remote Python Engineer'
        ]);
        
        $differentLocationJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category1->id,
            'status' => 'active',
            'location' => 'New York',
            'job_type' => 'Full-time',
            'title' => 'Python Developer'
        ]);
        
        $differentJobTypeJob = Job::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category1->id,
            'status' => 'active',
            'location' => 'Remote',
            'job_type' => 'Part-time',
            'title' => 'Python Developer'
        ]);
        
        $response = $this->get(route('jobs.index', [
            'category' => $category1->id,
            'location' => 'Remote',
            'job_type' => 'Full-time',
            'search' => 'Python'
        ]));
        
        $response->assertStatus(200);
        $response->assertSee($matchingJob->title);
        $response->assertDontSee($differentCategoryJob->title);
        $response->assertDontSee($differentLocationJob->title);
        $response->assertDontSee($differentJobTypeJob->title);
    }
} 