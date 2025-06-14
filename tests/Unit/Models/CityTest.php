<?php

namespace Tests\Unit\Models;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Models\User;
use App\Models\Company;
use App\Models\Job;
use App\Models\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * City Model Test Suite - Enhanced Enhanced
 * 
 * Testing comprehensive City model functionality including:
 * - Model attributes and relationships
 * - All scopes (30+ scopes)
 * - Helper methods and attributes
 * - Caching functionality
 * - Geographic calculations
 * - Validation rules
 */
class CityTest extends TestCase
{
    use RefreshDatabase;

    protected City $city;
    protected State $state;
    protected Country $country;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test country
        $this->country = Country::factory()->create([
            'name' => 'Test Country',
            'is_active' => true
        ]);
        
        // Create test state
        $this->state = State::factory()->create([
            'name' => 'Test State',
            'country_id' => $this->country->id,
            'is_active' => true
        ]);
        
        // Create test city
        $this->city = City::factory()->create([
            'name' => 'Test City',
            'state_id' => $this->state->id,
            'is_active' => true,
            'is_featured' => false,
            'is_metropolitan' => false,
            'is_major' => false,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'timezone' => 'America/New_York',
            'population' => 8000000
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
            'state_id',
            'name',
            'is_active',
            'is_featured',
            'latitude',
            'longitude',
            'timezone',
            'population',
            'is_metropolitan',
            'is_major'
        ];

        $this->assertEquals($expected, $this->city->getFillable());
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $expected = [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_metropolitan' => 'boolean',
            'is_major' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'population' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime'
        ];

        $this->assertEquals($expected, $this->city->getCasts());
    }

    /** @test */
    public function it_hides_deleted_at_attribute()
    {
        $expected = ['deleted_at'];
        $this->assertEquals($expected, $this->city->getHidden());
    }

    // =============================================
    // RELATIONSHIP TESTS
    // =============================================

    /** @test */
    public function it_belongs_to_state()
    {
        $this->assertInstanceOf(State::class, $this->city->state);
        $this->assertEquals($this->state->id, $this->city->state->id);
    }

    /** @test */
    public function it_has_country_through_state()
    {
        $this->assertInstanceOf(Country::class, $this->city->country);
        $this->assertEquals($this->country->id, $this->city->country->id);
    }

    /** @test */
    public function it_has_many_users()
    {
        $users = User::factory()->count(3)->create(['city_id' => $this->city->id]);
        
        $this->assertCount(3, $this->city->users);
        $this->assertInstanceOf(User::class, $this->city->users->first());
    }

    /** @test */
    public function it_has_many_companies()
    {
        $companies = Company::factory()->count(2)->create(['city_id' => $this->city->id]);
        
        $this->assertCount(2, $this->city->companies);
        $this->assertInstanceOf(Company::class, $this->city->companies->first());
    }

    /** @test */
    public function it_has_many_jobs()
    {
        $jobs = Job::factory()->count(4)->create(['city_id' => $this->city->id]);
        
        $this->assertCount(4, $this->city->jobs);
        $this->assertInstanceOf(Job::class, $this->city->jobs->first());
    }

    /** @test */
    public function it_has_many_candidates()
    {
        $candidates = Candidate::factory()->count(5)->create(['city_id' => $this->city->id]);
        
        $this->assertCount(5, $this->city->candidates);
        $this->assertInstanceOf(Candidate::class, $this->city->candidates->first());
    }

    // =============================================
    // SCOPE TESTS - Basic Status
    // =============================================

    /** @test */
    public function scope_active_returns_only_active_cities()
    {
        City::factory()->create(['state_id' => $this->state->id, 'is_active' => false]);
        $activeCity = City::factory()->create(['state_id' => $this->state->id, 'is_active' => true]);

        $results = City::active()->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertTrue($results->contains($activeCity));
        $this->assertCount(2, $results);
    }

