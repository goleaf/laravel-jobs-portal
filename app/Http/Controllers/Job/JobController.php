<?php

namespace App\Http\Controllers\Job;

use App\Models\Country;
use App\Models\FeaturedRecord;
use App\Models\FrontSetting;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\ReportedJob;
use App\Models\State;
use App\Models\Transaction;
use App\Repositories\JobRepository;
use App\Http\Requests\Job\IndexJobRequest;
use App\Http\Requests\Job\ShowJobRequest;
use App\Http\Requests\Job\CreateJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Http\Requests\Job\GetStatesJobRequest;
use App\Http\Requests\Job\GetCitiesJobRequest;
use App\Http\Requests\Job\CreateJobStoreJobJobRequest;
use App\Http\Requests\Job\UpdateJobUpdateJobJobRequest;
use App\Http\Requests\Job\DestroyJobRequest;
use App\Http\Requests\Job\ChangeIsSuspendedJobRequest;
use App\Http\Requests\Job\GetJobsJobRequest;
use App\Http\Requests\Job\ShowJobsJobRequest;
use App\Http\Requests\Job\DeleteJobRequest;
use App\Http\Requests\Job\ShowReportedJobsJobRequest;
use App\Http\Requests\Job\ChangeJobStatusJobRequest;
use App\Http\Requests\Job\DeleteReportedJobsJobRequest;
use App\Http\Requests\Job\ShowReportedJobNoteJobRequest;
use App\Http\Requests\Job\MakeFeaturedJobRequest;
use App\Http\Requests\Job\MakeUnFeaturedJobRequest;
use App\Http\Requests\Job\GetExpiredJobsJobRequest;
use App\Http\Resources\Job\JobIndexResource;
use App\Http\Resources\Job\JobShowResource;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Throwable;

class JobController extends AppBaseController
{
    /** @var JobRepository */
    private $jobRepository;

    public function __construct(JobRepository $jobRepo)
    {
        $this->jobRepository = $jobRepo;
    }

    /**
     * Display a listing of the Job.
     *
     * @param  IndexJobRequest  $request
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(IndexJobRequest $request): View
    {
        $statusArray = Job::STATUS_ARRAY;
        $validated = $request->getValidatedWithDefaults();

        // Check job creation limit with caching
        $canCreateJob = cache()->remember(
            "user.{auth()->id()}.can_create_job", 
            300, 
            fn() => $this->checkJobLimit()
        );

        if (!$canCreateJob) {
            Flash::error(__('messages.flash.job_create_limit'));
        }

        return view('employer.jobs.index', compact('statusArray', 'validated'));
    }

    /**
     * Show the form for creating a new Job.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        // Cache the form data for better performance
        $data = cache()->remember('job.form_data', 3600, function () {
            return $this->jobRepository->prepareData();
        });

        return view('employer.jobs.create')->with('data', $data);
    }

    /**
     * Store a newly created Job in storage.
     *
     * @return RedirectResponse|Redirector
     *
     * @throws Throwable
     */
    public function store(CreateJobRequest $request): RedirectResponse
    {
        try {
            $input = $request->validated();
            
            // Sanitize and prepare input
            $input = $this->prepareJobInput($input, $request);
            
            // Check job limit for live jobs
            if ($input['status'] == Job::STATUS_OPEN && !$this->checkJobLimit()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => __('messages.flash.job_create_limit')]);
            }

            $job = $this->jobRepository->store($input);

            // Clear relevant caches
            $this->clearJobCaches();

            $message = isset($request->saveDraft) 
                ? __('messages.flash.job_draft') 
                : __('messages.flash.job_save');
                
            Flash::success($message);

