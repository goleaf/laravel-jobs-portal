<?php

namespace App\Http\Controllers;

use App\Actions\CreateJob;
use App\Actions\ProcessJobApplication;
use App\Actions\PublishJob;
use App\Actions\SendJobAlert;
use App\Dtos\JobApplicationData;
use App\Dtos\JobData;
use App\Http\Requests\CreateJobRequest;
use App\Http\Requests\JobApplicationRequest;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * ActionableJobController - Demonstrating Clean Architecture with Actionable Package
 * 
 * This controller showcases how to use the Actionable package to:
 * - Eliminate fat controllers
 * - Create reusable business logic
 * - Enable easy testing and maintenance
 * - Support background processing
 */
class ActionableJobController extends Controller
{
    /**
     * Create a new job using clean action-based architecture
     * 
     * ✅ Before Actionable: 200+ lines of controller logic
     * ✅ After Actionable: Clean, focused, single responsibility
     */
    public function store(CreateJobRequest $request): JsonResponse
    {
        try {
            // 🎯 Convert request to DTO with automatic validation and transformation
            $jobData = JobData::fromArray($request->validated());
            
            // 🚀 Execute business logic in a single, expressive call
            $job = CreateJob::run($jobData, auth()->id());
            
            // 📱 Transform to API-friendly response using DTO
            return response()->json([
                'success' => true,
                'message' => 'Job created successfully',
                'data' => JobData::fromModel($job)->toArray(),
                'job_id' => $job->id,
                'slug' => $job->slug
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Job creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request_data' => $request->validated()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create job',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Process job application with comprehensive business logic
     * 
     * ✅ Includes: Validation, notifications, analytics, background processing
     * ✅ All in one clean action call!
     */
    public function applyForJob(JobApplicationRequest $request, Job $job): JsonResponse
    {
        try {
            // 🎯 Create application DTO with smart field mapping
            $applicationData = JobApplicationData::fromArray(
                array_merge($request->validated(), [
                    'job_id' => $job->id,
                    'candidate_id' => auth()->user()->candidate->id,
                    'applied_at' => now(),
                    'application_source' => $request->header('User-Agent') ? 'web' : 'api'
                ])
            );
            
            // 🚀 Process application with full business logic
            $application = ProcessJobApplication::run($applicationData);
            
            // 📊 Return comprehensive response
            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => JobApplicationData::fromModel($application)->toArray(),
                'application_id' => $application->id,
                'status' => $application->getStatusDisplayName(),
                'next_steps' => $this->getApplicationNextSteps($application)
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Publish job with background processing
     * 
     * ✅ Auto-notifications, SEO updates, social posting - all handled!
     */
    public function publish(Job $job): JsonResponse
    {
        try {
            // 🎯 Single action call handles complex publishing workflow
            $publishedJob = PublishJob::run($job);
            
            // 🔄 Queue background tasks for immediate response
            SendJobAlert::dispatch($publishedJob);
            
            return response()->json([
                'success' => true,
                'message' => 'Job published successfully',
                'data' => JobData::fromModel($publishedJob)->toArray(),
                'published_at' => $publishedJob->published_at?->format('Y-m-d H:i:s'),
                'expiry_date' => $publishedJob->job_expiry_date?->format('Y-m-d')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get job applications with filtered data based on user role
     * 
     * ✅ DTO automatically handles field filtering and privacy settings
     */
    public function getApplications(Job $job, Request $request): JsonResponse
    {
        $applications = $job->appliedJobs()
            ->with(['candidate'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->recent, function ($query) {
                return $query->where('applied_at', '>=', now()->subDays(7));
            })
            ->orderBy('applied_at', 'desc')
            ->paginate($request->per_page ?? 15);

        // 🎯 Transform each application using DTO with privacy controls
        $transformedApplications = $applications->getCollection()->map(function ($application) {
            $applicationData = JobApplicationData::fromModel($application);
            
            // 🔒 Automatically handle privacy settings from DTO
            return $applicationData->toArray();
        });

        return response()->json([
            'success' => true,
            'data' => $transformedApplications,
            'pagination' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total()
            ],
            'statistics' => [
                'total_applications' => $job->application_count,
                'pending_review' => $job->appliedJobs()->pending()->count(),
                'shortlisted' => $job->appliedJobs()->shortlisted()->count(),
                'hired' => $job->appliedJobs()->hired()->count()
            ]
        ]);
    }

    /**
     * Batch operations using actions - process multiple applications
     * 
     * ✅ Clean, consistent, reusable across different contexts
     */
    public function batchUpdateApplications(Request $request): JsonResponse
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:job_applications,id',
            'action' => 'required|in:approve,reject,shortlist',
            'notes' => 'nullable|string|max:1000'
        ]);

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($request->application_ids as $applicationId) {
            try {
                $application = JobApplication::findOrFail($applicationId);
                
                // 🎯 Use dedicated actions for each operation
                match($request->action) {
                    'approve' => ApproveJobApplication::run($application, $request->notes),
                    'reject' => RejectJobApplication::run($application, $request->notes),
                    'shortlist' => ShortlistJobApplication::run($application, $request->notes),
                };
                
                $results[] = [
                    'id' => $applicationId,
                    'status' => 'success',
                    'new_status' => $application->fresh()->status
                ];
                $successCount++;
                
            } catch (\Exception $e) {
                $results[] = [
                    'id' => $applicationId,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
                $errorCount++;
            }
        }

        return response()->json([
            'success' => $errorCount === 0,
            'message' => "Processed {$successCount} applications successfully" . 
                        ($errorCount > 0 ? ", {$errorCount} failed" : ""),
            'results' => $results,
            'summary' => [
                'total_processed' => count($request->application_ids),
                'successful' => $successCount,
                'failed' => $errorCount
            ]
        ]);
    }

    /**
     * Get application next steps based on current status
     */
    private function getApplicationNextSteps(JobApplication $application): array
    {
        return match($application->status) {
            'pending' => [
                'message' => 'Your application is being reviewed',
                'estimated_response_time' => '3-5 business days',
                'actions' => ['You can track your application status in your dashboard']
            ],
            'reviewed' => [
                'message' => 'Your application has been reviewed',
                'actions' => ['Wait for further updates from the employer']
            ],
            'shortlisted' => [
                'message' => 'Congratulations! You have been shortlisted',
                'actions' => ['Prepare for potential interview invitation']
            ],
            default => [
                'message' => 'Application status updated',
                'actions' => ['Check your email for updates']
            ]
        };
    }
}

/*
🎉 ACTIONABLE BENEFITS DEMONSTRATED:

✅ FAT CONTROLLER ELIMINATION
   - Before: 200+ lines of business logic in controllers
   - After: Clean, focused controller methods (10-20 lines each)

✅ REUSABLE BUSINESS LOGIC
   - ProcessJobApplication::run() can be used in API, web, CLI, tests
   - Same logic everywhere, no duplication

✅ BACKGROUND PROCESSING MADE EASY
   - Change ProcessJobApplication::run() to ::dispatch()
   - Instant queue support without code changes

✅ SMART DTOS WITH ATTRIBUTES
   - #[FieldName] for API-friendly naming
   - #[DateFormat] for consistent date formatting
   - #[Ignore] for privacy protection
   - #[ArrayOf] for nested object handling

✅ TESTABILITY
   - Each action can be unit tested independently
   - Mock actions easily in integration tests
   - Clear separation of concerns

✅ MAINTAINABILITY
   - Single Responsibility Principle enforced
   - Easy to modify business logic without touching controllers
   - Consistent patterns across the application

✅ DEVELOPER EXPERIENCE
   - IntelliSense support for all actions
   - Clear, expressive code that tells a story
   - Easy onboarding for new developers
*/ 