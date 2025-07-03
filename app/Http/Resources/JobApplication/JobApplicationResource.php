<?php

namespace App\Http\Resources\JobApplication;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class JobApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isCandidate = $user && $user->id === $this->candidate_id;
        $isEmployer = $user && $user->hasRole('employer')
            && $user->companies()->pluck('id')->contains($this->job->company_id ?? null);
        $isAdmin = $user && $user->hasRole('admin');
        $canViewPrivate = $isCandidate || $isEmployer || $isAdmin;

        return [
            // Basic Information
            'id' => $this->id,
            'status' => $this->status,
            'status_label' => __('job_application.status.'.$this->status),
            'application_date' => $this->created_at,
            'application_date_human' => $this->created_at->diffForHumans(),

            // Job Information
            'job' => $this->whenLoaded('job', function () {
                return [
                    'id' => $this->job->id,
                    'title' => $this->job->title,
                    'slug' => $this->job->slug,
                    'status' => $this->job->status,
                    'deadline' => $this->job->deadline,
                    'is_active' => $this->job->is_active,
                    'location' => $this->job->location,
                    'job_type' => $this->whenLoaded('job.jobType', function () {
                        return [
                            'id' => $this->job->jobType->id,
                            'name' => $this->job->jobType->name,
                        ];
                    }),
                    'category' => $this->whenLoaded('job.category', function () {
                        return [
                            'id' => $this->job->category->id,
                            'name' => $this->job->category->name,
                        ];
                    }),
                ];
            }),

            // Company Information
            'company' => $this->whenLoaded('job.company', function () {
                return [
                    'id' => $this->job->company->id,
                    'name' => $this->job->company->name,
                    'slug' => $this->job->company->slug,
                    'logo' => $this->job->company->logo,
                    'is_verified' => $this->job->company->is_profile_verified,
                ];
            }),

            // Candidate Information (visible to employers and admins)
            'candidate' => $this->when($canViewPrivate, function () {
                return $this->whenLoaded('candidate', function () {
                    return [
                        'id' => $this->candidate->id,
                        'name' => $this->candidate->name,
                        'email' => $this->candidate->email,
                        'phone' => $this->candidate->phone,
                        'avatar' => $this->candidate->avatar,
                        'profile' => $this->whenLoaded('candidate.candidateProfile', function () {
                            return [
                                'experience_years' => $this->candidate->candidateProfile->experience ?? 0,
                                'current_salary' => $this->candidate->candidateProfile->current_salary,
                                'expected_salary' => $this->candidate->candidateProfile->expected_salary,
                                'is_available' => $this->candidate->candidateProfile->is_available,
                                'location' => $this->candidate->candidateProfile->full_address,
                            ];
                        }),
                    ];
                });
            }),

            // Application Details
            'application_details' => [
                'expected_salary' => $this->when($canViewPrivate, $this->expected_salary),
                'salary_currency' => $this->when($canViewPrivate, $this->salary_currency),
                'cover_letter' => $this->when(
                    $canViewPrivate && $this->cover_letter,
                    \Str::limit($this->cover_letter, 200)
                ),
                'has_cover_letter' => ! empty($this->cover_letter),
                'availability_date' => $this->availability_date,
                'notice_period' => $this->notice_period,
                'willing_to_relocate' => $this->willing_to_relocate,
                'willing_to_travel' => $this->willing_to_travel,
                'remote_work_preference' => $this->remote_work_preference,
            ],

            // Resume Information
            'resume' => $this->whenLoaded('resume', function () {
                return [
                    'id' => $this->resume->id,
                    'name' => $this->resume->name,
                    'file_url' => $this->resume->file_url,
                    'file_size' => $this->resume->file_size,
                    'updated_at' => $this->resume->updated_at,
                ];
            }),

            // Skills
            'skills' => $this->whenLoaded('skills', function () {
                return $this->skills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'category' => $skill->category,
                    ];
                });
            }),

            // Application Progress
            'progress' => [
                'current_stage' => $this->status,
                'stages' => [
                    'applied' => [
                        'completed' => true,
                        'date' => $this->created_at,
                        'label' => __('job_application.stages.applied'),
                    ],
                    'reviewed' => [
                        'completed' => in_array($this->status, ['shortlisted', 'interview', 'hired', 'rejected']),
                        'date' => $this->reviewed_at,
                        'label' => __('job_application.stages.reviewed'),
                    ],
                    'shortlisted' => [
                        'completed' => in_array($this->status, ['shortlisted', 'interview', 'hired']),
                        'date' => $this->shortlisted_at,
                        'label' => __('job_application.stages.shortlisted'),
                    ],
                    'interview' => [
                        'completed' => in_array($this->status, ['interview', 'hired']),
                        'date' => $this->interview_scheduled_at,
                        'label' => __('job_application.stages.interview'),
                    ],
                    'hired' => [
                        'completed' => $this->status === 'hired',
                        'date' => $this->hired_at,
                        'label' => __('job_application.stages.hired'),
                    ],
                ],
                'progress_percentage' => $this->getProgressPercentage(),
            ],

            // Statistics
            'statistics' => [
                'views_count' => $this->views_count ?? 0,
                'downloads_count' => $this->downloads_count ?? 0,
                'notes_count' => $this->whenCounted('notes'),
                'interviews_count' => $this->whenCounted('interviews'),
            ],

            // Timestamps
            'timestamps' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'created_at_human' => $this->created_at->diffForHumans(),
                'updated_at_human' => $this->updated_at->diffForHumans(),
            ],

            // User Context
            'user_context' => [
                'can_edit' => $isCandidate,
                'can_withdraw' => $isCandidate && in_array($this->status, ['pending', 'shortlisted']),
                'can_view_details' => $canViewPrivate,
                'can_update_status' => $isEmployer || $isAdmin,
                'can_schedule_interview' => $isEmployer || $isAdmin,
                'can_add_notes' => $isEmployer || $isAdmin,
                'can_download_resume' => $canViewPrivate,
            ],

            // Links
            'links' => [
                'show' => route('api.job-applications.show', $this->id),
                'edit' => $this->when($isCandidate, route('api.job-applications.edit', $this->id)),
                'job' => route('api.jobs.show', $this->job_id),
                'company' => route('api.companies.show', $this->job->company_id ?? 0),
                'candidate' => $this->when($canViewPrivate, route('api.candidates.show', $this->candidate_id)),
                'resume_download' => $this->when(
                    $canViewPrivate && $this->resume,
                    route('api.resumes.download', $this->resume_id)
                ),
            ],

            // SEO & Meta
            'seo' => [
                'title' => __('job_application.seo_title', [
                    'job' => $this->job->title ?? '',
                    'company' => $this->job->company->name ?? '',
                ]),
                'description' => __('job_application.seo_description', [
                    'status' => __('job_application.status.'.$this->status),
                    'date' => $this->created_at->format('M d, Y'),
                ]),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'locale' => app()->getLocale(),
                'currency' => config('app.currency', 'USD'),
                'timezone' => $request->user()?->timezone ?? config('app.timezone'),
                'generated_at' => now()->toISOString(),
            ],
        ];
    }

    /**
     * Calculate application progress percentage.
     */
    protected function getProgressPercentage(): int
    {
        $statusProgress = [
            'pending' => 20,
            'reviewed' => 40,
            'shortlisted' => 60,
            'interview' => 80,
            'hired' => 100,
            'rejected' => 0,
        ];

        return $statusProgress[$this->status] ?? 0;
    }

    /**
     * Check if user can view candidate information.
     */
    private function canViewCandidateInfo(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Candidate can view their own info
        if (Auth::id() === $this->candidate_id) {
            return true;
        }

        // Employer can view candidate info for their jobs
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view application details.
     */
    private function canViewApplicationDetails(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Candidate can view their own application details
        if (Auth::id() === $this->candidate_id) {
            return true;
        }

        // Employer can view details for their jobs
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view resume.
     */
    private function canViewResume(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Candidate can view their own resume
        if (Auth::id() === $this->candidate_id) {
            return true;
        }

        // Employer can view resume if application is shortlisted or beyond
        if (Auth::user()->hasRole('Employer')
            && Auth::id() === $this->job->company->user_id
            && in_array($this->status, ['shortlisted', 'interviewed', 'hired'])) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view metadata.
     */
    private function canViewMetadata(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view feedback.
     */
    private function canViewFeedback(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Candidate can view feedback on their application
        if (Auth::id() === $this->candidate_id && $this->feedback_notes) {
            return true;
        }

        // Employer can view feedback they provided
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view communications.
     */
    private function canViewCommunications(): bool
    {
        return $this->canViewApplicationDetails();
    }

    /**
     * Check if user can view interview notes.
     */
    private function canViewInterviewNotes(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Employer can view notes for their jobs
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view interview feedback.
     */
    private function canViewInterviewFeedback(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Candidate can view feedback on their interview
        if (Auth::id() === $this->candidate_id) {
            return true;
        }

        // Employer can view feedback they provided
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view documents.
     */
    private function canViewDocuments(): bool
    {
        return $this->canViewApplicationDetails();
    }

    /**
     * Check if user can view statistics.
     */
    private function canViewStatistics(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Employer can view statistics for their jobs
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view next actions.
     */
    private function canViewNextActions(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Employer can view next actions for their jobs
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if application has portfolio links.
     */
    private function hasPortfolioLinks(): bool
    {
        return $this->portfolio_url || $this->github_url || $this->linkedin_url;
    }

    /**
     * Check if user can edit the application.
     */
    private function canEdit(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Candidate can edit their own application if it's still in applied status
        if (Auth::id() === $this->candidate_id && $this->status === 'applied') {
            return true;
        }

        return false;
    }

    /**
     * Check if user can withdraw the application.
     */
    private function canWithdraw(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Candidate can withdraw their own application if not hired/rejected
        if (Auth::id() === $this->candidate_id
            && ! in_array($this->status, ['hired', 'rejected', 'withdrawn'])) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can view the application.
     */
    private function canView(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Candidate can view their own application
        if (Auth::id() === $this->candidate_id) {
            return true;
        }

        // Employer can view applications for their jobs
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    // Additional permission methods would be implemented here...
}
