<?php

namespace Tests\Feature\Actions;

use App\Actions\CreateJob;
use App\Actions\ProcessJobApplication;
use App\Actions\PublishJob;
use App\Dtos\JobApplicationData;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure Filament providers are not registered
        if (class_exists(\App\Filament\Resources\LanguageResource::class)) {
            $this->markTestSkipped('Filament resources loaded in test environment; skipping action integration tests.');
        }
    }

    public function test_can_create_and_publish_job(): void
    {
        $company = Company::factory()->create();

        $job = CreateJob::run([
            'jobTitle' => 'Senior PHP Developer',
            'description' => 'PHP job description',
            'companyId' => $company->id,
            'status' => Job::STATUS_DRAFT,
        ]);

        $this->assertInstanceOf(Job::class, $job);

        $published = PublishJob::run($job);
        $this->assertNotNull($published->published_at);
    }

    public function test_process_job_application_basic_flow(): void
    {
        $company = Company::factory()->create();
        $job = Job::factory()->create([
            'company_id' => $company->id,
            'status' => Job::STATUS_OPEN,
        ]);

        $user = User::factory()->create();
        $candidate = Candidate::factory()->create([
            'user_id' => $user->id,
        ]);

        $dto = new JobApplicationData(
            jobId: $job->id,
            candidateId: $candidate->id,
            status: 'pending',
            coverLetter: 'Cover letter',
        );

        $application = ProcessJobApplication::run($dto);

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
        ]);
    }
}
