<?php

namespace Tests\Unit\Models;

use App\Models\JobType;
use App\Models\Job;
use Database\Factories\JobTypeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class JobTypeTest extends TestCase
{
    use RefreshDatabase;

    protected JobType $jobType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jobType = JobType::factory()->create();
    }

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'description',
            'is_default',
            'is_active',
            'sort_order',
            'icon',
            'color',
            'is_featured',
            'meta_title',
            'meta_description',
            'slug',
        ];

        $this->assertEquals($fillable, $this->jobType->getFillable());
    }

    /** @test */
    public function it_casts_attributes_correctly(): void
    {
        $casts = [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];

        foreach ($casts as $attribute => $cast) {
            $this->assertEquals($cast, $this->jobType->getCasts()[$attribute]);
        }
    }

    /** @test */
    public function it_has_jobs_relationship(): void
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->jobType->jobs());
    }

    /** @test */
    public function active_scope_returns_only_active_job_types(): void
    {
        JobType::factory()->active()->count(3)->create();
        JobType::factory()->inactive()->count(2)->create();

        $activeJobTypes = JobType::active()->get();

        $this->assertCount(4, $activeJobTypes); // 3 created + 1 from setUp (random)
        $this->assertTrue($activeJobTypes->every(fn($jobType) => $jobType->is_active));
    }

    /** @test */
    public function inactive_scope_returns_only_inactive_job_types(): void
    {
        JobType::factory()->active()->count(3)->create();
        JobType::factory()->inactive()->count(2)->create();

        $inactiveJobTypes = JobType::inactive()->get();

        $this->assertGreaterThanOrEqual(2, $inactiveJobTypes->count());
        $this->assertTrue($inactiveJobTypes->every(fn($jobType) => !$jobType->is_active));
    }

    /** @test */
    public function default_scope_returns_only_default_job_types(): void
    {
        JobType::factory()->default()->count(2)->create();
        JobType::factory()->custom()->count(3)->create();

        $defaultJobTypes = JobType::default()->get();

        $this->assertGreaterThanOrEqual(2, $defaultJobTypes->count());
        $this->assertTrue($defaultJobTypes->every(fn($jobType) => $jobType->is_default));
    }

    /** @test */
    public function custom_scope_returns_only_custom_job_types(): void
    {
        JobType::factory()->default()->count(2)->create();
        JobType::factory()->custom()->count(3)->create();

        $customJobTypes = JobType::custom()->get();

        $this->assertGreaterThanOrEqual(3, $customJobTypes->count());
        $this->assertTrue($customJobTypes->every(fn($jobType) => !$jobType->is_default));
    }

    /** @test */
    public function with_jobs_scope_returns_job_types_with_jobs(): void
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
    public function search_scope_finds_job_types_by_name_and_description(): void
    {
        $searchableJobType = JobType::factory()->create([
            'name' => 'Full-Time Developer',
            'description' => 'Looking for experienced software developer'
        ]);
        
        $nonSearchableJobType = JobType::factory()->create([
            'name' => 'Manager',
            'description' => 'Management position'
        ]);

        $foundByName = JobType::search('Developer')->get();
        $foundByDescription = JobType::search('software')->get();
        $notFound = JobType::search('Marketing')->get();

        $this->assertTrue($foundByName->contains('id', $searchableJobType->id));
        $this->assertTrue($foundByDescription->contains('id', $searchableJobType->id));
        $this->assertFalse($notFound->contains('id', $searchableJobType->id));
    }

    /** @test */
    public function recent_scope_returns_recently_created_job_types(): void
    {
        $recentJobType = JobType::factory()->create(['created_at' => now()->subDays(10)]);
        $oldJobType = JobType::factory()->create(['created_at' => now()->subDays(40)]);

        $recentJobTypes = JobType::recent(30)->get();

        $this->assertTrue($recentJobTypes->contains('id', $recentJobType->id));
        $this->assertFalse($recentJobTypes->contains('id', $oldJobType->id));
    }

    /** @test */
    public function popular_scope_returns_job_types_ordered_by_job_count(): void
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
    public function alphabetical_scope_orders_by_name(): void
    {
        JobType::factory()->create(['name' => 'Zebra']);
        JobType::factory()->create(['name' => 'Alpha']);
        JobType::factory()->create(['name' => 'Beta']);

        $alphabeticalJobTypes = JobType::alphabetical()->get();

        $this->assertEquals('Alpha', $alphabeticalJobTypes->first()->name);
        $this->assertEquals('Zebra', $alphabeticalJobTypes->last()->name);
    }

    /** @test */
    public function full_time_scope_finds_full_time_job_types(): void
    {
        JobType::factory()->fullTime()->create();
        JobType::factory()->create(['name' => 'Part-Time']);

        $fullTimeJobTypes = JobType::fullTime()->get();

        $this->assertGreaterThan(0, $fullTimeJobTypes->count());
        $this->assertTrue($fullTimeJobTypes->every(function ($jobType) {
            return stripos($jobType->name, 'full') !== false && stripos($jobType->name, 'time') !== false;
        }));
    }

    /** @test */
    public function part_time_scope_finds_part_time_job_types(): void
    {
        JobType::factory()->partTime()->create();
        JobType::factory()->create(['name' => 'Full-Time']);

        $partTimeJobTypes = JobType::partTime()->get();

        $this->assertGreaterThan(0, $partTimeJobTypes->count());
        $this->assertTrue($partTimeJobTypes->every(function ($jobType) {
            return stripos($jobType->name, 'part') !== false && stripos($jobType->name, 'time') !== false;
        }));
    }

    /** @test */
    public function remote_scope_finds_remote_job_types(): void
    {
        JobType::factory()->remote()->create();
        JobType::factory()->create(['name' => 'Work From Home']);
        JobType::factory()->create(['name' => 'On-Site']);

        $remoteJobTypes = JobType::remote()->get();

        $this->assertGreaterThan(0, $remoteJobTypes->count());
    }

    /** @test */
    public function usage_count_attribute_returns_job_count(): void
    {
        $jobType = JobType::factory()->create();
        Job::factory()->count(3)->create(['job_type_id' => $jobType->id]);

        $this->assertEquals(3, $jobType->usage_count);
    }

    /** @test */
    public function formatted_usage_stats_attribute_returns_correct_data(): void
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
    public function is_high_demand_method_returns_correct_boolean(): void
    {
        $highDemandJobType = JobType::factory()->create();
        $lowDemandJobType = JobType::factory()->create();

        Job::factory()->count(60)->create(['job_type_id' => $highDemandJobType->id]);
        Job::factory()->count(5)->create(['job_type_id' => $lowDemandJobType->id]);

        $this->assertTrue($highDemandJobType->isHighDemand());
        $this->assertFalse($lowDemandJobType->isHighDemand());
    }

    /** @test */
    public function is_full_time_method_returns_correct_boolean(): void
    {
        $fullTimeJobType = JobType::factory()->create(['name' => 'Full-Time Position']);
        $partTimeJobType = JobType::factory()->create(['name' => 'Part-Time Position']);

        $this->assertTrue($fullTimeJobType->isFullTime());
        $this->assertFalse($partTimeJobType->isFullTime());
    }

    /** @test */
    public function is_part_time_method_returns_correct_boolean(): void
    {
        $partTimeJobType = JobType::factory()->create(['name' => 'Part-Time Position']);
        $fullTimeJobType = JobType::factory()->create(['name' => 'Full-Time Position']);

        $this->assertTrue($partTimeJobType->isPartTime());
        $this->assertFalse($fullTimeJobType->isPartTime());
    }

    /** @test */
    public function is_remote_method_returns_correct_boolean(): void
    {
        $remoteJobType = JobType::factory()->create(['name' => 'Remote Work']);
        $workFromHomeJobType = JobType::factory()->create(['name' => 'Work From Home']);
        $onsiteJobType = JobType::factory()->create(['name' => 'On-Site Position']);

        $this->assertTrue($remoteJobType->isRemote());
        $this->assertTrue($workFromHomeJobType->isRemote());
        $this->assertFalse($onsiteJobType->isRemote());
    }

    /** @test */
    public function get_related_types_returns_related_job_types(): void
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
    public function cache_is_cleared_when_job_type_is_updated(): void
    {
        $jobType = JobType::factory()->create();
        $cacheKey = "job_type.{$jobType->id}";
        
        Cache::put($cacheKey, 'test_value', 3600);
        $this->assertEquals('test_value', Cache::get($cacheKey));

        $jobType->update(['name' => 'Updated Name']);

        $this->assertNull(Cache::get($cacheKey));
    }

    /** @test */
    public function cache_is_cleared_when_job_type_is_deleted(): void
    {
        $jobType = JobType::factory()->create();
        $cacheKey = "job_type.{$jobType->id}";
        
        Cache::put($cacheKey, 'test_value', 3600);
        $this->assertEquals('test_value', Cache::get($cacheKey));

        $jobType->delete();

        $this->assertNull(Cache::get($cacheKey));
    }

    /** @test */
    public function activity_logging_is_configured(): void
    {
        $this->assertTrue(in_array(\Spatie\Activitylog\Traits\LogsActivity::class, class_uses($this->jobType)));
    }
}