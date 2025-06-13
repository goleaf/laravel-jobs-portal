<?php

namespace Tests\Unit\Models;

use App\Models\SalaryPeriod;
use App\Models\Job;
use App\Models\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * SalaryPeriod Model Test Suite - Context7 Enhanced
 * 
 * Testing comprehensive SalaryPeriod model functionality including:
 * - Model attributes and relationships
 * - All scopes (25+ scopes)
 * - Helper methods and attributes
 * - Caching functionality
 * - Period type detection
 * - Salary conversion calculations
 * - Validation rules
 */
class SalaryPeriodTest extends TestCase
{
    use RefreshDatabase;

    protected SalaryPeriod $salaryPeriod;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test salary period
        $this->salaryPeriod = SalaryPeriod::factory()->create([
            'period' => 'Per Hour',
            'description' => 'Hourly salary payment',
            'is_active' => true,
            'is_default' => false,
            'is_featured' => false,
            'sort_order' => 1,
            'multiplier_hours' => 1.0,
            'multiplier_days' => 8.0,
            'multiplier_months' => 173.33,
            'multiplier_years' => 2080.0,
        ]);
        
        Cache::flush();
    }

    // =============================================
    // BASIC MODEL TESTS
    // =============================================

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $expected = [
            'period',
            'description',
            'is_active',
            'is_default',
            'is_featured',
            'sort_order',
            'multiplier_hours',
            'multiplier_days',
            'multiplier_months',
            'multiplier_years',
        ];

        $this->assertEquals($expected, $this->salaryPeriod->getFillable());
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $expected = [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'multiplier_hours' => 'decimal:4',
            'multiplier_days' => 'decimal:4',
            'multiplier_months' => 'decimal:4',
            'multiplier_years' => 'decimal:4',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

        $this->assertEquals($expected, $this->salaryPeriod->getCasts());
    }

    /** @test */
    public function it_hides_deleted_at_attribute()
    {
        $expected = ['deleted_at'];
        $this->assertEquals($expected, $this->salaryPeriod->getHidden());
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $this->assertEquals('salary_periods', $this->salaryPeriod->getTable());
    }

    // =============================================
    // RELATIONSHIP TESTS
    // =============================================

    /** @test */
    public function it_has_many_jobs()
    {
        $jobs = Job::factory()->count(3)->create(['salary_period_id' => $this->salaryPeriod->id]);
        
        $this->assertCount(3, $this->salaryPeriod->jobs);
        $this->assertInstanceOf(Job::class, $this->salaryPeriod->jobs->first());
    }

    /** @test */
    public function it_has_many_candidates()
    {
        $candidates = Candidate::factory()->count(2)->create(['salary_period_id' => $this->salaryPeriod->id]);
        
        $this->assertCount(2, $this->salaryPeriod->candidates);
        $this->assertInstanceOf(Candidate::class, $this->salaryPeriod->candidates->first());
    }

    // =============================================
    // SCOPE TESTS - Basic Status
    // =============================================

    /** @test */
    public function scope_active_returns_only_active_periods()
    {
        SalaryPeriod::factory()->create(['is_active' => false]);
        $activePeriod = SalaryPeriod::factory()->create(['is_active' => true]);

        $results = SalaryPeriod::active()->get();
        
        $this->assertTrue($results->contains($this->salaryPeriod));
        $this->assertTrue($results->contains($activePeriod));
        $this->assertCount(2, $results);
    }

    /** @test */
    public function scope_inactive_returns_only_inactive_periods()
    {
        $inactivePeriod = SalaryPeriod::factory()->create(['is_active' => false]);

        $results = SalaryPeriod::inactive()->get();
        
        $this->assertTrue($results->contains($inactivePeriod));
        $this->assertFalse($results->contains($this->salaryPeriod));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_featured_returns_only_featured_periods()
    {
        $featuredPeriod = SalaryPeriod::factory()->create(['is_featured' => true]);

        $results = SalaryPeriod::featured()->get();
        
        $this->assertTrue($results->contains($featuredPeriod));
        $this->assertFalse($results->contains($this->salaryPeriod));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_default_returns_only_default_periods()
    {
        $defaultPeriod = SalaryPeriod::factory()->create(['is_default' => true]);

        $results = SalaryPeriod::default()->get();
        
        $this->assertTrue($results->contains($defaultPeriod));
        $this->assertFalse($results->contains($this->salaryPeriod));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_custom_returns_only_custom_periods()
    {
        SalaryPeriod::factory()->create(['is_default' => true]);

        $results = SalaryPeriod::custom()->get();
        
        $this->assertTrue($results->contains($this->salaryPeriod));
        $this->assertCount(1, $results);
    }

    // =============================================
    // SCOPE TESTS - Period Types
    // =============================================

    /** @test */
    public function scope_hourly_returns_hourly_periods()
    {
        $hourlyPeriod1 = SalaryPeriod::factory()->create(['period' => 'Per Hour']);
        $hourlyPeriod2 = SalaryPeriod::factory()->create(['period' => 'Hourly Rate']);
        $hourlyPeriod3 = SalaryPeriod::factory()->create(['period' => '$/hr']);
        SalaryPeriod::factory()->create(['period' => 'Per Month']);

        $results = SalaryPeriod::hourly()->get();
        
        $this->assertTrue($results->contains($this->salaryPeriod));
        $this->assertTrue($results->contains($hourlyPeriod1));
        $this->assertTrue($results->contains($hourlyPeriod2));
        $this->assertTrue($results->contains($hourlyPeriod3));
        $this->assertCount(4, $results);
    }

    /** @test */
    public function scope_daily_returns_daily_periods()
    {
        $dailyPeriod1 = SalaryPeriod::factory()->create(['period' => 'Per Day']);
        $dailyPeriod2 = SalaryPeriod::factory()->create(['period' => 'Daily Rate']);
        SalaryPeriod::factory()->create(['period' => 'Per Month']);

        $results = SalaryPeriod::daily()->get();
        
        $this->assertTrue($results->contains($dailyPeriod1));
        $this->assertTrue($results->contains($dailyPeriod2));
        $this->assertCount(2, $results);
    }

    /** @test */
    public function scope_weekly_returns_weekly_periods()
    {
        $weeklyPeriod1 = SalaryPeriod::factory()->create(['period' => 'Per Week']);
        $weeklyPeriod2 = SalaryPeriod::factory()->create(['period' => 'Weekly Salary']);
        SalaryPeriod::factory()->create(['period' => 'Per Month']);

        $results = SalaryPeriod::weekly()->get();
        
        $this->assertTrue($results->contains($weeklyPeriod1));
        $this->assertTrue($results->contains($weeklyPeriod2));
        $this->assertCount(2, $results);
    }

    /** @test */
    public function scope_monthly_returns_monthly_periods()
    {
        $monthlyPeriod1 = SalaryPeriod::factory()->create(['period' => 'Per Month']);
        $monthlyPeriod2 = SalaryPeriod::factory()->create(['period' => 'Monthly Salary']);
        SalaryPeriod::factory()->create(['period' => 'Per Year']);

        $results = SalaryPeriod::monthly()->get();
        
        $this->assertTrue($results->contains($monthlyPeriod1));
        $this->assertTrue($results->contains($monthlyPeriod2));
        $this->assertCount(2, $results);
    }

    /** @test */
    public function scope_yearly_returns_yearly_periods()
    {
        $yearlyPeriod1 = SalaryPeriod::factory()->create(['period' => 'Per Year']);
        $yearlyPeriod2 = SalaryPeriod::factory()->create(['period' => 'Annual Salary']);
        $yearlyPeriod3 = SalaryPeriod::factory()->create(['period' => 'Yearly Payment']);
        SalaryPeriod::factory()->create(['period' => 'Per Month']);

        $results = SalaryPeriod::yearly()->get();
        
        $this->assertTrue($results->contains($yearlyPeriod1));
        $this->assertTrue($results->contains($yearlyPeriod2));
        $this->assertTrue($results->contains($yearlyPeriod3));
        $this->assertCount(3, $results);
    }

    // =============================================
    // SCOPE TESTS - Search & Filtering
    // =============================================

    /** @test */
    public function scope_search_finds_periods_by_name_or_description()
    {
        $period1 = SalaryPeriod::factory()->create([
            'period' => 'Custom Period',
            'description' => 'Special rate'
        ]);
        $period2 = SalaryPeriod::factory()->create([
            'period' => 'Standard Rate',
            'description' => 'Hourly custom payment'
        ]);
        SalaryPeriod::factory()->create([
            'period' => 'Different',
            'description' => 'Unrelated'
        ]);

        $results = SalaryPeriod::search('custom')->get();
        
        $this->assertTrue($results->contains($period1));
        $this->assertTrue($results->contains($period2));
        $this->assertCount(2, $results);
    }

    /** @test */
    public function scope_recent_returns_recently_created_periods()
    {
        $oldPeriod = SalaryPeriod::factory()->create([
            'created_at' => now()->subDays(60)
        ]);

        $results = SalaryPeriod::recent(30)->get();
        
        $this->assertTrue($results->contains($this->salaryPeriod));
        $this->assertFalse($results->contains($oldPeriod));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_old_returns_old_periods()
    {
        $oldPeriod = SalaryPeriod::factory()->create([
            'created_at' => now()->subDays(400)
        ]);

        $results = SalaryPeriod::old(365)->get();
        
        $this->assertTrue($results->contains($oldPeriod));
        $this->assertFalse($results->contains($this->salaryPeriod));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_alphabetical_orders_periods_by_name()
    {
        $periodA = SalaryPeriod::factory()->create(['period' => 'Alpha Period']);
        $periodZ = SalaryPeriod::factory()->create(['period' => 'Zulu Period']);

        $results = SalaryPeriod::alphabetical()->get();
        
        $this->assertEquals('Alpha Period', $results->first()->period);
        $this->assertEquals('Zulu Period', $results->last()->period);
    }

    /** @test */
    public function scope_by_order_orders_periods_by_sort_order()
    {
        $period1 = SalaryPeriod::factory()->create(['sort_order' => 1, 'period' => 'A']);
        $period2 = SalaryPeriod::factory()->create(['sort_order' => 2, 'period' => 'B']);
        $period3 = SalaryPeriod::factory()->create(['sort_order' => null, 'period' => 'C']);

        $results = SalaryPeriod::byOrder()->get();
        
        $this->assertEquals($this->salaryPeriod->id, $results->first()->id); // sort_order = 1
        $this->assertEquals($period2->id, $results->get(1)->id); // sort_order = 2
    }

    // =============================================
    // SCOPE TESTS - Relationships
    // =============================================

    /** @test */
    public function scope_with_jobs_returns_periods_with_jobs()
    {
        Job::factory()->create(['salary_period_id' => $this->salaryPeriod->id]);
        $periodWithoutJobs = SalaryPeriod::factory()->create();

        $results = SalaryPeriod::withJobs()->get();
        
        $this->assertTrue($results->contains($this->salaryPeriod));
        $this->assertFalse($results->contains($periodWithoutJobs));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_with_active_jobs_returns_periods_with_active_jobs()
    {
        Job::factory()->create([
            'salary_period_id' => $this->salaryPeriod->id,
            'status' => 'active',
            'expire_date' => now()->addDays(30)
        ]);
        
        $periodWithInactiveJob = SalaryPeriod::factory()->create();
        Job::factory()->create([
            'salary_period_id' => $periodWithInactiveJob->id,
            'status' => 'inactive'
        ]);

        $results = SalaryPeriod::withActiveJobs()->get();
        
        $this->assertTrue($results->contains($this->salaryPeriod));
        $this->assertFalse($results->contains($periodWithInactiveJob));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_with_candidates_returns_periods_with_candidates()
    {
        Candidate::factory()->create(['salary_period_id' => $this->salaryPeriod->id]);
        $periodWithoutCandidates = SalaryPeriod::factory()->create();

        $results = SalaryPeriod::withCandidates()->get();
        
        $this->assertTrue($results->contains($this->salaryPeriod));
        $this->assertFalse($results->contains($periodWithoutCandidates));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_with_active_candidates_returns_periods_with_active_candidates()
    {
        Candidate::factory()->create([
            'salary_period_id' => $this->salaryPeriod->id,
            'is_active' => true
        ]);
        
        $periodWithInactiveCandidate = SalaryPeriod::factory()->create();
        Candidate::factory()->create([
            'salary_period_id' => $periodWithInactiveCandidate->id,
            'is_active' => false
        ]);

        $results = SalaryPeriod::withActiveCandidates()->get();
        
        $this->assertTrue($results->contains($this->salaryPeriod));
        $this->assertFalse($results->contains($periodWithInactiveCandidate));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_popular_returns_periods_ordered_by_active_jobs_count()
    {
        $period2 = SalaryPeriod::factory()->create();
        
        // Period 1 has 1 active job
        Job::factory()->create([
            'salary_period_id' => $this->salaryPeriod->id,
            'status' => 'active'
        ]);
        
        // Period 2 has 2 active jobs
        Job::factory()->count(2)->create([
            'salary_period_id' => $period2->id,
            'status' => 'active'
        ]);

        $results = SalaryPeriod::popular(10)->get();
        
        $this->assertEquals($period2->id, $results->first()->id);
        $this->assertEquals($this->salaryPeriod->id, $results->last()->id);
    }

    /** @test */
    public function scope_trending_returns_periods_with_recent_jobs()
    {
        $period2 = SalaryPeriod::factory()->create();
        
        // Recent jobs
        Job::factory()->create([
            'salary_period_id' => $this->salaryPeriod->id,
            'status' => 'active',
            'created_at' => now()->subDays(5)
        ]);
        
        // Old job
        Job::factory()->create([
            'salary_period_id' => $period2->id,
            'status' => 'active',
            'created_at' => now()->subDays(60)
        ]);

        $results = SalaryPeriod::trending(30, 10)->get();
        
        $this->assertTrue($results->contains($this->salaryPeriod));
        $this->assertFalse($results->contains($period2));
        $this->assertCount(1, $results);
    }

    // =============================================
    // HELPER METHOD TESTS
    // =============================================

    /** @test */
    public function get_display_name_attribute_includes_description()
    {
        $expected = "Per Hour (Hourly salary payment)";
        $this->assertEquals($expected, $this->salaryPeriod->display_name);

        $periodWithoutDescription = SalaryPeriod::factory()->create([
            'period' => 'Per Month',
            'description' => null
        ]);
        $this->assertEquals('Per Month', $periodWithoutDescription->display_name);
    }

    /** @test */
    public function get_period_type_attribute_detects_period_types_correctly()
    {
        $this->assertEquals('hourly', $this->salaryPeriod->period_type);

        $dailyPeriod = SalaryPeriod::factory()->create(['period' => 'Per Day']);
        $this->assertEquals('daily', $dailyPeriod->period_type);

        $weeklyPeriod = SalaryPeriod::factory()->create(['period' => 'Per Week']);
        $this->assertEquals('weekly', $weeklyPeriod->period_type);

        $monthlyPeriod = SalaryPeriod::factory()->create(['period' => 'Per Month']);
        $this->assertEquals('monthly', $monthlyPeriod->period_type);

        $yearlyPeriod = SalaryPeriod::factory()->create(['period' => 'Per Year']);
        $this->assertEquals('yearly', $yearlyPeriod->period_type);

        $customPeriod = SalaryPeriod::factory()->create(['period' => 'Custom Payment']);
        $this->assertEquals('custom', $customPeriod->period_type);
    }

    /** @test */
    public function period_type_check_methods_work_correctly()
    {
        $this->assertTrue($this->salaryPeriod->isHourly());
        $this->assertFalse($this->salaryPeriod->isDaily());
        $this->assertFalse($this->salaryPeriod->isWeekly());
        $this->assertFalse($this->salaryPeriod->isMonthly());
        $this->assertFalse($this->salaryPeriod->isYearly());

        $monthlyPeriod = SalaryPeriod::factory()->create(['period' => 'Per Month']);
        $this->assertTrue($monthlyPeriod->isMonthly());
        $this->assertFalse($monthlyPeriod->isHourly());
    }

    /** @test */
    public function convert_to_yearly_calculates_correctly()
    {
        // Test with multiplier
        $yearly = $this->salaryPeriod->convertToYearly(25.0); // $25/hour
        $this->assertEquals(52000.0, $yearly); // 25 * 2080 hours

        // Test without multiplier (default calculation)
        $period = SalaryPeriod::factory()->create([
            'period' => 'Per Month',
            'multiplier_years' => null
        ]);
        $yearly = $period->convertToMonthly(5000.0); // $5000/month
        $this->assertEquals(60000.0, $yearly); // 5000 * 12 months
    }

    /** @test */
    public function convert_to_monthly_calculates_correctly()
    {
        $monthly = $this->salaryPeriod->convertToMonthly(25.0); // $25/hour
        $expected = 52000.0 / 12; // Yearly equivalent / 12
        $this->assertEquals($expected, $monthly);
    }

    /** @test */
    public function convert_to_hourly_calculates_correctly()
    {
        $hourly = $this->salaryPeriod->convertToHourly(25.0); // $25/hour
        $this->assertEquals(25.0, $hourly); // Should be same for hourly

        $yearlyPeriod = SalaryPeriod::factory()->create(['period' => 'Per Year']);
        $hourly = $yearlyPeriod->convertToHourly(52000.0); // $52k/year
        $expected = 52000.0 / (40 * 52); // 40 hours/week * 52 weeks
        $this->assertEquals($expected, $hourly);
    }

    // =============================================
    // CACHING TESTS
    // =============================================

    /** @test */
    public function get_cached_active_returns_cached_results()
    {
        SalaryPeriod::factory()->count(3)->create(['is_active' => true]);

        $results1 = SalaryPeriod::getCachedActive();
        $results2 = SalaryPeriod::getCachedActive();

        $this->assertCount(4, $results1); // Including setup period
        $this->assertEquals($results1, $results2);
        $this->assertTrue(Cache::has('salary_periods_active'));
    }

    /** @test */
    public function get_cached_featured_returns_cached_results()
    {
        SalaryPeriod::factory()->create(['is_featured' => true, 'is_active' => true]);

        $results1 = SalaryPeriod::getCachedFeatured();
        $results2 = SalaryPeriod::getCachedFeatured();

        $this->assertCount(1, $results1);
        $this->assertEquals($results1, $results2);
        $this->assertTrue(Cache::has('salary_periods_featured'));
    }

    /** @test */
    public function get_cached_by_type_returns_cached_results()
    {
        $results1 = SalaryPeriod::getCachedByType('hourly');
        $results2 = SalaryPeriod::getCachedByType('hourly');

        $this->assertCount(1, $results1);
        $this->assertEquals($results1, $results2);
        $this->assertTrue(Cache::has('salary_periods_type_hourly'));
    }

    /** @test */
    public function clear_caches_removes_related_cache_keys()
    {
        // Set up some cache entries
        SalaryPeriod::getCachedActive();
        SalaryPeriod::getCachedFeatured();
        SalaryPeriod::getCachedByType('hourly');

        $this->salaryPeriod->clearCaches();

        $this->assertFalse(Cache::has('salary_periods_active'));
        $this->assertFalse(Cache::has('salary_periods_featured'));
        $this->assertFalse(Cache::has('salary_periods_type_hourly'));
    }

    // =============================================
    // VALIDATION RULES TESTS
    // =============================================

    /** @test */
    public function validation_rules_are_correctly_defined()
    {
        $expectedRules = [
            'period' => 'required|string|max:150|unique:salary_periods,period',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0|max:999',
            'multiplier_hours' => 'nullable|numeric|min:0',
            'multiplier_days' => 'nullable|numeric|min:0',
            'multiplier_months' => 'nullable|numeric|min:0',
            'multiplier_years' => 'nullable|numeric|min:0',
        ];

        $this->assertEquals($expectedRules, SalaryPeriod::$rules);
    }

    /** @test */
    public function update_rules_exclude_current_period_from_unique_check()
    {
        $updateRules = SalaryPeriod::updateRules($this->salaryPeriod->id);
        
        $this->assertStringContains("unique:salary_periods,period,{$this->salaryPeriod->id}", $updateRules['period']);
    }

    // =============================================
    // ATTRIBUTE ACCESSOR TESTS
    // =============================================

    /** @test */
    public function jobs_count_attribute_returns_cached_count()
    {
        Job::factory()->count(3)->create(['salary_period_id' => $this->salaryPeriod->id]);
        
        $count = $this->salaryPeriod->jobs_count;
        
        $this->assertEquals(3, $count);
        $this->assertTrue(Cache::has("salary_period_{$this->salaryPeriod->id}_jobs_count"));
    }

    /** @test */
    public function active_jobs_count_attribute_returns_cached_count()
    {
        Job::factory()->count(2)->create([
            'salary_period_id' => $this->salaryPeriod->id,
            'status' => 'active'
        ]);
        Job::factory()->create([
            'salary_period_id' => $this->salaryPeriod->id,
            'status' => 'inactive'
        ]);
        
        $count = $this->salaryPeriod->active_jobs_count;
        
        $this->assertEquals(2, $count);
        $this->assertTrue(Cache::has("salary_period_{$this->salaryPeriod->id}_active_jobs_count"));
    }

    /** @test */
    public function candidates_count_attribute_returns_cached_count()
    {
        Candidate::factory()->count(4)->create(['salary_period_id' => $this->salaryPeriod->id]);
        
        $count = $this->salaryPeriod->candidates_count;
        
        $this->assertEquals(4, $count);
        $this->assertTrue(Cache::has("salary_period_{$this->salaryPeriod->id}_candidates_count"));
    }

    /** @test */
    public function active_candidates_count_attribute_returns_cached_count()
    {
        Candidate::factory()->count(3)->create([
            'salary_period_id' => $this->salaryPeriod->id,
            'is_active' => true
        ]);
        Candidate::factory()->create([
            'salary_period_id' => $this->salaryPeriod->id,
            'is_active' => false
        ]);
        
        $count = $this->salaryPeriod->active_candidates_count;
        
        $this->assertEquals(3, $count);
        $this->assertTrue(Cache::has("salary_period_{$this->salaryPeriod->id}_active_candidates_count"));
    }
}