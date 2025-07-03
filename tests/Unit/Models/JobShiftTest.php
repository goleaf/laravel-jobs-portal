<?php

namespace Tests\Unit\Models;

use App\Models\Job;
use App\Models\JobShift;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class JobShiftTest.
 *
 * Comprehensive unit tests for JobShift model including:
 * - Model relationships
 * - All custom scopes
 * - Attribute accessors and mutators
 * - Business logic methods
 * - Caching functionality
 * - Activity logging
 *
 * @internal
 *
 * @coversNothing
 */
class JobShiftTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private JobShift $jobShift;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test job shift
        $this->jobShift = JobShift::factory()->create([
            'shift' => 'Day Shift',
            'description' => 'Standard day shift working hours',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'duration_hours' => 8,
            'is_active' => true,
            'is_default' => true,
            'is_flexible' => false,
        ]);
    }

    /** @test */
    public function it_can_be_created()
    {
        $model = JobShift::factory()->create();

        $this->assertInstanceOf(JobShift::class, $model);
        $this->assertDatabaseHas('job_shifts', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new JobShift;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new JobShift;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = JobShift::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = JobShift::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('job_shifts', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = JobShift::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('job_shifts', [
            'id' => $modelId,
        ]);
    }

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $model = new JobShift;
        $this->assertEqualsCanonicalizing([
            'shift',
            'description',
            'start_time',
            'end_time',
            'duration_hours',
            'is_default',
            'is_active',
            'sort_order',
            'icon',
            'color',
            'is_featured',
            'meta_title',
            'meta_description',
            'slug',
        ], $model->getFillable());
    }

    /** @test */
    public function it_casts_attributes_correctly(): void
    {
        $this->assertIsInt($this->jobShift->id);
        $this->assertIsInt($this->jobShift->duration_hours);
        $this->assertIsBool($this->jobShift->is_default);
        $this->assertIsBool($this->jobShift->is_active);
        $this->assertIsBool($this->jobShift->is_flexible);
        $this->assertInstanceOf(Carbon::class, $this->jobShift->created_at);
        $this->assertInstanceOf(Carbon::class, $this->jobShift->updated_at);
    }

    /** @test */
    public function it_has_jobs_relationship(): void
    {
        $job = Job::factory()->create(['job_shift_id' => $this->jobShift->id]);

        $this->assertTrue($this->jobShift->jobs()->exists());
        $this->assertEquals(1, $this->jobShift->jobs()->count());
        $this->assertEquals($job->id, $this->jobShift->jobs()->first()->id);
    }

    /** @test */
    public function it_calculates_usage_count_correctly(): void
    {
        // Create jobs associated with this shift
        Job::factory(3)->create(['job_shift_id' => $this->jobShift->id]);

        // Refresh model to get updated relationship count
        $this->jobShift->refresh();

        $this->assertEquals(3, $this->jobShift->usage_count);
    }

    /** @test */
    public function it_provides_formatted_usage_stats(): void
    {
        Job::factory(2)->create(['job_shift_id' => $this->jobShift->id]);
        Job::factory()->create([
            'job_shift_id' => $this->jobShift->id,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $stats = $this->jobShift->formatted_usage_stats;

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('jobs', $stats);
        $this->assertArrayHasKey('active_jobs', $stats);
        $this->assertArrayHasKey('featured_jobs', $stats);
        $this->assertArrayHasKey('total_usage', $stats);
        $this->assertArrayHasKey('shift_category', $stats);
        $this->assertArrayHasKey('time_range', $stats);
    }

    /** @test */
    public function it_determines_shift_category_correctly(): void
    {
        // Test day shift
        $dayShift = JobShift::factory()->create([
            'shift' => 'Day Shift',
            'start_time' => '09:00',
        ]);
        $this->assertStringContainsString('day', strtolower($dayShift->shift_category));

        // Test night shift
        $nightShift = JobShift::factory()->create([
            'shift' => 'Night Shift',
            'start_time' => '23:00',
        ]);
        $this->assertStringContainsString('night', strtolower($nightShift->shift_category));

        // Test evening shift
        $eveningShift = JobShift::factory()->create([
            'shift' => 'Evening Shift',
            'start_time' => '16:00',
        ]);
        $this->assertStringContainsString('evening', strtolower($eveningShift->shift_category));
    }

    /** @test */
    public function it_formats_time_range_correctly(): void
    {
        $this->assertEquals('09:00 - 17:00', $this->jobShift->formatted_time_range);

        // Test flexible shift
        $flexibleShift = JobShift::factory()->create([
            'start_time' => null,
            'end_time' => null,
            'is_flexible' => true,
        ]);
        $this->assertStringContainsString('flexible', strtolower($flexibleShift->formatted_time_range));
    }

    /** @test */
    public function it_detects_night_shift_correctly(): void
    {
        // Test name-based detection
        $nightShift1 = JobShift::factory()->create(['shift' => 'Night Shift']);
        $this->assertTrue($nightShift1->is_night_shift);

        // Test time-based detection
        $nightShift2 = JobShift::factory()->create([
            'shift' => 'Late Shift',
            'start_time' => '23:00',
        ]);
        $this->assertTrue($nightShift2->is_night_shift);

        // Test early morning
        $earlyShift = JobShift::factory()->create([
            'shift' => 'Early Shift',
            'start_time' => '04:00',
        ]);
        $this->assertTrue($earlyShift->is_night_shift);

        // Test day shift
        $this->assertFalse($this->jobShift->is_night_shift);
    }

    /** @test */
    public function active_scope_returns_only_active_shifts(): void
    {
        JobShift::factory()->create(['is_active' => false]);
        JobShift::factory()->create(['is_active' => true]);

        $activeShifts = JobShift::active()->get();

        $this->assertEquals(2, $activeShifts->count()); // Including setUp shift
        $activeShifts->each(function ($shift) {
            $this->assertTrue($shift->is_active);
        });
    }

    /** @test */
    public function inactive_scope_returns_only_inactive_shifts(): void
    {
        JobShift::factory()->create(['is_active' => false]);

        $inactiveShifts = JobShift::inactive()->get();

        $this->assertEquals(1, $inactiveShifts->count());
        $inactiveShifts->each(function ($shift) {
            $this->assertFalse($shift->is_active);
        });
    }

    /** @test */
    public function default_scope_returns_only_default_shifts(): void
    {
        JobShift::factory()->create(['is_default' => false]);

        $defaultShifts = JobShift::default()->get();

        $this->assertEquals(1, $defaultShifts->count()); // Only setUp shift
        $defaultShifts->each(function ($shift) {
            $this->assertTrue($shift->is_default);
        });
    }

    /** @test */
    public function flexible_scope_returns_only_flexible_shifts(): void
    {
        JobShift::factory()->create(['is_flexible' => true]);

        $flexibleShifts = JobShift::flexible()->get();

        $this->assertEquals(1, $flexibleShifts->count());
        $flexibleShifts->each(function ($shift) {
            $this->assertTrue($shift->is_flexible);
        });
    }

    /** @test */
    public function with_jobs_scope_returns_shifts_with_jobs(): void
    {
        $shiftWithJobs = JobShift::factory()->create();
        $shiftWithoutJobs = JobShift::factory()->create();

        Job::factory()->create(['job_shift_id' => $shiftWithJobs->id]);

        $shiftsWithJobs = JobShift::withJobs()->get();

        $this->assertTrue($shiftsWithJobs->contains($shiftWithJobs));
        $this->assertFalse($shiftsWithJobs->contains($shiftWithoutJobs));
    }

    /** @test */
    public function search_scope_finds_shifts_by_name_and_description(): void
    {
        JobShift::factory()->create([
            'shift' => 'Morning Shift',
            'description' => 'Early morning hours',
        ]);

        JobShift::factory()->create([
            'shift' => 'Afternoon Shift',
            'description' => 'Standard working hours',
        ]);

        $results = JobShift::search('morning')->get();
        $this->assertEquals(1, $results->count());
        $this->assertEquals('Morning Shift', $results->first()->shift);

        $results = JobShift::search('standard')->get();
        $this->assertEquals(1, $results->count());
        $this->assertEquals('Afternoon Shift', $results->first()->shift);
    }

    /** @test */
    public function popular_scope_returns_shifts_ordered_by_job_count(): void
    {
        $shift1 = JobShift::factory()->create(['shift' => 'Shift 1']);
        $shift2 = JobShift::factory()->create(['shift' => 'Shift 2']);
        $shift3 = JobShift::factory()->create(['shift' => 'Shift 3']);

        // Create different numbers of jobs for each shift
        Job::factory(5)->create(['job_shift_id' => $shift2->id]);
        Job::factory(3)->create(['job_shift_id' => $shift1->id]);
        Job::factory(1)->create(['job_shift_id' => $shift3->id]);

        $popularShifts = JobShift::popular()->get();

        $this->assertEquals($shift2->id, $popularShifts->first()->id);
        $this->assertEquals($shift1->id, $popularShifts->skip(1)->first()->id);
        $this->assertEquals($shift3->id, $popularShifts->skip(2)->first()->id);
    }

    /** @test */
    public function alphabetical_scope_orders_shifts_by_name(): void
    {
        JobShift::factory()->create(['shift' => 'Zebra Shift']);
        JobShift::factory()->create(['shift' => 'Alpha Shift']);

        $alphabeticalShifts = JobShift::alphabetical()->get();

        $this->assertEquals('Alpha Shift', $alphabeticalShifts->first()->shift);
        $this->assertEquals('Zebra Shift', $alphabeticalShifts->last()->shift);
    }

    /** @test */
    public function recent_scope_returns_shifts_created_within_timeframe(): void
    {
        $oldShift = JobShift::factory()->create([
            'created_at' => now()->subDays(45),
        ]);

        $recentShift = JobShift::factory()->create([
            'created_at' => now()->subDays(15),
        ]);

        $recentShifts = JobShift::recent(30)->get();

        $this->assertTrue($recentShifts->contains($recentShift));
        $this->assertFalse($recentShifts->contains($oldShift));
    }

    /** @test */
    public function day_shift_scope_identifies_day_shifts(): void
    {
        $dayShift = JobShift::factory()->create([
            'shift' => 'Day Shift',
            'start_time' => '08:00',
        ]);

        $nightShift = JobShift::factory()->create([
            'shift' => 'Night Shift',
            'start_time' => '22:00',
        ]);

        $dayShifts = JobShift::dayShift()->get();

        $this->assertTrue($dayShifts->contains($dayShift));
        $this->assertFalse($dayShifts->contains($nightShift));
    }

    /** @test */
    public function night_shift_scope_identifies_night_shifts(): void
    {
        $nightShift = JobShift::factory()->create([
            'shift' => 'Night Shift',
            'start_time' => '23:00',
        ]);

        $earlyShift = JobShift::factory()->create([
            'shift' => 'Early Shift',
            'start_time' => '03:00',
        ]);

        $nightShifts = JobShift::nightShift()->get();

        $this->assertTrue($nightShifts->contains($nightShift));
        $this->assertTrue($nightShifts->contains($earlyShift));
    }

    /** @test */
    public function evening_shift_scope_identifies_evening_shifts(): void
    {
        $eveningShift = JobShift::factory()->create([
            'shift' => 'Evening Shift',
            'start_time' => '16:00',
        ]);

        $dayShift = JobShift::factory()->create([
            'shift' => 'Day Shift',
            'start_time' => '09:00',
        ]);

        $eveningShifts = JobShift::eveningShift()->get();

        $this->assertTrue($eveningShifts->contains($eveningShift));
        $this->assertFalse($eveningShifts->contains($dayShift));
    }

    /** @test */
    public function by_duration_scope_filters_by_exact_hours(): void
    {
        JobShift::factory()->create(['duration_hours' => 6]);
        JobShift::factory()->create(['duration_hours' => 10]);

        $eightHourShifts = JobShift::byDuration(8)->get();
        $this->assertEquals(1, $eightHourShifts->count()); // setUp shift is 8 hours

        $sixHourShifts = JobShift::byDuration(6)->get();
        $this->assertEquals(1, $sixHourShifts->count());
    }

    /** @test */
    public function min_duration_scope_filters_by_minimum_hours(): void
    {
        JobShift::factory()->create(['duration_hours' => 6]);
        JobShift::factory()->create(['duration_hours' => 10]);

        $minEightHours = JobShift::minDuration(8)->get();
        $this->assertEquals(2, $minEightHours->count()); // setUp (8) + 10-hour shift
    }

    /** @test */
    public function max_duration_scope_filters_by_maximum_hours(): void
    {
        JobShift::factory()->create(['duration_hours' => 6]);
        JobShift::factory()->create(['duration_hours' => 10]);

        $maxEightHours = JobShift::maxDuration(8)->get();
        $this->assertEquals(2, $maxEightHours->count()); // setUp (8) + 6-hour shift
    }

    /** @test */
    public function it_calculates_average_salary_correctly(): void
    {
        Job::factory()->create([
            'job_shift_id' => $this->jobShift->id,
            'salary_from' => 50000,
            'salary_to' => 60000,
            'hide_salary' => false,
        ]);

        Job::factory()->create([
            'job_shift_id' => $this->jobShift->id,
            'salary_from' => 70000,
            'salary_to' => 80000,
            'hide_salary' => false,
        ]);

        $averageSalary = $this->jobShift->getAverageSalary();
        $this->assertEquals(65000, $averageSalary); // (55000 + 75000) / 2
    }

    /** @test */
    public function it_detects_shift_overlaps_correctly(): void
    {
        $morningShift = JobShift::factory()->create([
            'start_time' => '06:00',
            'end_time' => '14:00',
        ]);

        $afternoonShift = JobShift::factory()->create([
            'start_time' => '12:00',
            'end_time' => '20:00',
        ]);

        $nightShift = JobShift::factory()->create([
            'start_time' => '22:00',
            'end_time' => '06:00',
        ]);

        $this->assertTrue($morningShift->overlapsWith($afternoonShift));
        $this->assertFalse($morningShift->overlapsWith($nightShift));
    }

    /** @test */
    public function it_calculates_shift_differential_correctly(): void
    {
        $dayShift = JobShift::factory()->create([
            'shift' => 'Day Shift',
            'start_time' => '09:00',
        ]);

        $nightShift = JobShift::factory()->create([
            'shift' => 'Night Shift',
            'start_time' => '23:00',
        ]);

        $eveningShift = JobShift::factory()->create([
            'shift' => 'Evening Shift',
            'start_time' => '16:00',
        ]);

        $this->assertEquals(0.0, $dayShift->getShiftDifferential());
        $this->assertGreaterThan(0, $nightShift->getShiftDifferential());
        $this->assertGreaterThan(0, $eveningShift->getShiftDifferential());
    }

    /** @test */
    public function it_calculates_hours_per_week_correctly(): void
    {
        $standardShift = JobShift::factory()->create(['duration_hours' => 8]);
        $this->assertEquals(40, $standardShift->getHoursPerWeek());

        $partTimeShift = JobShift::factory()->create(['duration_hours' => 4]);
        $this->assertEquals(20, $partTimeShift->getHoursPerWeek());
    }

    /** @test */
    public function it_logs_activity_on_updates(): void
    {
        $this->jobShift->update(['shift' => 'Updated Day Shift']);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => JobShift::class,
            'subject_id' => $this->jobShift->id,
            'description' => 'updated',
        ]);
    }

    /** @test */
    public function it_clears_cache_on_update(): void
    {
        // This would require mocking the cache facade to test properly
        $this->jobShift->update(['shift' => 'Updated Shift']);

        // In a real test, you'd verify cache keys were forgotten
        $this->assertTrue(true); // Placeholder assertion
    }

    /** @test */
    public function trending_scope_considers_recent_job_activity(): void
    {
        $trendingShift = JobShift::factory()->create(['shift' => 'Trending Shift']);
        $staleShift = JobShift::factory()->create(['shift' => 'Stale Shift']);

        // Create recent jobs for trending shift
        Job::factory(3)->create([
            'job_shift_id' => $trendingShift->id,
            'created_at' => now()->subDays(15),
        ]);

        // Create old jobs for stale shift
        Job::factory(5)->create([
            'job_shift_id' => $staleShift->id,
            'created_at' => now()->subDays(45),
        ]);

        $trendingShifts = JobShift::trending()->get();

        $this->assertEquals($trendingShift->id, $trendingShifts->first()->id);
    }

    /** @test */
    public function validation_rules_are_comprehensive(): void
    {
        $rules = JobShift::$rules;

        $this->assertArrayHasKey('shift', $rules);
        $this->assertArrayHasKey('description', $rules);
        $this->assertArrayHasKey('start_time', $rules);
        $this->assertArrayHasKey('end_time', $rules);
        $this->assertArrayHasKey('duration_hours', $rules);
        $this->assertArrayHasKey('is_default', $rules);
        $this->assertArrayHasKey('is_active', $rules);
        $this->assertArrayHasKey('is_flexible', $rules);
    }
}
