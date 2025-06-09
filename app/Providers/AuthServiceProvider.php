<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Candidate;
use App\Models\Plan;
use App\Models\Resume;
use App\Models\Skill;
use App\Models\JobCategory;
use App\Models\Transaction;
use App\Models\User;
use App\Policies\CompanyPolicy;
use App\Policies\JobPolicy;
use App\Policies\JobApplicationPolicy;
use App\Policies\CandidatePolicy;
use App\Policies\PlanPolicy;
use App\Policies\ResumePolicy;
use App\Policies\SkillPolicy;
use App\Policies\JobCategoryPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Company::class => CompanyPolicy::class,
        Job::class => JobPolicy::class,
        JobApplication::class => JobApplicationPolicy::class,
        Candidate::class => CandidatePolicy::class,
        Resume::class => ResumePolicy::class,
        Skill::class => SkillPolicy::class,
        JobCategory::class => JobCategoryPolicy::class,
        Plan::class => PlanPolicy::class,
        Transaction::class => TransactionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // No Socialite functionality needed
    }
}
