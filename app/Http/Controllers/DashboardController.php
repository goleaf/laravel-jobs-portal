<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Repositories\DashboardRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends AppBaseController
{
    /** @var DashboardRepository */
    private $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    /**
     * Display the dashboard.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return redirect('/login');
        }

        // Admin dashboard
        if ($user->hasRole('Admin')) {
            $stats = [
                'total_users' => User::count(),
                'total_jobs' => Job::count(),
                'total_companies' => Company::count(),
                'total_candidates' => Candidate::count(),
                'active_jobs' => Job::where('status', 'open')->count(),
                'pending_jobs' => Job::where('status', 'pending')->count(),
            ];

            return view('dashboard.admin', compact('stats'));
        }

        // Employer dashboard
        if ($user->hasRole('Employer')) {
            $company = $user->company;
            $stats = [
                'total_jobs' => $company ? $company->jobs()->count() : 0,
                'active_jobs' => $company ? $company->jobs()->where('status', 'open')->count() : 0,
                'applications' => $company ? $company->jobs()->withCount('jobApplications')->get()->sum('job_applications_count') : 0,
            ];

            return view('dashboard.employer', compact('stats'));
        }

        // Candidate dashboard
        if ($user->hasRole('Candidate')) {
            $candidate = $user->candidate;
            $stats = [
                'applications' => $candidate ? $candidate->jobApplications()->count() : 0,
                'favourites' => $candidate ? $candidate->favouriteJobs()->count() : 0,
                'profile_views' => $candidate ? $candidate->views ?? 0 : 0,
            ];

            return view('dashboard.candidate', compact('stats'));
        }

        // Default dashboard
        return view('dashboard.index');
    }

    public function dashboardChartData(DashboardChartDataDashboardRequest $request): JsonResponse
    {
        $input = $request->all();
        $data['weeklyChartData'] = $this->dashboardRepository->getWeeklyChartData($input);
        $data['postStatisticsChartData'] = $this->dashboardRepository->getPostStatisticsChartData($input);

        return $this->sendResponse($data, 'Dashboard Chart data retrieved successfully.');
    }

    /**
     * @return Factory|View
     */
    public function employerDashboard(): View
    {
        $data = $this->dashboardRepository->getEmployerDashboardData();
        $data['recentJobs'] = $this->dashboardRepository->getEmployerRecentJobsData();
        $data['recentFollowers'] = $this->dashboardRepository->getEmployerRecentFollowerData();
        $data['jobStatus'] = Job::whereCompanyId(getLoggedInUser()->owner_id)->pluck('job_title', 'id');
        $data['gender'] = Job::GENDER;

        return view('employer.dashboard.index')->with($data);
    }

    public function employerDashboardChart(EmployerDashboardChartDashboardRequest $request): JsonResponse
    {
        $input = $request->all();
        $data = $this->dashboardRepository->getEmployerDashboardChartData($input);
        $data['dates'] = $this->dashboardRepository->getDate($input['start_date'], $input['end_date']);

        return $this->sendResponse($data, 'employer bar chart retrieved successfully.');
    }
}
