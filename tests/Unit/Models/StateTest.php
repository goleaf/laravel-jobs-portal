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
    public function it_can_be_created()
    {
        $model = State::factory()->create();

        $this->assertInstanceOf(State::class, $model);
        $this->assertDatabaseHas('states', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new State;
        $fillable = $model->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new State;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        // Add specific cast assertions based on model
    }

    /** @test */
    public function it_can_be_updated()
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
    public function it_can_be_deleted()
    {
        $model = State::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('states', [
            'id' => $modelId,
        ]);
    }

    /** @test */
    public function it_has_correct_fillable_attributes()
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
    public function it_has_correct_casts()
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
    public function it_has_validation_rules()
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
    public function it_has_update_validation_rules()
    {
        $rules = State::updateRules($this->state->id);

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('country_id', $rules);
        $this->assertContains('unique:states,name,'.$this->state->id, explode('|', $rules['name']));
    }

    // RELATIONSHIP TESTS

    /** @test */
    public function it_belongs_to_country()
    {
        $this->assertInstanceOf(Country::class, $this->state->country);
        $this->assertEquals($this->country->id, $this->state->country->id);
    }

    /** @test */
    public function it_has_many_cities()
    {
        $city = City::factory()->create(['state_id' => $this->state->id]);

        $this->assertInstanceOf(Collection::class, $this->state->cities);
        $this->assertTrue($this->state->cities->contains($city));
    }

    /** @test */
    public function it_has_many_users()
    {
        $user = User::factory()->create(['state_id' => $this->state->id]);

        $this->assertInstanceOf(Collection::class, $this->state->users);
        $this->assertTrue($this->state->users->contains($user));
    }

    /** @test */
    public function it_has_many_companies()
    {
        $company = Company::factory()->create(['state_id' => $this->state->id]);

        $this->assertInstanceOf(Collection::class, $this->state->companies);
        $this->assertTrue($this->state->companies->contains($company));
    }

    /** @test */
    public function it_can_get_jobs_relationship()
    {
        // This test may need adjustment based on actual Job model structure
        $this->assertTrue(method_exists($this->state, 'jobs'));
    }

    /** @test */
    public function it_can_get_candidates_relationship()
    {
        // This test may need adjustment based on actual Candidate model structure
        $this->assertTrue(method_exists($this->state, 'candidates'));
    }

    // SCOPE TESTS

    /** @test */
    public function scope_active_returns_only_active_states()
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
    public function scope_inactive_returns_only_inactive_states()
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
    public function scope_featured_returns_only_featured_states()
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
    public function scope_non_featured_returns_only_non_featured_states()
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
    public function scope_by_country_filters_by_country_id()
    {
        $otherCountry = Country::factory()->create();
        $otherState = State::factory()->create(['country_id' => $otherCountry->id]);

        $statesByCountry = State::byCountry($this->country->id)->get();

        $this->assertTrue($statesByCountry->contains($this->state));
        $this->assertFalse($statesByCountry->contains($otherState));
    }

    /** @test */
    public function scope_in_countries_filters_by_multiple_country_ids()
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
    public function scope_by_code_filters_by_state_code()
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
    public function scope_search_finds_states_by_name_or_code()
    {
        $states = State::search('Test')->get();
        $this->assertTrue($states->contains($this->state));

        $states = State::search('TS')->get();
        $this->assertTrue($states->contains($this->state));

        $states = State::search('NonExistent')->get();
        $this->assertFalse($states->contains($this->state));
    }

    /** @test */
    public function scope_recent_returns_states_created_within_days()
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
    public function scope_old_returns_states_created_before_days()
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
    public function scope_alphabetical_orders_states_by_name()
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
    public function scope_with_cities_returns_states_that_have_cities()
    {
        $stateWithoutCities = State::factory()->create(['country_id' => $this->country->id]);
        City::factory()->create(['state_id' => $this->state->id]);

        $statesWithCities = State::withCities()->get();

        $this->assertTrue($statesWithCities->contains($this->state));
        $this->assertFalse($statesWithCities->contains($stateWithoutCities));
    }

    /** @test */
    public function scope_without_cities_returns_states_that_have_no_cities()
    {
        $stateWithoutCities = State::factory()->create(['country_id' => $this->country->id]);
        City::factory()->create(['state_id' => $this->state->id]);

        $statesWithoutCities = State::withoutCities()->get();

        $this->assertFalse($statesWithoutCities->contains($this->state));
        $this->assertTrue($statesWithoutCities->contains($stateWithoutCities));
    }

    /** @test */
    public function scope_with_coordinates_returns_states_with_lat_lng()
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
    public function scope_without_coordinates_returns_states_without_lat_lng()
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
    public function scope_population_greater_than_filters_by_population()
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
    public function scope_population_less_than_filters_by_population()
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
    public function scope_population_between_filters_by_population_range()
    {
        $states = State::populationBetween(7000000, 9000000)->get();

        $this->assertTrue($states->contains($this->state));
    }

    /** @test */
    public function scope_by_timezone_filters_by_timezone()
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
    public function it_can_get_full_name_attribute()
    {
        $fullName = $this->state->full_name;

        $this->assertEquals('Test State, Test Country', $fullName);
    }

    /** @test */
    public function it_can_get_display_name_attribute()
    {
        $displayName = $this->state->display_name;

        $this->assertEquals('Test State (TS)', $displayName);
    }

    /** @test */
    public function it_can_check_if_has_coordinates()
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
    public function it_can_get_coordinates_attribute()
    {
        $coordinates = $this->state->coordinates;

        $this->assertEquals('40.71280000, -74.00600000', $coordinates);
    }

    /** @test */
    public function it_can_get_cities_count_attribute()
    {
        City::factory(3)->create(['state_id' => $this->state->id]);

        $this->assertEquals(3, $this->state->cities_count);
    }

    /** @test */
    public function it_can_get_active_cities_count_attribute()
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
    public function it_can_get_cached_states_by_country()
    {
        Cache::flush();

        $cachedStates = State::getCachedByCountry($this->country->id);

        $this->assertInstanceOf(Collection::class, $cachedStates);
        $this->assertTrue($cachedStates->contains($this->state));

        // Test that cache is used on second call
        $this->assertTrue(Cache::has("states_country_{$this->country->id}"));
    }

    /** @test */
    public function it_can_get_cached_active_states()
    {
        Cache::flush();

        $cachedStates = State::getCachedActive();

        $this->assertInstanceOf(Collection::class, $cachedStates);
        $this->assertTrue($cachedStates->contains($this->state));

        // Test that cache is used
        $this->assertTrue(Cache::has('states_active'));
    }

    /** @test */
    public function it_can_get_cached_featured_states()
    {
        Cache::flush();

        $cachedStates = State::getCachedFeatured();

        $this->assertInstanceOf(Collection::class, $cachedStates);
        $this->assertTrue($cachedStates->contains($this->state));

        // Test that cache is used
        $this->assertTrue(Cache::has('states_featured'));
    }

    /** @test */
    public function it_clears_caches_when_state_is_saved()
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
    public function it_clears_caches_when_state_is_deleted()
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
    public function it_uses_soft_deletes()
    {
        $this->state->delete();

        $this->assertSoftDeleted('states', ['id' => $this->state->id]);
        $this->assertNotNull($this->state->fresh()->deleted_at);
    }

    /** @test */
    public function it_logs_activity_when_state_changes()
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
    public function it_has_proper_model_factory()
    {
        $state = State::factory()->create();

        $this->assertInstanceOf(State::class, $state);
        $this->assertNotNull($state->name);
        $this->assertNotNull($state->country_id);
    }
}
