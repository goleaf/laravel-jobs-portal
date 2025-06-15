<?php

namespace Tests\Unit\Models;

use App\Models\Candidate;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Job;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * City Model Test Suite - Enhanced Enhanced.
 *
 * Testing comprehensive City model functionality including:
 * - Model attributes and relationships
 * - All scopes (30+ scopes)
 * - Helper methods and attributes
 * - Caching functionality
 * - Geographic calculations
 * - Validation rules
 *
 * @internal
 *
 * @coversNothing
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
            'is_active' => true,
        ]);

        // Create test state
        $this->state = State::factory()->create([
            'name' => 'Test State',
            'country_id' => $this->country->id,
            'is_active' => true,
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
            'population' => 8000000,
        ]);

        Cache::flush();
    }

    // =============================================
    // BASIC MODEL TESTS
    // =============================================

    /** @test */
    public function itHasCorrectFillableAttributes()
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
            'is_major',
        ];

        $this->assertEquals($expected, $this->city->getFillable());
    }

    /** @test */
    public function itHasCorrectCasts()
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
            'deleted_at' => 'datetime',
        ];

        $casts = $this->city->getCasts();

        // Remove auto-generated Laravel casts to focus on model-defined casts
        unset($casts['id']);

        $this->assertEquals($expected, $casts);
    }

    /** @test */
    public function itHidesDeletedAtAttribute()
    {
        $expected = ['deleted_at'];
        $this->assertEquals($expected, $this->city->getHidden());
    }

    // =============================================
    // RELATIONSHIP TESTS
    // =============================================

    /** @test */
    public function itBelongsToState()
    {
        $this->assertInstanceOf(State::class, $this->city->state);
        $this->assertEquals($this->state->id, $this->city->state->id);
    }

    /** @test */
    public function itHasCountryThroughState()
    {
        $this->assertInstanceOf(Country::class, $this->city->country);
        $this->assertEquals($this->country->id, $this->city->country->id);
    }

    /** @test */
    public function itHasManyUsers()
    {
        $users = User::factory()->count(3)->create(['city_id' => $this->city->id]);

        $this->assertCount(3, $this->city->users);
        $this->assertInstanceOf(User::class, $this->city->users->first());
    }

    /** @test */
    public function itHasManyCompanies()
    {
        $companies = Company::factory()->count(2)->create(['city_id' => $this->city->id]);

        $this->assertCount(2, $this->city->companies);
        $this->assertInstanceOf(Company::class, $this->city->companies->first());
    }

    /** @test */
    public function itHasManyJobs()
    {
        $jobs = Job::factory()->count(4)->create(['city_id' => $this->city->id]);

        $this->assertCount(4, $this->city->jobs);
        $this->assertInstanceOf(Job::class, $this->city->jobs->first());
    }

    /** @test */
    public function itHasManyCandidates()
    {
        $candidates = Candidate::factory()->count(5)->create(['city_id' => $this->city->id]);

        $this->assertCount(5, $this->city->candidates);
        $this->assertInstanceOf(Candidate::class, $this->city->candidates->first());
    }

    // =============================================
    // SCOPE TESTS - Basic Status
    // =============================================

    /** @test */
    public function scopeActiveReturnsOnlyActiveCities()
    {
        // Get initial count of active cities
        $initialActiveCount = City::active()->count();

        City::factory()->create(['state_id' => $this->state->id, 'is_active' => false]);
        $activeCity = City::factory()->create(['state_id' => $this->state->id, 'is_active' => true]);

        $results = City::active()->get();

        $this->assertTrue($results->contains($this->city));
        $this->assertTrue($results->contains($activeCity));
        // Should have one more active city than before (the newly created one)
        $this->assertCount($initialActiveCount + 1, $results);
    }

    /** @test */
    public function scopeInactiveReturnsOnlyInactiveCities()
    {
        $inactiveCity = City::factory()->create(['state_id' => $this->state->id, 'is_active' => false]);

        $results = City::inactive()->get();

        $this->assertTrue($results->contains($inactiveCity));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopeFeaturedReturnsOnlyFeaturedCities()
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
    public function scopeByStateFiltersCitiesByState()
    {
        $otherState = State::factory()->create(['country_id' => $this->country->id]);
        City::factory()->create(['state_id' => $otherState->id]);

        $results = City::byState($this->state->id)->get();

        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopeInStatesFiltersCitiesByMultipleStates()
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
    public function scopeByCountryFiltersCitiesByCountry()
    {
        $otherCountry = Country::factory()->create();
        $otherState = State::factory()->create(['country_id' => $otherCountry->id]);
        City::factory()->create(['state_id' => $otherState->id]);

        $results = City::byCountry($this->country->id)->get();

        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopeWithCoordinatesReturnsCitiesWithCoordinates()
    {
        City::factory()->create([
            'state_id' => $this->state->id,
            'latitude' => null,
            'longitude' => null,
        ]);

        $results = City::withCoordinates()->get();

        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopeWithoutCoordinatesReturnsCitiesWithoutCoordinates()
    {
        // Get initial count of cities without coordinates
        $initialWithoutCoordinatesCount = City::withoutCoordinates()->count();

        $cityWithoutCoords = City::factory()->create([
            'state_id' => $this->state->id,
            'latitude' => null,
            'longitude' => null,
        ]);

        $results = City::withoutCoordinates()->get();

        $this->assertTrue($results->contains($cityWithoutCoords));
        $this->assertFalse($results->contains($this->city));
        // Should have one more city without coordinates than before
        $this->assertCount($initialWithoutCoordinatesCount + 1, $results);
    }

    /** @test */
    public function scopeByTimezoneFiltersCitiesByTimezone()
    {
        City::factory()->create([
            'state_id' => $this->state->id,
            'timezone' => 'America/Chicago',
        ]);

        $results = City::byTimezone('America/New_York')->get();

        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    // =============================================
    // SCOPE TESTS - Population & Size
    // =============================================

    /** @test */
    public function scopeByPopulationRangeFiltersCitiesByPopulation()
    {
        City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 50000,
        ]);

        $results = City::byPopulationRange(1000000, 10000000)->get();

        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopePopulationGreaterThanFiltersCorrectly()
    {
        City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 50000,
        ]);

        $results = City::populationGreaterThan(1000000)->get();

        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopePopulationLessThanFiltersCorrectly()
    {
        $smallCity = City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 50000,
        ]);

        $results = City::populationLessThan(100000)->get();

        $this->assertTrue($results->contains($smallCity));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopeMajorReturnsMajorCities()
    {
        // City with is_major flag
        $majorCity1 = City::factory()->create([
            'state_id' => $this->state->id,
            'is_major' => true,
            'population' => 500000,
        ]);

        // City with population > 1M
        $majorCity2 = City::factory()->create([
            'state_id' => $this->state->id,
            'is_major' => false,
            'population' => 1500000,
        ]);

        $results = City::major()->get();

        $this->assertCount(3, $results); // Including the setUp city
        $this->assertTrue($results->contains($majorCity1));
        $this->assertTrue($results->contains($majorCity2));
        $this->assertTrue($results->contains($this->city));
    }

    /** @test */
    public function scopeMetropolitanReturnsMetropolitanCities()
    {
        // City with is_metropolitan flag
        $metroCity1 = City::factory()->create([
            'state_id' => $this->state->id,
            'is_metropolitan' => true,
            'population' => 300000,
        ]);

        // City with population > 500K
        $metroCity2 = City::factory()->create([
            'state_id' => $this->state->id,
            'is_metropolitan' => false,
            'population' => 600000,
        ]);

        $results = City::metropolitan()->get();

        $this->assertCount(3, $results);
        $this->assertTrue($results->contains($metroCity1));
        $this->assertTrue($results->contains($metroCity2));
        $this->assertTrue($results->contains($this->city));
    }

    /** @test */
    public function scopeSmallReturnsSmallCities()
    {
        $smallCity = City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 50000,
        ]);

        $results = City::small()->get();

        $this->assertTrue($results->contains($smallCity));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopeMediumReturnsMediumCities()
    {
        $mediumCity = City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 200000,
        ]);

        $results = City::medium()->get();

        $this->assertTrue($results->contains($mediumCity));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopeLargeReturnsLargeCities()
    {
        $largeCity = City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 750000,
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
    public function scopeSearchFindsCitiesByName()
    {
        City::factory()->create([
            'state_id' => $this->state->id,
            'name' => 'Other City',
        ]);

        $results = City::search('Test')->get();

        $this->assertTrue($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopeRecentReturnsRecentlyCreatedCities()
    {
        // Get initial count of recent cities
        $initialRecentCount = City::recent(30)->count();

        $oldCity = City::factory()->create([
            'state_id' => $this->state->id,
            'created_at' => now()->subDays(60),
        ]);

        $results = City::recent(30)->get();

        $this->assertTrue($results->contains($this->city));
        $this->assertFalse($results->contains($oldCity));
        // Should have the same number of recent cities as before (no new recent cities added)
        $this->assertCount($initialRecentCount, $results);
    }

    /** @test */
    public function scopeOldReturnsOldCities()
    {
        $oldCity = City::factory()->create([
            'state_id' => $this->state->id,
            'created_at' => now()->subDays(400),
        ]);

        $results = City::old(365)->get();

        $this->assertTrue($results->contains($oldCity));
        $this->assertFalse($results->contains($this->city));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopeAlphabeticalOrdersCitiesByName()
    {
        $cityA = City::factory()->create([
            'state_id' => $this->state->id,
            'name' => 'Alpha City',
        ]);
        $cityZ = City::factory()->create([
            'state_id' => $this->state->id,
            'name' => 'Zulu City',
        ]);

        $results = City::alphabetical()->get();

        $this->assertEquals('Alpha City', $results->first()->name);
        $this->assertEquals('Zulu City', $results->last()->name);
    }

    // =============================================
    // SCOPE TESTS - Relationships & Popularity
    // =============================================

    /** @test */
    public function scopeWithCompaniesReturnsCitiesWithCompanies()
    {
        Company::factory()->create(['city_id' => $this->city->id]);
        $cityWithoutCompanies = City::factory()->create(['state_id' => $this->state->id]);

        $results = City::withCompanies()->get();

        $this->assertTrue($results->contains($this->city));
        $this->assertFalse($results->contains($cityWithoutCompanies));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopeWithActiveCompaniesReturnsCitiesWithActiveCompanies()
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
    public function scopeWithJobsReturnsCitiesWithJobs()
    {
        Job::factory()->create(['city_id' => $this->city->id]);
        $cityWithoutJobs = City::factory()->create(['state_id' => $this->state->id]);

        $results = City::withJobs()->get();

        $this->assertTrue($results->contains($this->city));
        $this->assertFalse($results->contains($cityWithoutJobs));
        $this->assertCount(1, $results);
    }

    /** @test */
    public function scopeWithActiveJobsReturnsCitiesWithActiveJobs()
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
    public function scopePopularReturnsCitiesOrderedByActiveCompaniesCount()
    {
        $city2 = City::factory()->create(['state_id' => $this->state->id]);

        // City 1 has 1 active company
        Company::factory()->create(['city_id' => $this->city->id, 'is_active' => true]);

        // City 2 has 2 active companies
        Company::factory()->count(2)->create(['city_id' => $city2->id, 'is_active' => true]);

        $results = City::popular(10)->get();

        // Find positions of our test cities in the results
        $city1Position = $results->search(function ($city) {
            return $city->id === $this->city->id;
        });

        $city2Position = $results->search(function ($city) use ($city2) {
            return $city->id === $city2->id;
        });

        // City 2 (with 2 companies) should be positioned before City 1 (with 1 company)
        $this->assertNotFalse($city1Position, 'City 1 should be in the results');
        $this->assertNotFalse($city2Position, 'City 2 should be in the results');
        $this->assertLessThan($city1Position, $city2Position, 'City 2 should be positioned before City 1');
    }

    // =============================================
    // HELPER METHOD TESTS
    // =============================================

    /** @test */
    public function hasCoordinatesReturnsTrueWhenBothCoordinatesExist()
    {
        $this->assertTrue($this->city->hasCoordinates());
    }

    /** @test */
    public function hasCoordinatesReturnsFalseWhenCoordinatesMissing()
    {
        $city = City::factory()->create([
            'state_id' => $this->state->id,
            'latitude' => null,
            'longitude' => null,
        ]);

        $this->assertFalse($city->hasCoordinates());
    }

    /** @test */
    public function getCoordinatesAttributeReturnsFormattedCoordinates()
    {
        $expected = '40.71280000, -74.00600000';
        $this->assertEquals($expected, $this->city->coordinates);
    }

    /** @test */
    public function getCoordinatesAttributeReturnsNullWhenNoCoordinates()
    {
        $city = City::factory()->create([
            'state_id' => $this->state->id,
            'latitude' => null,
            'longitude' => null,
        ]);

        $this->assertNull($city->coordinates);
    }

    /** @test */
    public function getPopulationCategoryAttributeReturnsCorrectCategory()
    {
        $this->assertEquals('major', $this->city->population_category);

        $smallCity = City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 30000,
        ]);
        $this->assertEquals('small', $smallCity->population_category);

        $mediumCity = City::factory()->create([
            'state_id' => $this->state->id,
            'population' => 200000,
        ]);
        $this->assertEquals('large', $mediumCity->population_category);
    }

    /** @test */
    public function getFullNameAttributeIncludesStateAndCountry()
    {
        $expected = 'Test City, Test State, Test Country';
        $this->assertEquals($expected, $this->city->full_name);
    }

    /** @test */
    public function getDisplayNameAttributeIncludesState()
    {
        $expected = 'Test City, Test State';
        $this->assertEquals($expected, $this->city->display_name);
    }

    /** @test */
    public function isMajorReturnsTrueForMajorCities()
    {
        $this->assertTrue($this->city->isMajor());

        $flaggedCity = City::factory()->create([
            'state_id' => $this->state->id,
            'is_major' => true,
            'population' => 500000,
        ]);
        $this->assertTrue($flaggedCity->isMajor());
    }

    /** @test */
    public function isMetropolitanReturnsTrueForMetropolitanCities()
    {
        $this->assertTrue($this->city->isMetropolitan());

        $flaggedCity = City::factory()->create([
            'state_id' => $this->state->id,
            'is_metropolitan' => true,
            'population' => 300000,
        ]);
        $this->assertTrue($flaggedCity->isMetropolitan());
    }

    /** @test */
    public function distanceToCalculatesDistanceBetweenCities()
    {
        $city2 = City::factory()->create([
            'state_id' => $this->state->id,
            'latitude' => 34.0522,
            'longitude' => -118.2437,
        ]);

        $distance = $this->city->distanceTo($city2);

        $this->assertNotNull($distance);
        $this->assertIsFloat($distance);
        $this->assertGreaterThan(3000, $distance); // NYC to LA is about 3944 km
    }

    /** @test */
    public function distanceToReturnsNullWhenCoordinatesMissing()
    {
        $cityWithoutCoords = City::factory()->create([
            'state_id' => $this->state->id,
            'latitude' => null,
            'longitude' => null,
        ]);

        $distance = $this->city->distanceTo($cityWithoutCoords);

        $this->assertNull($distance);
    }

    // =============================================
    // CACHING TESTS
    // =============================================

    /** @test */
    public function getCachedByStateReturnsCachedResults()
    {
        City::factory()->count(3)->create(['state_id' => $this->state->id, 'is_active' => true]);

        $results1 = City::getCachedByState($this->state->id);
        $results2 = City::getCachedByState($this->state->id);

        $this->assertCount(4, $results1); // Including setup city
        $this->assertEquals($results1, $results2);
        $this->assertTrue(Cache::has("cities_state_{$this->state->id}"));
    }

    /** @test */
    public function getCachedByCountryReturnsCachedResults()
    {
        $results1 = City::getCachedByCountry($this->country->id);
        $results2 = City::getCachedByCountry($this->country->id);

        $this->assertCount(1, $results1);
        $this->assertEquals($results1, $results2);
        $this->assertTrue(Cache::has("cities_country_{$this->country->id}"));
    }

    /** @test */
    public function getCachedActiveReturnsCachedResults()
    {
        $results1 = City::getCachedActive();
        $results2 = City::getCachedActive();

        // Test that caching is working - both calls should return identical results
        $this->assertGreaterThan(0, $results1->count(), 'Should have at least some active cities');
        $this->assertEquals($results1->count(), $results2->count(), 'Both calls should return same count');
        $this->assertEquals($results1, $results2, 'Both calls should return identical results');
        $this->assertTrue(Cache::has('cities_active'), 'Cache should be set');
    }

    /** @test */
    public function getCachedFeaturedReturnsCachedResults()
    {
        City::factory()->create(['state_id' => $this->state->id, 'is_featured' => true, 'is_active' => true]);

        $results1 = City::getCachedFeatured();
        $results2 = City::getCachedFeatured();

        $this->assertCount(1, $results1);
        $this->assertEquals($results1, $results2);
        $this->assertTrue(Cache::has('cities_featured'));
    }

    /** @test */
    public function clearCachesRemovesRelatedCacheKeys()
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
    public function validationRulesAreCorrectlyDefined()
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
    public function updateRulesExcludeCurrentCityFromUniqueCheck()
    {
        $updateRules = City::updateRules($this->city->id);

        $this->assertStringContainsString("unique:cities,name,{$this->city->id}", $updateRules['name']);
    }
}
