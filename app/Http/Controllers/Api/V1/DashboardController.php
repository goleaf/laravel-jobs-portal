<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Company;
use App\Models\Candidate;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_jobs' => Job::count(),
                'active_jobs' => Job::where('status', 1)->count(),
                'total_companies' => Company::count(),
                'active_companies' => Company::count(),
                'total_candidates' => Candidate::count(),
                'verified_candidates' => 0, // Simplified for now
                'total_applications' => 0, // Simplified for now
                'pending_applications' => 0,
                'approved_applications' => 0,
                'rejected_applications' => 0,
            ];

            // Add percentage calculations
            $stats['active_jobs_percentage'] = $stats['total_jobs'] > 0 
                ? round(($stats['active_jobs'] / $stats['total_jobs']) * 100, 1) 
                : 0;
            
            $stats['active_companies_percentage'] = $stats['total_companies'] > 0 
                ? round(($stats['active_companies'] / $stats['total_companies']) * 100, 1) 
                : 0;

            $stats['verified_candidates_percentage'] = $stats['total_candidates'] > 0 
                ? round(($stats['verified_candidates'] / $stats['total_candidates']) * 100, 1) 
                : 0;

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Dashboard statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get recent jobs
     */
    public function getRecentJobs(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            
            $jobs = Job::select([
                    'id', 'job_title', 'company_id', 'job_category_id', 'job_type_id', 
                    'status', 'created_at', 'updated_at'
                ])
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->job_title,
                        'company' => 'N/A', // Simplified for now
                        'category' => 'N/A', // Simplified for now
                        'type' => 'Full-time', // Simplified for now
                        'is_active' => $job->status == 1,
                        'status' => $job->status == 1 ? 'Active' : 'Inactive',
                        'created_at' => $job->created_at?->toISOString(),
                        'created_ago' => $job->created_at?->diffForHumans(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $jobs,
                'message' => 'Recent jobs retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recent jobs',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get recent applications
     */
    public function getRecentApplications(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            
            $applications = JobApplication::with([
                'candidate:id,first_name,last_name,email',
                'job:id,title'
            ])
                ->select([
                    'id', 'candidate_id', 'job_id', 'status', 
                    'expected_salary', 'created_at'
                ])
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($application) {
                    return [
                        'id' => $application->id,
                        'candidate_name' => $application->candidate 
                            ? trim($application->candidate->first_name . ' ' . $application->candidate->last_name)
                            : 'N/A',
                        'candidate_email' => $application->candidate?->email ?? 'N/A',
                        'job_title' => $application->job?->title ?? 'N/A',
                        'status' => ucfirst($application->status ?? 'pending'),
                        'expected_salary' => $application->expected_salary ? number_format($application->expected_salary) : 'Not specified',
                        'created_at' => $application->created_at?->toISOString(),
                        'created_ago' => $application->created_at?->diffForHumans(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $applications,
                'message' => 'Recent applications retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recent applications',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get application status distribution
     */
    public function getApplicationStatusDistribution(): JsonResponse
    {
        try {
            $distribution = JobApplication::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [ucfirst($item->status ?? 'pending') => $item->count];
                });

            // Ensure all statuses are present
            $statuses = ['Pending', 'Approved', 'Rejected', 'Withdrawn'];
            $result = [];
            
            foreach ($statuses as $status) {
                $result[] = [
                    'status' => $status,
                    'count' => $distribution[$status] ?? 0
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Application status distribution retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve application status distribution',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get monthly job posting trends
     */
    public function getJobPostingTrends(Request $request): JsonResponse
    {
        try {
            $months = $request->get('months', 6);
            
            $trends = Job::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
                ->where('created_at', '>=', Carbon::now()->subMonths($months))
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(function ($item) {
                    return [
                        'month' => $item->month,
                        'month_name' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                        'count' => $item->count
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $trends,
                'message' => 'Job posting trends retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job posting trends',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get top performing companies by job count
     */
    public function getTopCompanies(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 5);
            
            $companies = Company::withCount('jobs')
                ->having('jobs_count', '>', 0)
                ->orderBy('jobs_count', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'jobs_count' => $company->jobs_count,
                        'is_active' => $company->is_active,
                        'logo' => $company->logo,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $companies,
                'message' => 'Top companies retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve top companies',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'data' => []
            ], 500);
        }
    }
} 