<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Job;
use App\Models\JobType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobTypeTest extends TestCase
{
    use RefreshDatabase;

    protected JobType $jobType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary models for foreign key relationships
        $this->createBasicModels();
    }

    /** @test */
    public function itHasCorrectFillableAttributes(): void
    {
        $fillable = [
            'name', 'description', 'is_default', 'is_active', 'sort_order',
            'icon', 'color', 'is_featured', 'meta_title', 'meta_description', 'slug',
        ];

        $jobType = new JobType();

        foreach ($fillable as $attribute) {
            $this->assertContains($attribute, $jobType->getFillable());
        }
    }

    /** @test */
    public function itCastsAttributesCorrectly(): void
    {
        $jobType = new JobType();
        $casts = $jobType->getCasts();

        $this->assertEquals('boolean', $casts['is_active']);
        $this->assertEquals('boolean', $casts['is_default']);
        $this->assertEquals('boolean', $casts['is_featured']);
        $this->assertEquals('integer', $casts['sort_order']);
        $this->assertEquals('datetime', $casts['created_at']);
        $this->assertEquals('datetime', $casts['updated_at']);
    }

    /** @test */
    public function itHasJobsRelationship(): void
    {
        $jobType = JobType::factory()->create();

        $this->assertTrue(method_exists($jobType, 'jobs'));
    }

    /** @test */
    public function activeScopeReturnsOnlyActiveJobTypes(): void
    {
        // Clear existing data and create fresh test data
        JobType::query()->delete();

        JobType::factory()->active()->count(3)->create();
        JobType::factory()->inactive()->count(2)->create();

        $activeJobTypes = JobType::active()->get();

        $this->assertCount(3, $activeJobTypes);
        $this->assertTrue($activeJobTypes->every(fn ($jobType) => $jobType->is_active));
    }

    /** @test */
    public function inactiveScopeReturnsOnlyInactiveJobTypes(): void
    {
        JobType::factory()->active()->count(3)->create();
        JobType::factory()->inactive()->count(2)->create();

        $inactiveJobTypes = JobType::inactive()->get();

        $this->assertGreaterThanOrEqual(2, $inactiveJobTypes->count());
        $this->assertTrue($inactiveJobTypes->every(fn ($jobType) => !$jobType->is_active));
    }

    /** @test */
    public function defaultScopeReturnsOnlyDefaultJobTypes(): void
    {
        JobType::factory()->default()->count(2)->create();
        JobType::factory()->custom()->count(3)->create();

        $defaultJobTypes = JobType::default()->get();

        $this->assertGreaterThanOrEqual(2, $defaultJobTypes->count());
        $this->assertTrue($defaultJobTypes->every(fn ($jobType) => $jobType->is_default));
    }

    /** @test */
    public function customScopeReturnsOnlyCustomJobTypes(): void
    {
        JobType::factory()->default()->count(2)->create();
        JobType::factory()->custom()->count(3)->create();

        $customJobTypes = JobType::custom()->get();

        $this->assertGreaterThanOrEqual(3, $customJobTypes->count());
        $this->assertTrue($customJobTypes->every(fn ($jobType) => !$jobType->is_default));
    }

    /** @test */
    public function withJobsScopeReturnsJobTypesWithJobs(): void
    {
        $jobTypeWithJobs = JobType::factory()->create();
        $jobTypeWithoutJobs = JobType::factory()->create();

        // Assuming Job model exists and has job_type_id
        Job::factory()->create(['job_type_id' => $jobTypeWithJobs->id]);

        $jobTypesWithJobs = JobType::withJobs()->get();

        $this->assertTrue($jobTypesWithJobs->contains('id', $jobTypeWithJobs->id));
        $this->assertFalse($jobTypesWithJobs->contains('id', $jobTypeWithoutJobs->id));
    }

    /** @test */
    public function searchScopeFindsJobTypesByNameAndDescription(): void
    {
        $searchableJobType = JobType::factory()->create([
            'name' => 'Full-Time Developer',
            'description' => 'Looking for experienced software developer',
        ]);

        $nonSearchableJobType = JobType::factory()->create([
            'name' => 'Manager',
            'description' => 'Management position',
        ]);

        $foundByName = JobType::search('Developer')->get();
        $foundByDescription = JobType::search('software')->get();
        $notFound = JobType::search('Marketing')->get();

        $this->assertTrue($foundByName->contains('id', $searchableJobType->id));
        $this->assertTrue($foundByDescription->contains('id', $searchableJobType->id));
        $this->assertFalse($notFound->contains('id', $searchableJobType->id));
    }

    /** @test */
    public function recentScopeReturnsRecentlyCreatedJobTypes(): void
    {
        $recentJobType = JobType::factory()->create(['created_at' => now()->subDays(10)]);
        $oldJobType = JobType::factory()->create(['created_at' => now()->subDays(40)]);

        $recentJobTypes = JobType::recent(30)->get();

        $this->assertTrue($recentJobTypes->contains('id', $recentJobType->id));
        $this->assertFalse($recentJobTypes->contains('id', $oldJobType->id));
    }

    /** @test */
    public function popularScopeReturnsJobTypesOrderedByJobCount(): void
    {
        $popularJobType = JobType::factory()->create();
        $unpopularJobType = JobType::factory()->create();

        // Create more jobs for popular job type
        Job::factory()->count(5)->create(['job_type_id' => $popularJobType->id]);
        Job::factory()->count(2)->create(['job_type_id' => $unpopularJobType->id]);

        $popularJobTypes = JobType::popular(10)->get();

        $this->assertEquals($popularJobType->id, $popularJobTypes->first()->id);
    }

    /** @test */
    public function alphabeticalScopeOrdersByName(): void
    {
        JobType::factory()->create(['name' => 'Zebra']);
        JobType::factory()->create(['name' => 'Alpha']);
        JobType::factory()->create(['name' => 'Beta']);

        $alphabeticalJobTypes = JobType::alphabetical()->get();

        $this->assertEquals('Alpha', $alphabeticalJobTypes->first()->name);
        $this->assertEquals('Zebra', $alphabeticalJobTypes->last()->name);
    }

    /** @test */
    public function fullTimeScopeFindsFullTimeJobTypes(): void
    {
        JobType::factory()->fullTime()->create();
        JobType::factory()->create(['name' => 'Part-Time']);

        $fullTimeJobTypes = JobType::fullTime()->get();

        $this->assertGreaterThan(0, $fullTimeJobTypes->count());
        $this->assertTrue($fullTimeJobTypes->every(function ($jobType) {
            return false !== stripos($jobType->name, 'full') && false !== stripos($jobType->name, 'time');
        }));
    }

    /** @test */
    public function partTimeScopeFindsPartTimeJobTypes(): void
    {
        JobType::factory()->partTime()->create();
        JobType::factory()->create(['name' => 'Full-Time']);

        $partTimeJobTypes = JobType::partTime()->get();

        $this->assertGreaterThan(0, $partTimeJobTypes->count());
        $this->assertTrue($partTimeJobTypes->every(function ($jobType) {
            return false !== stripos($jobType->name, 'part') && false !== stripos($jobType->name, 'time');
        }));
    }

    /** @test */
    public function remoteScopeFindsRemoteJobTypes(): void
    {
        JobType::factory()->remote()->create();
        JobType::factory()->create(['name' => 'Work From Home']);
        JobType::factory()->create(['name' => 'On-Site']);

        $remoteJobTypes = JobType::remote()->get();

        $this->assertGreaterThan(0, $remoteJobTypes->count());
    }

    /** @test */
    public function usageCountAttributeReturnsJobCount(): void
    {
        $jobType = JobType::factory()->create();
        Job::factory()->count(3)->create(['job_type_id' => $jobType->id]);

        $this->assertEquals(3, $jobType->usage_count);
    }

    /** @test */
    public function formattedUsageStatsAttributeReturnsCorrectData(): void
    {
        $jobType = JobType::factory()->create();
        Job::factory()->count(5)->create(['job_type_id' => $jobType->id]);
        Job::factory()->count(3)->create(['job_type_id' => $jobType->id, 'is_active' => true]);

        $stats = $jobType->formatted_usage_stats;

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('jobs', $stats);
        $this->assertArrayHasKey('active_jobs', $stats);
        $this->assertArrayHasKey('total_usage', $stats);
        $this->assertArrayHasKey('demand_level', $stats);
        $this->assertEquals(5, $stats['jobs']);
    }

    /** @test */
    public function isHighDemandMethodReturnsCorrectBoolean(): void
    {
        $highDemandJobType = JobType::factory()->create();
        $lowDemandJobType = JobType::factory()->create();

        Job::factory()->count(60)->create(['job_type_id' => $highDemandJobType->id]);
        Job::factory()->count(5)->create(['job_type_id' => $lowDemandJobType->id]);

        $this->assertTrue($highDemandJobType->isHighDemand());
        $this->assertFalse($lowDemandJobType->isHighDemand());
    }

    /** @test */
    public function isFullTimeMethodReturnsCorrectBoolean(): void
    {
        $fullTimeJobType = JobType::factory()->create(['name' => 'Full-Time Position']);
        $partTimeJobType = JobType::factory()->create(['name' => 'Part-Time Position']);

        $this->assertTrue($fullTimeJobType->isFullTime());
        $this->assertFalse($partTimeJobType->isFullTime());
    }

    /** @test */
    public function isPartTimeMethodReturnsCorrectBoolean(): void
    {
        $partTimeJobType = JobType::factory()->create(['name' => 'Part-Time Position']);
        $fullTimeJobType = JobType::factory()->create(['name' => 'Full-Time Position']);

        $this->assertTrue($partTimeJobType->isPartTime());
        $this->assertFalse($fullTimeJobType->isPartTime());
    }

    /** @test */
    public function isRemoteMethodReturnsCorrectBoolean(): void
    {
        $remoteJobType = JobType::factory()->create(['name' => 'Remote Work']);
        $workFromHomeJobType = JobType::factory()->create(['name' => 'Work From Home']);
        $onsiteJobType = JobType::factory()->create(['name' => 'On-Site Position']);

        $this->assertTrue($remoteJobType->isRemote());
        $this->assertTrue($workFromHomeJobType->isRemote());
        $this->assertFalse($onsiteJobType->isRemote());
    }

    /** @test */
    public function getRelatedTypesReturnsRelatedJobTypes(): void
    {
        $jobType = JobType::factory()->create();
        $relatedJobTypes = JobType::factory()->count(10)->create();

        foreach ($relatedJobTypes as $index => $relatedJobType) {
            Job::factory()->count($index + 1)->create(['job_type_id' => $relatedJobType->id]);
        }

        $related = $jobType->getRelatedTypes(5);

        $this->assertCount(5, $related);
        $this->assertFalse($related->contains('id', $jobType->id)); // Shouldn't include itself
    }

    /** @test */
    public function cacheIsClearedWhenJobTypeIsUpdated(): void
    {
        $jobType = JobType::factory()->create();
        $cacheKey = "job_type.{$jobType->id}";

        Cache::put($cacheKey, 'test_value', 3600);
        $this->assertEquals('test_value', Cache::get($cacheKey));

        $jobType->update(['name' => 'Updated Name']);

        $this->assertNull(Cache::get($cacheKey));
    }

    /** @test */
    public function cacheIsClearedWhenJobTypeIsDeleted(): void
    {
        $jobType = JobType::factory()->create();
        $cacheKey = "job_type.{$jobType->id}";

        Cache::put($cacheKey, 'test_value', 3600);
        $this->assertEquals('test_value', Cache::get($cacheKey));

        $jobType->delete();

        $this->assertNull(Cache::get($cacheKey));
    }

    /** @test */
    public function activityLoggingIsConfigured(): void
    {
        $this->assertTrue(in_array(LogsActivity::class, class_uses($this->jobType)));
    }

    private function createBasicModels(): void
    {
        // Create a basic company without slug first, then add slug
        $company = new Company();
        $company->fill([
            'user_id' => User::factory()->create()->id,
            'ceo' => 'Test CEO',
            'no_of_offices' => 1,
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => 2020,
            'details' => 'Test company',
            'website' => 'https://example.com',
            'location' => 'Test Location',
            'is_featured' => false,
            'fax' => '123-456-7890',
            'facebook_url' => 'https://facebook.com/test',
            'twitter_url' => 'https://twitter.com/test',
            'linkedin_url' => 'https://linkedin.com/test',
            'google_plus_url' => 'https://plus.google.com/test',
            'pinterest_url' => 'https://pinterest.com/test',
            'unique_id' => 'TEST123',
            'slug' => 'test-company',
        ]);
        $company->save();
    }
}
