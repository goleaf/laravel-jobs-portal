<?php

namespace App\Traits;

use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\FunctionalArea;
use App\Models\Industry;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\Skill;
use App\Models\State;
use App\Models\User;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;

/**
 * Trait HasDeepJobPortalRelationships
 *
 * Integrates staudenmeir/eloquent-has-many-deep package for complex
 * multi-level relationships in the job portal system.
 *
 * Package: https://github.com/staudenmeir/eloquent-has-many-deep
 * Documentation: https://madewithlaravel.com/eloquent-has-many-deep
 *
 * This trait provides deep relationship methods that allow traversing
 * multiple levels of relationships in a single query, dramatically
 * improving performance and simplifying complex queries.
 */
trait HasDeepJobPortalRelationships
{
    /**
     * Get all jobs in the user's location hierarchy.
     *
     * Path: User -> Country -> State -> City -> Jobs
     *
     * This allows getting all jobs available in a user's location
     * without needing multiple queries or complex joins.
     */
    public function locationJobs(): HasManyDeep
    {
        return $this->hasManyDeep(
            Job::class,
            [Country::class, State::class, City::class],
            [
                'id',        // User.country_id = Country.id
                'country_id', // Country.id = State.country_id
                'state_id',  // State.id = City.state_id
                'city_id',    // City.id = Job.city_id
            ],
            [
                'country_id', // User.country_id
                'id',        // Country.id
                'id',        // State.id
                'id',         // City.id
            ]
        );
    }

    /**
     * Get all job applications for jobs in user's company.
     *
     * Path: User -> Company -> Jobs -> JobApplications
     *
     * Perfect for employers to see all applications across all their jobs.
     */
    public function companyJobApplications(): HasManyDeep
    {
        return $this->hasManyDeep(
            JobApplication::class,
            [Company::class, Job::class],
            [
                'id',         // User.id = Company.user_id
                'company_id', // Company.id = Job.company_id
                'job_id',      // Job.id = JobApplication.job_id
            ],
            [
                'id',         // User.id
                'user_id',    // Company.user_id
                'id',          // Job.id
            ]
        );
    }

    /**
     * Get all candidates in the same region (country->state->city).
     *
     * Path: User -> Country -> State -> City -> Users (Candidates)
     *
     * Useful for finding local talent or networking.
     */
    public function regionCandidates(): HasManyDeep
    {
        return $this->hasManyDeep(
            User::class,
            [Country::class, State::class, City::class],
            [
                'id',        // User.country_id = Country.id
                'country_id', // Country.id = State.country_id
                'state_id',  // State.id = City.state_id
                'city_id',    // City.id = User.city_id
            ],
            [
                'country_id', // User.country_id
                'id',        // Country.id
                'id',        // State.id
                'id',         // City.id
            ]
        )->where('users.id', '!=', $this->id)
            ->whereHas('candidate'); // Only candidates
    }

    /**
     * Get all jobs in the same industry as user's company.
     *
     * Path: User -> Company -> Industry -> Companies -> Jobs
     *
     * Great for competitive analysis and market research.
     */
    public function industryJobs(): HasManyDeep
    {
        return $this->hasManyDeep(
            Job::class,
            [Company::class, Industry::class, Company::class],
            [
                'id',          // User.id = Company.user_id
                'industry_id', // Company.industry_id = Industry.id
                'id',          // Industry.id = Company.industry_id
                'company_id',   // Company.id = Job.company_id
            ],
            [
                'id',          // User.id
                'user_id',     // Company.user_id
                'id',          // Industry.id
                'industry_id',  // Company.industry_id
            ]
        );
    }

    /**
     * Get all skills through job applications.
     *
     * Path: User -> JobApplications -> Jobs -> JobSkills -> Skills
     *
     * Shows all skills required for jobs a candidate has applied to.
     */
    public function appliedJobSkills(): HasManyDeep
    {
        return $this->hasManyDeep(
            Skill::class,
            [JobApplication::class, Job::class, 'jobs_skill'],
            [
                'id',      // User.id = JobApplication.candidate_id
                'job_id',  // JobApplication.job_id = Job.id
                'job_id',  // Job.id = jobs_skill.job_id
                'skill_id', // jobs_skill.skill_id = Skill.id
            ],
            [
                'id',          // User.id
                'candidate_id', // JobApplication.candidate_id
                'id',          // Job.id
                'id',           // Skill.id
            ]
        );
    }

