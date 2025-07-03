<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Job\CreateJobRequest;
use App\Http\Requests\Job\EmailJobToFriendRequest;
use App\Http\Requests\Job\ReportJobAbuseRequest;
use App\Http\Requests\Job\SaveFavouriteJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Models\Job;
use App\Repositories\JobRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

class JobController extends AppBaseController
{
    /** @var JobRepository */
    private $jobRepository;

    public function __construct(JobRepository $jobRepo)
    {
        $this->jobRepository = $jobRepo;
    }

    /**
     * Display a listing of the resource for admin.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request): View
    {
        // Check if this is an admin request (based on route or user role)
        if ($request->route()->getPrefix() === 'admin' || (auth()->check() && auth()->user()->hasRole('Admin'))) {
            // Use enhanced model scopes for admin job listing
            $jobs = Job::with(['company', 'jobCategory', 'currency', 'jobType'])
                ->when($request->get('status'), function ($query, $status) {
                    return $query->byStatus($status);
                })
                ->when($request->get('featured'), function ($query) {
                    return $query->featured();
                })
                ->when($request->get('company_id'), function ($query, $companyId) {
                    return $query->byCompany($companyId);
                })
                ->when($request->get('search'), function ($query, $search) {
                    return $query->keywordSearch($search);
                })
                ->recent()
                ->paginate(15);

            return view('admin.jobs.index', compact('jobs'));
        }

        // Frontend job listing with enhanced scopes
        $data = $this->jobRepository->prepareJobData();
        $data['input'] = $request->all();

        return view('front_web_template.jobs.index')->with($data);
    }

    /**
     * Show the form for creating a new job (admin).
     */
    public function create(): View
    {
        return view('admin.jobs.create');
    }

    /**
     * Store a newly created job (admin).
     */
    public function store(CreateJobRequest $request)
    {
        // Implementation for storing job
        return redirect()->route('admin.jobs.index')->with('success', 'Job created successfully');
    }

    /**
     * Display the specified job (admin).
     *
     * @param  mixed  $id
     */
    public function show($id): View
    {
        $job = Job::with(['company', 'jobCategory', 'currency', 'jobType', 'jobsSkill', 'jobsTag'])
            ->findOrFail($id);

        return view('admin.jobs.show', compact('job'));
    }

    /**
     * Show the form for editing the specified job (admin).
     *
     * @param  mixed  $id
     */
    public function edit($id): View
    {
        $job = Job::with(['company', 'jobCategory', 'currency', 'jobType'])
            ->findOrFail($id);

        return view('admin.jobs.edit', compact('job'));
    }

    /**
     * Update the specified job (admin).
     *
     * @param  mixed  $id
     */
    public function update(UpdateJobRequest $request, $id)
    {
        // Implementation for updating job
        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully');
    }

