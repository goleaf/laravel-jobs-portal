<?php

namespace App\Http\Controllers\Enhanced;

use App\Http\Controllers\AppBaseController;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobApplicationSchedule;
use App\Models\JobStage;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Repositories\JobApplicationRepository;
use App\Http\Requests\Job\StoreJobApplicationRequest;
use App\Http\Requests\Job\DeleteJobApplicationRequest;
use App\Http\Requests\Job\ChangeJobApplicationStatusRequest;
use App\Http\Requests\Job\DownloadMediaJobApplicationRequest;
use App\Http\Requests\Job\ChangeJobStageJobApplicationRequest;
use App\Http\Requests\Job\ViewSlotsScreenJobApplicationRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Enhanced JobApplicationController - Enhanced patterns implementation
 * 
 * Demonstrates modern Laravel controller patterns with:
 * - Advanced caching strategies
 * - Comprehensive error handling
 * - Performance optimization
 * - Enhanced repository usage
 * - Bulk operations support
 * - Interview scheduling management
 * - Status workflow management
 */
class JobApplicationController extends AppBaseController
{
    /** @var JobApplicationRepository */
    private $jobApplicationRepository;

    /**
     * Cache TTL for job application operations (30 minutes)
     */
    private const CACHE_TTL = 1800;

    public function __construct(JobApplicationRepository $jobApplicationRepository)
    {
        $this->jobApplicationRepository = $jobApplicationRepository;
    }

    /**
     * Display a listing of job applications with enhanced filtering and search
     */
    public function index(int $jobId, StoreJobApplicationRequest $request)
    {
        try {
            // Verify job ownership with enhanced security
            if (!$this->verifyJobOwnership($jobId)) {
                if ($this->isApiRequest($request)) {
                    return $this->sendError('Unauthorized access to job applications', 403);
                }
                return view('errors.404');
            }

            // Check if this is an API request
            if ($this->isApiRequest($request)) {
                return $this->getJobApplicationsApi($jobId, $request);
            }

            // For web requests, return the view with enhanced data
            $data = $this->prepareJobApplicationsIndexData($jobId, $request);
            return view('employer.job_applications.index', $data);

        } catch (Exception $e) {
            Log::error('Error in JobApplicationController@index', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            if ($this->isApiRequest($request)) {
                return $this->sendServerError('Failed to retrieve job applications');
            }

            return redirect()->back()->with('error', 'Failed to load job applications');
        }
    }

    /**
     * Get job applications for API requests with enhanced filtering
     */
    private function getJobApplicationsApi(int $jobId, Request $request): JsonResponse
    {
        $cacheKey = $this->buildCacheKey('job_applications.api', array_merge($request->all(), ['job_id' => $jobId]));

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($jobId, $request) {
            $query = JobApplication::where('job_id', $jobId)
                                  ->with(['candidate.user', 'job', 'jobStage']);

            // Apply Enhanced scopes for filtering
            if ($request->filled('status')) {
                $query->byStatus($request->get('status'));
            }

            if ($request->filled('stage_id')) {
                $query->byStage($request->get('stage_id'));
            }

            if ($request->filled('search')) {
                $query->search($request->get('search'));
            }

            if ($request->filled('date_from')) {
                $query->appliedAfter($request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->appliedBefore($request->get('date_to'));
            }

            if ($request->filled('experience_level')) {
                $query->byExperienceLevel($request->get('experience_level'));
            }

            // Apply sorting
            $sortBy = $request->get('sort', 'created_at');
            $sortDirection = $request->get('direction', 'desc');
            
            if (in_array($sortBy, ['created_at', 'updated_at', 'status', 'expected_salary'])) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->latest();
            }

            return $query->paginate($request->get('per_page', 15));
        });

        return $this->sendPaginatedResponse($data, 'Job applications retrieved successfully');
    }

