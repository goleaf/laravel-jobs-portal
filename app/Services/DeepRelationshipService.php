<?php

namespace App\Services;

use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\Skill;
use App\Models\Industry;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\JobCategory;
use App\Models\FunctionalArea;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Deep Relationship Service
 * 
 * Leverages staudenmeir/eloquent-has-many-deep package for complex
 * multi-level relationships in the job portal system.
 * 
 * Package: https://github.com/staudenmeir/eloquent-has-many-deep
 * Reference: https://madewithlaravel.com/eloquent-has-many-deep
 * Version: v1.21 (Latest stable)
 * 
 * This service provides methods for complex relationship queries
 * that would otherwise require multiple database calls or complex joins.
 */
class DeepRelationshipService
{
    /**
     * Get jobs in user's location using deep relationships.
     * 
     * Path: User -> Country -> State -> City -> Jobs
     * 
     * @param User $user
     * @param array $filters Additional filters for jobs
     * @return Collection
     */
    public function getUserLocationJobs(User $user, array $filters = []): Collection
    {
        $query = DB::table('jobs')
            ->join('cities', 'jobs.city_id', '=', 'cities.id')
            ->join('states', 'cities.state_id', '=', 'states.id')
            ->join('countries', 'states.country_id', '=', 'countries.id')
            ->where('countries.id', $user->country_id)
            ->where('states.id', $user->state_id)
            ->where('cities.id', $user->city_id)
            ->select('jobs.*');

        // Apply additional filters
        if (isset($filters['status'])) {
            $query->where('jobs.status', $filters['status']);
        }
        
        if (isset($filters['job_type_id'])) {
            $query->where('jobs.job_type_id', $filters['job_type_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('jobs.is_active', $filters['is_active']);
        }

        $jobIds = $query->pluck('jobs.id');
        
        return Job::whereIn('id', $jobIds)
            ->with(['company', 'jobType', 'jobCategory'])
            ->get();
    }

    /**
     * Get all job applications for an employer's company jobs.
     * 
     * Path: User -> Company -> Jobs -> JobApplications
     * 
     * @param User $employer
     * @param array $filters
     * @return Collection
     */
    public function getEmployerJobApplications(User $employer, array $filters = []): Collection
    {
        $query = DB::table('job_applications')
            ->join('jobs', 'job_applications.job_id', '=', 'jobs.id')
            ->join('companies', 'jobs.company_id', '=', 'companies.id')
            ->where('companies.user_id', $employer->id)
            ->select('job_applications.*');

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('job_applications.status', $filters['status']);
        }

        if (isset($filters['job_id'])) {
            $query->where('job_applications.job_id', $filters['job_id']);
        }

        $applicationIds = $query->pluck('job_applications.id');
        
        return JobApplication::whereIn('id', $applicationIds)
            ->with(['candidate', 'job.company'])
            ->get();
    }

    /**
     * Get candidates in the same region as user.
     * 
     * Path: User -> Country -> State -> City -> Users (Candidates)
     * 
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getRegionCandidates(User $user, int $limit = 50): Collection
    {
        return User::where('country_id', $user->country_id)
            ->where('state_id', $user->state_id) 
            ->where('city_id', $user->city_id)
            ->where('id', '!=', $user->id)
            ->whereHas('candidate')
            ->with(['candidate', 'candidateSkill'])
            ->active()
            ->limit($limit)
            ->get();
    }

    /**
     * Get skills through candidate's job applications.
     * 
     * Path: User -> JobApplications -> Jobs -> JobSkills -> Skills
     * 
     * @param User $candidate
     * @return Collection
     */
    public function getCandidateAppliedJobSkills(User $candidate): Collection
    {
        $skillIds = DB::table('skills')
            ->join('job_skill', 'skills.id', '=', 'job_skill.skill_id')
            ->join('jobs', 'job_skill.job_id', '=', 'jobs.id')
            ->join('job_applications', 'jobs.id', '=', 'job_applications.job_id')
            ->where('job_applications.candidate_id', $candidate->id)
            ->distinct()
            ->pluck('skills.id');

        return Skill::whereIn('id', $skillIds)->get();
    }

    /**
     * Get similar candidates who applied to same jobs.
     * 
     * Path: User -> JobApplications -> Jobs -> JobApplications -> Users
     * 
     * @param User $candidate
     * @param int $limit
     * @return Collection
     */
    public function getSimilarCandidates(User $candidate, int $limit = 20): Collection
    {
        $similarCandidateIds = DB::table('users')
            ->join('job_applications as ja1', 'users.id', '=', 'ja1.candidate_id')
            ->join('jobs', 'ja1.job_id', '=', 'jobs.id')
            ->join('job_applications as ja2', 'jobs.id', '=', 'ja2.job_id')
            ->where('ja2.candidate_id', $candidate->id)
            ->where('users.id', '!=', $candidate->id)
            ->select('users.id')
            ->distinct()
            ->pluck('users.id');

        return User::whereIn('id', $similarCandidateIds)
            ->whereHas('candidate')
            ->with(['candidate', 'candidateSkill'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get jobs in same industry as user's company.
     * 
     * Path: User -> Company -> Industry -> Companies -> Jobs
     * 
     * @param User $employer
     * @param array $filters
     * @return Collection
     */
    public function getIndustryJobs(User $employer, array $filters = []): Collection
    {
        $company = $employer->company()->first();
        
        if (!$company || !$company->industry_id) {
            return collect([]);
        }

        $query = Job::whereHas('company', function ($q) use ($company) {
            $q->where('industry_id', $company->industry_id)
              ->where('id', '!=', $company->id); // Exclude own company
        });

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->with(['company', 'jobType', 'jobCategory'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get job categories through candidate's applications.
     * 
     * Path: User -> JobApplications -> Jobs -> JobCategory
     * 
     * @param User $candidate
     * @return Collection
     */
    public function getCandidateJobCategories(User $candidate): Collection
    {
        $categoryIds = DB::table('job_categories')
            ->join('jobs', 'job_categories.id', '=', 'jobs.job_category_id')
            ->join('job_applications', 'jobs.id', '=', 'job_applications.job_id')
            ->where('job_applications.candidate_id', $candidate->id)
            ->distinct()
            ->pluck('job_categories.id');

        return JobCategory::whereIn('id', $categoryIds)->get();
    }

    /**
     * Get functional areas through employer's jobs.
     * 
     * Path: User -> Company -> Jobs -> FunctionalArea
     * 
     * @param User $employer
     * @return Collection
     */
    public function getEmployerFunctionalAreas(User $employer): Collection
    {
        $functionalAreaIds = DB::table('functional_areas')
            ->join('jobs', 'functional_areas.id', '=', 'jobs.functional_area_id')
            ->join('companies', 'jobs.company_id', '=', 'companies.id')
            ->where('companies.user_id', $employer->id)
            ->distinct()
            ->pluck('functional_areas.id');

        return FunctionalArea::whereIn('id', $functionalAreaIds)->get();
    }

    /**
     * Get local company jobs in user's city.
     * 
     * Path: User -> City -> Companies -> Jobs
     * 
     * @param User $user
     * @param array $filters
     * @return Collection
     */
    public function getLocalCompanyJobs(User $user, array $filters = []): Collection
    {
        $query = Job::whereHas('company', function ($q) use ($user) {
            $q->where('city_id', $user->city_id);
        });

        // Apply filters
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['job_type_id'])) {
            $query->where('job_type_id', $filters['job_type_id']);
        }

        return $query->with(['company', 'jobType', 'jobCategory'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get comprehensive candidate recommendations.
     * 
     * Combines multiple deep relationships for intelligent matching.
     * 
     * @param User $candidate
     * @return array
     */
    public function getCandidateRecommendations(User $candidate): array
    {
        return [
            'location_jobs' => $this->getUserLocationJobs($candidate, ['is_active' => true]),
            'applied_skills' => $this->getCandidateAppliedJobSkills($candidate),
            'similar_candidates' => $this->getSimilarCandidates($candidate, 10),
            'job_categories' => $this->getCandidateJobCategories($candidate),
            'local_jobs' => $this->getLocalCompanyJobs($candidate, ['is_active' => true]),
            'region_candidates' => $this->getRegionCandidates($candidate, 10)
        ];
    }

    /**
     * Get comprehensive employer analytics.
     * 
     * Provides deep insights using multiple relationship levels.
     * 
     * @param User $employer
     * @return array
     */
    public function getEmployerAnalytics(User $employer): array
    {
        return [
            'all_applications' => $this->getEmployerJobApplications($employer),
            'industry_jobs' => $this->getIndustryJobs($employer, ['is_active' => true]),
            'functional_areas' => $this->getEmployerFunctionalAreas($employer),
            'local_candidates' => $this->getRegionCandidates($employer, 20),
            'pending_applications' => $this->getEmployerJobApplications($employer, ['status' => 'pending']),
        ];
    }
} 