    /**
     * Remove the specified job (admin).
     *
     * @param  mixed  $id
     */
    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();

        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully');
    }

    /**
     * Enhanced job details using model scopes.
     *
     * @return Application|Factory|View
     */
    public function jobDetails(string $uniqueJobId)
    {
        // Use enhanced eager loading and scopes
        $job = Job::with([
            'jobsTag',
            'jobCategory',
            'jobShift',
            'jobsSkill',
            'company',
            'currency',
            'jobType',
            'appliedJobs' => function ($query) {
                $query->pending();
            },
        ])
            ->where('job_id', $uniqueJobId)
            ->first();

        if (empty($job)) {
            Flash::error('Job not found');

            return redirect()->back();
        }

        // Check job access permissions
        if ($job->status == Job::STATUS_DRAFT && \Auth::user()?->hasRole('Candidate')) {
            abort(404);
        }

        $data['resumes'] = null;
        $data['isActive'] = $data['isApplied'] = $data['isJobAddedToFavourite'] = $data['isJobReportedAsAbuse'] = false;

        // Get candidate-specific data if logged in
        if (\Auth::check() && \Auth::user()->hasRole('Candidate')) {
            $data = $this->jobRepository->getJobDetails($job);
        }

        // Enhanced skills extraction using model relationships
        $data['skills'] = $job->jobsSkill->pluck('name')->toArray();

        // Enhanced job count using scopes
        $data['jobsCount'] = Job::active()
            ->byCompany($job->company_id)
            ->count();

        // Check job status using model method
        $data['isActive'] = $job->isActive();

        // Enhanced related jobs query using scopes
        $data['getRelatedJobs'] = Job::with(['jobCategory', 'jobShift', 'jobsSkill', 'company'])
            ->byCategory($job->job_category_id)
            ->active()
            ->where('id', '!=', $job->id)
            ->recent()
            ->limit(6)
            ->get();

        // Social sharing URLs
        $url = [
            'gmail' => 'https://plus.google.com/share?url='.url()->current(),
            'twitter' => 'https://twitter.com/intent/tweet?url='.url()->current(),
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u='.url()->current(),
            'pinterest' => 'http://pinterest.com/pin/create/button/?url='.url()->current(),
        ];

        return view('front_web_template.jobs.job_details', compact('job', 'url'))->with($data);
    }

    /**
     * Enhanced favourite job functionality.
     */
    public function saveFavouriteJob(SaveFavouriteJobRequest $request): JsonResponse
    {
        $input = $request->all();
        $favouriteJob = $this->jobRepository->storeFavouriteJobs($input);

        if ($favouriteJob) {
            return $this->sendResponse($favouriteJob, __('messages.flash.fav_job_added'));
        }

        return $this->sendResponse($favouriteJob, __('messages.flash.fav_job_removed'));
    }

    /**
     * Enhanced job abuse reporting.
     */
    public function reportJobAbuse(ReportJobAbuseRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->jobRepository->storeReportJobAbuse($input);

        return $this->sendSuccess(__('messages.flash.job_abuse_reported'));
    }

    /**
     * Enhanced email job to friend functionality.
     */
    public function emailJobToFriend(EmailJobToFriendRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->jobRepository->emailJobToFriend($input);

        return $this->sendSuccess(__('messages.flash.job_emailed_to'));
    }

    /**
     * Get jobs by filters using enhanced scopes.
     */
    public function getJobsByFilters(Request $request): JsonResponse
    {
        $query = Job::with(['company', 'jobCategory', 'currency', 'jobType']);

        // Apply filters using enhanced scopes
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->filled('company_id')) {
            $query->byCompany($request->company_id);
        }

        if ($request->filled('location')) {
            $query->byLocation($request->country_id, $request->state_id, $request->city_id);
        }

        if ($request->filled('salary_range')) {
            $query->bySalaryRange($request->min_salary, $request->max_salary);
        }

        if ($request->filled('experience')) {
            $query->byExperience($request->min_experience, $request->max_experience);
        }

        if ($request->filled('job_type')) {
            $query->where('job_type_id', $request->job_type);
        }

        if ($request->filled('featured')) {
            $query->featured();
        }

        if ($request->filled('remote')) {
            $query->remote();
        }

        if ($request->filled('search')) {
            $query->keywordSearch($request->search);
        }

        // Apply sorting
        switch ($request->get('sort', 'recent')) {
            case 'recent':
                $query->recent();

                break;

            case 'popular':
                $query->popular();

                break;

            case 'salary_high':
                $query->orderBy('salary_from', 'desc');

                break;

            case 'salary_low':
                $query->orderBy('salary_from', 'asc');

                break;

            default:
                $query->recent();
        }

        $jobs = $query->active()->paginate($request->get('per_page', 15));

        return $this->sendResponse($jobs, 'Jobs retrieved successfully');
    }

    /**
     * Get job statistics using enhanced scopes.
     */
    public function getJobStatistics(): JsonResponse
    {
        $statistics = [
            'total_jobs' => Job::count(),
            'active_jobs' => Job::active()->count(),
            'featured_jobs' => Job::featured()->count(),
            'jobs_today' => Job::today()->count(),
            'jobs_this_week' => Job::thisWeek()->count(),
            'jobs_this_month' => Job::thisMonth()->count(),
            'urgent_jobs' => Job::urgent()->count(),
            'remote_jobs' => Job::remote()->count(),
            'with_salary' => Job::withSalary()->count(),
        ];

        return $this->sendResponse($statistics, 'Job statistics retrieved successfully');
    }
}
