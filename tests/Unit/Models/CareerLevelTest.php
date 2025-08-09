<?php

namespace Tests\Unit\Models;

use App\Models\Candidate;
use App\Models\CareerLevel;
use App\Models\Job;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CareerLevelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $model = CareerLevel::factory()->create();

        $this->assertInstanceOf(CareerLevel::class, $model);
        $this->assertDatabaseHas('career_levels', [
            'id' => $model->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $model = new CareerLevel;
        $fillable = $model->getFillable();

        $expectedFillable = ['level_name', 'description', 'is_default', 'is_active'];

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
        foreach ($expectedFillable as $field) {
            $this->assertContains($field, $fillable);
        }
    }

    /** @test */
    public function it_has_proper_casts()
    {
        $model = new CareerLevel;
        $casts = $model->getCasts();

        $this->assertIsArray($casts);
        $this->assertArrayHasKey('id', $casts);
        $this->assertArrayHasKey('is_default', $casts);
        $this->assertArrayHasKey('is_active', $casts);
        $this->assertArrayHasKey('created_at', $casts);
        $this->assertArrayHasKey('updated_at', $casts);

        // Verify cast types
        $this->assertEquals('int', $casts['id']);
        $this->assertEquals('boolean', $casts['is_default']);
        $this->assertEquals('boolean', $casts['is_active']);
        $this->assertEquals('datetime', $casts['created_at']);
        $this->assertEquals('datetime', $casts['updated_at']);
    }

    /** @test */
    public function it_can_be_updated()
    {
        $model = CareerLevel::factory()->create();

        $newData = ['level_name' => 'Updated Career Level'];
        $model->update($newData);

        $this->assertDatabaseHas('career_levels', [
            'id' => $model->id,
            'level_name' => 'Updated Career Level',
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $model = CareerLevel::factory()->create();
        $modelId = $model->id;

        $model->delete();

        $this->assertDatabaseMissing('career_levels', [
            'id' => $modelId,
        ]);
    }

    /** @test */
    public function it_has_jobs_relationship()
    {
        $careerLevel = CareerLevel::factory()->create();
        $jobs = Job::factory()->count(3)->create(['career_level_id' => $careerLevel->id]);

        $this->assertInstanceOf(HasMany::class, $careerLevel->jobs());
        $this->assertCount(3, $careerLevel->jobs);
        $this->assertEquals($jobs->pluck('id')->sort(), $careerLevel->jobs->pluck('id')->sort());
    }

    /** @test */
    public function it_has_candidates_relationship()
    {
        $this->markTestSkipped('Candidates depend on users/auth (removed). Skipping relationship test.');
    }

    /** @test */
    public function scope_active_returns_active_career_levels()
    {
        // Get initial count of active career levels
        $initialActiveCount = CareerLevel::active()->count();

        // Create test data
        CareerLevel::factory()->count(3)->create(['is_active' => true]);
        CareerLevel::factory()->count(2)->create(['is_active' => false]);

        $activeCareerLevels = CareerLevel::active()->get();

        // Should have initial count + 3 new active ones
        $this->assertCount($initialActiveCount + 3, $activeCareerLevels);

        // Check that all returned records are active
        $activeCareerLevels->each(function ($careerLevel) {
            $this->assertTrue($careerLevel->is_active);
        });
    }

    /** @test */
    public function scope_inactive_returns_inactive_career_levels()
    {
        // Get initial count of inactive career levels
        $initialInactiveCount = CareerLevel::inactive()->count();

        CareerLevel::factory()->count(3)->create(['is_active' => true]);
        CareerLevel::factory()->count(2)->create(['is_active' => false]);

        $inactiveCareerLevels = CareerLevel::inactive()->get();

        // Should have initial count + 2 new inactive ones
        $this->assertCount($initialInactiveCount + 2, $inactiveCareerLevels);
        $inactiveCareerLevels->each(function ($careerLevel) {
            $this->assertFalse($careerLevel->is_active);
        });
    }

    /** @test */
    public function scope_default_returns_default_career_levels()
    {
        // Get initial count of default career levels
        $initialDefaultCount = CareerLevel::default()->count();

        CareerLevel::factory()->count(3)->create(['is_default' => false]);
        CareerLevel::factory()->count(1)->create(['is_default' => true]);

        $defaultCareerLevels = CareerLevel::default()->get();

        // Should have initial count + 1 new default one
        $this->assertCount($initialDefaultCount + 1, $defaultCareerLevels);
        $this->assertTrue($defaultCareerLevels->last()->is_default);
    }

    /** @test */
    public function scope_custom_returns_custom_career_levels()
    {
        // Get initial count of custom career levels
        $initialCustomCount = CareerLevel::custom()->count();

        CareerLevel::factory()->count(2)->create(['is_default' => false]);
        CareerLevel::factory()->count(1)->create(['is_default' => true]);

        $customCareerLevels = CareerLevel::custom()->get();

        // Should have initial count + 2 new custom ones
        $this->assertCount($initialCustomCount + 2, $customCareerLevels);
        $customCareerLevels->take(2)->each(function ($careerLevel) {
            $this->assertFalse($careerLevel->is_default);
        });
    }

    /** @test */
    public function scope_search_finds_career_levels_by_level_name()
    {
        $uniqueTestLevel = CareerLevel::factory()->create(['level_name' => 'TestUniqueEntry Level']);
        CareerLevel::factory()->create(['level_name' => 'Senior Management']);
        CareerLevel::factory()->create(['level_name' => 'Mid-Level Executive']);

        $results = CareerLevel::search('TestUniqueEntry')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('TestUniqueEntry Level', $results->first()->level_name);
        $this->assertEquals($uniqueTestLevel->id, $results->first()->id);
    }

    /** @test */
    public function scope_recent_returns_recently_created_career_levels()
    {
        // Ensure clean state by truncating table
        CareerLevel::truncate();

        // Create old career levels
        CareerLevel::factory()->count(2)->create(['created_at' => now()->subDays(60)]);

        // Create recent career levels
        CareerLevel::factory()->count(3)->create(['created_at' => now()->subDays(15)]);

        $recentCareerLevels = CareerLevel::recent(30)->get();

        $this->assertCount(3, $recentCareerLevels);
    }

    /** @test */
    public function scope_old_returns_old_career_levels()
    {
        // Create old career levels
        CareerLevel::factory()->count(2)->create(['created_at' => now()->subDays(400)]);

        // Create recent career levels
        CareerLevel::factory()->count(3)->create(['created_at' => now()->subDays(15)]);

        $oldCareerLevels = CareerLevel::old(365)->get();

        $this->assertCount(2, $oldCareerLevels);
    }

    /** @test */
    public function scope_with_jobs_returns_career_levels_that_have_jobs()
    {
        $careerLevelWithJobs = CareerLevel::factory()->create();
        $careerLevelWithoutJobs = CareerLevel::factory()->create();

        Job::factory()->create(['career_level_id' => $careerLevelWithJobs->id]);

        $careerLevelsWithJobs = CareerLevel::withJobs()->get();

        $this->assertCount(1, $careerLevelsWithJobs);
        $this->assertEquals($careerLevelWithJobs->id, $careerLevelsWithJobs->first()->id);
    }

    /** @test */
    public function scope_with_candidates_returns_career_levels_that_have_candidates()
    {
        // Get initial count of career levels with candidates
        $initialWithCandidatesCount = CareerLevel::withCandidates()->count();

        $this->markTestSkipped('Candidates depend on users/auth (removed). Skipping scope test.');
    }

    /** @test */
    public function scope_alphabetical_orders_career_levels_by_level_name()
    {
        $zebraLevel = CareerLevel::factory()->create(['level_name' => 'Zebra Level']);
        $alphaLevel = CareerLevel::factory()->create(['level_name' => 'Alpha Level']);
        $betaLevel = CareerLevel::factory()->create(['level_name' => 'Beta Level']);

        $orderedCareerLevels = CareerLevel::alphabetical()->get();

        // Check that our test levels are in the correct alphabetical order within the results
        $alphaPosition = $orderedCareerLevels->search(function ($item) use ($alphaLevel) {
            return $item->id === $alphaLevel->id;
        });
        $betaPosition = $orderedCareerLevels->search(function ($item) use ($betaLevel) {
            return $item->id === $betaLevel->id;
        });
        $zebraPosition = $orderedCareerLevels->search(function ($item) use ($zebraLevel) {
            return $item->id === $zebraLevel->id;
        });

        // Alpha should come before Beta, and Beta should come before Zebra
        $this->assertTrue($alphaPosition < $betaPosition);
        $this->assertTrue($betaPosition < $zebraPosition);
    }

    /** @test */
    public function scope_popular_returns_most_used_career_levels()
    {
        $popularCareerLevel = CareerLevel::factory()->create();
        $lessPopularCareerLevel = CareerLevel::factory()->create();

        // Create more jobs for the popular level
        Job::factory()->count(5)->create(['career_level_id' => $popularCareerLevel->id]);
        Job::factory()->count(2)->create(['career_level_id' => $lessPopularCareerLevel->id]);

        $popularCareerLevels = CareerLevel::popular(1)->get();

        $this->assertCount(1, $popularCareerLevels);
        $this->assertEquals($popularCareerLevel->id, $popularCareerLevels->first()->id);
    }

    /** @test */
    public function scope_entry_returns_entry_level_career_levels()
    {
        // Get initial count to work with existing data
        $initialEntryCount = CareerLevel::entry()->count();

        $entryLevel1 = CareerLevel::factory()->create(['level_name' => 'TestUniqueEntry Level']);
        $entryLevel2 = CareerLevel::factory()->create(['level_name' => 'TestUniqueJunior Position']);
        CareerLevel::factory()->create(['level_name' => 'TestSenior Management']);

        $entryCareerLevels = CareerLevel::entry()->get();

        // Should have initial count + 2 new entry levels
        $this->assertCount($initialEntryCount + 2, $entryCareerLevels);

        // Verify our specific entry levels are included
        $this->assertTrue($entryCareerLevels->contains('id', $entryLevel1->id));
        $this->assertTrue($entryCareerLevels->contains('id', $entryLevel2->id));
    }

    /** @test */
    public function scope_senior_returns_senior_level_career_levels()
    {
        // Get initial count to work with existing data
        $initialSeniorCount = CareerLevel::senior()->count();

        $seniorLevel1 = CareerLevel::factory()->create(['level_name' => 'TestUniqueSenior Developer']);
        $seniorLevel2 = CareerLevel::factory()->create(['level_name' => 'TestUniqueSenior Manager']);
        CareerLevel::factory()->create(['level_name' => 'TestJunior Developer']);

        $seniorCareerLevels = CareerLevel::senior()->get();

        // Should have initial count + 2 new senior levels
        $this->assertCount($initialSeniorCount + 2, $seniorCareerLevels);

        // Verify our specific senior levels are included
        $this->assertTrue($seniorCareerLevels->contains('id', $seniorLevel1->id));
        $this->assertTrue($seniorCareerLevels->contains('id', $seniorLevel2->id));
    }

    /** @test */
    public function scope_management_returns_management_career_levels()
    {
        // Get initial count to work with existing data
        $initialManagementCount = CareerLevel::management()->count();

        $managerLevel = CareerLevel::factory()->create(['level_name' => 'TestUniqueManager']);
        $executiveLevel = CareerLevel::factory()->create(['level_name' => 'TestUniqueExecutive Level']);
        CareerLevel::factory()->create(['level_name' => 'TestDeveloper']);

        $managementCareerLevels = CareerLevel::management()->get();

        // Should have initial count + 2 new management levels
        $this->assertCount($initialManagementCount + 2, $managementCareerLevels);

        // Verify our specific management levels are included
        $this->assertTrue($managementCareerLevels->contains('id', $managerLevel->id));
        $this->assertTrue($managementCareerLevels->contains('id', $executiveLevel->id));
    }
}
