<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): JsonResponse|View
    {
        $stats = $this->getDashboardStatistics();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Dashboard statistics retrieved successfully',
            ]);
        }

        return view('admin.dashboard.index', compact('stats'));
    }

    /**
     * Get dashboard statistics for API.
     */
    public function getStats(): JsonResponse
    {
        $stats = $this->getDashboardStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Dashboard statistics retrieved successfully',
        ]);
    }

    /**
     * Get quick overview stats.
     */
    public function getOverview(): JsonResponse
    {
        $overview = [
            'total_users' => User::count(),
            'total_jobs' => Job::count(),
            'total_companies' => Company::count(),
            'total_applications' => JobApplication::count(),
            'active_users_today' => User::where('last_login_at', '>=', Carbon::today())->count(),
            'jobs_posted_today' => Job::whereDate('created_at', Carbon::today())->count(),
            'applications_today' => JobApplication::whereDate('created_at', Carbon::today())->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $overview,
            'message' => 'Dashboard overview retrieved successfully',
        ]);
    }

    /**
     * Get dashboard statistics.
     */
    protected function getDashboardStatistics(): array
    {
        $stats = [
            // User Statistics
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_candidates' => User::where('role', 'candidate')->count(),
            'total_employers' => User::where('role', 'employer')->count(),
            'total_admins' => User::where('role', 'admin')->count(),

            // Job Statistics
            'total_jobs' => Job::count(),
            'active_jobs' => Job::where('status', 'open')
                ->where('job_expiry_date', '>=', Carbon::now())
                ->count(),
            'expired_jobs' => Job::where('job_expiry_date', '<', Carbon::now())->count(),
            'featured_jobs' => Job::whereHas('activeFeatured')->count(),

            // Company Statistics
            'total_companies' => Company::count(),
            'active_companies' => Company::whereHas('user', function ($q) {
                $q->where('is_active', true);
            })->count(),
            'featured_companies' => Company::whereHas('activeFeatured')->count(),

            // Application Statistics
            'total_applications' => JobApplication::count(),
            'pending_applications' => JobApplication::where('status', 'pending')->count(),
            'accepted_applications' => JobApplication::where('status', 'accepted')->count(),
            'rejected_applications' => JobApplication::where('status', 'rejected')->count(),

            // Recent Activity
            'recent_registrations' => $this->getRecentRegistrations(),
            'recent_jobs' => $this->getRecentJobs(),
            'recent_applications' => $this->getRecentApplications(),

            // Charts Data
            'user_growth_chart' => $this->getUserGrowthData(),
            'job_posting_chart' => $this->getJobPostingData(),
            'application_status_chart' => $this->getApplicationStatusData(),
        ];

        return $stats;
    }

    /**
     * Get recent user registrations.
     */
    protected function getRecentRegistrations(): array
    {
        return User::with(['profile'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->first_name.' '.$user->last_name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at->format('M d, Y'),
                    'is_active' => $user->is_active,
                ];
            })
            ->toArray()
        ;
    }

    /**
     * Get recent job postings.
     */
    protected function getRecentJobs(): array
    {
        return Job::with(['company.user', 'jobCategory'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->job_title,
                    'company' => $job->company->company_name ?? 'N/A',
                    'category' => $job->jobCategory->name ?? 'N/A',
                    'status' => $job->status,
                    'created_at' => $job->created_at->format('M d, Y'),
                    'expiry_date' => $job->job_expiry_date ? Carbon::parse($job->job_expiry_date)->format('M d, Y') : null,
                ];
            })
            ->toArray()
        ;
    }

    /**
     * Get recent job applications.
     */
    protected function getRecentApplications(): array
    {
        return JobApplication::with(['candidate.user', 'job'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'candidate_name' => $application->candidate->user->first_name.' '.$application->candidate->user->last_name,
                    'job_title' => $application->job->job_title ?? 'N/A',
                    'status' => $application->status,
                    'applied_at' => $application->created_at->format('M d, Y'),
                ];
            })
            ->toArray()
        ;
    }

    /**
     * Get user growth data for charts.
     */
    protected function getUserGrowthData(): array
    {
        $months = [];
        $data = [];

        for ($i = 11; $i >= 0; --$i) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count()
            ;

            $data[] = $count;
        }

        return [
            'labels' => $months,
            'data' => $data,
        ];
    }

    /**
     * Get job posting data for charts.
     */
    protected function getJobPostingData(): array
    {
        $months = [];
        $data = [];

        for ($i = 11; $i >= 0; --$i) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $count = Job::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count()
            ;

            $data[] = $count;
        }

        return [
            'labels' => $months,
            'data' => $data,
        ];
    }

    /**
     * Get application status distribution.
     */
    protected function getApplicationStatusData(): array
    {
        $statusData = JobApplication::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
        ;

        return [
            'labels' => $statusData->pluck('status')->toArray(),
            'data' => $statusData->pluck('count')->toArray(),
        ];
    }
}