    /** @test */
    public function scope_inactive_returns_only_inactive_cities()
    {
        $inactiveCity = City::factory()->create(['state_id' => $this->state->id, 'is_active' => false]);

        $results = City::inactive()->get();
        
        $this->assertTrue($results->contains($inactiveCity));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_featured_returns_only_featured_cities()
    {
        $featuredCity = City::factory()->create(['state_id' => $this->state->id, 'is_featured' => true]);

        $results = City::featured()->get();
        
        $this->assertTrue($results->contains($featuredCity));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    // =============================================
    // SCOPE TESTS - Location & Geography
    // =============================================

    /** @test */
    public function scope_by_state_filters_cities_by_state()
    {
        $otherState = State::factory()->create(['country_id' => $this->country->id]);
        City::factory()->create(['state_id' => $otherState->id]);

        $results = City::byState($this->state->id)->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_in_states_filters_cities_by_multiple_states()
    {
        $state2 = State::factory()->create(['country_id' => $this->country->id]);
        $state3 = State::factory()->create(['country_id' => $this->country->id]);
        
        $city2 = City::factory()->create(['state_id' => $state2->id]);
        City::factory()->create(['state_id' => $state3->id]);

        $results = City::inStates([$this->state->id, $state2->id])->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertTrue($results->contains($city2));
        $this->assertCount(2, $results);
    }

    /** @test */
    public function scope_by_country_filters_cities_by_country()
    {
        $otherCountry = Country::factory()->create();
        $otherState = State::factory()->create(['country_id' => $otherCountry->id]);
        City::factory()->create(['state_id' => $otherState->id]);

        $results = City::byCountry($this->country->id)->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_with_coordinates_returns_cities_with_coordinates()
    {
        City::factory()->create([
            'state_id' => $this->state->id,
            'latitude' => null,
            'longitude' => null
        ]);

        $results = City::withCoordinates()->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_without_coordinates_returns_cities_without_coordinates()
    {
        $cityWithoutCoords = City::factory()->create([
            'state_id' => $this->state->id,
            'latitude' => null,
            'longitude' => null
        ]);

        $results = City::withoutCoordinates()->get();
        
        $this->assertTrue($results->contains($cityWithoutCoords));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_by_timezone_filters_cities_by_timezone()
    {
        City::factory()->create([
            'state_id' => $this->state->id,
            'timezone' => 'America/Chicago'
        ]);

        $results = City::byTimezone('America/New_York')->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    // =============================================
    // SCOPE TESTS - Population & Size
    // =============================================

    /** @test */
    public function scope_by_population_range_filters_cities_by_population()
    {
        City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 50000
        ]);

        $results = City::byPopulationRange(1000000, 10000000)->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_population_greater_than_filters_correctly()
    {
        City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 50000
        ]);

        $results = City::populationGreaterThan(1000000)->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_population_less_than_filters_correctly()
    {
        $smallCity = City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 50000
        ]);

        $results = City::populationLessThan(100000)->get();
        
        $this->assertTrue($results->contains($smallCity));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_major_returns_major_cities()
    {
        // City with is_major flag
        $majorCity1 = City::factory()->create([
            'state_id' => $this->state->id,
            'is_major' => true,
            'population' => 500000
        ]);

        // City with population > 1M
        $majorCity2 = City::factory()->create([
            'state_id' => $this->state->id,
            'is_major' => false,
            'population' => 1500000
        ]);

        $results = City::major()->get();
        
        $this->assertCount(3, $results); // Including the setUp city
        $this->assertTrue($results->contains($majorCity1));
        $this->assertTrue($results->contains($majorCity2));
        $this->assertTrue($results->contains($this->city));
    }

    /** @test */
    public function scope_metropolitan_returns_metropolitan_cities()
    {
        // City with is_metropolitan flag
        $metroCity1 = City::factory()->create([
            'state_id' => $this->state->id,
            'is_metropolitan' => true,
            'population' => 300000
        ]);

        // City with population > 500K
        $metroCity2 = City::factory()->create([
            'state_id' => $this->state->id,
            'is_metropolitan' => false,
            'population' => 600000
        ]);

        $results = City::metropolitan()->get();
        
        $this->assertCount(3, $results);
        $this->assertTrue($results->contains($metroCity1));
        $this->assertTrue($results->contains($metroCity2));
        $this->assertTrue($results->contains($this->city));
    }

    /** @test */
    public function scope_small_returns_small_cities()
    {
        $smallCity = City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 50000
        ]);

        $results = City::small()->get();
        
        $this->assertTrue($results->contains($smallCity));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_medium_returns_medium_cities()
    {
        $mediumCity = City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 200000
        ]);

        $results = City::medium()->get();
        
        $this->assertTrue($results->contains($mediumCity));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_large_returns_large_cities()
    {
        $largeCity = City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 750000
        ]);

        $results = City::large()->get();
        
        $this->assertTrue($results->contains($largeCity));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    // =============================================
    // SCOPE TESTS - Search & Filtering
    // =============================================

    /** @test */
    public function scope_search_finds_cities_by_name()
    {
        City::factory()->create([
            'state_id' => $this->state->id,
            'name' => 'Other City'
        ]);

        $results = City::search('Test')->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_recent_returns_recently_created_cities()
    {
        $oldCity = City::factory()->create([
            'state_id' => $this->state->id,
            'created_at' => now()->subDays(60)
        ]);

        $results = City::recent(30)->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertFalse($results->contains($oldCity));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_old_returns_old_cities()
    {
        $oldCity = City::factory()->create([
            'state_id' => $this->state->id,
            'created_at' => now()->subDays(400)
        ]);

        $results = City::old(365)->get();
        
        $this->assertTrue($results->contains($oldCity));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_alphabetical_orders_cities_by_name()
    {
        $cityA = City::factory()->create([
            'state_id' => $this->state->id,
            'name' => 'Alpha City'
        ]);
        $cityZ = City::factory()->create([
            'state_id' => $this->state->id,
            'name' => 'Zulu City'
        ]);

        $results = City::alphabetical()->get();
        
        $this->assertEquals('Alpha City', $results->first()->name);
        $this->assertEquals('Zulu City', $results->last()->name);
    }

    // =============================================
    // SCOPE TESTS - Relationships & Popularity
    // =============================================

    /** @test */
    public function scope_with_companies_returns_cities_with_companies()
    {
        Company::factory()->create(['city_id' => $this->city->id]);
        $cityWithoutCompanies = City::factory()->create(['state_id' => $this->state->id]);

        $results = City::withCompanies()->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertFalse($results->contains($cityWithoutCompanies));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_with_active_companies_returns_cities_with_active_companies()
    {
        Company::factory()->create(['city_id' => $this->city->id, 'is_active' => true]);
        $cityWithInactiveCompany = City::factory()->create(['state_id' => $this->state->id]);
        Company::factory()->create(['city_id' => $cityWithInactiveCompany->id, 'is_active' => false]);

        $results = City::withActiveCompanies()->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertFalse($results->contains($cityWithInactiveCompany));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_with_jobs_returns_cities_with_jobs()
    {
        Job::factory()->create(['city_id' => $this->city->id]);
        $cityWithoutJobs = City::factory()->create(['state_id' => $this->state->id]);

        $results = City::withJobs()->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertFalse($results->contains($cityWithoutJobs));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_with_active_jobs_returns_cities_with_active_jobs()
    {
        Job::factory()->create(['city_id' => $this->city->id, 'status' => 'active']);
        $cityWithInactiveJob = City::factory()->create(['state_id' => $this->state->id]);
        Job::factory()->create(['city_id' => $cityWithInactiveJob->id, 'status' => 'inactive']);

        $results = City::withActiveJobs()->get();
        
        $this->assertTrue($results->contains($this->city));
        $this->assertFalse($results->contains($cityWithInactiveJob));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scope_popular_returns_cities_ordered_by_active_companies_count()
    {
        $city2 = City::factory()->create(['state_id' => $this->state->id]);
        
        // City 1 has 1 active company
        Company::factory()->create(['city_id' => $this->city->id, 'is_active' => true]);
        
        // City 2 has 2 active companies
        Company::factory()->count(2)->create(['city_id' => $city2->id, 'is_active' => true]);

        $results = City::popular(10)->get();
        
        $this->assertEquals($city2->id, $results->first()->id);
        $this->assertEquals($this->city->id, $results->last()->id);
    }

    // =============================================
    // HELPER METHOD TESTS
    // =============================================

    /** @test */
    public function has_coordinates_returns_true_when_both_coordinates_exist()
    {
        $this->assertTrue($this->city->hasCoordinates());
    }

    /** @test */
    public function has_coordinates_returns_false_when_coordinates_missing()
    {
        $city = City::factory()->create([
            'state_id' => $this->state->id,
            'latitude' => null,
            'longitude' => null
        ]);

        $this->assertFalse($city->hasCoordinates());
    }

    /** @test */
    public function get_coordinates_attribute_returns_formatted_coordinates()
    {
        $expected = "40.71280000, -74.00600000";
        $this->assertEquals($expected, $this->city->coordinates);
    }

    /** @test */
    public function get_coordinates_attribute_returns_null_when_no_coordinates()
    {
        $city = City::factory()->create([
            'state_id' => $this->state->id,
            'latitude' => null,
            'longitude' => null
        ]);

        $this->assertNull($city->coordinates);
    }

    /** @test */
    public function get_population_category_attribute_returns_correct_category()
    {
        $this->assertEquals('major', $this->city->population_category);

        $smallCity = City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 30000
        ]);
        $this->assertEquals('small', $smallCity->population_category);

        $mediumCity = City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 200000
        ]);
        $this->assertEquals('large', $mediumCity->population_category);
    }

    /** @test */
    public function get_full_name_attribute_includes_state_and_country()
    {
        $expected = "Test City, Test State, Test Country";
        $this->assertEquals($expected, $this->city->full_name);
    }

    /** @test */
    public function get_display_name_attribute_includes_state()
    {
        $expected = "Test City, Test State";
        $this->assertEquals($expected, $this->city->display_name);
    }

    /** @test */
    public function is_major_returns_true_for_major_cities()
    {
        $this->assertTrue($this->city->isMajor());

        $flaggedCity = City::factory()->create([
            'state_id' => $this->state->id,
            'is_major' => true,
            'population' => 500000
        ]);
        $this->assertTrue($flaggedCity->isMajor());
    }

    /** @test */
    public function is_metropolitan_returns_true_for_metropolitan_cities()
    {
        $this->assertTrue($this->city->isMetropolitan());

        $flaggedCity = City::factory()->create([
            'state_id' => $this->state->id,
            'is_metropolitan' => true,
            'population' => 300000
        ]);
        $this->assertTrue($flaggedCity->isMetropolitan());
    }

    /** @test */
    public function distance_to_calculates_distance_between_cities()
    {
        $city2 = City::factory()->create([
            'state_id' => $this->state->id,
            'latitude' => 34.0522,
            'longitude' => -118.2437
        ]);

        $distance = $this->city->distanceTo($city2);
        
        $this->assertNotNull($distance);
        $this->assertIsFloat($distance);
        $this->assertGreaterThan(3000, $distance); // NYC to LA is about 3944 km
    }

    /** @test */
    public function distance_to_returns_null_when_coordinates_missing()
    {
        $cityWithoutCoords = City::factory()->create([
            'state_id' => $this->state->id,
            'latitude' => null,
            'longitude' => null
        ]);

        $distance = $this->city->distanceTo($cityWithoutCoords);
        
        $this->assertNull($distance);
    }

    // =============================================
    // CACHING TESTS
    // =============================================

    /** @test */
    public function get_cached_by_state_returns_cached_results()
    {
        City::factory()->count(3)->create(['state_id' => $this->state->id, 'is_active' => true]);

        $results1 = City::getCachedByState($this->state->id);
        $results2 = City::getCachedByState($this->state->id);

        $this->assertCount(4, $results1); // Including setup city
        $this->assertEquals($results1, $results2);
        $this->assertTrue(Cache::has("cities_state_{$this->state->id}"));
    }

    /** @test */
    public function get_cached_by_country_returns_cached_results()
    {
        $results1 = City::getCachedByCountry($this->country->id);
        $results2 = City::getCachedByCountry($this->country->id);

        $this->assertCount(1, $results1);
        $this->assertEquals($results1, $results2);
        $this->assertTrue(Cache::has("cities_country_{$this->country->id}"));
    }

    /** @test */
    public function get_cached_active_returns_cached_results()
    {
        $results1 = City::getCachedActive();
        $results2 = City::getCachedActive();

        $this->assertCount(1, $results1);
        $this->assertEquals($results1, $results2);
        $this->assertTrue(Cache::has('cities_active'));
    }

    /** @test */
    public function get_cached_featured_returns_cached_results()
    {
        City::factory()->create(['state_id' => $this->state->id, 'is_featured' => true, 'is_active' => true]);

        $results1 = City::getCachedFeatured();
        $results2 = City::getCachedFeatured();

        $this->assertCount(1, $results1);
        $this->assertEquals($results1, $results2);
        $this->assertTrue(Cache::has('cities_featured'));
    }

    /** @test */
    public function clear_caches_removes_related_cache_keys()
    {
        // Set up some cache entries
        City::getCachedByState($this->state->id);
        City::getCachedActive();
        City::getCachedFeatured();

        $this->city->clearCaches();

        $this->assertFalse(Cache::has("cities_state_{$this->state->id}"));
        $this->assertFalse(Cache::has('cities_active'));
        $this->assertFalse(Cache::has('cities_featured'));
    }

    // =============================================
    // VALIDATION RULES TESTS
    // =============================================

    /** @test */
    public function validation_rules_are_correctly_defined()
    {
        $expectedRules = [
            'name' => 'required|string|max:180|unique:cities,name',
            'state_id' => 'required|integer|exists:states,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_metropolitan' => 'boolean',
            'is_major' => 'boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'timezone' => 'nullable|string|max:50',
            'population' => 'nullable|integer|min:0',
        ];

        $this->assertEquals($expectedRules, City::$rules);
    }

    /** @test */
    public function update_rules_exclude_current_city_from_unique_check()
    {
        $updateRules = City::updateRules($this->city->id);
        
        $this->assertStringContains("unique:cities,name,{$this->city->id}", $updateRules['name']);
    }
}