    /**
     * Get all companies in user's functional areas.
     *
     * Path: User -> Candidate -> Skills -> Jobs -> Companies
     *
     * Find companies that hire for candidate's skill set.
     */
    public function skillBasedCompanies(): HasManyDeep
    {
        return $this->hasManyDeep(
            Company::class,
            ['candidates', 'candidate_skill', 'jobs_skill', Job::class],
            [
                'id',         // User.id = candidates.user_id
                'candidate_id', // candidates.id = candidate_skill.candidate_id
                'skill_id',   // candidate_skill.skill_id = jobs_skill.skill_id
                'job_id',     // jobs_skill.job_id = Job.id
                'company_id',  // Job.company_id = Company.id
            ],
            [
                'id',      // User.id
                'user_id', // candidates.user_id
                'id',      // Skill.id
                'id',      // Job.id
                'id',       // Company.id
            ]
        );
    }

    /**
     * Get all categories through user's job applications.
     *
     * Path: User -> JobApplications -> Jobs -> JobCategory
     *
     * Shows job categories the candidate is interested in.
     */
    public function appliedJobCategories(): HasManyDeep
    {
        return $this->hasManyDeep(
            JobCategory::class,
            [JobApplication::class, Job::class],
            [
                'id',              // User.id = JobApplication.candidate_id
                'job_id',          // JobApplication.job_id = Job.id
                'job_category_id',  // Job.job_category_id = JobCategory.id
            ],
            [
                'id',          // User.id
                'candidate_id', // JobApplication.candidate_id
                'id',           // Job.id
            ]
        );
    }

    /**
     * Get all functional areas through company jobs.
     *
     * Path: User -> Company -> Jobs -> FunctionalArea
     *
     * Shows all functional areas an employer posts jobs in.
     */
    public function companyFunctionalAreas(): HasManyDeep
    {
        return $this->hasManyDeep(
            FunctionalArea::class,
            [Company::class, Job::class],
            [
                'id',                 // User.id = Company.user_id
                'company_id',         // Company.id = Job.company_id
                'functional_area_id',  // Job.functional_area_id = FunctionalArea.id
            ],
            [
                'id',      // User.id
                'user_id', // Company.user_id
                'id',       // Job.id
            ]
        );
    }

    /**
     * Get users who applied to same jobs (potential connections).
     *
     * Path: User -> JobApplications -> Jobs -> JobApplications -> Users
     *
     * Great for networking with candidates who have similar interests.
     */
    public function similarCandidates(): HasManyDeep
    {
        return $this->hasManyDeep(
            User::class,
            [JobApplication::class, Job::class, JobApplication::class],
            [
                'id',          // User.id = JobApplication.candidate_id
                'job_id',      // JobApplication.job_id = Job.id
                'id',          // Job.id = JobApplication.job_id
                'candidate_id', // JobApplication.candidate_id = User.id
            ],
            [
                'id',          // User.id
                'candidate_id', // JobApplication.candidate_id
                'id',          // Job.id
                'job_id',       // JobApplication.job_id
            ]
        )->where('users.id', '!=', $this->id); // Exclude self
    }

    /**
     * Get all jobs posted by companies in same city.
     *
     * Path: User -> City -> Companies -> Jobs
     *
     * Find local job opportunities.
     */
    public function localCompanyJobs(): HasManyDeep
    {
        return $this->hasManyDeep(
            Job::class,
            [City::class, Company::class],
            [
                'id',         // User.city_id = City.id
                'city_id',    // City.id = Company.city_id
                'company_id',  // Company.id = Job.company_id
            ],
            [
                'city_id', // User.city_id
                'id',      // City.id
                'id',       // Company.id
            ]
        );
    }
}