    /**
     * Prepare data for job applications index view
     */
    private function prepareJobApplicationsIndexData(int $jobId, Request $request): array
    {
        $cacheKey = $this->buildCacheKey('job_applications.index.data', array_merge($request->all(), ['job_id' => $jobId]));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($jobId, $request) {
            // Get job with enhanced data
            $job = Job::with(['city', 'company', 'jobType', 'category'])->findOrFail($jobId);

            // Get job stages for the company
            $jobStage = JobStage::whereCompanyId(getLoggedInUser()->owner_id)
                              ->active()
                              ->orderBy('sort_order')
                              ->pluck('name', 'id');

            // Get status array
            $statusArray = JobApplication::STATUS;

            // Get applications with enhanced filtering
            $applications = JobApplication::where('job_id', $jobId)
                                        ->with(['candidate.user', 'jobStage'])
                                        ->when($request->filled('status'), function ($query) use ($request) {
                                            $query->byStatus($request->get('status'));
                                        })
                                        ->when($request->filled('stage_id'), function ($query) use ($request) {
                                            $query->byStage($request->get('stage_id'));
                                        })
                                        ->latest()
                                        ->paginate(20);

            // Get application statistics
            $statistics = $this->getJobApplicationStatistics($jobId);

            return [
                'jobId' => $jobId,
                'job' => $job,
                'jobStage' => $jobStage,
                'statusArray' => $statusArray,
                'applications' => $applications,
                'statistics' => $statistics,
                'filters' => $request->only(['status', 'stage_id', 'search'])
            ];
        });
    }

    /**
     * Get job application statistics for dashboard
     */
    private function getJobApplicationStatistics(int $jobId): array
    {
        return Cache::remember("job_applications.statistics.{$jobId}", self::CACHE_TTL, function () use ($jobId) {
            $baseQuery = JobApplication::where('job_id', $jobId);

            return [
                'total_applications' => $baseQuery->count(),
                'pending_applications' => $baseQuery->pending()->count(),
                'shortlisted_applications' => $baseQuery->shortlisted()->count(),
                'rejected_applications' => $baseQuery->rejected()->count(),
                'selected_applications' => $baseQuery->selected()->count(),
                'recent_applications' => $baseQuery->recent(7)->count(),
                'applications_today' => $baseQuery->today()->count(),
                'average_expected_salary' => $baseQuery->avg('expected_salary'),
                'conversion_rate' => $this->calculateConversionRate($jobId)
            ];
        });
    }

    /**
     * Remove the specified job application with enhanced validation
     */
    public function destroy(JobApplication $jobApplication, DeleteJobApplicationRequest $request): JsonResponse
    {
        try {
            $jobId = $request->get('jobId');

            // Enhanced security validation
            if (!$this->verifyJobApplicationOwnership($jobApplication, $jobId)) {
                return $this->sendError(__('messages.common.seems_message'), 403);
            }

            DB::beginTransaction();

            // Check if application has scheduled interviews
            $hasSchedules = JobApplicationSchedule::where('job_application_id', $jobApplication->id)->exists();
            
            if ($hasSchedules) {
                // Archive instead of delete if has schedules
                $jobApplication->update([
                    'status' => JobApplication::CANCELLED,
                    'deleted_at' => now(),
                    'deleted_by' => auth()->id()
                ]);
                $message = 'Job application archived due to existing interview schedules';
            } else {
                $this->jobApplicationRepository->delete($jobApplication->id);
                $message = __('messages.flash.job_application_delete');
            }

            // Clear related caches
            $this->clearJobApplicationCaches($jobId, $jobApplication->id);

            // Log the deletion
            Log::info('Job application deleted/archived', [
                'job_application_id' => $jobApplication->id,
                'job_id' => $jobId,
                'action' => $hasSchedules ? 'archived' : 'deleted',
                'deleted_by' => auth()->id()
            ]);

            DB::commit();

            return $this->sendSuccess($message);

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error deleting job application', [
                'job_application_id' => $jobApplication->id,
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to delete job application');
        }
    }

    /**
     * Change job application status with enhanced workflow management
     */
    public function changeJobApplicationStatus($id, $status, ChangeJobApplicationStatusRequest $request): JsonResponse
    {
        try {
            $jobId = $request->get('jobId');

            // Enhanced security validation
            if (!$this->verifyJobApplicationOwnership($id, $jobId)) {
                return $this->sendError(__('messages.common.seems_message'), 403);
            }

            DB::beginTransaction();

            $jobApplication = JobApplication::with(['candidate.user', 'job'])->findOrFail($id);

            // Enhanced status validation
            if (!$this->canChangeStatus($jobApplication->status, $status)) {
                return $this->sendError(
                    JobApplication::STATUS[$jobApplication->status] . ' job cannot be ' . JobApplication::STATUS[$status]
                );
            }

            // Update status with audit trail
            $oldStatus = $jobApplication->status;
            $jobApplication->update([
                'status' => $status,
                'status_changed_by' => auth()->id(),
                'status_changed_at' => now()
            ]);

            // Send notifications based on status change
            $this->sendStatusChangeNotification($jobApplication, $status);

            // Clear related caches
            $this->clearJobApplicationCaches($jobId, $id);

            // Log the status change
            Log::info('Job application status changed', [
                'job_application_id' => $id,
                'job_id' => $jobId,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'changed_by' => auth()->id()
            ]);

            DB::commit();

            return $this->sendSuccess(__('messages.flash.status_change'));

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error changing job application status', [
                'job_application_id' => $id,
                'status' => $status,
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to change application status');
        }
    }

    /**
     * Download media with enhanced security and error handling
     */
    public function downloadMedia(DownloadMediaJobApplicationRequest $request)
    {
        try {
            $jobApplicationId = $request->jobApplication;
            
            $jobApplication = JobApplication::where('id', $jobApplicationId)
                                          ->whereHas('job', function ($q) {
                                              $q->where('company_id', getLoggedInUser()->company->id);
                                          })
                                          ->first();

            if (!$jobApplication) {
                if ($this->isApiRequest($request)) {
                    return $this->sendError('Job application not found', 404);
                }
                return view('errors.404');
            }

            [$file, $headers] = $this->jobApplicationRepository->downloadMedia($jobApplication);

            // Log the download
            Log::info('Job application media downloaded', [
                'job_application_id' => $jobApplicationId,
                'downloaded_by' => auth()->id(),
                'file_size' => strlen($file)
            ]);

            return response($file, 200, $headers);

        } catch (Exception $e) {
            Log::error('Error downloading job application media', [
                'job_application_id' => $request->jobApplication ?? null,
                'error' => $e->getMessage()
            ]);

            if ($this->isApiRequest($request)) {
                return $this->sendServerError('Failed to download media');
            }

            return view('errors.404');
        }
    }

    /**
     * Change job stage with enhanced validation
     */
    public function changeJobStage(ChangeJobStageJobApplicationRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $jobApplication = JobApplication::findOrFail($request->get('job_application_id'));
            $oldStageId = $jobApplication->job_stage_id;
            $newStageId = $request->get('job_stage');

            $jobApplication->update([
                'job_stage_id' => $newStageId,
                'stage_changed_by' => auth()->id(),
                'stage_changed_at' => now()
            ]);

            // Clear related caches
            $this->clearJobApplicationCaches($jobApplication->job_id, $jobApplication->id);

            // Log the stage change
            Log::info('Job application stage changed', [
                'job_application_id' => $jobApplication->id,
                'old_stage_id' => $oldStageId,
                'new_stage_id' => $newStageId,
                'changed_by' => auth()->id()
            ]);

            DB::commit();

            return $this->sendSuccess(__('messages.flash.job_stage_change'));

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error changing job stage', [
                'job_application_id' => $request->get('job_application_id'),
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to change job stage');
        }
    }

    /**
     * Bulk actions for job applications
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:shortlist,reject,select,delete,change_stage',
            'application_ids' => 'required|array|min:1',
            'application_ids.*' => 'exists:job_applications,id',
            'job_id' => 'required|exists:jobs,id',
            'stage_id' => 'required_if:action,change_stage|exists:job_stages,id'
        ]);

        try {
            DB::beginTransaction();

            $applicationIds = $request->get('application_ids');
            $action = $request->get('action');
            $jobId = $request->get('job_id');
            $affectedCount = 0;

            // Verify ownership of all applications
            $validApplications = JobApplication::whereIn('id', $applicationIds)
                                             ->where('job_id', $jobId)
                                             ->whereHas('job', function ($q) {
                                                 $q->where('company_id', getLoggedInUser()->company->id);
                                             })
                                             ->pluck('id')
                                             ->toArray();

            if (count($validApplications) !== count($applicationIds)) {
                return $this->sendError('Some applications do not belong to your company');
            }

            switch ($action) {
                case 'shortlist':
                    $affectedCount = JobApplication::whereIn('id', $applicationIds)
                                                 ->update([
                                                     'status' => JobApplication::SHORT_LIST,
                                                     'status_changed_by' => auth()->id(),
                                                     'status_changed_at' => now()
                                                 ]);
                    break;

                case 'reject':
                    $affectedCount = JobApplication::whereIn('id', $applicationIds)
                                                 ->update([
                                                     'status' => JobApplication::REJECTED,
                                                     'status_changed_by' => auth()->id(),
                                                     'status_changed_at' => now()
                                                 ]);
                    break;

                case 'select':
                    $affectedCount = JobApplication::whereIn('id', $applicationIds)
                                                 ->update([
                                                     'status' => JobApplication::COMPLETE,
                                                     'status_changed_by' => auth()->id(),
                                                     'status_changed_at' => now()
                                                 ]);
                    break;

                case 'change_stage':
                    $affectedCount = JobApplication::whereIn('id', $applicationIds)
                                                 ->update([
                                                     'job_stage_id' => $request->get('stage_id'),
                                                     'stage_changed_by' => auth()->id(),
                                                     'stage_changed_at' => now()
                                                 ]);
                    break;

                case 'delete':
                    $affectedCount = JobApplication::whereIn('id', $applicationIds)->delete();
                    break;
            }

            // Clear related caches
            $this->clearJobApplicationCaches($jobId);

            // Log the bulk action
            Log::info('Bulk action performed on job applications', [
                'action' => $action,
                'job_id' => $jobId,
                'application_ids' => $applicationIds,
                'affected_count' => $affectedCount,
                'performed_by' => auth()->id()
            ]);

            DB::commit();

            return $this->sendSuccess("Successfully {$action}ed {$affectedCount} application(s)");

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error performing bulk action on job applications', [
                'action' => $request->get('action'),
                'application_ids' => $request->get('application_ids'),
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to perform bulk action');
        }
    }

    /**
     * Verify job ownership with enhanced security
     */
    private function verifyJobOwnership(int $jobId): bool
    {
        $userId = Auth::user()->owner_id;
        return Job::where('id', $jobId)->where('company_id', $userId)->exists();
    }

    /**
     * Verify job application ownership
     */
    private function verifyJobApplicationOwnership($jobApplicationId, int $jobId): bool
    {
        if ($jobApplicationId instanceof JobApplication) {
            $jobApplicationId = $jobApplicationId->id;
        }

        $jobCandidateIds = JobApplication::where('job_id', $jobId)->pluck('id')->toArray();
        return in_array($jobApplicationId, $jobCandidateIds);
    }

    /**
     * Check if status change is allowed
     */
    private function canChangeStatus(int $currentStatus, int $newStatus): bool
    {
        // Prevent changing from final states
        if (in_array($currentStatus, [JobApplication::REJECTED, JobApplication::COMPLETE])) {
            return false;
        }

        return true;
    }

    /**
     * Send notification based on status change
     */
    private function sendStatusChangeNotification(JobApplication $jobApplication, int $status): void
    {
        $candidateUserId = $jobApplication->candidate->user->id;
        $jobTitle = $jobApplication->job->job_title;

        switch ($status) {
            case JobApplication::REJECTED:
                if (NotificationSetting::where('key', 'CANDIDATE_REJECTED_FOR_JOB')->first()?->value == 1) {
                    addNotification([
                        Notification::CANDIDATE_REJECTED_FOR_JOB,
                        $candidateUserId,
                        Notification::CANDIDATE,
                        'Your application is Rejected for ' . $jobTitle,
                    ]);
                }
                break;

            case JobApplication::COMPLETE:
                if (NotificationSetting::where('key', 'CANDIDATE_SELECTED_FOR_JOB')->first()?->value == 1) {
                    addNotification([
                        Notification::CANDIDATE_SELECTED_FOR_JOB,
                        $candidateUserId,
                        Notification::CANDIDATE,
                        'You are selected for ' . $jobTitle,
                    ]);
                }
                break;

            case JobApplication::SHORT_LIST:
                if (NotificationSetting::where('key', 'CANDIDATE_SHORTLISTED_FOR_JOB')->first()?->value == 1) {
                    addNotification([
                        Notification::CANDIDATE_SHORTLISTED_FOR_JOB,
                        $candidateUserId,
                        Notification::CANDIDATE,
                        'Your application is Shortlisted for ' . $jobTitle,
                    ]);
                }
                break;
        }
    }

    /**
     * Calculate conversion rate for job applications
     */
    private function calculateConversionRate(int $jobId): float
    {
        $totalApplications = JobApplication::where('job_id', $jobId)->count();
        $selectedApplications = JobApplication::where('job_id', $jobId)
                                             ->where('status', JobApplication::COMPLETE)
                                             ->count();

        if ($totalApplications === 0) {
            return 0.0;
        }

        return round(($selectedApplications / $totalApplications) * 100, 2);
    }

    /**
     * Clear job application related caches
     */
    private function clearJobApplicationCaches(int $jobId, ?int $applicationId = null): void
    {
        $tags = [
            'job_applications',
            'job_applications.api',
            'job_applications.index',
            "job_applications.statistics.{$jobId}"
        ];

        if ($applicationId) {
            $tags[] = "job_application.{$applicationId}";
        }

        foreach ($tags as $tag) {
            Cache::tags($tag)->flush();
        }

        // Clear specific cache keys
        Cache::forget("job_applications.statistics.{$jobId}");
    }
} 