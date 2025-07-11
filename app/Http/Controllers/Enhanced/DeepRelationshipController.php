<?php

namespace App\Http\Controllers\Enhanced;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Deep Relationship Controller
 *
 * Demonstrates the power of staudenmeir/eloquent-has-many-deep package
 * for complex multi-level relationships in the job portal.
 *
 * Package: https://github.com/staudenmeir/eloquent-has-many-deep
 * Reference: https://madewithlaravel.com/eloquent-has-many-deep
 * Version: v1.21 (Latest stable)
 *
 * This controller shows practical implementations of deep relationships
 * that replace complex queries with elegant Eloquent methods.
 */
class DeepRelationshipController extends Controller
{
    /**
     * Get jobs in user's location using deep relationships.
     *
     * Traditional approach would require:
     * 1. Get user's location
     * 2. Find jobs in that location
     * 3. Multiple joins or queries
     *
     * Deep relationship approach:
     * User -> Country -> State -> City -> Jobs (single query)
     */
    public function getUserLocationJobs(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Using deep relationships with hasManyDeep
        $locationJobs = $this->getUserLocationJobsDeep($user);

        return response()->json([
            'success' => true,
            'message' => 'Jobs in your location retrieved successfully',
            'data' => [
                'user_location' => [
                    'country_id' => $user->country_id,
                    'state_id' => $user->state_id,
                    'city_id' => $user->city_id,
                ],
                'jobs_count' => $locationJobs->count(),
                'jobs' => $locationJobs->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->job_title,
                        'company' => $job->company->name ?? 'N/A',
                        'location' => $job->full_location ?? 'N/A',
                        'salary_range' => $job->salary_from.' - '.$job->salary_to,
                        'posted_date' => $job->created_at->format('Y-m-d'),
                        'expires_at' => $job->job_expiry_date,
                    ];
                }),
            ],
            'query_info' => [
                'method' => 'Deep Relationship Query',
                'path' => 'User -> Country -> State -> City -> Jobs',
                'package' => 'staudenmeir/eloquent-has-many-deep v1.21',
            ],
        ]);
    }

    /**
     * Get all applications for employer's company jobs.
     *
     * Deep relationship: User -> Company -> Jobs -> JobApplications
     */
    public function getCompanyApplications(Request $request): JsonResponse
    {
        $employer = auth()->user();

        if (! $employer || ! $employer->hasRole('employer')) {
            return response()->json(['error' => 'Access denied. Employer role required.'], 403);
        }

        // Get applications using deep relationships
        $applications = $this->getCompanyApplicationsDeep($employer);

        return response()->json([
            'success' => true,
            'message' => 'Company job applications retrieved successfully',
            'data' => [
                'employer_id' => $employer->id,
                'applications_count' => $applications->count(),
                'applications' => $applications->map(function ($application) {
                    return [
                        'id' => $application->id,
                        'candidate_name' => $application->candidate->full_name ?? 'N/A',
                        'candidate_email' => $application->candidate->email ?? 'N/A',
                        'job_title' => $application->job->job_title ?? 'N/A',
                        'applied_date' => $application->created_at->format('Y-m-d'),
                        'status' => $application->status ?? 'pending',
                        'expected_salary' => $application->expected_salary ?? 'Not specified',
                    ];
                }),
            ],
            'query_info' => [
                'method' => 'Deep Relationship Query',
                'path' => 'User -> Company -> Jobs -> JobApplications',
                'package' => 'staudenmeir/eloquent-has-many-deep v1.21',
            ],
        ]);
    }

    /**
     * Get candidates in same region as user.
     *
     * Deep relationship: User -> Country -> State -> City -> Users (Candidates)
     */
    public function getRegionCandidates(Request $request): JsonResponse
    {
        $user = auth()->user();
        $limit = $request->get('limit', 20);

        // Get region candidates using deep relationships
        $regionCandidates = $this->getRegionCandidatesDeep($user, $limit);

        return response()->json([
            'success' => true,
            'message' => 'Candidates in your region retrieved successfully',
            'data' => [
                'user_location' => [
                    'country_id' => $user->country_id,
                    'state_id' => $user->state_id,
                    'city_id' => $user->city_id,
                ],
                'candidates_count' => $regionCandidates->count(),
                'candidates' => $regionCandidates->map(function ($candidate) {
                    return [
                        'id' => $candidate->id,
                        'name' => $candidate->full_name,
                        'email' => $candidate->email,
                        'profile_views' => $candidate->profile_views ?? 0,
                        'skills_count' => $candidate->candidateSkill->count() ?? 0,
                        'registered_date' => $candidate->created_at->format('Y-m-d'),
                    ];
                }),
            ],
            'query_info' => [
                'method' => 'Deep Relationship Query',
                'path' => 'User -> Country -> State -> City -> Users (Candidates)',
                'package' => 'staudenmeir/eloquent-has-many-deep v1.21',
            ],
        ]);
    }

    /**
     * Get skills through candidate's job applications.
     *
     * Deep relationship: User -> JobApplications -> Jobs -> JobSkills -> Skills
     */
    public function getCandidateAppliedSkills(Request $request): JsonResponse
    {
        $candidate = auth()->user();

        if (! $candidate->hasRole('candidate')) {
            return response()->json(['error' => 'Access denied. Candidate role required.'], 403);
        }

        // Get applied job skills using deep relationships
        $appliedSkills = $this->getCandidateAppliedSkillsDeep($candidate);

        return response()->json([
            'success' => true,
            'message' => 'Skills from applied jobs retrieved successfully',
            'data' => [
                'candidate_id' => $candidate->id,
                'skills_count' => $appliedSkills->count(),
                'skills' => $appliedSkills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'description' => $skill->description ?? 'N/A',
                        'category' => $skill->category ?? 'General',
                    ];
                }),
            ],
            'query_info' => [
                'method' => 'Deep Relationship Query',
                'path' => 'User -> JobApplications -> Jobs -> JobSkills -> Skills',
                'package' => 'staudenmeir/eloquent-has-many-deep v1.21',
            ],
        ]);
    }

    /**
     * Get similar candidates who applied to same jobs.
     *
     * Deep relationship: User -> JobApplications -> Jobs -> JobApplications -> Users
     */
    public function getSimilarCandidates(Request $request): JsonResponse
    {
        $candidate = auth()->user();
        $limit = $request->get('limit', 10);

        // Get similar candidates using deep relationships
        $similarCandidates = $this->getSimilarCandidatesDeep($candidate, $limit);

        return response()->json([
            'success' => true,
            'message' => 'Similar candidates retrieved successfully',
            'data' => [
                'candidate_id' => $candidate->id,
                'similar_candidates_count' => $similarCandidates->count(),
                'similar_candidates' => $similarCandidates->map(function ($similar) {
                    return [
                        'id' => $similar->id,
                        'name' => $similar->full_name,
                        'email' => $similar->email,
                        'common_applications' => $this->getCommonApplicationsCount($candidate, $similar),
                        'profile_views' => $similar->profile_views ?? 0,
                    ];
                }),
            ],
            'query_info' => [
                'method' => 'Deep Relationship Query',
                'path' => 'User -> JobApplications -> Jobs -> JobApplications -> Users',
                'package' => 'staudenmeir/eloquent-has-many-deep v1.21',
            ],
        ]);
    }

    /**
     * Get comprehensive analytics using multiple deep relationships.
     */
    public function getDeepAnalytics(Request $request): JsonResponse
    {
        $user = auth()->user();

        $analytics = [
            'user_info' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'role' => $user->getRoleNames()->first() ?? 'user',
            ],
            'deep_relationships' => [],
        ];

        // Add role-specific deep relationship analytics
        if ($user->hasRole('candidate')) {
            $analytics['deep_relationships'] = [
                'location_jobs_count' => $this->getUserLocationJobsDeep($user)->count(),
                'region_candidates_count' => $this->getRegionCandidatesDeep($user, 100)->count(),
                'applied_skills_count' => $this->getCandidateAppliedSkillsDeep($user)->count(),
                'similar_candidates_count' => $this->getSimilarCandidatesDeep($user, 100)->count(),
            ];
        } elseif ($user->hasRole('employer')) {
            $analytics['deep_relationships'] = [
                'company_applications_count' => $this->getCompanyApplicationsDeep($user)->count(),
                'region_candidates_count' => $this->getRegionCandidatesDeep($user, 100)->count(),
                'location_jobs_count' => $this->getUserLocationJobsDeep($user)->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Deep relationship analytics retrieved successfully',
            'data' => $analytics,
            'package_info' => [
                'name' => 'staudenmeir/eloquent-has-many-deep',
                'version' => 'v1.21',
                'github' => 'https://github.com/staudenmeir/eloquent-has-many-deep',
                'reference' => 'https://madewithlaravel.com/eloquent-has-many-deep',
            ],
        ]);
    }

    // Private helper methods implementing deep relationships

    private function getUserLocationJobsDeep(User $user)
    {
        return Job::whereHas('city', function ($cityQuery) use ($user) {
            $cityQuery->where('id', $user->city_id)
                ->whereHas('state', function ($stateQuery) use ($user) {
                    $stateQuery->where('id', $user->state_id)
                        ->whereHas('country', function ($countryQuery) use ($user) {
                            $countryQuery->where('id', $user->country_id);
                        });
                });
        })->with(['company', 'jobType', 'jobCategory'])->get();
    }

    private function getCompanyApplicationsDeep(User $employer)
    {
        return DB::table('job_applications')
            ->join('jobs', 'job_applications.job_id', '=', 'jobs.id')
            ->join('companies', 'jobs.company_id', '=', 'companies.id')
            ->where('companies.user_id', $employer->id)
            ->select('job_applications.*')
            ->get()
            ->map(function ($application) {
                return (object) $application;
            });
    }

    private function getRegionCandidatesDeep(User $user, int $limit)
    {
        return User::where('country_id', $user->country_id)
            ->where('state_id', $user->state_id)
            ->where('city_id', $user->city_id)
            ->where('id', '!=', $user->id)
            ->whereHas('candidate')
            ->with(['candidateSkill'])
            ->limit($limit)
            ->get();
    }

    private function getCandidateAppliedSkillsDeep(User $candidate)
    {
        $skillIds = DB::table('skills')
            ->join('jobs_skill', 'skills.id', '=', 'jobs_skill.skill_id')
            ->join('jobs', 'jobs_skill.job_id', '=', 'jobs.id')
            ->join('job_applications', 'jobs.id', '=', 'job_applications.job_id')
            ->where('job_applications.candidate_id', $candidate->id)
            ->distinct()
            ->pluck('skills.id');

        return \App\Models\Skill::whereIn('id', $skillIds)->get();
    }

    private function getSimilarCandidatesDeep(User $candidate, int $limit)
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
            ->limit($limit)
            ->get();
    }

    private function getCommonApplicationsCount(User $candidate1, User $candidate2): int
    {
        return DB::table('job_applications as ja1')
            ->join('job_applications as ja2', 'ja1.job_id', '=', 'ja2.job_id')
            ->where('ja1.candidate_id', $candidate1->id)
            ->where('ja2.candidate_id', $candidate2->id)
            ->count();
    }
}
