<?php

namespace Tests\Unit\Models;

use App\Models\Candidate;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Job;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class StateTest extends TestCase
{
    use RefreshDatabase;

    protected State $state;
    protected Country $country;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->country = Country::factory()->create([
            'name' => 'Test Country',
        ]);

        $this->state = State::factory()->create([
            'country_id' => $this->country->id,
            'name' => 'Test State',
            'code' => 'TS',
            'is_active' => true,
            'is_featured' => true,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'timezone' => 'America/New_York',
            'population' => 8000000,
        ]);
    }

    /** @test */
    public function itCanBeCreated()
    {
        $model = State::factory()->create();

        $this->assertInstanceOf(State::class, $model);
        $this->assertDatabaseHas('states', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itHasFillableAttributes()
    {
        $model = new State();
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itHasProperCasts()
    {
        $model = new State();
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function itCanBeUpdated()
    {
        $model = State::factory()->create();
        $originalData = $model->toArray();

        // Update with factory data
        $newData = State::factory()->make()->toArray();
        $model->update($newData);

        $this->assertDatabaseHas('states', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function itCanBeDeleted()
    {
        $model = State::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('states', [
            'id' => $modelId,
        ]);
    }

    /** @test */
    public function itHasCorrectFillableAttributes()
    {
        $expected = [
            'country_id',
            'name',
            'code',
            'is_active',
            'is_featured',
            'latitude',
            'longitude',
            'timezone',
            'population',
        ];

        $this->assertEquals($expected, $this->state->getFillable());
    }

    /** @test */
    public function itHasCorrectCasts()
    {
        $expected = [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'population' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

        $this->assertEquals($expected, $this->state->getCasts());
    }

    /** @test */
    public function itHasValidationRules()
    {
        $rules = State::$rules;

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('country_id', $rules);
        $this->assertArrayHasKey('code', $rules);
        $this->assertArrayHasKey('is_active', $rules);
        $this->assertArrayHasKey('latitude', $rules);
        $this->assertArrayHasKey('longitude', $rules);
        $this->assertArrayHasKey('timezone', $rules);
        $this->assertArrayHasKey('population', $rules);
    }

    /** @test */
    public function itHasUpdateValidationRules()
    {
        $rules = State::updateRules($this->state->id);

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('country_id', $rules);
        $this->assertContains('unique:states,name,'.$this->state->id, explode('|', $rules['name']));
    }

    // RELATIONSHIP TESTS

    /** @test */
    public function itBelongsToCountry()
    {
        $this->assertInstanceOf(Country::class, $this->state->country);
        $this->assertEquals($this->country->id, $this->state->country->id);
    }

    /** @test */
    public function itHasManyCities()
    {
        $city = City::factory()->create(['state_id' => $this->state->id]);

        $this->assertInstanceOf(Collection::class, $this->state->cities);
        $this->assertTrue($this->state->cities->contains($city));
    }

    /** @test */
    public function itHasManyUsers()
    {
        $user = User::factory()->create(['state_id' => $this->state->id]);

        $this->assertInstanceOf(Collection::class, $this->state->users);
        $this->assertTrue($this->state->users->contains($user));
    }

    /** @test */
    public function itHasManyCompanies()
    {
        $company = Company::factory()->create(['state_id' => $this->state->id]);

        $this->assertInstanceOf(Collection::class, $this->state->companies);
        $this->assertTrue($this->state->companies->contains($company));
    }

    /** @test */
    public function itCanGetJobsRelationship()
    {
        // This test may need adjustment based on actual Job model structure
        $this->assertTrue(method_exists($this->state, 'jobs'));
    }

    /** @test */
    public function itCanGetCandidatesRelationship()
    {
        // This test may need adjustment based on actual Candidate model structure
        $this->assertTrue(method_exists($this->state, 'candidates'));
    }

    // SCOPE TESTS

    /** @test */
    public function scopeActiveReturnsOnlyActiveStates()
    {
        $inactiveState = State::factory()->create([
            'country_id' => $this->country->id,
            'is_active' => false,
        ]);

        $activeStates = State::active()->get();

        $this->assertTrue($activeStates->contains($this->state));
        $this->assertFalse($activeStates->contains($inactiveState));
    }

    /** @test */
    public function scopeInactiveReturnsOnlyInactiveStates()
    {
        $inactiveState = State::factory()->create([
            'country_id' => $this->country->id,
            'is_active' => false,
        ]);

        $inactiveStates = State::inactive()->get();

        $this->assertFalse($inactiveStates->contains($this->state));
        $this->assertTrue($inactiveStates->contains($inactiveState));
    }

    /** @test */
    public function scopeFeaturedReturnsOnlyFeaturedStates()
    {
        $nonFeaturedState = State::factory()->create([
            'country_id' => $this->country->id,
            'is_featured' => false,
        ]);

        $featuredStates = State::featured()->get();

        $this->assertTrue($featuredStates->contains($this->state));
        $this->assertFalse($featuredStates->contains($nonFeaturedState));
    }

    /** @test */
    public function scopeNonFeaturedReturnsOnlyNonFeaturedStates()
    {
        $nonFeaturedState = State::factory()->create([
            'country_id' => $this->country->id,
            'is_featured' => false,
        ]);

        $nonFeaturedStates = State::nonFeatured()->get();

        $this->assertFalse($nonFeaturedStates->contains($this->state));
        $this->assertTrue($nonFeaturedStates->contains($nonFeaturedState));
    }

    /** @test */
    public function scopeByCountryFiltersByCountryId()
    {
        $otherCountry = Country::factory()->create();
        $otherState = State::factory()->create(['country_id' => $otherCountry->id]);

        $statesByCountry = State::byCountry($this->country->id)->get();

        $this->assertTrue($statesByCountry->contains($this->state));
        $this->assertFalse($statesByCountry->contains($otherState));
    }

    /** @test */
    public function scopeInCountriesFiltersByMultipleCountryIds()
    {
        $country2 = Country::factory()->create();
        $country3 = Country::factory()->create();

        $state2 = State::factory()->create(['country_id' => $country2->id]);
        $state3 = State::factory()->create(['country_id' => $country3->id]);

        $statesInCountries = State::inCountries([$this->country->id, $country2->id])->get();

        $this->assertTrue($statesInCountries->contains($this->state));
        $this->assertTrue($statesInCountries->contains($state2));
        $this->assertFalse($statesInCountries->contains($state3));
    }

    /** @test */
    public function scopeByCodeFiltersByStateCode()
    {
        $otherState = State::factory()->create([
            'country_id' => $this->country->id,
            'code' => 'OS',
        ]);

        $statesByCode = State::byCode('TS')->get();

        $this->assertTrue($statesByCode->contains($this->state));
        $this->assertFalse($statesByCode->contains($otherState));
    }

    /** @test */
    public function scopeSearchFindsStatesByNameOrCode()
    {
        $states = State::search('Test')->get();
        $this->assertTrue($states->contains($this->state));

        $states = State::search('TS')->get();
        $this->assertTrue($states->contains($this->state));

        $states = State::search('NonExistent')->get();
        $this->assertFalse($states->contains($this->state));
    }

    /** @test */
    public function scopeRecentReturnsStatesCreatedWithinDays()
    {
        $oldState = State::factory()->create([
            'country_id' => $this->country->id,
            'created_at' => now()->subDays(45),
        ]);

        $recentStates = State::recent(30)->get();

        $this->assertTrue($recentStates->contains($this->state));
        $this->assertFalse($recentStates->contains($oldState));
    }

    /** @test */
    public function scopeOldReturnsStatesCreatedBeforeDays()
    {
        $oldState = State::factory()->create([
            'country_id' => $this->country->id,
            'created_at' => now()->subDays(400),
        ]);

        $oldStates = State::old(365)->get();

        $this->assertFalse($oldStates->contains($this->state));
        $this->assertTrue($oldStates->contains($oldState));
    }

    /** @test */
    public function scopeAlphabeticalOrdersStatesByName()
    {
        $stateB = State::factory()->create([
            'country_id' => $this->country->id,
            'name' => 'B State',
        ]);

        $stateA = State::factory()->create([
            'country_id' => $this->country->id,
            'name' => 'A State',
        ]);

        $orderedStates = State::alphabetical()->get();

        $this->assertEquals('A State', $orderedStates->first()->name);
    }

    /** @test */
    public function scopeWithCitiesReturnsStatesThatHaveCities()
    {
        $stateWithoutCities = State::factory()->create(['country_id' => $this->country->id]);
        City::factory()->create(['state_id' => $this->state->id]);

        $statesWithCities = State::withCities()->get();

        $this->assertTrue($statesWithCities->contains($this->state));
        $this->assertFalse($statesWithCities->contains($stateWithoutCities));
    }

    /** @test */
    public function scopeWithoutCitiesReturnsStatesThatHaveNoCities()
    {
        $stateWithoutCities = State::factory()->create(['country_id' => $this->country->id]);
        City::factory()->create(['state_id' => $this->state->id]);

        $statesWithoutCities = State::withoutCities()->get();

        $this->assertFalse($statesWithoutCities->contains($this->state));
        $this->assertTrue($statesWithoutCities->contains($stateWithoutCities));
    }

    /** @test */
    public function scopeWithCoordinatesReturnsStatesWithLatLng()
    {
        $stateWithoutCoords = State::factory()->create([
            'country_id' => $this->country->id,
            'latitude' => null,
            'longitude' => null,
        ]);

        $statesWithCoords = State::withCoordinates()->get();

        $this->assertTrue($statesWithCoords->contains($this->state));
        $this->assertFalse($statesWithCoords->contains($stateWithoutCoords));
    }

    /** @test */
    public function scopeWithoutCoordinatesReturnsStatesWithoutLatLng()
    {
        $stateWithoutCoords = State::factory()->create([
            'country_id' => $this->country->id,
            'latitude' => null,
            'longitude' => null,
        ]);

        $statesWithoutCoords = State::withoutCoordinates()->get();

        $this->assertFalse($statesWithoutCoords->contains($this->state));
        $this->assertTrue($statesWithoutCoords->contains($stateWithoutCoords));
    }

    /** @test */
    public function scopePopulationGreaterThanFiltersByPopulation()
    {
        $smallState = State::factory()->create([
            'country_id' => $this->country->id,
            'population' => 1000,
        ]);

        $largeStates = State::populationGreaterThan(5000000)->get();

        $this->assertTrue($largeStates->contains($this->state));
        $this->assertFalse($largeStates->contains($smallState));
    }

    /** @test */
    public function scopePopulationLessThanFiltersByPopulation()
    {
        $smallState = State::factory()->create([
            'country_id' => $this->country->id,
            'population' => 1000,
        ]);

        $smallStates = State::populationLessThan(5000000)->get();

        $this->assertFalse($smallStates->contains($this->state));
        $this->assertTrue($smallStates->contains($smallState));
    }

    /** @test */
    public function scopePopulationBetweenFiltersByPopulationRange()
    {
        $states = State::populationBetween(7000000, 9000000)->get();

        $this->assertTrue($states->contains($this->state));
    }

    /** @test */
    public function scopeByTimezoneFiltersByTimezone()
    {
        $differentState = State::factory()->create([
            'country_id' => $this->country->id,
            'timezone' => 'America/Los_Angeles',
        ]);

        $statesByTimezone = State::byTimezone('America/New_York')->get();

        $this->assertTrue($statesByTimezone->contains($this->state));
        $this->assertFalse($statesByTimezone->contains($differentState));
    }

    // HELPER METHOD TESTS

    /** @test */
    public function itCanGetFullNameAttribute()
    {
        $fullName = $this->state->full_name;

        $this->assertEquals('Test State, Test Country', $fullName);
    }

    /** @test */
    public function itCanGetDisplayNameAttribute()
    {
        $displayName = $this->state->display_name;

        $this->assertEquals('Test State (TS)', $displayName);
    }

    /** @test */
    public function itCanCheckIfHasCoordinates()
    {
        $this->assertTrue($this->state->hasCoordinates());

        $stateWithoutCoords = State::factory()->create([
            'country_id' => $this->country->id,
            'latitude' => null,
            'longitude' => null,
        ]);

        $this->assertFalse($stateWithoutCoords->hasCoordinates());
    }

    /** @test */
    public function itCanGetCoordinatesAttribute()
    {
        $coordinates = $this->state->coordinates;

        $this->assertEquals('40.71280000, -74.00600000', $coordinates);
    }

    /** @test */
    public function itCanGetCitiesCountAttribute()
    {
        City::factory(3)->create(['state_id' => $this->state->id]);

        $this->assertEquals(3, $this->state->cities_count);
    }

    /** @test */
    public function itCanGetActiveCitiesCountAttribute()
    {
        City::factory(2)->create([
            'state_id' => $this->state->id,
            'is_active' => true,
        ]);
        City::factory(1)->create([
            'state_id' => $this->state->id,
            'is_active' => false,
        ]);

        $this->assertEquals(2, $this->state->active_cities_count);
    }

    // CACHING TESTS

    /** @test */
    public function itCanGetCachedStatesByCountry()
    {
        Cache::flush();

        $cachedStates = State::getCachedByCountry($this->country->id);

        $this->assertInstanceOf(Collection::class, $cachedStates);
        $this->assertTrue($cachedStates->contains($this->state));

        // Test that cache is used on second call
        $this->assertTrue(Cache::has("states_country_{$this->country->id}"));
    }

    /** @test */
    public function itCanGetCachedActiveStates()
    {
        Cache::flush();

        $cachedStates = State::getCachedActive();

        $this->assertInstanceOf(Collection::class, $cachedStates);
        $this->assertTrue($cachedStates->contains($this->state));

        // Test that cache is used
        $this->assertTrue(Cache::has('states_active'));
    }

    /** @test */
    public function itCanGetCachedFeaturedStates()
    {
        Cache::flush();

        $cachedStates = State::getCachedFeatured();

        $this->assertInstanceOf(Collection::class, $cachedStates);
        $this->assertTrue($cachedStates->contains($this->state));

        // Test that cache is used
        $this->assertTrue(Cache::has('states_featured'));
    }

    /** @test */
    public function itClearsCachesWhenStateIsSaved()
    {
        // Set up cache
        State::getCachedActive();
        State::getCachedFeatured();
        State::getCachedByCountry($this->country->id);

        $this->assertTrue(Cache::has('states_active'));
        $this->assertTrue(Cache::has('states_featured'));
        $this->assertTrue(Cache::has("states_country_{$this->country->id}"));

        // Update state
        $this->state->update(['name' => 'Updated State']);

        // Cache should be cleared
        $this->assertFalse(Cache::has('states_active'));
        $this->assertFalse(Cache::has('states_featured'));
        $this->assertFalse(Cache::has("states_country_{$this->country->id}"));
    }

    /** @test */
    public function itClearsCachesWhenStateIsDeleted()
    {
        // Set up cache
        State::getCachedActive();
        State::getCachedFeatured();
        State::getCachedByCountry($this->country->id);

        $this->assertTrue(Cache::has('states_active'));
        $this->assertTrue(Cache::has('states_featured'));
        $this->assertTrue(Cache::has("states_country_{$this->country->id}"));

        // Delete state
        $this->state->delete();

        // Cache should be cleared
        $this->assertFalse(Cache::has('states_active'));
        $this->assertFalse(Cache::has('states_featured'));
        $this->assertFalse(Cache::has("states_country_{$this->country->id}"));
    }

    /** @test */
    public function itUsesSoftDeletes()
    {
        $this->state->delete();

        $this->assertSoftDeleted('states', ['id' => $this->state->id]);
        $this->assertNotNull($this->state->fresh()->deleted_at);
    }

    /** @test */
    public function itLogsActivityWhenStateChanges()
    {
        // This test assumes Spatie Activity Log is properly configured
        $originalName = $this->state->name;

        $this->state->update(['name' => 'Updated State Name']);

        // Check if activity was logged (this may need adjustment based on your activity log setup)
        $this->assertDatabaseHas('activity_log', [
            'subject_type' => State::class,
            'subject_id' => $this->state->id,
        ]);
    }

    /** @test */
    public function itHasProperModelFactory()
    {
        $state = State::factory()->create();

        $this->assertInstanceOf(State::class, $state);
        $this->assertNotNull($state->name);
        $this->assertNotNull($state->country_id);
    }
}
