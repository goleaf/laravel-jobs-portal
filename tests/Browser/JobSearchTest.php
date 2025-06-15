<?php

namespace Tests\Browser;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobSearchTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function visitorCanSearchForJobsOnHomepage()
    {
        $category = JobCategory::factory()->create(['name' => 'Web Development']);
        $jobType = JobType::factory()->create(['name' => 'Full Time']);

        // Create several jobs with different titles
        Job::factory()->create([
            'job_title' => 'Senior PHP Developer',
            'job_category_id' => $category->id,
            'job_type_id' => $jobType->id,
            'status' => Job::STATUS_OPEN,
        ]);

        Job::factory()->create([
            'job_title' => 'Junior PHP Developer',
            'job_category_id' => $category->id,
            'job_type_id' => $jobType->id,
            'status' => Job::STATUS_OPEN,
        ]);

        Job::factory()->create([
            'job_title' => 'Java Developer',
            'job_category_id' => $category->id,
            'job_type_id' => $jobType->id,
            'status' => Job::STATUS_OPEN,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Find your dream job')
                ->type('search', 'PHP')
                ->select('category', 'Web Development')
                ->select('job_type', 'Full Time')
                ->press('Search')
                ->assertSee('Senior PHP Developer')
                ->assertSee('Junior PHP Developer')
                ->assertDontSee('Java Developer')
            ;
        });
    }

    /** @test */
    public function visitorCanViewJobDetails()
    {
        $job = Job::factory()->create([
            'job_title' => 'Software Engineer',
            'status' => Job::STATUS_OPEN,
            'description' => 'We are looking for an experienced software engineer',
            'no_preference' => true,
        ]);

        $this->browse(function (Browser $browser) use ($job) {
            $browser->visit('/jobs/'.$job->id)
                ->assertSee('Software Engineer')
                ->assertSee('We are looking for an experienced software engineer')
                ->assertSee('Apply Now')
            ;
        });
    }

    /** @test */
    public function visitorCanBrowseJobsByCategory()
    {
        $category1 = JobCategory::factory()->create(['name' => 'Web Development']);
        $category2 = JobCategory::factory()->create(['name' => 'Mobile Development']);

        Job::factory()->create([
            'job_title' => 'Web Developer',
            'job_category_id' => $category1->id,
            'status' => Job::STATUS_OPEN,
        ]);

        Job::factory()->create([
            'job_title' => 'Mobile App Developer',
            'job_category_id' => $category2->id,
            'status' => Job::STATUS_OPEN,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/jobs')
                ->assertSee('Web Developer')
                ->assertSee('Mobile App Developer')
                ->click('.category-filters a[data-id="Web Development"]')
                ->waitForText('Web Developer')
                ->assertSee('Web Developer')
                ->assertDontSee('Mobile App Developer')
            ;
        });
    }

    /** @test */
    public function candidateCanApplyForJob()
    {
        $user = User::factory()->create([
            'email' => 'candidate@example.com',
            'password' => bcrypt('password123'),
            'user_type' => User::CANDIDATE,
            'is_active' => true,
        ]);

        $candidate = Candidate::factory()->create(['user_id' => $user->id]);

        $job = Job::factory()->create([
            'job_title' => 'PHP Developer',
            'status' => Job::STATUS_OPEN,
        ]);

        $this->browse(function (Browser $browser) use ($job) {
            $browser->visit('/login')
                ->type('email', 'candidate@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->assertPathIs('/dashboard')
                ->visit('/jobs/'.$job->id)
                ->assertSee('PHP Developer')
                ->assertSee('Apply Now')
                ->click('.apply-job-btn')
                ->waitForText('Job Application')
                ->assertSee('Job Application')
                ->type('cover_letter', 'I am interested in this job position')
                ->attach('resume', __DIR__.'/files/resume.pdf')
                ->press('Submit Application')
                ->waitForText('Your application has been submitted successfully')
                ->assertSee('Your application has been submitted successfully')
            ;
        });
    }

    /** @test */
    public function employerCanPostANewJob()
    {
        $user = User::factory()->create([
            'email' => 'employer@example.com',
            'password' => bcrypt('password123'),
            'user_type' => User::EMPLOYER,
            'is_active' => true,
        ]);

        $company = Company::factory()->create(['user_id' => $user->id]);

        $category = JobCategory::factory()->create();
        $jobType = JobType::factory()->create();

        $this->browse(function (Browser $browser) use ($category, $jobType) {
            $browser->visit('/login')
                ->type('email', 'employer@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->assertPathIs('/dashboard')
                ->visit('/employer/jobs/create')
                ->assertSee('Create Job')
                ->type('job_title', 'Senior Developer')
                ->select('job_category_id', $category->id)
                ->select('job_type_id', $jobType->id)
                ->type('position', '3')
                ->check('no_preference')
                ->type('description', 'We are looking for a senior developer')
                ->check('hide_salary')
                ->type('salary_from', '50000')
                ->type('salary_to', '70000')
                ->press('Save')
                ->waitForText('Job created successfully')
                ->assertSee('Job created successfully')
            ;
        });
    }

    /** @test */
    public function candidateCanViewAppliedJobs()
    {
        $user = User::factory()->create([
            'email' => 'candidate@example.com',
            'password' => bcrypt('password123'),
            'user_type' => User::CANDIDATE,
            'is_active' => true,
        ]);

        $candidate = Candidate::factory()->create(['user_id' => $user->id]);

        $job = Job::factory()->create([
            'job_title' => 'UI/UX Designer',
            'status' => Job::STATUS_OPEN,
        ]);

        // Create an application for this candidate
        $candidate->jobApplications()->create([
            'job_id' => $job->id,
            'status' => 'applied',
            'cover_letter' => 'I am interested in this position',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'candidate@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->assertPathIs('/dashboard')
                ->visit('/candidate/applied-jobs')
                ->assertSee('Applied Jobs')
                ->assertSee('UI/UX Designer');
        });
    }
}
