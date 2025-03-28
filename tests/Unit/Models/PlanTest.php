<?php

namespace Tests\Unit\Models;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_plan()
    {
        $plan = Plan::create([
            'name' => 'Basic Plan',
            'amount' => 9.99,
            'job_limit' => 5,
            'featured_job_limit' => 1,
            'validity_in_days' => 30,
            'is_trial_plan' => 0,
            'currency_id' => 1,
        ]);
        
        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => 'Basic Plan',
            'amount' => 9.99,
        ]);
    }

    /** @test */
    public function it_has_many_subscriptions()
    {
        $plan = Plan::create([
            'name' => 'Premium Plan',
            'amount' => 19.99,
            'job_limit' => 10,
            'featured_job_limit' => 3,
            'validity_in_days' => 30,
            'is_trial_plan' => 0,
            'currency_id' => 1,
        ]);
        
        // Create subscriptions for this plan
        Subscription::create([
            'plan_id' => $plan->id,
            'user_id' => 1,
            'ends_at' => now()->addDays(30),
            'status' => 1,
        ]);
        
        Subscription::create([
            'plan_id' => $plan->id,
            'user_id' => 2,
            'ends_at' => now()->addDays(30),
            'status' => 1,
        ]);
        
        $this->assertCount(2, $plan->subscriptions);
    }

    /** @test */
    public function it_can_determine_if_plan_is_free()
    {
        $freePlan = Plan::create([
            'name' => 'Free Plan',
            'amount' => 0,
            'job_limit' => 2,
            'featured_job_limit' => 0,
            'validity_in_days' => 30,
            'is_trial_plan' => 0,
            'currency_id' => 1,
        ]);
        
        $paidPlan = Plan::create([
            'name' => 'Paid Plan',
            'amount' => 29.99,
            'job_limit' => 15,
            'featured_job_limit' => 5,
            'validity_in_days' => 30,
            'is_trial_plan' => 0,
            'currency_id' => 1,
        ]);
        
        $this->assertTrue($freePlan->isFree());
        $this->assertFalse($paidPlan->isFree());
    }

    /** @test */
    public function it_can_determine_if_plan_is_trial()
    {
        $trialPlan = Plan::create([
            'name' => 'Trial Plan',
            'amount' => 0,
            'job_limit' => 2,
            'featured_job_limit' => 0,
            'validity_in_days' => 14,
            'is_trial_plan' => 1,
            'currency_id' => 1,
        ]);
        
        $regularPlan = Plan::create([
            'name' => 'Regular Plan',
            'amount' => 19.99,
            'job_limit' => 10,
            'featured_job_limit' => 2,
            'validity_in_days' => 30,
            'is_trial_plan' => 0,
            'currency_id' => 1,
        ]);
        
        $this->assertTrue($trialPlan->isTrialPlan());
        $this->assertFalse($regularPlan->isTrialPlan());
    }

    /** @test */
    public function it_can_calculate_duration_in_months()
    {
        $monthlyPlan = Plan::create([
            'name' => 'Monthly Plan',
            'amount' => 19.99,
            'job_limit' => 10,
            'featured_job_limit' => 2,
            'validity_in_days' => 30,
            'is_trial_plan' => 0,
            'currency_id' => 1,
        ]);
        
        $quarterlyPlan = Plan::create([
            'name' => 'Quarterly Plan',
            'amount' => 49.99,
            'job_limit' => 30,
            'featured_job_limit' => 6,
            'validity_in_days' => 90,
            'is_trial_plan' => 0,
            'currency_id' => 1,
        ]);
        
        $annualPlan = Plan::create([
            'name' => 'Annual Plan',
            'amount' => 199.99,
            'job_limit' => 120,
            'featured_job_limit' => 24,
            'validity_in_days' => 365,
            'is_trial_plan' => 0,
            'currency_id' => 1,
        ]);
        
        $this->assertEquals(1, $monthlyPlan->durationInMonths());
        $this->assertEquals(3, $quarterlyPlan->durationInMonths());
        $this->assertEquals(12, $annualPlan->durationInMonths());
    }
} 