            return redirect(route('jobs.index'));
            
        } catch (Exception $e) {
            logger()->error('Job creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'input' => $request->except(['password', '_token'])
            ]);
            
            Flash::error(__('messages.flash.job_create_error'));
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified Job.
     *
     * @return Factory|View
     */
    public function show(Job $job): View
    {
        // Eager load relationships for better performance
        $job->load([
            'company.user',
            'jobCategory',
            'jobType',
            'careerLevel',
            'functionalArea',
            'jobsSkill',
            'jobsTag'
        ]);

        return view('employer.jobs.show')->with('job', $job);
    }

    /**
     * Show the form for editing the specified Job.
     *
     * @return Factory|View|RedirectResponse
     */
    public function edit(Job $job)
    {
        // Authorization check
        if (!$this->canUserEditJob($job)) {
            return view('errors.404');
        }

        // Status check
        if ($job->status == Job::STATUS_CLOSED) {
            return redirect(route('jobs.index'))
                ->withErrors(__('messages.flash.close_job'));
        }

        // Cache form data
        $data = cache()->remember('job.form_data', 3600, function () {
            return $this->jobRepository->prepareData();
        });
        
        $data['jobTags'] = $job->jobsTag()->pluck('tag_id')->toArray();
        
        // Get location data efficiently
        [$states, $cities] = $this->getLocationData($job);

        return view('employer.jobs.edit', compact('data', 'job', 'cities', 'states'));
    }

    /**
     * Update the specified Job in storage.
     *
     * @return RedirectResponse|Redirector
     *
     * @throws Throwable
     */
    public function update(Job $job, UpdateJobRequest $request): RedirectResponse
    {
        try {
            // Authorization check
            if (!$this->canUserEditJob($job)) {
                return $this->sendError(__('messages.common.seems_message'));
            }

            // Check job limit for status changes
            if ($job->status != Job::STATUS_OPEN && !$this->checkJobLimit()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => __('messages.flash.job_create_limit')]);
            }

            $input = $request->validated();
            $input = $this->prepareJobInput($input, $request);

            $job = $this->jobRepository->update($input, $job);

            // Clear relevant caches
            $this->clearJobCaches();

            Flash::success(__('messages.flash.job_update'));

            return redirect(route('jobs.index'));
            
        } catch (Exception $e) {
            logger()->error('Job update failed', [
                'job_id' => $job->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            Flash::error(__('messages.flash.job_update_error'));
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified Job from storage.
     *
     * @param  Job  $job
     * @param  DestroyJobRequest  $request
     * @return RedirectResponse
     */
    public function destroy(Job $job, DestroyJobRequest $request): RedirectResponse
    {
        try {
            $this->jobRepository->delete($job->id);

            // Clear relevant caches
            $this->clearJobCaches();

            Flash::success(__('messages.flash.job_delete'));

            return redirect(route('jobs.index'));
        } catch (Exception $e) {
            logger()->error('Job deletion failed', [
                'user_id' => auth()->id(),
                'job_id' => $job->id,
                'error' => $e->getMessage()
            ]);

            Flash::error(__('messages.flash.job_delete_error'));
            return redirect()->back();
        }
    }

    /**
     * Get states for a country with caching.
     */
    public function getStates(GetStatesJobRequest $request)
    {
        $countryId = $request->get('postal');

        $states = cache()->remember("states.{$countryId}", 3600, function () use ($countryId) {
            return getStates($countryId);
        });

        return $this->sendResponse($states, 'Retrieved successfully');
    }

    /**
     * Get cities for a state with caching.
     */
    public function getCities(GetCitiesJobRequest $request)
    {
        $stateId = $request->get('state');

        $cities = cache()->remember("cities.{$stateId}", 3600, function () use ($stateId) {
            return getCities($stateId);
        });

        return $this->sendResponse($cities, 'Retrieved successfully');
    }

    /**
     * Prepare job input data.
     */
    private function prepareJobInput(array $input, StoreRequest $request): array
    {
        $input['hide_salary'] = isset($input['hide_salary']) ? 1 : 0;
        $input['is_freelance'] = isset($input['is_freelance']) ? 1 : 0;
        $input['status'] = isset($request->saveAsDraft) ? Job::STATUS_DRAFT : Job::STATUS_OPEN;
        
        // Add user context
        $input['company_id'] = auth()->user()->company->id ?? null;
        
        return $input;
    }

    /**
     * Check if user can edit the job.
     */
    private function canUserEditJob(Job $job): bool
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return true;
        }
        
        return $job->company->user_id === $user->id;
    }

    /**
     * Get location data for job editing.
     */
    private function getLocationData(Job $job): array
    {
        $states = null;
        $cities = null;
        
        if ($job->country_id) {
            $states = cache()->remember("states.{$job->country_id}", 3600, function () use ($job) {
                return getStates($job->country_id);
            });
        }
        
        if ($job->state_id) {
            $cities = cache()->remember("cities.{$job->state_id}", 3600, function () use ($job) {
                return getCities($job->state_id);
            });
        }
        
        return [$states, $cities];
    }

    /**
     * Clear job-related caches.
     */
    private function clearJobCaches(): void
    {
        cache()->forget('job.featured');
        cache()->forget('jobs.active');
        cache()->forget("user.{auth()->id()}.can_create_job");
    }

    /**
     * Check job creation limit with improved logic.
     */
    public function checkJobLimit(): bool
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Get user's subscription or plan limits
        $subscription = $user->subscriptions()->active()->first();
        
        if (!$subscription) {
            return false; // No active subscription
        }
        
        $plan = $subscription->plan;
        $currentJobCount = Job::where('company_id', $user->company->id)
            ->where('status', Job::STATUS_OPEN)
            ->count();
            
        return $currentJobCount < $plan->job_limit;
    }

    /**
     * @param  Request  $request
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function getJobs(GetJobsJobRequest $request): View
    {
        return view('admin.jobs.index');
    }

    /**
     * @return Factory|View
     */
    public function createJob(GetJobsJobRequest $request): View
    {
        $data = $this->jobRepository->prepareData();

        return view('admin.jobs.create')->with('data', $data);
    }

    /**
     * @return RedirectResponse|Redirector
     *
     * @throws Throwable
     */
    public function storeJob(CreateJobStoreJobJobRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['hide_salary'] = (isset($input['hide_salary'])) ? 1 : 0;
        $input['is_freelance'] = (isset($input['is_freelance'])) ? 1 : 0;
        $input['status'] = Job::STATUS_OPEN;
        $this->jobRepository->store($input);

        Flash::success(__('messages.flash.job_save'));

        return redirect(route('admin.jobs.index'));
    }

    /**
     * Show the form for editing the specified Job.
     *
     * @return Factory|View
     */
    public function editJob(Job $job)
    {
        if ($job->status == Job::STATUS_CLOSED) {
            Flash::error(__('messages.flash.close_job'));

            return redirect(route('admin.jobs.index'));
        }
        $data = $this->jobRepository->prepareData();
        $data['jobTags'] = $job->jobsTag()->pluck('tag_id')->toArray();
        $states = $cities = null;
        if (isset($job->country_id)) {
            $states = getStates($job->country_id);
        }
        if (isset($job->state_id)) {
            $cities = getCities($job->state_id);
        }
        // Use new scopes for active countries
        $countries = Country::active()->alphabetical()->pluck('name', 'id');

        return view('jobs.edit', compact('data', 'job', 'cities', 'states', 'countries'));
    }

    /**
     * Update the specified Job in storage.
     *
     * @return RedirectResponse|Redirector
     *
     * @throws Throwable
     */
    public function updateJob(Job $job, UpdateJobUpdateJobJobRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['hide_salary'] = (isset($input['hide_salary'])) ? 1 : 0;
        $input['is_freelance'] = (isset($input['is_freelance'])) ? 1 : 0;

        $this->jobRepository->update($input, $job);

        Flash::success(__('messages.flash.job_update'));

        return redirect(route('admin.jobs.index'));
    }

    /**
     * Display the specified Job.
     *
     * @return Factory|View
     */
    public function showJobs(Job $job, ShowJobsJobRequest $request): View
    {
        $job->load([
            'company.user',
            'jobCategory',
            'jobType',
            'careerLevel',
            'functionalArea',
            'jobsSkill',
            'jobsTag'
        ]);

        return view('admin.jobs.show')->with('job', $job);
    }

    /**
     * Remove the specified Job from storage.
     *
     * @return RedirectResponse|Redirector
     *
     * @throws Exception
     */
    public function delete(Job $job, DeleteJobRequest $request)
    {
        if ($job->company_id != getLoggedInUser()->company->id) {
            return $this->sendError(__('messages.common.seems_message'));
        }

        if ($job->status == Job::STATUS_OPEN && $job->is_featured == 1) {
            $featuredRecord = FeaturedRecord::where('owner_type', Job::class)->where('owner_id',
                $job->id)->first();
            if (! empty($featuredRecord)) {
                $featuredRecord->delete();
            }
        }

        $this->jobRepository->delete($job->id);

        return $this->sendSuccess(__('messages.flash.job_delete'));
    }

    /**
     * Toggle the suspension status of a Job.
     *
     * @param  Job  $job
     * @param  ChangeIsSuspendedJobRequest  $request
     * @return RedirectResponse
     */
    public function changeIsSuspended(Job $job, ChangeIsSuspendedJobRequest $request): RedirectResponse
    {
        try {
            $isSuspended = $job->is_suspended ? Job::NOT_SUSPENDED : Job::STATUS_SUSPENDED;
            $job->update(['is_suspended' => $isSuspended]);

            // Clear relevant caches
            $this->clearJobCaches();

            $message = $job->is_suspended ? __('messages.flash.job_suspended') : __('messages.flash.job_unsuspended');
            Flash::success($message);

            return redirect(route('reported-jobs.index'));
        } catch (Exception $e) {
            logger()->error('Job suspension toggle failed', [
                'user_id' => auth()->id(),
                'job_id' => $job->id,
                'error' => $e->getMessage()
            ]);

            Flash::error(__('messages.flash.job_suspension_error'));
            return redirect()->back();
        }
    }

    /**
     * @param  Request  $request
     * @return Application|Factory
     */
    public function showReportedJobs(ShowReportedJobsJobRequest $request): View
    {
        return view('admin.reported_jobs.index');
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function changeJobStatus($id, $status, ChangeJobStatusJobRequest $request)
    {
        $job = Job::findOrFail($id);

        if ($job->company_id != getLoggedInUser()->company->id) {
            return $this->sendError(__('messages.common.seems_message'));
        }

        $job->update(['status' => $status]);

        return $this->sendSuccess(__('messages.flash.status_change'));
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function deleteReportedJobs(ReportedJob $reportedJob, DeleteReportedJobsJobRequest $request)
    {
        $reportedJob->delete();

        return $this->sendSuccess(__('messages.flash.reported_job_delete'));
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function showReportedJobNote(ReportedJob $reportedJob, ShowReportedJobNoteJobRequest $request)
    {
        return $this->sendResponse($reportedJob, 'Reported Job retrieved successfully.');
    }

    /**
     * @return mixed
     */
    public function makeFeatured($jobId, MakeFeaturedJobRequest $request)
    {
        try {
            $user = getLoggedInUser();
            $plan = $user->company->activeSubscription->plan;
            $featuredJobLimit = $plan->featured_job_limit;
            $currentFeaturedJobs = Job::whereCompanyId($user->company->id)->whereIsFeatured(true)->count();
            if ($currentFeaturedJobs >= $featuredJobLimit) {
                return $this->sendError(__('messages.flash.featured_job_limit_over'));
            }

            $job = Job::findOrFail($jobId);
            if ($job->company_id != $user->company->id) {
                return $this->sendError(__('messages.common.seems_message'));
            }

            if ($job->status != Job::STATUS_OPEN) {
                return $this->sendError(__('messages.flash.only_open_job'));
            }

            DB::beginTransaction();

            $job->update(['is_featured' => 1]);
            $startDate = Carbon::now();
            $endDate = Carbon::now()->addDays(FrontSetting::where('key', 'featured_jobs_days')->first()->value);
            $featuredRecord = FeaturedRecord::create([
                'owner_type' => Job::class,
                'owner_id' => $job->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            if (NotificationSetting::where('key', 'NEW_FEATURED_JOB_AVAILABLE')->first()->value == 1) {
                $users = getAdminNotificationUserIds();
                foreach ($users as $userId) {
                    addNotification([
                        Notification::NEW_FEATURED_JOB_AVAILABLE,
                        $userId,
                        Notification::ADMIN,
                        $job->job_title.' job is featured from '.$startDate.' to '.$endDate,
                    ]);
                }
            }

            $transaction = Transaction::create([
                'owner_type' => Job::class,
                'owner_id' => $job->id,
                'user_id' => $user->id,
                'amount' => FrontSetting::where('key', 'featured_jobs_price')->first()->value,
                'type' => Transaction::FEATURED_JOB,
            ]);

            DB::commit();

            return $this->sendSuccess(__('messages.flash.job_featured'));
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function makeUnFeatured($jobId, MakeUnFeaturedJobRequest $request)
    {
        $job = Job::findOrFail($jobId);
        if ($job->company_id != getLoggedInUser()->company->id) {
            return $this->sendError(__('messages.common.seems_message'));
        }

        $job->update(['is_featured' => 0]);
        $featuredRecord = FeaturedRecord::where('owner_type', Job::class)->where('owner_id', $job->id)->first();
        if (! empty($featuredRecord)) {
            $featuredRecord->delete();
        }

        return $this->sendSuccess(__('messages.flash.job_unfeatured'));
    }

    /**
     * @param  Request  $request
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function getExpiredJobs(GetExpiredJobsJobRequest $request): View
    {
        return view('admin.expired_jobs.index');
    }
}
